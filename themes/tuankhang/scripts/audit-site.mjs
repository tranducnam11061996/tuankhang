import process from 'node:process';
import http from 'node:http';
import https from 'node:https';

const TIMEOUT_MS = 10_000;
const CONCURRENCY = Math.max(1, Number.parseInt(process.env.TK_SITE_CONCURRENCY ?? '8', 10) || 8);
const MAX_URLS = 2_000;

function siteArgument() {
  const direct = process.argv.find((argument) => argument.startsWith('--site='));
  return direct ? direct.slice('--site='.length) : 'http://localhost/tuankhang';
}

function attributes(tag) {
  const result = new Map();
  const expression = /([:\w-]+)\s*=\s*(?:"([^"]*)"|'([^']*)'|([^\s>]+))/gu;
  for (const match of tag.matchAll(expression)) {
    result.set(match[1].toLowerCase(), match[2] ?? match[3] ?? match[4] ?? '');
  }
  return result;
}

function metaContent(html, name) {
  for (const match of html.matchAll(/<meta\b[^>]*>/giu)) {
    const values = attributes(match[0]);
    if ((values.get('name') ?? '').toLowerCase() === name) {
      return values.get('content') ?? '';
    }
  }
  return '';
}

function canonicalHref(html) {
  for (const match of html.matchAll(/<link\b[^>]*>/giu)) {
    const values = attributes(match[0]);
    if ((values.get('rel') ?? '').toLowerCase().split(/\s+/u).includes('canonical')) {
      return values.get('href') ?? '';
    }
  }
  return '';
}

function locations(xml) {
  return [...xml.matchAll(/<loc>\s*([^<]+?)\s*<\/loc>/giu)].map((match) => match[1]
    .replaceAll('&amp;', '&')
    .replaceAll('&lt;', '<')
    .replaceAll('&gt;', '>')
    .trim());
}

function internalLinks(html, pageUrl, origin) {
  const links = [];
  for (const match of html.matchAll(/<a\b[^>]*>/giu)) {
    const href = attributes(match[0]).get('href');
    if (!href || /^(?:#|mailto:|tel:|javascript:|data:)/iu.test(href)) continue;
    let url;
    try {
      url = new URL(href, pageUrl);
    } catch {
      continue;
    }
    if (url.origin !== origin || /\/(?:wp-admin|wp-login\.php|wp-json)(?:\/|$)/iu.test(url.pathname)) continue;
    if (url.search || /\/(?:feed)(?:\/|$)/iu.test(url.pathname)) continue;
    url.hash = '';
    links.push(url.toString());
  }
  return links;
}

async function request(url, options = {}) {
  return requestHttp(new URL(url), options.method ?? 'GET');
}

function requestHttp(url, method, redirects = 0) {
  return new Promise((resolve, reject) => {
    const client = url.protocol === 'https:' ? https : http;
    const requestHandle = client.request(url, {
      method,
      agent: false,
      headers: { 'user-agent': 'TuanKhang-Site-Audit/1.0', connection: 'close' },
    }, (response) => {
      const status = response.statusCode ?? 0;
      if (status >= 300 && status < 400 && response.headers.location) {
        response.resume();
        if (redirects >= 5) {
          reject(new Error('too many redirects'));
          return;
        }
        resolve(requestHttp(new URL(response.headers.location, url), method, redirects + 1));
        return;
      }
      const chunks = [];
      response.on('data', (chunk) => chunks.push(chunk));
      response.on('end', () => {
        const body = Buffer.concat(chunks).toString('utf8');
        resolve({
          status,
          ok: status >= 200 && status < 300,
          text: async () => body,
        });
      });
    });
    requestHandle.setTimeout(TIMEOUT_MS, () => requestHandle.destroy(new Error(`request timed out after ${TIMEOUT_MS}ms`)));
    requestHandle.on('error', reject);
    requestHandle.end();
  });
}

async function mapConcurrent(items, worker) {
  let cursor = 0;
  const runners = Array.from({ length: Math.min(CONCURRENCY, items.length) }, async () => {
    while (cursor < items.length) {
      const item = items[cursor];
      cursor += 1;
      await worker(item);
    }
  });
  await Promise.all(runners);
}

async function main() {
  const base = new URL(siteArgument().replace(/\/?$/u, '/'));
  const sitemapQueue = [new URL('wp-sitemap.xml', base).toString()];
  const sitemapUrls = new Set();
  const pageUrls = new Set();
  const issues = [];

  while (sitemapQueue.length) {
    const sitemapUrl = sitemapQueue.shift();
    if (sitemapUrls.has(sitemapUrl)) continue;
    sitemapUrls.add(sitemapUrl);
    const response = await request(sitemapUrl);
    if (!response.ok) {
      issues.push(`${sitemapUrl}: HTTP ${response.status}`);
      continue;
    }
    for (const location of locations(await response.text())) {
      const url = new URL(location);
      if (url.origin !== base.origin) {
        issues.push(`${sitemapUrl}: cross-origin URL ${url}`);
      } else if (url.pathname.endsWith('.xml')) {
        sitemapQueue.push(url.toString());
      } else {
        pageUrls.add(url.toString());
      }
    }
    if (sitemapUrls.size + sitemapQueue.length + pageUrls.size > MAX_URLS) {
      throw new Error(`Audit exceeded ${MAX_URLS} URLs.`);
    }
  }

  const linkSources = new Map();
  await mapConcurrent([...pageUrls], async (url) => {
    let response;
    try {
      response = await request(url);
    } catch (error) {
      issues.push(`${url}: ${error.message}`);
      return;
    }
    if (!response.ok) {
      issues.push(`${url}: HTTP ${response.status}`);
      return;
    }
    const html = await response.text();
    const robots = metaContent(html, 'robots').toLowerCase();
    const description = metaContent(html, 'description').trim();
    const canonical = canonicalHref(html).trim();
    if (robots.includes('noindex')) issues.push(`${url}: sitemap URL is noindex`);
    if (!description) issues.push(`${url}: missing meta description`);
    if (!canonical) {
      issues.push(`${url}: missing canonical`);
    } else {
      try {
        if (new URL(canonical, url).origin !== base.origin) issues.push(`${url}: cross-origin canonical ${canonical}`);
      } catch {
        issues.push(`${url}: invalid canonical ${canonical}`);
      }
    }
    for (const link of internalLinks(html, url, base.origin)) {
      if (!linkSources.has(link)) linkSources.set(link, url);
    }
  });

  await mapConcurrent([...linkSources.keys()], async (url) => {
    try {
      let response = await request(url, { method: 'HEAD' });
      if (response.status === 405) response = await request(url);
      if (response.status >= 400) issues.push(`${url}: internal link HTTP ${response.status} (from ${linkSources.get(url)})`);
    } catch (error) {
      issues.push(`${url}: internal link failed (${error.message}; from ${linkSources.get(url)})`);
    }
  });

  for (const path of ['?s=implant', '__tk-validator-missing-page__/']) {
    const url = new URL(path, base);
    const response = await request(url);
    const robots = metaContent(await response.text(), 'robots').toLowerCase();
    if (!robots.includes('noindex')) issues.push(`${url}: expected noindex`);
  }

  if (issues.length) {
    console.error(`Site audit failed with ${issues.length} issue(s):`);
    issues.forEach((issue) => console.error(`- ${issue}`));
    process.exit(1);
  }

  console.log(`Site audit passed: ${sitemapUrls.size} sitemaps, ${pageUrls.size} indexable pages, ${linkSources.size} internal links.`);
}

main().catch((error) => {
  console.error(`Site audit failed: ${error.stack ?? error.message}`);
  process.exit(1);
});

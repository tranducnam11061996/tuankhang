import { readdir, readFile, stat } from 'node:fs/promises';
import http from 'node:http';
import https from 'node:https';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const SCRIPT_PATH = fileURLToPath(import.meta.url);
const THEME_ROOT = path.resolve(path.dirname(SCRIPT_PATH), '..');
const SOURCE_EXTENSIONS = new Set(['.php', '.css', '.js', '.mjs', '.json']);
const SOURCE_DIRECTORIES = ['assets/src', 'css', 'inc', 'js', 'scripts', 'template-parts'];
const MAX_SITE_URLS = 2000;
const REQUEST_TIMEOUT_MS = 10_000;
const SITE_CONCURRENCY = Math.max(1, Number.parseInt(process.env.TK_SITE_CONCURRENCY ?? '5', 10) || 5);
const LEGACY_MARKER_SOURCE_ALLOWLIST = new Set([
  'scripts/migrate-legacy-translations.php',
  'scripts/validate-unicode.mjs',
  'scripts/validate-unicode.test.mjs',
]);

const MOJIBAKE_PATTERNS = [
  {
    label: 'UTF-8 bytes decoded as Latin-1/Windows-1252',
    expression: /\u00C3[\u0080-\u00BF]/gu,
  },
  {
    label: 'double-decoded Vietnamese sequence',
    expression: /\u00E1[\u00BA\u00BB][\u0080-\u00BF\u2018\u2019\u201C\u201D\u2039\u203A]/gu,
  },
  {
    label: 'misdecoded Vietnamese D/d with stroke',
    expression: /\u00C4[\u0090\u0091\u2018\u2019]/gu,
  },
  {
    label: 'misdecoded Vietnamese horn character',
    expression: /\u00C6[\u00A0\u00A1\u00AF\u00B0]/gu,
  },
];

function compactSnippet(text, index, width = 44) {
  const start = Math.max(0, index - width);
  const end = Math.min(text.length, index + width);
  return text.slice(start, end).replace(/\s+/gu, ' ').trim();
}

export function decodeUtf8(bytes, label = 'input') {
  try {
    return new TextDecoder('utf-8', { fatal: true }).decode(bytes);
  } catch (error) {
    throw new Error(`${label}: invalid UTF-8 byte sequence`, { cause: error });
  }
}

export function findUnicodeIssues(text) {
  const issues = [];
  let index = text.indexOf('\uFFFD');

  while (index !== -1) {
    issues.push({
      kind: 'replacement-character',
      index,
      message: 'contains Unicode replacement character U+FFFD',
      snippet: compactSnippet(text, index),
    });
    index = text.indexOf('\uFFFD', index + 1);
  }

  for (const { label, expression } of MOJIBAKE_PATTERNS) {
    expression.lastIndex = 0;
    for (const match of text.matchAll(expression)) {
      issues.push({
        kind: 'mojibake',
        index: match.index,
        message: `contains likely mojibake (${label})`,
        snippet: compactSnippet(text, match.index),
      });
    }
  }

  return issues.sort((left, right) => left.index - right.index);
}

export function findLegacyTranslationIssues(text) {
  const issues = [];
  const expression = /\[:(?:vi|en)?\]/giu;
  for (const match of text.matchAll(expression)) {
    issues.push({
      kind: 'legacy-translation-marker',
      index: match.index,
      message: `contains legacy translation marker ${match[0]}`,
      snippet: compactSnippet(text, match.index),
    });
  }
  return issues;
}

async function walkSourceDirectory(directory) {
  const files = [];
  let entries;

  try {
    entries = await readdir(directory, { withFileTypes: true });
  } catch (error) {
    if (error.code === 'ENOENT') {
      return files;
    }
    throw error;
  }

  for (const entry of entries) {
    if (entry.name === 'node_modules' || entry.name === '.git' || entry.name === 'dist') {
      continue;
    }

    const entryPath = path.join(directory, entry.name);
    if (entry.isDirectory()) {
      files.push(...await walkSourceDirectory(entryPath));
    } else if (entry.isFile() && SOURCE_EXTENSIONS.has(path.extname(entry.name).toLowerCase())) {
      files.push(entryPath);
    }
  }

  return files;
}

async function collectSourceFiles() {
  const rootEntries = await readdir(THEME_ROOT, { withFileTypes: true });
  const rootFiles = rootEntries
    .filter((entry) => entry.isFile() && SOURCE_EXTENSIONS.has(path.extname(entry.name).toLowerCase()))
    .map((entry) => path.join(THEME_ROOT, entry.name));
  const nestedFiles = [];

  for (const directory of SOURCE_DIRECTORIES) {
    nestedFiles.push(...await walkSourceDirectory(path.join(THEME_ROOT, directory)));
  }

  return [...new Set([...rootFiles, ...nestedFiles])].sort();
}

async function scanSourceFiles() {
  const files = await collectSourceFiles();
  const issues = [];

  for (const file of files) {
    const relativeFile = path.relative(THEME_ROOT, file).replaceAll('\\', '/');
    const bytes = await readFile(file);
    let text;

    try {
      text = decodeUtf8(bytes, relativeFile);
    } catch (error) {
      issues.push({ target: relativeFile, message: error.message, snippet: '' });
      continue;
    }

    for (const issue of findUnicodeIssues(text)) {
      issues.push({ target: relativeFile, message: issue.message, snippet: issue.snippet });
    }
    if (!LEGACY_MARKER_SOURCE_ALLOWLIST.has(relativeFile)) {
      for (const issue of findLegacyTranslationIssues(text)) {
        issues.push({ target: relativeFile, message: issue.message, snippet: issue.snippet });
      }
    }
  }

  return { files, issues };
}

async function validateFontIntegration() {
  const issues = [];
  const fontPath = path.join(THEME_ROOT, 'assets/dist/fonts/manrope-vietnamese.woff2');
  const siteCssPath = path.join(THEME_ROOT, 'assets/src/site.css');
  const contentCssPath = path.join(THEME_ROOT, 'assets/src/content.css');

  try {
    const fontStats = await stat(fontPath);
    if (!fontStats.isFile() || fontStats.size === 0) {
      issues.push({ target: 'assets/dist/fonts/manrope-vietnamese.woff2', message: 'Vietnamese WOFF2 is empty', snippet: '' });
    } else {
      const fontBytes = await readFile(fontPath);
      if (fontBytes.subarray(0, 4).toString('ascii') !== 'wOF2') {
        issues.push({ target: 'assets/dist/fonts/manrope-vietnamese.woff2', message: 'invalid WOFF2 signature', snippet: '' });
      }
    }
  } catch (error) {
    issues.push({
      target: 'assets/dist/fonts/manrope-vietnamese.woff2',
      message: error.code === 'ENOENT' ? 'Vietnamese WOFF2 is missing' : error.message,
      snippet: '',
    });
  }

  const siteCss = decodeUtf8(await readFile(siteCssPath), 'assets/src/site.css');
  const contentCss = decodeUtf8(await readFile(contentCssPath), 'assets/src/content.css');
  const vietnameseFontFace = /@font-face\s*\{(?=[^}]*font-family:\s*["']TK Manrope["'])(?=[^}]*src:\s*url\(["']\.\/fonts\/manrope-vietnamese\.woff2["']\)\s*format\(["']woff2["']\))(?=[^}]*font-weight:\s*400\s+800)(?=[^}]*unicode-range:[^}]*U\+1EA0-1EF9)[^}]*\}/isu;
  const fontChecks = [
    {
      ok: vietnameseFontFace.test(siteCss),
      target: 'assets/src/site.css',
      message: 'TK Manrope Vietnamese @font-face is incomplete or invalid',
    },
    {
      ok: /--font-sans:\s*["']TK Manrope["']/u.test(siteCss),
      target: 'assets/src/site.css',
      message: '--font-sans does not start with TK Manrope',
    },
    {
      ok: /\.tk-content-context\s*\{[^}]*font-family:\s*var\(--font-sans\)/su.test(contentCss),
      target: 'assets/src/content.css',
      message: '.tk-content-context must inherit var(--font-sans)',
    },
    {
      ok: /@theme\s*\{[^}]*--font-sans:\s*initial\s*;/su.test(contentCss),
      target: 'assets/src/content.css',
      message: 'content theme must disable Tailwind default --font-sans emission',
    },
  ];

  for (const check of fontChecks) {
    if (!check.ok) {
      issues.push({ target: check.target, message: check.message, snippet: '' });
    }
  }

  return issues;
}

function decodeXmlEntities(value) {
  return value
    .replaceAll('&amp;', '&')
    .replaceAll('&lt;', '<')
    .replaceAll('&gt;', '>')
    .replaceAll('&quot;', '"')
    .replaceAll('&apos;', "'");
}

export function extractSitemapLocations(xml) {
  const locations = [];
  const expression = /<loc>\s*([^<]+?)\s*<\/loc>/giu;

  for (const match of xml.matchAll(expression)) {
    locations.push(decodeXmlEntities(match[1].trim()));
  }

  return locations;
}

function hasUtf8Charset(contentType) {
  return /charset\s*=\s*["']?utf-8\b/iu.test(contentType);
}

async function fetchUtf8Document(url) {
  const response = await requestDocument(url);
  if (response.status < 200 || response.status >= 300) {
    throw new Error(`HTTP ${response.status} ${response.statusMessage}`);
  }
  const contentType = response.headers['content-type'] ?? '';
  if (!hasUtf8Charset(contentType)) {
    throw new Error(`response Content-Type must declare charset=UTF-8 (received: ${contentType || 'missing'})`);
  }
  return decodeUtf8(response.bytes, url.toString());
}

function requestDocument(url, redirects = 0) {
  return new Promise((resolve, reject) => {
    const client = url.protocol === 'https:' ? https : http;
    const request = client.get(url, {
      agent: false,
      headers: { 'user-agent': 'TuanKhang-Unicode-Validator/1.0', connection: 'close' },
    }, (response) => {
      const status = response.statusCode ?? 0;
      if (status >= 300 && status < 400 && response.headers.location) {
        response.resume();
        if (redirects >= 5) {
          reject(new Error('too many redirects'));
          return;
        }
        resolve(requestDocument(new URL(response.headers.location, url), redirects + 1));
        return;
      }
      const chunks = [];
      response.on('data', (chunk) => chunks.push(chunk));
      response.on('end', () => resolve({
        status,
        statusMessage: response.statusMessage ?? '',
        headers: response.headers,
        bytes: Buffer.concat(chunks),
      }));
    });
    request.setTimeout(REQUEST_TIMEOUT_MS, () => request.destroy(new Error(`request timed out after ${REQUEST_TIMEOUT_MS}ms`)));
    request.on('error', reject);
  });
}

async function mapConcurrent(items, limit, worker) {
  let cursor = 0;
  const runners = Array.from({ length: Math.min(limit, items.length) }, async () => {
    while (cursor < items.length) {
      const item = items[cursor];
      cursor += 1;
      await worker(item);
    }
  });

  await Promise.all(runners);
}

async function scanSite(siteValue) {
  const baseUrl = new URL(siteValue.endsWith('/') ? siteValue : `${siteValue}/`);
  const origin = baseUrl.origin;
  const sitemapQueue = [new URL('wp-sitemap.xml', baseUrl).toString()];
  const visitedSitemaps = new Set();
  const pageUrls = new Set();
  const issues = [];

  while (sitemapQueue.length > 0) {
    const sitemapUrl = sitemapQueue.shift();
    if (visitedSitemaps.has(sitemapUrl)) {
      continue;
    }
    visitedSitemaps.add(sitemapUrl);

    let xml;
    try {
      xml = await fetchUtf8Document(new URL(sitemapUrl));
    } catch (error) {
      issues.push({ target: sitemapUrl, message: error.message, snippet: '' });
      continue;
    }

    for (const issue of findUnicodeIssues(xml)) {
      issues.push({ target: sitemapUrl, message: issue.message, snippet: issue.snippet });
    }
    for (const issue of findLegacyTranslationIssues(xml)) {
      issues.push({ target: sitemapUrl, message: issue.message, snippet: issue.snippet });
    }

    for (const location of extractSitemapLocations(xml)) {
      let url;
      try {
        url = new URL(location);
      } catch {
        issues.push({ target: sitemapUrl, message: `invalid <loc> URL: ${location}`, snippet: '' });
        continue;
      }

      if (url.origin !== origin) {
        issues.push({ target: sitemapUrl, message: `cross-origin <loc> is not allowed: ${url}`, snippet: '' });
        continue;
      }

      if (url.pathname.toLowerCase().endsWith('.xml')) {
        sitemapQueue.push(url.toString());
      } else {
        pageUrls.add(url.toString());
      }

      if (visitedSitemaps.size + sitemapQueue.length + pageUrls.size > MAX_SITE_URLS) {
        throw new Error(`site scan exceeded the ${MAX_SITE_URLS}-URL safety limit`);
      }
    }
  }

  await mapConcurrent([...pageUrls], SITE_CONCURRENCY, async (url) => {
    let html;
    try {
      html = await fetchUtf8Document(new URL(url));
    } catch (error) {
      issues.push({ target: url, message: error.message, snippet: '' });
      return;
    }

    for (const issue of findUnicodeIssues(html)) {
      issues.push({ target: url, message: issue.message, snippet: issue.snippet });
    }
    for (const issue of findLegacyTranslationIssues(html)) {
      issues.push({ target: url, message: issue.message, snippet: issue.snippet });
    }
  });

  return {
    issues,
    sitemapCount: visitedSitemaps.size,
    pageCount: pageUrls.size,
  };
}

function parseSiteArgument(argumentsList) {
  for (let index = 0; index < argumentsList.length; index += 1) {
    const argument = argumentsList[index];
    if (argument.startsWith('--site=')) {
      return argument.slice('--site='.length);
    }
    if (argument === '--site') {
      return argumentsList[index + 1];
    }
  }
  return null;
}

function printIssues(issues) {
  for (const issue of issues) {
    const suffix = issue.snippet ? `\n    ${issue.snippet}` : '';
    console.error(`- ${issue.target}: ${issue.message}${suffix}`);
  }
}

async function main() {
  const site = parseSiteArgument(process.argv.slice(2));
  const { files, issues: sourceIssues } = await scanSourceFiles();
  const fontIssues = await validateFontIntegration();
  const issues = [...sourceIssues, ...fontIssues];
  let siteSummary = '';

  if (site) {
    const result = await scanSite(site);
    issues.push(...result.issues);
    siteSummary = `, ${result.sitemapCount} sitemaps and ${result.pageCount} rendered URLs`;
  }

  if (issues.length > 0) {
    console.error(`Unicode validation failed with ${issues.length} issue(s):`);
    printIssues(issues);
    process.exitCode = 1;
    return;
  }

  console.log(`Unicode validation passed: ${files.length} source files, Vietnamese font integration${siteSummary}.`);
}

const isMain = process.argv[1] && path.resolve(process.argv[1]) === SCRIPT_PATH;
if (isMain) {
  main().catch((error) => {
    console.error(`Unicode validation failed: ${error.stack ?? error.message}`);
    process.exitCode = 1;
  });
}

import { access } from 'node:fs/promises';
import { resolve } from 'node:path';

import {
  downloadFontAsset,
  verifyExistingFont,
} from './font-integrity.mjs';

const outputDirectory = resolve('assets/dist/fonts');
const userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/126.0.0.0 Safari/537.36';
const subsets = ['vietnamese', 'latin'];

const fonts = [
  {
    family: 'Manrope',
    query: 'family=Manrope:wght@400..800',
    filename: 'manrope',
    integrity: {
      latin: {
        bytes: 24836,
        sha256: 'A30DDCD349703AFF7464C34BEF3FFFDFF405EE50C113440D7C8693C02D210972',
      },
      vietnamese: {
        bytes: 8520,
        sha256: '6BBB044AB420E07EDB0A3042D2EB314B85E83A0182E945A15AB3B9092668DFD5',
      },
    },
  },
  {
    family: 'Source Serif 4',
    query: 'family=Source+Serif+4:opsz,wght@8..60,600..700',
    filename: 'source-serif-4',
    integrity: {
      latin: {
        bytes: 122360,
        sha256: 'F2EA9C12D2FE9BD3A9589B02AD2C0909DA88F30938C91ADC838C4F4098F9F9E0',
      },
      vietnamese: {
        bytes: 29652,
        sha256: 'E08AD84517B0CF6F4090101FEDB82F9897CF5FB8B1684C65530F476C2803492D',
      },
    },
  },
];

async function fileExists(filename) {
  try {
    await access(filename);
    return true;
  } catch {
    return false;
  }
}

function extractSubsetUrl(stylesheet, family, subset) {
  const expression = new RegExp(`/\\*\\s*${subset}\\s*\\*/\\s*@font-face\\s*\\{([\\s\\S]*?)\\}`, 'i');
  const block = stylesheet.match(expression)?.[1] ?? '';
  const sourceUrl = block.match(/src:\s*url\(([^)]+)\)/i)?.[1]?.replace(/["']/g, '').trim();

  if (!sourceUrl) {
    throw new Error(`Could not find the ${subset} subset for ${family}.`);
  }

  return sourceUrl;
}

async function fetchStylesheet(font) {
  const stylesheetUrl = new URL(`https://fonts.googleapis.com/css2?${font.query}&display=swap`);
  const response = await fetch(stylesheetUrl, {
    headers: { 'user-agent': userAgent },
    redirect: 'follow',
  });

  if (!response.ok) {
    throw new Error(`Could not download CSS for ${font.family}: HTTP ${response.status}.`);
  }

  const finalUrl = new URL(response.url || stylesheetUrl.href);
  if (finalUrl.protocol !== 'https:' || finalUrl.hostname !== 'fonts.googleapis.com') {
    throw new Error(`Untrusted stylesheet redirect for ${font.family}: ${finalUrl.href}`);
  }

  const contentType = (response.headers.get('content-type') || '').toLowerCase();
  if (!contentType.startsWith('text/css')) {
    throw new Error(`Unexpected stylesheet content type for ${font.family}: ${contentType || 'missing'}.`);
  }

  const stylesheet = await response.text();
  if (Buffer.byteLength(stylesheet, 'utf8') > 256 * 1024) {
    throw new Error(`Stylesheet response is too large for ${font.family}.`);
  }

  return stylesheet;
}

for (const font of fonts) {
  const missingSubsets = [];

  for (const subset of subsets) {
    const destination = resolve(outputDirectory, `${font.filename}-${subset}.woff2`);
    if (!(await fileExists(destination))) {
      missingSubsets.push(subset);
      continue;
    }

    await verifyExistingFont(destination, font.integrity[subset]);
    console.log(`Verified ${font.family} ${subset}.`);
  }

  if (!missingSubsets.length) {
    continue;
  }

  const stylesheet = await fetchStylesheet(font);
  for (const subset of missingSubsets) {
    const destination = resolve(outputDirectory, `${font.filename}-${subset}.woff2`);
    const sourceUrl = extractSubsetUrl(stylesheet, font.family, subset);
    await downloadFontAsset({
      sourceUrl,
      destination,
      expected: font.integrity[subset],
    });
    console.log(`Downloaded and verified ${font.family} ${subset}.`);
  }
}

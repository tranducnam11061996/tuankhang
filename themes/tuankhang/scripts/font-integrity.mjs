import { createHash } from 'node:crypto';
import { mkdir, readFile, rename, rm, stat, writeFile } from 'node:fs/promises';
import path from 'node:path';

export const MAX_FONT_BYTES = 512 * 1024;

const allowedContentTypes = new Set([
  'font/woff2',
  'application/font-woff2',
]);

export function sha256(buffer) {
  return createHash('sha256').update(buffer).digest('hex').toUpperCase();
}

export function validateFontSourceUrl(value) {
  const url = new URL(value);

  if (
    url.protocol !== 'https:'
    || url.hostname !== 'fonts.gstatic.com'
    || (url.port && url.port !== '443')
    || url.username
    || url.password
  ) {
    throw new Error(`Untrusted font source URL: ${url.href}`);
  }

  return url;
}

export function verifyFontBuffer(buffer, expected, contentType = '') {
  const bytes = Buffer.isBuffer(buffer) ? buffer : Buffer.from(buffer);
  const normalizedContentType = contentType.split(';', 1)[0].trim().toLowerCase();

  if (normalizedContentType && !allowedContentTypes.has(normalizedContentType)) {
    throw new Error(`Unexpected font content type: ${normalizedContentType}`);
  }

  if (bytes.length > MAX_FONT_BYTES) {
    throw new Error(`Font exceeds the ${MAX_FONT_BYTES}-byte safety limit.`);
  }

  if (bytes.length !== expected.bytes) {
    throw new Error(`Font size mismatch: expected ${expected.bytes}, received ${bytes.length}.`);
  }

  const digest = sha256(bytes);
  if (digest !== expected.sha256.toUpperCase()) {
    throw new Error(`Font SHA-256 mismatch: expected ${expected.sha256}, received ${digest}.`);
  }

  return digest;
}

export async function verifyExistingFont(destination, expected) {
  const metadata = await stat(destination);
  if (metadata.size > MAX_FONT_BYTES) {
    throw new Error(`Font exceeds the ${MAX_FONT_BYTES}-byte safety limit.`);
  }

  const buffer = await readFile(destination);
  verifyFontBuffer(buffer, expected);
}

async function readBoundedResponse(response) {
  if (!response.body || typeof response.body.getReader !== 'function') {
    const buffer = Buffer.from(await response.arrayBuffer());
    if (buffer.length > MAX_FONT_BYTES) {
      throw new Error(`Font response exceeds the ${MAX_FONT_BYTES}-byte safety limit.`);
    }
    return buffer;
  }

  const reader = response.body.getReader();
  const chunks = [];
  let totalBytes = 0;

  try {
    while (true) {
      const { done, value } = await reader.read();
      if (done) break;

      const chunk = Buffer.from(value);
      totalBytes += chunk.length;
      if (totalBytes > MAX_FONT_BYTES) {
        await reader.cancel();
        throw new Error(`Font response exceeds the ${MAX_FONT_BYTES}-byte safety limit.`);
      }
      chunks.push(chunk);
    }
  } finally {
    reader.releaseLock();
  }

  return Buffer.concat(chunks, totalBytes);
}

export async function downloadFontAsset({ sourceUrl, destination, expected, fetchImpl = fetch }) {
  const trustedSource = validateFontSourceUrl(sourceUrl);
  const response = await fetchImpl(trustedSource, { redirect: 'follow' });

  if (!response.ok) {
    throw new Error(`Font download failed with HTTP ${response.status}.`);
  }

  validateFontSourceUrl(response.url || trustedSource.href);

  const contentLength = Number(response.headers.get('content-length') || 0);
  if (contentLength > MAX_FONT_BYTES) {
    throw new Error(`Font response exceeds the ${MAX_FONT_BYTES}-byte safety limit.`);
  }

  const buffer = await readBoundedResponse(response);
  verifyFontBuffer(buffer, expected, response.headers.get('content-type') || '');

  await mkdir(path.dirname(destination), { recursive: true });
  const temporary = `${destination}.${process.pid}.${Date.now()}.tmp`;

  try {
    await writeFile(temporary, buffer, { flag: 'wx' });
    await rename(temporary, destination);
  } catch (error) {
    await rm(temporary, { force: true });
    throw error;
  }
}

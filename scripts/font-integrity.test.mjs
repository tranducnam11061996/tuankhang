import assert from 'node:assert/strict';
import { mkdtemp, readFile, readdir, rm } from 'node:fs/promises';
import { tmpdir } from 'node:os';
import path from 'node:path';
import test from 'node:test';

import {
  MAX_FONT_BYTES,
  downloadFontAsset,
  sha256,
  validateFontSourceUrl,
  verifyFontBuffer,
} from './font-integrity.mjs';

function responseFor(buffer, overrides = {}) {
  const headers = new Map([
    ['content-length', String(buffer.length)],
    ['content-type', 'font/woff2'],
  ]);

  return {
    ok: true,
    status: 200,
    url: 'https://fonts.gstatic.com/s/example/font.woff2',
    headers: { get: (name) => headers.get(name.toLowerCase()) || null },
    arrayBuffer: async () => buffer,
    ...overrides,
  };
}

test('accepts only HTTPS fonts.gstatic.com source URLs', () => {
  assert.equal(
    validateFontSourceUrl('https://fonts.gstatic.com/s/example/font.woff2').hostname,
    'fonts.gstatic.com',
  );

  for (const source of [
    'http://fonts.gstatic.com/s/example/font.woff2',
    'https://fonts.gstatic.com.example.test/font.woff2',
    'https://example.test/font.woff2',
    'https://fonts.gstatic.com:444/font.woff2',
  ]) {
    assert.throws(() => validateFontSourceUrl(source), /Untrusted font source URL/);
  }
});

test('verifies content type, size, safety limit, and SHA-256', () => {
  const buffer = Buffer.from('pinned-font-fixture');
  const expected = { bytes: buffer.length, sha256: sha256(buffer) };

  assert.equal(verifyFontBuffer(buffer, expected, 'font/woff2'), expected.sha256);
  assert.throws(() => verifyFontBuffer(buffer, expected, 'text/html'), /content type/);
  assert.throws(
    () => verifyFontBuffer(buffer, { ...expected, bytes: buffer.length + 1 }),
    /size mismatch/,
  );
  assert.throws(
    () => verifyFontBuffer(buffer, { ...expected, sha256: '0'.repeat(64) }),
    /SHA-256 mismatch/,
  );
  assert.throws(
    () => verifyFontBuffer(Buffer.alloc(MAX_FONT_BYTES + 1), { bytes: MAX_FONT_BYTES + 1, sha256: '' }),
    /safety limit/,
  );
});

test('downloads through a trusted final URL and writes atomically', async (context) => {
  const directory = await mkdtemp(path.join(tmpdir(), 'tk-font-integrity-'));
  context.after(() => rm(directory, { recursive: true, force: true }));
  const destination = path.join(directory, 'font.woff2');
  const buffer = Buffer.from('trusted-font');

  await downloadFontAsset({
    sourceUrl: 'https://fonts.gstatic.com/s/example/font.woff2',
    destination,
    expected: { bytes: buffer.length, sha256: sha256(buffer) },
    fetchImpl: async () => responseFor(buffer),
  });

  assert.deepEqual(await readFile(destination), buffer);
  assert.deepEqual(await readdir(directory), ['font.woff2']);
});

test('rejects an untrusted redirect without leaving a partial file', async (context) => {
  const directory = await mkdtemp(path.join(tmpdir(), 'tk-font-integrity-'));
  context.after(() => rm(directory, { recursive: true, force: true }));
  const buffer = Buffer.from('untrusted-font');

  await assert.rejects(
    downloadFontAsset({
      sourceUrl: 'https://fonts.gstatic.com/s/example/font.woff2',
      destination: path.join(directory, 'font.woff2'),
      expected: { bytes: buffer.length, sha256: sha256(buffer) },
      fetchImpl: async () => responseFor(buffer, { url: 'https://example.test/font.woff2' }),
    }),
    /Untrusted font source URL/,
  );

  assert.deepEqual(await readdir(directory), []);
});

test('rejects an oversized response without leaving a partial file', async (context) => {
  const directory = await mkdtemp(path.join(tmpdir(), 'tk-font-integrity-'));
  context.after(() => rm(directory, { recursive: true, force: true }));
  const buffer = Buffer.alloc(MAX_FONT_BYTES + 1);

  await assert.rejects(
    downloadFontAsset({
      sourceUrl: 'https://fonts.gstatic.com/s/example/font.woff2',
      destination: path.join(directory, 'font.woff2'),
      expected: { bytes: buffer.length, sha256: sha256(buffer) },
      fetchImpl: async () => responseFor(buffer, {
        headers: { get: (name) => (name.toLowerCase() === 'content-type' ? 'font/woff2' : null) },
      }),
    }),
    /safety limit/,
  );

  assert.deepEqual(await readdir(directory), []);
});

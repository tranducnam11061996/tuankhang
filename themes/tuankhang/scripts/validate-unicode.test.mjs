import assert from 'node:assert/strict';
import test from 'node:test';

import {
  decodeUtf8,
  extractSitemapLocations,
  findLegacyTranslationIssues,
  findUnicodeIssues,
} from './validate-unicode.mjs';

test('accepts valid Vietnamese UTF-8 text', () => {
  const text = 'Dịch vụ hỗ trợ phẫu thuật và cấy ghép Implant';
  const bytes = new TextEncoder().encode(text);

  assert.equal(decodeUtf8(bytes), text);
  assert.deepEqual(findUnicodeIssues(text), []);
});

test('rejects invalid UTF-8 byte sequences', () => {
  const invalidBytes = Uint8Array.from([0x44, 0xc3, 0x28]);

  assert.throws(() => decodeUtf8(invalidBytes, 'fixture'), /invalid UTF-8 byte sequence/u);
});

test('detects the Unicode replacement character', () => {
  const text = `Dịch v${String.fromCodePoint(0xfffd)}`;
  const issues = findUnicodeIssues(text);

  assert.equal(issues.length, 1);
  assert.equal(issues[0].kind, 'replacement-character');
});

test('detects Vietnamese mojibake assembled from code points', () => {
  const text = String.fromCodePoint(0x44, 0xe1, 0xbb, 0x2039, 0x63, 0x68);
  const issues = findUnicodeIssues(text);

  assert.equal(issues.length, 1);
  assert.equal(issues[0].kind, 'mojibake');
});

test('extracts and decodes sitemap locations', () => {
  const xml = '<urlset><url><loc>http://localhost/tuankhang/?s=răng&amp;p=1</loc></url></urlset>';

  assert.deepEqual(extractSitemapLocations(xml), ['http://localhost/tuankhang/?s=răng&p=1']);
});

test('detects all legacy translation marker forms', () => {
  const issues = findLegacyTranslationIssues('[:vi]Tiếng Việt[:en]English[:]');

  assert.deepEqual(issues.map((issue) => issue.kind), [
    'legacy-translation-marker',
    'legacy-translation-marker',
    'legacy-translation-marker',
  ]);
});

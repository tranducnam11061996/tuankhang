import assert from 'node:assert/strict';
import { readdir, readFile } from 'node:fs/promises';
import path from 'node:path';
import test from 'node:test';
import { fileURLToPath } from 'node:url';

const scriptsDirectory = path.dirname(fileURLToPath(import.meta.url));
const bootstrapName = 'cli-bootstrap.php';
const cliEntrypoints = [
  'build-content-images.php',
  'build-home-images.php',
  'build-product-images.php',
  'manage-site-plugins.php',
  'migrate-legacy-translations.php',
];

test('every PHP script is explicitly classified', async () => {
  const files = (await readdir(scriptsDirectory))
    .filter((file) => file.endsWith('.php'))
    .sort();

  assert.deepEqual(files, [bootstrapName, ...cliEntrypoints].sort());
});

test('CLI bootstrap fails closed without disclosing an error', async () => {
  const source = await readFile(path.join(scriptsDirectory, bootstrapName), 'utf8');

  assert.match(source, /PHP_SAPI\s*!==\s*['"]cli['"]/);
  assert.match(source, /http_response_code\(404\)/);
  assert.match(source, /\bexit\s*;/);
  assert.doesNotMatch(source, /STDERR|echo|print\s*\(/);
});

for (const filename of cliEntrypoints) {
  test(`${filename} loads the CLI guard before side effects`, async () => {
    const source = await readFile(path.join(scriptsDirectory, filename), 'utf8');
    const guardPosition = source.indexOf("require_once __DIR__ . '/cli-bootstrap.php';");

    assert.notEqual(guardPosition, -1, `${filename} must load cli-bootstrap.php`);

    for (const marker of ['ini_set(', 'wp-load.php']) {
      const sideEffectPosition = source.indexOf(marker);
      if (sideEffectPosition !== -1) {
        assert.ok(
          guardPosition < sideEffectPosition,
          `${filename} must load the CLI guard before ${marker}`,
        );
      }
    }
  });
}

import { existsSync } from 'node:fs';
import { spawnSync } from 'node:child_process';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const scriptDirectory = path.dirname(fileURLToPath(import.meta.url));
const migrationScript = path.join(scriptDirectory, 'migrate-legacy-translations.php');
const candidates = [
  process.env.TK_PHP_BINARY,
  'D:/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe',
  'php',
].filter(Boolean);

let result;
let phpBinary;
for (const candidate of candidates) {
  if (candidate !== 'php' && !existsSync(candidate)) {
    continue;
  }
  const attempt = spawnSync(candidate, [migrationScript, '--check', '--locale=vi'], {
    encoding: 'utf8',
    stdio: 'pipe',
  });
  if (!attempt.error || attempt.error.code !== 'ENOENT') {
    result = attempt;
    phpBinary = candidate;
    break;
  }
}

if (!result) {
  console.error('Database validation failed: PHP CLI was not found. Set TK_PHP_BINARY to the PHP executable.');
  process.exit(1);
}

if (result.stdout) process.stdout.write(result.stdout);
if (result.stderr) process.stderr.write(result.stderr);
if (result.status !== 0) {
  console.error(`Database validation failed via ${phpBinary}.`);
  process.exit(result.status ?? 1);
}

console.log('Database validation passed: no legacy translation markers remain.');

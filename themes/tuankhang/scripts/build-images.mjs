import { existsSync, readdirSync } from 'node:fs';
import { spawnSync } from 'node:child_process';
import { resolve } from 'node:path';

const candidates = [
  process.env.TK_PHP,
  'D:/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe',
].filter(Boolean);

const phpRoot = 'D:/laragon/bin/php';
if (existsSync(phpRoot)) {
  for (const directory of readdirSync(phpRoot).sort().reverse()) {
    candidates.push(resolve(phpRoot, directory, 'php.exe'));
  }
}

const php = candidates.find((candidate) => existsSync(candidate));
if (!php) throw new Error('Không tìm thấy PHP CLI. Đặt biến TK_PHP tới php.exe rồi chạy lại.');

for (const script of ['build-home-images.php', 'build-product-images.php', 'build-content-images.php']) {
  const result = spawnSync(php, [resolve('scripts', script)], { cwd: resolve('.'), stdio: 'inherit' });
  if (result.status !== 0) process.exit(result.status ?? 1);
}

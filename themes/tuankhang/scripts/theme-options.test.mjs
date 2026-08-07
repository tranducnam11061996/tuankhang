import assert from 'node:assert/strict';
import { readdir, readFile } from 'node:fs/promises';
import path from 'node:path';
import test from 'node:test';
import { fileURLToPath } from 'node:url';

const themeDirectory = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const allowedLegacyFiles = new Set([
  path.join('inc', 'theme-options', 'migration.php'),
  path.join('inc', 'theme-options', 'schema.php'),
]);

async function phpFiles(directory = themeDirectory) {
  const entries = await readdir(directory, { withFileTypes: true });
  const files = [];
  for (const entry of entries) {
    const absolute = path.join(directory, entry.name);
    const relative = path.relative(themeDirectory, absolute);
    if (entry.isDirectory()) {
      if (entry.name !== 'node_modules' && entry.name !== 'scripts') {
        files.push(...await phpFiles(absolute));
      }
    } else if (entry.name.endsWith('.php') && !allowedLegacyFiles.has(relative)) {
      files.push(absolute);
    }
  }
  return files;
}

test('runtime templates do not reintroduce legacy Theme Options sources', async () => {
  const forbidden = [
    [/get_post_meta\s*\(\s*61\b/, 'legacy settings page ID 61'],
    [/wp_get_nav_menu_(?:items|object)\s*\(\s*(?:25|29)\b/, 'legacy menu ID 25/29'],
    [/contact-form-7[^\]]*\bid\s*=\s*["']?14\b/i, 'hard-coded Contact Form 7 ID 14'],
    [/m\.me\/108890274790969/i, 'hard-coded Messenger URL'],
    [/\btk_home_field\s*\(/, 'legacy homepage field accessor'],
    [/\btk_site_field\s*\(/, 'legacy site field accessor'],
  ];

  for (const filename of await phpFiles()) {
    const source = await readFile(filename, 'utf8');
    for (const [pattern, label] of forbidden) {
      assert.doesNotMatch(source, pattern, `${path.relative(themeDirectory, filename)} contains ${label}`);
    }
  }
});

test('Theme Options modules and menu locations are wired into the theme', async () => {
  const functionsSource = await readFile(path.join(themeDirectory, 'functions.php'), 'utf8');
  for (const module of ['schema.php', 'runtime.php', 'migration.php', 'admin.php']) {
    assert.match(functionsSource, new RegExp(`inc/theme-options/${module.replace('.', '\\.')}['"]`));
  }
  assert.match(functionsSource, /['"]primary['"]\s*=>/);
  assert.match(functionsSource, /['"]footer['"]\s*=>/);
});

test('global frontend consumers use Theme Options accessors', async () => {
  for (const relative of ['header.php', 'footer.php', 'front-page.php', 'page.php']) {
    const source = await readFile(path.join(themeDirectory, relative), 'utf8');
    assert.match(source, /tk_(?:site|home)_option\s*\(/, `${relative} must read Theme Options`);
  }
});

test('Theme Options admin renders the Clinical Settings Console structure', async () => {
  const source = await readFile(path.join(themeDirectory, 'inc', 'theme-options', 'admin.php'), 'utf8');
  for (const section of [
    'brand', 'contact', 'branches-map', 'social', 'integrations',
    'hero', 'metrics', 'partners', 'story', 'capability', 'projects', 'final-cta',
  ]) {
    assert.match(source, new RegExp(`tk_theme_admin_section_open\\('${section}'`), `missing stable section ${section}`);
  }
  assert.match(source, /tk_theme_admin_heading_and_fixed_items\('values'/, 'missing stable section values');
  assert.match(source, /data-options-console/);
  assert.match(source, /data-theme-options-form/);
  assert.match(source, /data-save-status/);
  assert.match(source, /data-section-link/);
  assert.doesNotMatch(source, /<details class="tk-options-section/);
});

test('Theme Options admin assets include validation, dirty state, scrollspy and accessible reorder controls', async () => {
  const [script, styles] = await Promise.all([
    readFile(path.join(themeDirectory, 'assets', 'src', 'theme-options-admin.js'), 'utf8'),
    readFile(path.join(themeDirectory, 'assets', 'src', 'theme-options-admin.css'), 'utf8'),
  ]);

  for (const behavior of [
    /beforeunload/,
    /IntersectionObserver/,
    /data-validation/,
    /setPointerCapture/,
    /data-row-move/,
    /aria-current/,
  ]) {
    assert.match(script, behavior);
  }
  assert.match(styles, /\.tk-theme-options\s*\{/);
  assert.match(styles, /align-items:\s*start/);
  assert.match(styles, /@media\s*\(prefers-reduced-motion:\s*reduce\)/);
  assert.match(styles, /min-height:\s*44px/);
});

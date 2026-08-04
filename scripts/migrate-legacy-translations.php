<?php
/**
 * Audit and migrate legacy qTranslate/WP Multilang markers to one locale.
 *
 * Usage:
 *   php scripts/migrate-legacy-translations.php --check --locale=vi
 *   php scripts/migrate-legacy-translations.php --dry-run --locale=vi
 *   php scripts/migrate-legacy-translations.php --apply --locale=vi --backup-dir=D:\backups\tuankhang\...
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script is CLI-only.\n");
    exit(2);
}

function tk_legacy_marker_expression(): string
{
    return '/(?:\[:[a-zA-Z0-9_-]+\]|\[:\]|<!--:[a-zA-Z0-9_-]+-->|<!--:-->)/u';
}

function tk_legacy_has_marker(string $value): bool
{
    return preg_match(tk_legacy_marker_expression(), $value) === 1;
}

function tk_legacy_select_locale(string $value, string $locale = 'vi'): string
{
    if (!tk_legacy_has_marker($value)) {
        return $value;
    }

    $token_expression = '/(\[:([a-zA-Z0-9_-]+)\]|\[:\]|<!--:([a-zA-Z0-9_-]+)-->|<!--:-->)/u';
    preg_match_all($token_expression, $value, $matches, PREG_OFFSET_CAPTURE);

    $languages = array();
    foreach ($matches[0] as $index => $match) {
        $language = $matches[2][$index][0] ?: $matches[3][$index][0];
        if ($language !== '') {
            $languages[] = strtolower($language);
        }
    }

    $languages = array_values(array_unique($languages));
    $selected = in_array(strtolower($locale), $languages, true)
        ? strtolower($locale)
        : (in_array('en', $languages, true) ? 'en' : ($languages[0] ?? strtolower($locale)));

    $result = '';
    $cursor = 0;
    $active_language = null;

    foreach ($matches[0] as $index => $match) {
        [$token, $offset] = $match;
        $chunk = substr($value, $cursor, $offset - $cursor);
        if ($active_language === null || $active_language === $selected) {
            $result .= $chunk;
        }

        $language = $matches[2][$index][0] ?: $matches[3][$index][0];
        $active_language = $language !== '' ? strtolower($language) : null;
        $cursor = $offset + strlen($token);
    }

    $tail = substr($value, $cursor);
    if ($active_language === null || $active_language === $selected) {
        $result .= $tail;
    }

    return $result;
}

function tk_legacy_transform_value($value, string $locale, bool &$changed)
{
    if (is_string($value)) {
        if (function_exists('is_serialized') && is_serialized($value)) {
            $decoded = maybe_unserialize($value);
            $nested_changed = false;
            $migrated = tk_legacy_transform_value($decoded, $locale, $nested_changed);
            if ($nested_changed) {
                $changed = true;
                return maybe_serialize($migrated);
            }
            return $value;
        }

        $migrated = tk_legacy_select_locale($value, $locale);
        if ($migrated !== $value) {
            $changed = true;
        }
        return $migrated;
    }

    if (is_array($value)) {
        $migrated = array();
        foreach ($value as $key => $item) {
            $migrated[$key] = tk_legacy_transform_value($item, $locale, $changed);
        }
        return $migrated;
    }

    if (is_object($value)) {
        $migrated = clone $value;
        foreach (get_object_vars($migrated) as $key => $item) {
            $migrated->{$key} = tk_legacy_transform_value($item, $locale, $changed);
        }
        return $migrated;
    }

    return $value;
}

function tk_legacy_snippet(string $value, int $limit = 100): string
{
    $value = trim((string) preg_replace('/\s+/u', ' ', $value));
    return function_exists('mb_substr') ? mb_substr($value, 0, $limit) : substr($value, 0, $limit);
}

function tk_legacy_targets($wpdb): array
{
    return array(
        array('table' => $wpdb->posts, 'primary_key' => 'ID', 'column' => 'post_title', 'contexts' => array('post_type', 'post_status')),
        array('table' => $wpdb->posts, 'primary_key' => 'ID', 'column' => 'post_content', 'contexts' => array('post_type', 'post_status')),
        array('table' => $wpdb->posts, 'primary_key' => 'ID', 'column' => 'post_excerpt', 'contexts' => array('post_type', 'post_status')),
        array('table' => $wpdb->postmeta, 'primary_key' => 'meta_id', 'column' => 'meta_value', 'contexts' => array('post_id', 'meta_key')),
        array('table' => $wpdb->terms, 'primary_key' => 'term_id', 'column' => 'name', 'contexts' => array('slug')),
        array('table' => $wpdb->term_taxonomy, 'primary_key' => 'term_taxonomy_id', 'column' => 'description', 'contexts' => array('term_id', 'taxonomy')),
        array('table' => $wpdb->termmeta, 'primary_key' => 'meta_id', 'column' => 'meta_value', 'contexts' => array('term_id', 'meta_key')),
    );
}

function tk_legacy_find_thumbnail_fallback(int $post_id): int
{
    $content = (string) get_post_field('post_content', $post_id);
    preg_match_all('/(?:wp-image-|attachment_|wp-image%2D)(\d+)/i', $content, $matches);
    foreach (array_unique(array_map('intval', $matches[1] ?? array())) as $attachment_id) {
        $attachment = $attachment_id > 0 ? get_post($attachment_id) : null;
        if ($attachment && $attachment->post_type === 'attachment' && str_starts_with((string) get_post_mime_type($attachment_id), 'image/')) {
            return $attachment_id;
        }
    }

    $children = get_children(array(
        'post_parent' => $post_id,
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'post_status' => 'inherit',
        'numberposts' => 1,
        'orderby' => 'menu_order ID',
        'order' => 'ASC',
        'fields' => 'ids',
    ));

    return $children ? (int) reset($children) : 0;
}

function tk_legacy_scan_database($wpdb, string $locale): array
{
    $changes = array();
    $errors = array();
    $marker_cells = 0;

    foreach (tk_legacy_targets($wpdb) as $target) {
        $columns = array_merge(array($target['primary_key'], $target['column']), $target['contexts']);
        $column_sql = implode(', ', array_map(static fn($column) => "`{$column}`", $columns));
        $table = $target['table'];
        $column = $target['column'];
        $rows = $wpdb->get_results("SELECT {$column_sql} FROM `{$table}` WHERE `{$column}` LIKE '%[:%' OR `{$column}` LIKE '%<!--:%'", ARRAY_A);

        foreach ($rows as $row) {
            $before = (string) $row[$column];
            if (!tk_legacy_has_marker($before)) {
                continue;
            }
            $marker_cells++;
            $changed = false;
            $after = (string) tk_legacy_transform_value($before, $locale, $changed);
            $context = array_intersect_key($row, array_flip($target['contexts']));

            if (isset($context['meta_key']) && $context['meta_key'] === '_thumbnail_id') {
                $attachment_id = ctype_digit(trim($after)) ? (int) trim($after) : 0;
                $attachment = $attachment_id > 0 ? get_post($attachment_id) : null;
                if (!$attachment || $attachment->post_type !== 'attachment') {
                    $replacement_id = tk_legacy_find_thumbnail_fallback((int) ($context['post_id'] ?? 0));
                    if ($replacement_id > 0) {
                        $context['replaced_missing_thumbnail'] = $attachment_id;
                        $attachment_id = $replacement_id;
                    } else {
                        $errors[] = sprintf('%s.%s #%s resolves to invalid thumbnail ID %s and has no image fallback', $table, $column, $row[$target['primary_key']], $after);
                    }
                }
                $after = (string) $attachment_id;
                $changed = $after !== $before;
            }

            if (!$changed && tk_legacy_has_marker($after)) {
                $errors[] = sprintf('%s.%s #%s could not be normalized', $table, $column, $row[$target['primary_key']]);
                continue;
            }

            if ($after !== $before) {
                $changes[] = array(
                    'table' => $table,
                    'primary_key' => $target['primary_key'],
                    'id' => (int) $row[$target['primary_key']],
                    'column' => $column,
                    'context' => $context,
                    'before' => $before,
                    'after' => $after,
                    'before_sha256' => hash('sha256', $before),
                    'after_sha256' => hash('sha256', $after),
                );
            }
        }
    }

    return compact('marker_cells', 'changes', 'errors');
}

function tk_legacy_print_summary(array $scan): void
{
    $by_table = array();
    foreach ($scan['changes'] as $change) {
        $key = basename(str_replace('\\', '/', $change['table'])) . '.' . $change['column'];
        $by_table[$key] = ($by_table[$key] ?? 0) + 1;
    }

    echo json_encode(array(
        'marker_cells' => $scan['marker_cells'],
        'changes' => count($scan['changes']),
        'errors' => $scan['errors'],
        'by_target' => $by_table,
        'samples' => array_map(static fn($change) => array(
            'target' => $change['table'] . '.' . $change['column'],
            'id' => $change['id'],
            'context' => $change['context'],
            'before' => tk_legacy_snippet($change['before']),
            'after' => tk_legacy_snippet($change['after']),
        ), array_slice($scan['changes'], 0, 12)),
    ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
}

function tk_legacy_find_sql_backup(string $backup_dir): ?string
{
    if (!is_dir($backup_dir)) {
        return null;
    }
    $files = glob(rtrim($backup_dir, '/\\') . DIRECTORY_SEPARATOR . '*.sql') ?: array();
    foreach ($files as $file) {
        if (is_file($file) && filesize($file) > 0) {
            return $file;
        }
    }
    return null;
}

function tk_legacy_main(): int
{
    $options = getopt('', array('check', 'dry-run', 'apply', 'locale:', 'backup-dir:'));
    $modes = array_values(array_filter(array('check', 'dry-run', 'apply'), static fn($mode) => array_key_exists($mode, $options)));
    if (count($modes) !== 1) {
        fwrite(STDERR, "Choose exactly one mode: --check, --dry-run, or --apply.\n");
        return 2;
    }

    $locale = strtolower((string) ($options['locale'] ?? 'vi'));
    if (!preg_match('/^[a-z]{2,3}(?:[_-][a-z0-9]+)?$/', $locale)) {
        fwrite(STDERR, "Invalid --locale value.\n");
        return 2;
    }

    $wp_load = dirname(__DIR__, 4) . DIRECTORY_SEPARATOR . 'wp-load.php';
    if (!is_file($wp_load)) {
        fwrite(STDERR, "Unable to locate wp-load.php.\n");
        return 2;
    }
    require $wp_load;
    global $wpdb;

    $scan = tk_legacy_scan_database($wpdb, $locale);
    tk_legacy_print_summary($scan);

    if ($scan['errors']) {
        fwrite(STDERR, "Migration preflight failed. No data was changed.\n");
        return 1;
    }

    $mode = $modes[0];
    if ($mode === 'check') {
        return $scan['marker_cells'] === 0 ? 0 : 1;
    }
    if ($mode === 'dry-run') {
        return 0;
    }

    $backup_dir = isset($options['backup-dir']) ? (string) $options['backup-dir'] : '';
    $sql_backup = tk_legacy_find_sql_backup($backup_dir);
    if (!$sql_backup) {
        fwrite(STDERR, "--apply requires --backup-dir containing a non-empty SQL backup.\n");
        return 2;
    }

    $manifest_path = rtrim($backup_dir, '/\\') . DIRECTORY_SEPARATOR . 'legacy-translation-manifest-' . gmdate('Ymd-His') . '.json';
    $manifest = array(
        'created_at' => gmdate('c'),
        'site_url' => get_option('siteurl'),
        'locale' => $locale,
        'sql_backup' => $sql_backup,
        'changes' => $scan['changes'],
    );
    $encoded_manifest = wp_json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if (!$encoded_manifest || file_put_contents($manifest_path, $encoded_manifest) === false) {
        fwrite(STDERR, "Unable to write migration manifest. No data was changed.\n");
        return 2;
    }

    $wpdb->query('START TRANSACTION');
    try {
        foreach ($scan['changes'] as $change) {
            $updated = $wpdb->update(
                $change['table'],
                array($change['column'] => $change['after']),
                array($change['primary_key'] => $change['id']),
                array('%s'),
                array('%d')
            );
            if ($updated === false) {
                throw new RuntimeException(sprintf('Database update failed for %s.%s #%d', $change['table'], $change['column'], $change['id']));
            }
        }

        $verification = tk_legacy_scan_database($wpdb, $locale);
        if ($verification['marker_cells'] !== 0 || $verification['errors']) {
            throw new RuntimeException('Post-migration verification found remaining markers or invalid references.');
        }

        $wpdb->query('COMMIT');
    } catch (Throwable $error) {
        $wpdb->query('ROLLBACK');
        fwrite(STDERR, $error->getMessage() . " Transaction rolled back.\n");
        return 1;
    }

    wp_cache_flush();
    flush_rewrite_rules(false);
    if (function_exists('wp_cache_clear_cache')) {
        wp_cache_clear_cache();
    }

    echo sprintf("Migration applied: %d cell(s). Manifest: %s\n", count($scan['changes']), $manifest_path);
    return 0;
}

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    exit(tk_legacy_main());
}

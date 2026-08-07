<?php
/**
 * Versioned migration from the legacy settings page to Theme Options.
 */

if (!defined('ABSPATH')) {
    exit;
}

define('TK_THEME_OPTIONS_SCHEMA_VERSION', '1.0.0');

function tk_theme_legacy_meta($key, $default = '')
{
    $value = get_post_meta(61, $key, true);
    return $value !== '' ? $value : $default;
}

function tk_theme_legacy_image_id($value)
{
    if (is_array($value)) {
        return absint($value['ID'] ?? $value['id'] ?? 0);
    }
    if (is_numeric($value)) {
        return absint($value);
    }
    return is_string($value) ? absint(attachment_url_to_postid($value)) : 0;
}

function tk_theme_legacy_site_options()
{
    $defaults = tk_theme_site_defaults();
    $head_office = sanitize_text_field(tk_theme_legacy_meta('wpcf-dia-chi-cong-ty'));
    if ($head_office !== '') {
        $defaults['branches'][] = array('label' => 'Trụ sở', 'address' => $head_office);
    }
    $transaction_office = sanitize_text_field(tk_theme_legacy_meta('wpcf-van-phong-giao-dich'));
    if ($transaction_office !== '' && $transaction_office !== $head_office) {
        $defaults['branches'][] = array('label' => 'Văn phòng giao dịch', 'address' => $transaction_office);
    }

    $defaults['contact']['hotline'] = sanitize_text_field(tk_theme_legacy_meta('wpcf-so-hotline'));
    $defaults['contact']['landline'] = sanitize_text_field(tk_theme_legacy_meta('wpcf-so-may-ban'));
    $defaults['contact']['email'] = sanitize_email(tk_theme_legacy_meta('wpcf-email'));
    $defaults['social']['facebook_url'] = esc_url_raw(tk_theme_legacy_meta('wpcf-link-fanpage'));
    $defaults['social']['instagram_url'] = esc_url_raw(tk_theme_legacy_meta('wpcf-link-instagram'));
    $defaults['social']['youtube_url'] = esc_url_raw(tk_theme_legacy_meta('wpcf-link-kenh-youtube'));
    $defaults['social']['x_url'] = esc_url_raw(tk_theme_legacy_meta('wpcf-link-twitter'));
    return $defaults;
}

function tk_theme_legacy_home_options()
{
    $options = tk_theme_home_defaults();
    $legacy = static function ($key, $fallback = '') {
        return tk_theme_legacy_meta($key, $fallback);
    };

    foreach (array('eyebrow', 'title', 'description', 'primary_label', 'primary_url', 'secondary_label', 'secondary_url') as $key) {
        $value = $legacy('tk_home_hero_' . $key);
        if ($value !== '') {
            $options['hero'][$key] = in_array($key, array('primary_url', 'secondary_url'), true) ? tk_home_url($value, '') : $value;
        }
    }
    $options['hero']['image_id'] = tk_theme_legacy_image_id($legacy('tk_home_hero_image'))
        ?: tk_theme_legacy_image_id($legacy('duytv_story_image'))
        ?: tk_theme_legacy_image_id($legacy('wpcf-anhbanner-1'));
    $options['hero']['secondary_image_id'] = tk_theme_legacy_image_id($legacy('tk_home_hero_secondary_image')) ?: 2250;

    for ($index = 0; $index < 5; $index++) {
        $number = $index + 1;
        foreach (array('value', 'suffix', 'label') as $key) {
            $value = $legacy('tk_home_metric_' . $key . '_' . $number);
            if ($value !== '') {
                $options['metrics'][$index][$key] = sanitize_text_field($value);
            }
        }
    }

    for ($index = 1; $index <= 10; $index++) {
        $image_id = tk_theme_legacy_image_id($legacy('wpcf-doi-tac-' . $index));
        if ($image_id) {
            $options['partners'][] = array('image_id' => $image_id, 'name' => (string) get_the_title($image_id));
        }
    }

    $options['story']['title'] = sanitize_text_field($legacy('duytv_story_title', $options['story']['title']));
    $options['story']['content'] = wp_kses_post($legacy('duytv_story_content'));
    $options['story']['image_id'] = tk_theme_legacy_image_id($legacy('duytv_story_image'));
    $options['story']['url'] = tk_home_url($legacy('duytv_story_link'), $options['story']['url']);

    for ($index = 0; $index < 3; $index++) {
        $number = $index + 1;
        $options['values'][$index]['title'] = sanitize_text_field($legacy('duytv_info_title_' . $number, $options['values'][$index]['title']));
        $options['values'][$index]['description'] = wp_kses_post($legacy('duytv_info_des_' . $number));
        foreach (array('title', 'description', 'link') as $key) {
            $value = $legacy('tk_home_capability_' . $key . '_' . $number);
            if ($value !== '') {
                $target_key = $key === 'link' ? 'url' : $key;
                $options['capability']['items'][$index][$target_key] = $key === 'link' ? tk_home_url($value, '') : sanitize_textarea_field($value);
            }
        }
    }

    foreach (array('eyebrow', 'title', 'description') as $key) {
        $value = $legacy('tk_home_capability_' . $key);
        if ($value !== '') {
            $options['capability'][$key] = sanitize_textarea_field($value);
        }
    }
    $options['capability']['image_id'] = $options['hero']['secondary_image_id'];

    foreach (array('title', 'description', 'label', 'url') as $key) {
        $value = $legacy('tk_home_cta_' . $key);
        if ($value !== '') {
            $options['final_cta'][$key] = $key === 'url' ? tk_home_url($value, '') : sanitize_textarea_field($value);
        }
    }

    return $options;
}

function tk_theme_find_attachment_by_file($relative_file)
{
    global $wpdb;
    return absint($wpdb->get_var($wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value = %s LIMIT 1",
        ltrim((string) $relative_file, '/')
    )));
}

function tk_theme_import_local_image($source_path, $slug, $title)
{
    global $wpdb;
    $existing = absint($wpdb->get_var($wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_tk_theme_option_asset' AND meta_value = %s LIMIT 1",
        $slug
    )));
    if ($existing && wp_attachment_is_image($existing)) {
        return $existing;
    }
    if (!is_file($source_path) || !is_readable($source_path)) {
        return 0;
    }

    $bits = wp_upload_bits(basename($source_path), null, file_get_contents($source_path));
    if (!empty($bits['error']) || empty($bits['file'])) {
        return 0;
    }

    $mime = wp_check_filetype($bits['file']);
    $attachment_id = wp_insert_attachment(array(
        'post_mime_type' => $mime['type'] ?: 'image/jpeg',
        'post_title' => sanitize_text_field($title),
        'post_status' => 'inherit',
    ), $bits['file']);
    if (is_wp_error($attachment_id) || !$attachment_id) {
        return 0;
    }

    require_once ABSPATH . 'wp-admin/includes/image.php';
    wp_update_attachment_metadata($attachment_id, wp_generate_attachment_metadata($attachment_id, $bits['file']));
    update_post_meta($attachment_id, '_wp_attachment_image_alt', sanitize_text_field($title));
    update_post_meta($attachment_id, '_tk_theme_option_asset', sanitize_key($slug));
    return absint($attachment_id);
}

function tk_theme_seed_media_assets(&$site_options, &$home_options)
{
    $site_options['brand']['logo_id'] = tk_theme_import_local_image(get_theme_file_path('/image/logo.png'), 'site-logo', $site_options['brand']['company_name']);
    $site_options['brand']['logo_white_id'] = tk_theme_import_local_image(get_theme_file_path('/image/logo-white.png'), 'site-logo-white', $site_options['brand']['company_name']);

    $certificate_id = tk_theme_find_attachment_by_file('2021/08/bct.png');
    if (!$certificate_id) {
        $certificate_path = WP_CONTENT_DIR . '/uploads/2021/08/bct.png';
        $certificate_id = tk_theme_import_local_image($certificate_path, 'bo-cong-thuong', 'Đã thông báo với Bộ Công Thương');
    }
    $site_options['integrations']['certificate_image_id'] = $certificate_id;

    foreach ($home_options['projects'] as $index => $project) {
        $extensions = array('project-hanam' => 'png', 'project-dany' => 'png', 'project-phusan' => 'png', 'project-thucuc' => 'jpg', 'project-bichngoc' => 'jpg');
        $slug = $project['slug'];
        $extension = $extensions[$slug] ?? '';
        if ($extension) {
            $path = get_theme_file_path('/assets/src/images/projects/' . $slug . '.' . $extension);
            $home_options['projects'][$index]['image_id'] = tk_theme_import_local_image($path, $slug, $project['title']);
        }
    }
}

function tk_theme_options_migrate()
{
    if (!is_admin() || !current_user_can('manage_options')) {
        return;
    }
    if (get_option('tuankhang_theme_options_schema_version') === TK_THEME_OPTIONS_SCHEMA_VERSION) {
        return;
    }
    if (!add_option('tuankhang_theme_options_migration_lock', time(), '', false)) {
        return;
    }

    try {
        $site_exists = get_option('tuankhang_site_options', null);
        $home_exists = get_option('tuankhang_home_options', null);
        $site_options = is_array($site_exists) ? $site_exists : tk_theme_legacy_site_options();
        $home_options = is_array($home_exists) ? $home_exists : tk_theme_legacy_home_options();

        if (!is_array($site_exists) || !is_array($home_exists)) {
            tk_theme_seed_media_assets($site_options, $home_options);
        }
        if (!is_array($site_exists)) {
            add_option('tuankhang_site_options', $site_options, '', true);
        }
        if (!is_array($home_exists)) {
            add_option('tuankhang_home_options', $home_options, '', false);
        }

        $locations = get_theme_mod('nav_menu_locations', array());
        if (empty($locations['primary']) && wp_get_nav_menu_object(25)) {
            $locations['primary'] = 25;
        }
        if (empty($locations['footer']) && wp_get_nav_menu_object(29)) {
            $locations['footer'] = 29;
        }
        set_theme_mod('nav_menu_locations', $locations);

        if (is_array(get_option('tuankhang_site_options')) && is_array(get_option('tuankhang_home_options'))) {
            update_option('tuankhang_theme_options_schema_version', TK_THEME_OPTIONS_SCHEMA_VERSION, false);
        }
    } catch (Throwable $error) {
        error_log('Tuan Khang Theme Options migration: ' . $error->getMessage());
    }

    delete_option('tuankhang_theme_options_migration_lock');
}
add_action('admin_init', 'tk_theme_options_migrate', 5);

function tk_theme_options_legacy_admin_notice()
{
    if (!isset($_GET['post']) || absint(wp_unslash($_GET['post'])) !== 61 || !current_user_can('manage_options')) {
        return;
    }
    echo '<div class="notice notice-warning"><p><strong>Trang Cài đặt cũ đã ngừng sử dụng.</strong> Dữ liệu chỉ được giữ để rollback. Hãy chỉnh sửa tại <a href="' . esc_url(admin_url('themes.php?page=tuankhang-theme-options')) . '">Giao diện → Theme Options</a>.</p></div>';
}
add_action('admin_notices', 'tk_theme_options_legacy_admin_notice');

function tk_theme_options_make_legacy_page_readonly()
{
    if (!isset($_GET['post']) || absint(wp_unslash($_GET['post'])) !== 61 || !current_user_can('manage_options')) {
        return;
    }

    remove_post_type_support('page', 'editor');
    remove_post_type_support('page', 'thumbnail');
    remove_meta_box('submitdiv', 'page', 'side');
    remove_meta_box('pageparentdiv', 'page', 'side');
    remove_meta_box('slugdiv', 'page', 'normal');
}
add_action('load-post.php', 'tk_theme_options_make_legacy_page_readonly');

function tk_theme_options_legacy_page_admin_css()
{
    if (!isset($_GET['post']) || absint(wp_unslash($_GET['post'])) !== 61) {
        return;
    }
    echo '<style>#titlediv,#post-body-content{pointer-events:none;opacity:.65}#titlediv input{background:#f0f0f1}</style>';
}
add_action('admin_head-post.php', 'tk_theme_options_legacy_page_admin_css');

function tk_theme_options_hide_legacy_acf_groups($field_group)
{
    if (!isset($_GET['post']) || absint(wp_unslash($_GET['post'])) !== 61) {
        return $field_group;
    }
    return false;
}
add_filter('acf/prepare_field_group', 'tk_theme_options_hide_legacy_acf_groups', 100);

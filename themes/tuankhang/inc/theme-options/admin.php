<?php
/**
 * Settings API powered Theme Options screen.
 */

if (!defined('ABSPATH')) {
    exit;
}

function tk_theme_options_admin_menu()
{
    add_theme_page(
        'Theme Options',
        'Theme Options',
        'manage_options',
        'tuankhang-theme-options',
        'tk_theme_options_render_page'
    );
}
add_action('admin_menu', 'tk_theme_options_admin_menu');

function tk_theme_options_register_settings()
{
    register_setting('tuankhang_site_options_group', 'tuankhang_site_options', array(
        'type' => 'array',
        'sanitize_callback' => 'tk_theme_sanitize_site_options',
        'default' => array(),
    ));
    register_setting('tuankhang_home_options_group', 'tuankhang_home_options', array(
        'type' => 'array',
        'sanitize_callback' => 'tk_theme_sanitize_home_options',
        'default' => array(),
    ));
}
add_action('admin_init', 'tk_theme_options_register_settings');

function tk_theme_sanitize_url_value($value, $setting_key = '')
{
    $value = trim((string) $value);
    if ($value === '') {
        return '';
    }

    $url = esc_url_raw($value, array('http', 'https'));
    if (!$url) {
        add_settings_error('tuankhang_theme_options', 'invalid_' . sanitize_key($setting_key), 'URL không hợp lệ đã được bỏ qua.', 'error');
    }
    return $url ?: '';
}

function tk_theme_sanitize_image_id($value)
{
    $id = absint($value);
    return $id && wp_attachment_is_image($id) ? $id : 0;
}

function tk_theme_sanitize_site_options($input)
{
    $input = is_array($input) ? $input : array();
    $brand = is_array($input['brand'] ?? null) ? $input['brand'] : array();
    $contact = is_array($input['contact'] ?? null) ? $input['contact'] : array();
    $social = is_array($input['social'] ?? null) ? $input['social'] : array();
    $integrations = is_array($input['integrations'] ?? null) ? $input['integrations'] : array();

    $output = array(
        'brand' => array(
            'company_name' => sanitize_text_field($brand['company_name'] ?? ''),
            'logo_id' => tk_theme_sanitize_image_id($brand['logo_id'] ?? 0),
            'logo_white_id' => tk_theme_sanitize_image_id($brand['logo_white_id'] ?? 0),
        ),
        'contact' => array(
            'hotline' => sanitize_text_field($contact['hotline'] ?? ''),
            'landline' => sanitize_text_field($contact['landline'] ?? ''),
            'email' => sanitize_email($contact['email'] ?? ''),
            'consultation_url' => tk_theme_sanitize_url_value($contact['consultation_url'] ?? '', 'consultation_url'),
        ),
        'branches' => array(),
        'map_embed_url' => '',
        'social' => array(),
        'integrations' => array(
            'contact_form_id' => absint($integrations['contact_form_id'] ?? 0),
            'certificate_image_id' => tk_theme_sanitize_image_id($integrations['certificate_image_id'] ?? 0),
            'certificate_url' => tk_theme_sanitize_url_value($integrations['certificate_url'] ?? '', 'certificate_url'),
        ),
    );

    foreach (array_slice((array) ($input['branches'] ?? array()), 0, 10) as $branch) {
        if (!is_array($branch)) {
            continue;
        }
        $label = sanitize_text_field($branch['label'] ?? '');
        $address = sanitize_textarea_field($branch['address'] ?? '');
        if ($label !== '' || $address !== '') {
            $output['branches'][] = compact('label', 'address');
        }
    }

    $map_url = tk_theme_sanitize_url_value($input['map_embed_url'] ?? '', 'map_embed_url');
    if ($map_url !== '') {
        $host = strtolower((string) wp_parse_url($map_url, PHP_URL_HOST));
        $path = (string) wp_parse_url($map_url, PHP_URL_PATH);
        if (!preg_match('/(^|\.)google\.[a-z.]+$/', $host) || strpos($path, '/maps/embed') !== 0) {
            add_settings_error('tuankhang_theme_options', 'invalid_map_host', 'Google Maps URL phải là đường dẫn /maps/embed hợp lệ.', 'error');
            $map_url = '';
        }
    }
    $output['map_embed_url'] = $map_url;

    foreach (array('zalo_url', 'messenger_url', 'facebook_url', 'instagram_url', 'youtube_url', 'tiktok_url', 'linkedin_url', 'x_url') as $key) {
        $output['social'][$key] = tk_theme_sanitize_url_value($social[$key] ?? '', $key);
    }

    return $output;
}

function tk_theme_sanitize_home_options($input)
{
    $input = is_array($input) ? $input : array();
    $defaults = tk_theme_home_defaults();
    $hero = is_array($input['hero'] ?? null) ? $input['hero'] : array();
    $story = is_array($input['story'] ?? null) ? $input['story'] : array();
    $capability = is_array($input['capability'] ?? null) ? $input['capability'] : array();
    $final_cta = is_array($input['final_cta'] ?? null) ? $input['final_cta'] : array();

    $output = array(
        'hero' => array(
            'eyebrow' => sanitize_text_field($hero['eyebrow'] ?? ''),
            'title' => sanitize_text_field($hero['title'] ?? ''),
            'description' => sanitize_textarea_field($hero['description'] ?? ''),
            'image_id' => tk_theme_sanitize_image_id($hero['image_id'] ?? 0),
            'secondary_image_id' => tk_theme_sanitize_image_id($hero['secondary_image_id'] ?? 0),
            'primary_label' => sanitize_text_field($hero['primary_label'] ?? ''),
            'primary_url' => tk_theme_sanitize_url_value($hero['primary_url'] ?? '', 'hero_primary_url'),
            'secondary_label' => sanitize_text_field($hero['secondary_label'] ?? ''),
            'secondary_url' => tk_theme_sanitize_url_value($hero['secondary_url'] ?? '', 'hero_secondary_url'),
        ),
        'metrics' => array(),
        'partners_heading' => tk_theme_sanitize_heading_group($input['partners_heading'] ?? array(), false),
        'partners' => array(),
        'story' => array(
            'eyebrow' => sanitize_text_field($story['eyebrow'] ?? ''),
            'title' => sanitize_text_field($story['title'] ?? ''),
            'content' => wp_kses_post($story['content'] ?? ''),
            'image_id' => tk_theme_sanitize_image_id($story['image_id'] ?? 0),
            'url' => tk_theme_sanitize_url_value($story['url'] ?? '', 'story_url'),
        ),
        'values_heading' => tk_theme_sanitize_heading_group($input['values_heading'] ?? array()),
        'values' => array(),
        'capability' => array(
            'eyebrow' => sanitize_text_field($capability['eyebrow'] ?? ''),
            'title' => sanitize_text_field($capability['title'] ?? ''),
            'description' => sanitize_textarea_field($capability['description'] ?? ''),
            'image_id' => tk_theme_sanitize_image_id($capability['image_id'] ?? 0),
            'items' => array(),
        ),
        'projects_heading' => tk_theme_sanitize_heading_group($input['projects_heading'] ?? array()),
        'projects' => array(),
        'final_cta' => array(
            'title' => sanitize_text_field($final_cta['title'] ?? ''),
            'description' => sanitize_textarea_field($final_cta['description'] ?? ''),
            'label' => sanitize_text_field($final_cta['label'] ?? ''),
            'url' => tk_theme_sanitize_url_value($final_cta['url'] ?? '', 'final_cta_url'),
        ),
    );

    for ($index = 0; $index < 5; $index++) {
        $metric = is_array($input['metrics'][$index] ?? null) ? $input['metrics'][$index] : $defaults['metrics'][$index];
        $output['metrics'][] = array(
            'value' => sanitize_text_field($metric['value'] ?? ''),
            'suffix' => sanitize_text_field($metric['suffix'] ?? ''),
            'label' => sanitize_text_field($metric['label'] ?? ''),
        );
    }

    foreach (array_slice((array) ($input['partners'] ?? array()), 0, 20) as $partner) {
        if (!is_array($partner)) {
            continue;
        }
        $image_id = tk_theme_sanitize_image_id($partner['image_id'] ?? 0);
        $name = sanitize_text_field($partner['name'] ?? '');
        if ($image_id) {
            $output['partners'][] = compact('image_id', 'name');
        }
    }

    $value_icons = array('target', 'mission', 'vision');
    for ($index = 0; $index < 3; $index++) {
        $value = is_array($input['values'][$index] ?? null) ? $input['values'][$index] : array();
        $output['values'][] = array(
            'title' => sanitize_text_field($value['title'] ?? ''),
            'description' => wp_kses_post($value['description'] ?? ''),
            'icon' => $value_icons[$index],
        );
    }

    $capability_icons = array('portfolio', 'clinical', 'distribution');
    for ($index = 0; $index < 3; $index++) {
        $item = is_array($capability['items'][$index] ?? null) ? $capability['items'][$index] : array();
        $output['capability']['items'][] = array(
            'title' => sanitize_text_field($item['title'] ?? ''),
            'description' => sanitize_textarea_field($item['description'] ?? ''),
            'url' => tk_theme_sanitize_url_value($item['url'] ?? '', 'capability_url_' . $index),
            'icon' => $capability_icons[$index],
        );
    }

    for ($index = 0; $index < 5; $index++) {
        $project = is_array($input['projects'][$index] ?? null) ? $input['projects'][$index] : array();
        $output['projects'][] = array(
            'slug' => sanitize_key($project['slug'] ?? $defaults['projects'][$index]['slug']),
            'image_id' => tk_theme_sanitize_image_id($project['image_id'] ?? 0),
            'eyebrow' => sanitize_text_field($project['eyebrow'] ?? ''),
            'title' => sanitize_text_field($project['title'] ?? ''),
            'description' => sanitize_textarea_field($project['description'] ?? ''),
        );
    }

    return $output;
}

function tk_theme_sanitize_heading_group($group, $with_description = true)
{
    $group = is_array($group) ? $group : array();
    $output = array(
        'eyebrow' => sanitize_text_field($group['eyebrow'] ?? ''),
        'title' => sanitize_text_field($group['title'] ?? ''),
    );
    if ($with_description) {
        $output['description'] = sanitize_textarea_field($group['description'] ?? '');
    }
    return $output;
}

function tk_theme_options_admin_assets($hook_suffix)
{
    if ($hook_suffix !== 'appearance_page_tuankhang-theme-options') {
        return;
    }

    wp_enqueue_media();
    tk_enqueue_built_style('tuankhang-theme-options-admin', '/assets/dist/theme-options-admin.min.css');
    tk_enqueue_built_script('tuankhang-theme-options-admin', '/assets/dist/theme-options-admin.min.js');
}
add_action('admin_enqueue_scripts', 'tk_theme_options_admin_assets');

function tk_theme_admin_field_name($scope, $path)
{
    $name = tk_theme_option_name($scope);
    foreach (explode('.', $path) as $segment) {
        $name .= '[' . $segment . ']';
    }
    return $name;
}

function tk_theme_admin_icon($name, $class = '')
{
    $icons = array(
        'brand' => '<path d="M5 20h14M7 20V9l5-4 5 4v11M9.5 12h5M9.5 15h5"/>',
        'contact' => '<path d="M5 4h4l2 5-2.5 1.5a14 14 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2C9.7 20.5 3.5 14.3 3 6a2 2 0 0 1 2-2Z"/>',
        'location' => '<path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/>',
        'social' => '<circle cx="6" cy="12" r="2.5"/><circle cx="18" cy="6" r="2.5"/><circle cx="18" cy="18" r="2.5"/><path d="m8.2 10.8 7.5-3.6M8.2 13.2l7.5 3.6"/>',
        'integration' => '<path d="M8 12h8M12 8v8M7 3v4M17 17v4M3 7h4M17 17h4"/><rect x="7" y="7" width="10" height="10" rx="3"/>',
        'hero' => '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="m3 15 5-5 4 4 3-3 6 6"/><circle cx="16.5" cy="8.5" r="1.5"/>',
        'metrics' => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
        'partners' => '<circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.5"/><path d="M3 20a6 6 0 0 1 12 0M14 15a5 5 0 0 1 7 4.5"/>',
        'story' => '<path d="M5 4h11a3 3 0 0 1 3 3v13H7a2 2 0 0 1-2-2V4Z"/><path d="M8 8h7M8 12h7M8 16h4"/>',
        'values' => '<circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="3"/><path d="m15 9 5-5M17 4h3v3"/>',
        'capability' => '<path d="M4 20V8l8-4 8 4v12M8 20v-5h8v5M8 10h.01M12 10h.01M16 10h.01"/>',
        'projects' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M8 5V3h8v2M3 10h18"/>',
        'cta' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'image' => '<rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m3 16 5-5 4 4 3-3 6 6"/>',
        'grip' => '<circle cx="9" cy="7" r="1" fill="currentColor" stroke="none"/><circle cx="15" cy="7" r="1" fill="currentColor" stroke="none"/><circle cx="9" cy="12" r="1" fill="currentColor" stroke="none"/><circle cx="15" cy="12" r="1" fill="currentColor" stroke="none"/><circle cx="9" cy="17" r="1" fill="currentColor" stroke="none"/><circle cx="15" cy="17" r="1" fill="currentColor" stroke="none"/>',
        'more' => '<circle cx="5" cy="12" r="1.3" fill="currentColor" stroke="none"/><circle cx="12" cy="12" r="1.3" fill="currentColor" stroke="none"/><circle cx="19" cy="12" r="1.3" fill="currentColor" stroke="none"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'up' => '<path d="m6 14 6-6 6 6"/>',
        'down' => '<path d="m6 10 6 6 6-6"/>',
        'trash' => '<path d="M4 7h16M9 7V4h6v3M7 7l1 13h8l1-13M10 11v5M14 11v5"/>',
    );
    $paths = $icons[$name] ?? $icons['integration'];
    return '<svg class="tk-admin-icon ' . esc_attr($class) . '" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">' . $paths . '</svg>';
}

function tk_theme_admin_sections($tab)
{
    if ($tab === 'home') {
        return array(
            array('id' => 'hero', 'label' => 'Hero', 'icon' => 'hero'),
            array('id' => 'metrics', 'label' => 'Số liệu năng lực', 'icon' => 'metrics', 'count' => 5),
            array('id' => 'partners', 'label' => 'Đối tác', 'icon' => 'partners', 'count' => count((array) tk_home_option('partners', array()))),
            array('id' => 'story', 'label' => 'Câu chuyện thương hiệu', 'icon' => 'story'),
            array('id' => 'values', 'label' => 'Giá trị dẫn đường', 'icon' => 'values', 'count' => 3),
            array('id' => 'capability', 'label' => 'Năng lực', 'icon' => 'capability', 'count' => 3),
            array('id' => 'projects', 'label' => 'Dự án tiêu biểu', 'icon' => 'projects', 'count' => 5),
            array('id' => 'final-cta', 'label' => 'CTA cuối trang', 'icon' => 'cta'),
        );
    }

    return array(
        array('id' => 'brand', 'label' => 'Thương hiệu', 'icon' => 'brand'),
        array('id' => 'contact', 'label' => 'Liên hệ', 'icon' => 'contact'),
        array('id' => 'branches-map', 'label' => 'Chi nhánh & bản đồ', 'icon' => 'location', 'count' => count((array) tk_site_option('branches', array()))),
        array('id' => 'social', 'label' => 'Mạng xã hội', 'icon' => 'social'),
        array('id' => 'integrations', 'label' => 'Tích hợp & chứng nhận', 'icon' => 'integration'),
    );
}

function tk_theme_admin_input($scope, $path, $label, $type = 'text', $description = '', $attributes = array())
{
    $value = tk_theme_option($scope, $path, '');
    $name = tk_theme_admin_field_name($scope, $path);
    $id = 'tk-option-' . sanitize_html_class(str_replace('.', '-', $path));
    $description_id = $id . '-description';
    $error_id = $id . '-error';
    $validation = $type === 'email' ? 'email' : ($type === 'url' ? ($path === 'map_embed_url' ? 'map' : 'url') : ($path === 'integrations.contact_form_id' ? 'cf7' : ''));
    $input_attributes = array(
        'data-option-input' => '',
        'data-option-path' => $scope . '.' . $path,
    );
    if ($validation !== '') $input_attributes['data-validation'] = $validation;
    if ($type === 'email') $input_attributes['autocomplete'] = 'email';
    if ($type === 'tel') $input_attributes['autocomplete'] = 'tel';
    if ($type === 'url') $input_attributes['inputmode'] = 'url';
    $input_attributes = array_merge($input_attributes, is_array($attributes) ? $attributes : array());
    unset($input_attributes['rows']);
    $input_attributes['aria-describedby'] = trim(($description !== '' ? $description_id . ' ' : '') . $error_id);
    $render_attributes = '';
    foreach ($input_attributes as $attribute => $attribute_value) {
        $render_attributes .= ' ' . esc_attr($attribute);
        if ($attribute_value !== '') $render_attributes .= '="' . esc_attr((string) $attribute_value) . '"';
    }

    echo '<div class="tk-option-field">';
    echo '<label for="' . esc_attr($id) . '"><strong>' . esc_html($label) . '</strong></label>';
    if ($type === 'textarea') {
        $rows = max(2, absint($attributes['rows'] ?? 4));
        echo '<textarea id="' . esc_attr($id) . '" name="' . esc_attr($name) . '" rows="' . esc_attr((string) $rows) . '"' . $render_attributes . '>' . esc_textarea((string) $value) . '</textarea>';
    } else {
        echo '<input id="' . esc_attr($id) . '" name="' . esc_attr($name) . '" type="' . esc_attr($type) . '" value="' . esc_attr((string) $value) . '"' . $render_attributes . '>';
    }
    if ($description !== '') {
        echo '<p id="' . esc_attr($description_id) . '" class="description">' . esc_html($description) . '</p>';
    }
    echo '<p id="' . esc_attr($error_id) . '" class="tk-field-error" data-field-error role="alert" hidden></p>';
    echo '</div>';
}

function tk_theme_admin_media($scope, $path, $label, $variant = 'contain')
{
    $id = absint(tk_theme_option($scope, $path, 0));
    $name = tk_theme_admin_field_name($scope, $path);
    $preview = $id ? wp_get_attachment_image_url($id, 'thumbnail') : '';
    echo '<div class="tk-option-field tk-media-field tk-media-field--' . esc_attr($variant) . '" data-media-field data-option-path="' . esc_attr($scope . '.' . $path) . '">';
    echo '<span><strong>' . esc_html($label) . '</strong></span>';
    echo '<input type="hidden" name="' . esc_attr($name) . '" value="' . esc_attr((string) $id) . '" data-media-id>';
    echo '<div class="tk-media-preview" data-media-preview>' . ($preview ? '<img src="' . esc_url($preview) . '" alt="">' : '<span class="tk-media-empty">' . tk_theme_admin_icon('image') . '<small>Chưa chọn ảnh</small></span>') . '</div>';
    echo '<div class="tk-media-actions"><button type="button" class="button button-secondary" data-media-select data-select-label="Chọn ảnh" data-replace-label="Thay ảnh">' . ($preview ? 'Thay ảnh' : 'Chọn ảnh') . '</button><button type="button" class="button-link-delete" data-media-remove' . ($id ? '' : ' hidden') . '>Xóa ảnh</button></div>';
    echo '</div>';
}

function tk_theme_admin_section_open($id, $title, $description = '', $icon = 'integration', $count = null)
{
    echo '<section id="tk-section-' . esc_attr($id) . '" class="tk-options-section" data-options-section="' . esc_attr($id) . '">';
    echo '<header class="tk-options-section-header"><span class="tk-section-icon">' . tk_theme_admin_icon($icon) . '</span><div><h2>' . esc_html($title) . '</h2>';
    if ($description !== '') echo '<p>' . esc_html($description) . '</p>';
    echo '</div>' . ($count !== null ? '<span class="tk-section-count">' . esc_html((string) $count) . '</span>' : '') . '</header><div class="tk-options-section-body">';
}

function tk_theme_admin_section_close()
{
    echo '</div></section>';
}

function tk_theme_options_render_page()
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Bạn không có quyền truy cập trang này.', 'tuankhang'));
    }

    $tab = isset($_GET['tab']) && sanitize_key(wp_unslash($_GET['tab'])) === 'home' ? 'home' : 'site';
    $sections = tk_theme_admin_sections($tab);
    echo '<div class="wrap tk-theme-options" data-options-console data-active-tab="' . esc_attr($tab) . '">';
    echo '<form method="post" action="options.php" data-theme-options-form novalidate>';
    settings_fields($tab === 'home' ? 'tuankhang_home_options_group' : 'tuankhang_site_options_group');
    echo '<header class="tk-options-toolbar">';
    echo '<div class="tk-options-title"><span class="tk-options-kicker">Tuấn Khang Admin Console</span><h1>Theme Options</h1><p>Quản lý nội dung và thông tin dùng chung trên toàn bộ website.</p></div>';
    echo '<div class="tk-options-save-cluster"><span class="tk-save-status is-saved" data-save-status aria-live="polite"><i></i><span data-save-status-text>Đã đồng bộ</span></span><button type="submit" class="button button-primary tk-save-button" data-save-button><span>Lưu thay đổi</span></button></div>';
    echo '</header>';
    settings_errors('tuankhang_theme_options');
    echo '<nav class="tk-options-tabs" aria-label="Nhóm tùy chọn">';
    echo '<a class="tk-options-tab ' . ($tab === 'site' ? 'is-active' : '') . '" href="' . esc_url(admin_url('themes.php?page=tuankhang-theme-options&tab=site')) . '"' . ($tab === 'site' ? ' aria-current="page"' : '') . '>' . tk_theme_admin_icon('integration') . '<span>Thông tin website</span></a>';
    echo '<a class="tk-options-tab ' . ($tab === 'home' ? 'is-active' : '') . '" href="' . esc_url(admin_url('themes.php?page=tuankhang-theme-options&tab=home')) . '"' . ($tab === 'home' ? ' aria-current="page"' : '') . '>' . tk_theme_admin_icon('hero') . '<span>Trang chủ</span></a>';
    echo '</nav>';
    echo '<div class="tk-options-layout"><aside class="tk-options-sidebar"><p>Điều hướng nhanh</p><nav aria-label="Các khu vực Theme Options">';
    foreach ($sections as $section) {
        echo '<a href="#tk-section-' . esc_attr($section['id']) . '" data-section-link="' . esc_attr($section['id']) . '">' . tk_theme_admin_icon($section['icon']) . '<span>' . esc_html($section['label']) . '</span>';
        if (isset($section['count'])) echo '<small data-section-count="' . esc_attr($section['id']) . '">' . esc_html((string) $section['count']) . '</small>';
        echo '</a>';
    }
    echo '</nav></aside><main class="tk-options-content">';
    if ($tab === 'home') {
        tk_theme_options_render_home_fields();
    } else {
        tk_theme_options_render_site_fields();
    }
    echo '<div class="tk-options-bottom-save"><div><strong>Hoàn tất cấu hình?</strong><p>Kiểm tra các thay đổi trước khi cập nhật website.</p></div>';
    submit_button('Lưu thay đổi', 'secondary', 'submit', false, array('data-bottom-submit' => ''));
    echo '</div></main></div><div class="screen-reader-text" data-options-live aria-live="polite" aria-atomic="true"></div></form></div>';
}

function tk_theme_options_render_site_fields()
{
    tk_theme_admin_section_open('brand', 'Thương hiệu', 'Logo và tên doanh nghiệp dùng xuyên suốt header, footer và metadata.', 'brand');
    echo '<div class="tk-options-grid">';
    tk_theme_admin_input('site', 'brand.company_name', 'Tên doanh nghiệp');
    tk_theme_admin_media('site', 'brand.logo_id', 'Logo nền sáng', 'logo');
    tk_theme_admin_media('site', 'brand.logo_white_id', 'Logo nền tối', 'logo');
    echo '</div>';
    tk_theme_admin_section_close();

    tk_theme_admin_section_open('contact', 'Liên hệ', 'Thông tin hỗ trợ hiển thị trên header, footer và các cụm tư vấn.', 'contact');
    echo '<div class="tk-options-grid">';
    tk_theme_admin_input('site', 'contact.hotline', 'Hotline', 'tel');
    tk_theme_admin_input('site', 'contact.landline', 'Số máy bàn', 'tel');
    tk_theme_admin_input('site', 'contact.email', 'Email', 'email');
    tk_theme_admin_input('site', 'contact.consultation_url', 'URL trang tư vấn', 'url');
    echo '</div>';
    tk_theme_admin_section_close();

    tk_theme_admin_section_open('branches-map', 'Chi nhánh và bản đồ', 'Thứ tự chi nhánh tại đây cũng là thứ tự hiển thị ở footer.', 'location', count((array) tk_site_option('branches', array())));
    tk_theme_admin_repeater('site', 'branches', array('label' => 'Tên chi nhánh', 'address' => 'Địa chỉ'), 10);
    tk_theme_admin_input('site', 'map_embed_url', 'Google Maps Embed URL', 'url', 'Chỉ chấp nhận URL dạng https://www.google.com/maps/embed?...');
    tk_theme_admin_section_close();

    tk_theme_admin_section_open('social', 'Mạng xã hội', 'Để trống kênh không sử dụng; liên kết trống sẽ không hiển thị ngoài frontend.', 'social');
    echo '<div class="tk-options-grid">';
    foreach (array('zalo_url' => 'Zalo', 'messenger_url' => 'Messenger', 'facebook_url' => 'Facebook', 'instagram_url' => 'Instagram', 'youtube_url' => 'YouTube', 'tiktok_url' => 'TikTok', 'linkedin_url' => 'LinkedIn', 'x_url' => 'X / Twitter') as $key => $label) {
        tk_theme_admin_input('site', 'social.' . $key, $label, 'url');
    }
    echo '</div>';
    tk_theme_admin_section_close();

    tk_theme_admin_section_open('integrations', 'Tích hợp và chứng nhận', 'Thiết lập form tư vấn và chứng nhận Bộ Công Thương.', 'integration');
    echo '<div class="tk-options-grid">';
    tk_theme_admin_input('site', 'integrations.contact_form_id', 'Contact Form 7 ID', 'number');
    tk_theme_admin_input('site', 'integrations.certificate_url', 'URL chứng nhận Bộ Công Thương', 'url');
    tk_theme_admin_media('site', 'integrations.certificate_image_id', 'Ảnh chứng nhận', 'certificate');
    echo '</div>';
    tk_theme_admin_section_close();
}

function tk_theme_options_render_home_fields()
{
    tk_theme_admin_section_open('hero', 'Hero', 'Nội dung mở đầu và hai hành động chính của homepage.', 'hero');
    echo '<div class="tk-options-grid">';
    foreach (array('eyebrow' => 'Dòng giới thiệu', 'title' => 'Tiêu đề', 'description' => 'Mô tả') as $key => $label) {
        tk_theme_admin_input('home', 'hero.' . $key, $label, $key === 'description' ? 'textarea' : 'text');
    }
    tk_theme_admin_media('home', 'hero.image_id', 'Ảnh chính', 'hero');
    tk_theme_admin_media('home', 'hero.secondary_image_id', 'Ảnh phụ', 'hero-secondary');
    foreach (array('primary_label' => 'CTA chính - nhãn', 'primary_url' => 'CTA chính - URL', 'secondary_label' => 'CTA phụ - nhãn', 'secondary_url' => 'CTA phụ - URL') as $key => $label) {
        tk_theme_admin_input('home', 'hero.' . $key, $label, substr($key, -3) === 'url' ? 'url' : 'text');
    }
    echo '</div>';
    tk_theme_admin_section_close();

    tk_theme_admin_section_open('metrics', 'Số liệu năng lực', 'Năm chỉ số tạo bằng chứng năng lực trong phần mở đầu homepage.', 'metrics', 5);
    echo '<div class="tk-fixed-cards">';
    foreach ((array) tk_home_option('metrics', array()) as $index => $metric) {
        echo '<div class="tk-fixed-card"><h3><span>' . esc_html(sprintf('%02d', $index + 1)) . '</span>Số liệu ' . esc_html((string) ($index + 1)) . '</h3>';
        foreach (array('value' => 'Giá trị', 'suffix' => 'Hậu tố', 'label' => 'Nhãn') as $key => $label) {
            tk_theme_admin_input('home', 'metrics.' . $index . '.' . $key, $label);
        }
        echo '</div>';
    }
    echo '</div>';
    tk_theme_admin_section_close();

    tk_theme_admin_section_open('partners', 'Đối tác', 'Quản lý logo và thứ tự thương hiệu trong dải đối tác homepage.', 'partners', count((array) tk_home_option('partners', array())));
    echo '<div class="tk-options-grid">';
    tk_theme_admin_input('home', 'partners_heading.eyebrow', 'Dòng giới thiệu');
    tk_theme_admin_input('home', 'partners_heading.title', 'Tiêu đề');
    echo '</div>';
    tk_theme_admin_partner_repeater();
    tk_theme_admin_section_close();

    tk_theme_admin_section_open('story', 'Câu chuyện thương hiệu', 'Khối nội dung giới thiệu hành trình và định vị Tuấn Khang.', 'story');
    echo '<div class="tk-options-grid">';
    tk_theme_admin_input('home', 'story.eyebrow', 'Dòng giới thiệu');
    tk_theme_admin_input('home', 'story.title', 'Tiêu đề');
    tk_theme_admin_input('home', 'story.content', 'Nội dung', 'textarea');
    tk_theme_admin_input('home', 'story.url', 'URL xem thêm', 'url');
    tk_theme_admin_media('home', 'story.image_id', 'Hình ảnh', 'story');
    echo '</div>';
    tk_theme_admin_section_close();

    tk_theme_admin_heading_and_fixed_items('values', 'Giá trị dẫn đường', 'values_heading', 'values', 3, true);

    tk_theme_admin_section_open('capability', 'Năng lực', 'Ba năng lực cốt lõi và hình ảnh minh họa cho section nền navy.', 'capability', 3);
    echo '<div class="tk-options-grid">';
    foreach (array('eyebrow' => 'Dòng giới thiệu', 'title' => 'Tiêu đề', 'description' => 'Mô tả') as $key => $label) {
        tk_theme_admin_input('home', 'capability.' . $key, $label, $key === 'description' ? 'textarea' : 'text');
    }
    tk_theme_admin_media('home', 'capability.image_id', 'Hình ảnh', 'capability');
    echo '</div><div class="tk-fixed-cards">';
    for ($index = 0; $index < 3; $index++) {
        echo '<div class="tk-fixed-card"><h3><span>' . esc_html(sprintf('%02d', $index + 1)) . '</span>Năng lực ' . esc_html((string) ($index + 1)) . '</h3>';
        tk_theme_admin_input('home', 'capability.items.' . $index . '.title', 'Tiêu đề');
        tk_theme_admin_input('home', 'capability.items.' . $index . '.description', 'Mô tả', 'textarea');
        tk_theme_admin_input('home', 'capability.items.' . $index . '.url', 'URL', 'url');
        echo '</div>';
    }
    echo '</div>';
    tk_theme_admin_section_close();

    tk_theme_admin_section_open('projects', 'Dự án tiêu biểu', 'Vị trí 1 là card lớn bên trái; vị trí 2–5 là bốn card nhỏ.', 'projects', 5);
    echo '<div class="tk-options-grid">';
    foreach (array('eyebrow' => 'Dòng giới thiệu', 'title' => 'Tiêu đề', 'description' => 'Mô tả') as $key => $label) {
        tk_theme_admin_input('home', 'projects_heading.' . $key, $label, $key === 'description' ? 'textarea' : 'text');
    }
    echo '</div><div class="tk-fixed-cards">';
    foreach ((array) tk_home_option('projects', array()) as $index => $project) {
        echo '<div class="tk-fixed-card tk-project-option-card ' . ($index === 0 ? 'is-featured' : '') . '"><h3><span>' . esc_html(sprintf('%02d', $index + 1)) . '</span>' . ($index === 0 ? 'Dự án lớn' : 'Dự án nhỏ ' . esc_html((string) $index)) . '</h3>';
        echo '<input type="hidden" name="' . esc_attr(tk_theme_admin_field_name('home', 'projects.' . $index . '.slug')) . '" value="' . esc_attr($project['slug'] ?? '') . '">';
        tk_theme_admin_media('home', 'projects.' . $index . '.image_id', 'Hình ảnh', 'project');
        tk_theme_admin_input('home', 'projects.' . $index . '.eyebrow', 'Dòng giới thiệu');
        tk_theme_admin_input('home', 'projects.' . $index . '.title', 'Tên dự án');
        tk_theme_admin_input('home', 'projects.' . $index . '.description', 'Mô tả', 'textarea');
        echo '</div>';
    }
    echo '</div>';
    tk_theme_admin_section_close();

    tk_theme_admin_section_open('final-cta', 'CTA cuối trang', 'Lời mời tư vấn xuất hiện trước footer homepage.', 'cta');
    echo '<div class="tk-options-grid">';
    tk_theme_admin_input('home', 'final_cta.title', 'Tiêu đề');
    tk_theme_admin_input('home', 'final_cta.description', 'Mô tả', 'textarea');
    tk_theme_admin_input('home', 'final_cta.label', 'Nhãn nút');
    tk_theme_admin_input('home', 'final_cta.url', 'URL nút', 'url');
    echo '</div>';
    tk_theme_admin_section_close();
}

function tk_theme_admin_heading_and_fixed_items($section_id, $section_title, $heading_key, $items_key, $count, $rich_text = false)
{
    tk_theme_admin_section_open($section_id, $section_title, 'Ba nguyên tắc định hướng lựa chọn sản phẩm và phục vụ khách hàng.', 'values', $count);
    echo '<div class="tk-options-grid">';
    foreach (array('eyebrow' => 'Dòng giới thiệu', 'title' => 'Tiêu đề', 'description' => 'Mô tả') as $key => $label) {
        tk_theme_admin_input('home', $heading_key . '.' . $key, $label, $key === 'description' ? 'textarea' : 'text');
    }
    echo '</div><div class="tk-fixed-cards">';
    for ($index = 0; $index < $count; $index++) {
        echo '<div class="tk-fixed-card"><h3><span>' . esc_html(sprintf('%02d', $index + 1)) . '</span>Mục ' . esc_html((string) ($index + 1)) . '</h3>';
        tk_theme_admin_input('home', $items_key . '.' . $index . '.title', 'Tiêu đề');
        tk_theme_admin_input('home', $items_key . '.' . $index . '.description', 'Mô tả', $rich_text ? 'textarea' : 'text');
        echo '</div>';
    }
    echo '</div>';
    tk_theme_admin_section_close();
}

function tk_theme_admin_repeater($scope, $path, $fields, $max)
{
    $rows = (array) tk_theme_option($scope, $path, array());
    echo '<div class="tk-repeater" data-repeater data-sortable data-repeater-max="' . esc_attr((string) $max) . '" data-repeater-scope="' . esc_attr($scope) . '" data-repeater-path="' . esc_attr($path) . '"><div data-repeater-rows>';
    foreach ($rows as $index => $row) {
        tk_theme_admin_repeater_row($scope, $path, $index, (array) $row, $fields);
    }
    echo '</div><button type="button" class="button button-secondary tk-add-button" data-repeater-add>' . tk_theme_admin_icon('plus') . '<span>Thêm chi nhánh</span></button></div>';
}

function tk_theme_admin_repeater_row($scope, $path, $index, $row, $fields)
{
    $row_label = trim((string) ($row['label'] ?? '')) ?: 'Chi nhánh ' . ($index + 1);
    echo '<div class="tk-repeater-row tk-branch-row" data-repeater-row data-sortable-row data-row-label="' . esc_attr($row_label) . '">';
    echo '<button type="button" class="tk-sort-handle" data-sort-handle aria-label="Kéo để sắp xếp ' . esc_attr($row_label) . '">' . tk_theme_admin_icon('grip') . '</button>';
    echo '<span class="tk-row-index">' . esc_html(sprintf('%02d', $index + 1)) . '</span><div class="tk-repeater-row-fields">';
    foreach ($fields as $key => $label) {
        $name = tk_theme_admin_field_name($scope, $path . '.' . $index . '.' . $key);
        echo '<label><strong>' . esc_html($label) . '</strong><textarea rows="2" name="' . esc_attr($name) . '" data-repeater-field="' . esc_attr($key) . '" data-option-input>' . esc_textarea((string) ($row[$key] ?? '')) . '</textarea></label>';
    }
    echo '</div>' . tk_theme_admin_row_menu('data-repeater-remove', 'Xóa chi nhánh') . '</div>';
}

function tk_theme_admin_partner_repeater()
{
    $rows = (array) tk_home_option('partners', array());
    echo '<div class="tk-repeater tk-partner-repeater" data-partner-repeater data-sortable data-repeater-max="20"><div data-partner-rows>';
    foreach ($rows as $index => $row) {
        tk_theme_admin_partner_row($index, (array) $row);
    }
    echo '</div><button type="button" class="button button-secondary tk-add-button" data-partner-add>' . tk_theme_admin_icon('plus') . '<span>Thêm đối tác</span></button></div>';
}

function tk_theme_admin_partner_row($index, $row)
{
    $image_id = absint($row['image_id'] ?? 0);
    $preview = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : '';
    $row_label = trim((string) ($row['name'] ?? '')) ?: 'Đối tác ' . ($index + 1);
    echo '<div class="tk-repeater-row tk-partner-row" data-partner-row data-sortable-row data-row-label="' . esc_attr($row_label) . '">';
    echo '<button type="button" class="tk-sort-handle" data-sort-handle aria-label="Kéo để sắp xếp ' . esc_attr($row_label) . '">' . tk_theme_admin_icon('grip') . '</button><span class="tk-row-index">' . esc_html(sprintf('%02d', $index + 1)) . '</span>';
    echo '<div class="tk-media-field tk-partner-media" data-media-field>';
    echo '<input type="hidden" name="' . esc_attr(tk_theme_admin_field_name('home', 'partners.' . $index . '.image_id')) . '" value="' . esc_attr((string) $image_id) . '" data-media-id data-partner-field="image_id">';
    echo '<button type="button" class="tk-partner-preview" data-media-select aria-label="' . ($preview ? 'Thay ảnh ' : 'Chọn ảnh cho ') . esc_attr($row_label) . '"><span data-media-preview>' . ($preview ? '<img src="' . esc_url($preview) . '" alt="">' : tk_theme_admin_icon('image')) . '</span><small>' . ($preview ? 'Thay ảnh' : 'Chọn ảnh') . '</small></button>';
    echo '<button type="button" class="button-link-delete tk-partner-remove-image" data-media-remove' . ($image_id ? '' : ' hidden') . '>Xóa ảnh</button></div>';
    echo '<label class="tk-partner-name"><strong>Tên đối tác / alt</strong><input type="text" name="' . esc_attr(tk_theme_admin_field_name('home', 'partners.' . $index . '.name')) . '" value="' . esc_attr((string) ($row['name'] ?? '')) . '" data-partner-field="name" data-option-input></label>';
    echo tk_theme_admin_row_menu('data-partner-remove', 'Xóa đối tác') . '</div>';
}

function tk_theme_admin_row_menu($remove_attribute, $remove_label)
{
    return '<details class="tk-row-menu" data-row-menu><summary aria-label="Mở menu hành động">' . tk_theme_admin_icon('more') . '</summary><div class="tk-row-menu-popover"><button type="button" data-row-move="up">' . tk_theme_admin_icon('up') . '<span>Di chuyển lên</span></button><button type="button" data-row-move="down">' . tk_theme_admin_icon('down') . '<span>Di chuyển xuống</span></button><button type="button" class="is-destructive" ' . esc_attr($remove_attribute) . '>' . tk_theme_admin_icon('trash') . '<span>' . esc_html($remove_label) . '</span></button></div></details>';
}

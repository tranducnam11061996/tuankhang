<?php
/**
 * Tailwind homepage helpers.
 *
 * The homepage intentionally reads the ACF payload once and keeps the legacy
 * field names untouched. No helper in this file writes to the database.
 */

if (!defined('ABSPATH')) {
    exit;
}

function tk_home_page_id()
{
    return (int) apply_filters('tk_home_page_id', 61);
}

function tk_home_fields()
{
    static $fields = null;
    if ($fields !== null) {
        return $fields;
    }

    $fields = function_exists('get_fields') ? get_fields(tk_home_page_id()) : array();
    return is_array($fields) ? $fields : array();
}

function tk_home_field($key, $default = '')
{
    $fields = tk_home_fields();
    if (array_key_exists($key, $fields)) {
        return $fields[$key];
    }

    $value = get_post_meta(tk_home_page_id(), $key, true);
    return $value !== '' ? $value : $default;
}

function tk_home_language()
{
    return function_exists('wpm_get_language') ? (string) wpm_get_language() : 'vi';
}

function tk_home_text($vi, $en)
{
    return tk_home_language() === 'en' ? $en : $vi;
}

function tk_home_url($value, $default = '#')
{
    if (is_array($value)) {
        $value = $value['url'] ?? '';
    }
    $value = trim((string) $value);
    if ($value === '') {
        return $default;
    }
    if (preg_match_all('~https?://[^\s]+~i', $value, $matches) && !empty($matches[0])) {
        return (string) end($matches[0]);
    }
    return $value;
}

function tk_home_logo($white = false)
{
    $suffix = tk_home_language() === 'en' ? '-en' : '';
    $filename = $white ? 'logo-white' . $suffix . '.png' : 'logo' . $suffix . '.png';
    return get_theme_file_uri('image/' . $filename);
}

function tk_home_menu_tree($menu_id)
{
    $items = wp_get_nav_menu_items($menu_id);
    if (!$items || is_wp_error($items)) {
        return array();
    }

    $by_parent = array();
    foreach ($items as $item) {
        $parent = (int) $item->menu_item_parent;
        $by_parent[$parent][] = $item;
    }

    $build = function ($parent) use (&$build, &$by_parent) {
        $tree = array();
        foreach ($by_parent[$parent] ?? array() as $item) {
            $tree[] = array(
                'item' => $item,
                'children' => $build((int) $item->ID),
            );
        }
        return $tree;
    };

    return $build(0);
}

function tk_home_desktop_menu($nodes, $depth = 0)
{
    if (!$nodes) {
        return;
    }
    $list_class = $depth === 0 ? 'flex items-center' : 'tk-submenu';
    echo '<ul class="' . esc_attr($list_class) . '">';
    foreach ($nodes as $node) {
        $item = $node['item'];
        echo '<li class="tk-menu-item">';
        echo '<a class="tk-menu-link" href="' . esc_url($item->url) . '" title="' . esc_attr($item->attr_title ?: $item->title) . '">' . esc_html($item->title) . '</a>';
        tk_home_desktop_menu($node['children'], $depth + 1);
        echo '</li>';
    }
    echo '</ul>';
}

function tk_home_mobile_menu($nodes, $depth = 0)
{
    if (!$nodes) {
        return;
    }
    $class = $depth === 0 ? 'space-y-1' : 'ml-4 hidden border-l border-slate-200 pl-3';
    echo '<ul class="' . esc_attr($class) . '">';
    foreach ($nodes as $node) {
        $item = $node['item'];
        echo '<li class="border-b border-slate-100">';
        echo '<div class="flex items-center">';
        echo '<a class="flex min-h-11 flex-1 items-center py-2 font-semibold text-primary" href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
        if ($node['children']) {
            echo '<button type="button" data-submenu-toggle aria-expanded="false" class="flex size-11 items-center justify-center rounded text-primary" aria-label="' . esc_attr(tk_home_text('Mở menu con', 'Open submenu')) . '">';
            echo '<svg class="size-4" aria-hidden="true" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>';
            echo '</button>';
        }
        echo '</div>';
        tk_home_mobile_menu($node['children'], $depth + 1);
        echo '</li>';
    }
    echo '</ul>';
}

function tk_home_image_data($image)
{
    $id = 0;
    $url = '';
    $alt = '';
    $width = 0;
    $height = 0;

    if (is_array($image)) {
        $id = (int) ($image['ID'] ?? $image['id'] ?? 0);
        $url = (string) ($image['url'] ?? '');
        $alt = (string) ($image['alt'] ?? '');
        $width = (int) ($image['width'] ?? 0);
        $height = (int) ($image['height'] ?? 0);
    } elseif (is_numeric($image)) {
        $id = (int) $image;
    } elseif (is_string($image)) {
        $url = $image;
    }

    if (!$id && $url) {
        $id = (int) attachment_url_to_postid($url);
    }
    if ($id) {
        $url = $url ?: (string) wp_get_attachment_url($id);
        $alt = $alt ?: (string) get_post_meta($id, '_wp_attachment_image_alt', true);
        $meta = wp_get_attachment_metadata($id);
        if (is_array($meta)) {
            $width = $width ?: (int) ($meta['width'] ?? 0);
            $height = $height ?: (int) ($meta['height'] ?? 0);
        }
    }

    return compact('id', 'url', 'alt', 'width', 'height');
}

function tk_home_slot_config($slot)
{
    $configs = array(
        'hero' => array('widths' => array(480, 768, 1280, 1702), 'sizes' => '100vw', 'directory' => 'home'),
        'story' => array('widths' => array(480, 768, 1024), 'sizes' => '(min-width: 768px) 50vw, 100vw', 'directory' => 'home'),
        'news' => array('widths' => array(480, 768, 1024), 'sizes' => '(min-width: 1024px) 40vw, (min-width: 640px) 50vw, 100vw', 'directory' => 'home'),
        'product' => array('widths' => array(320, 480, 768), 'sizes' => '(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw', 'directory' => 'home'),
        'project' => array('widths' => array(320, 480, 768), 'sizes' => '(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw', 'directory' => 'home'),
        'partner' => array('widths' => array(160, 320), 'sizes' => '160px', 'directory' => 'home'),
        'product-thumb' => array('widths' => array(160, 320), 'sizes' => '88px', 'directory' => 'products'),
        'product-card' => array('widths' => array(320, 480, 768), 'sizes' => '(min-width: 1024px) 220px, (min-width: 480px) 50vw, 100vw', 'directory' => 'products'),
        'product-detail' => array('widths' => array(480, 768, 1024), 'sizes' => '(min-width: 1024px) 460px, 100vw', 'directory' => 'products'),
    );
    return $configs[$slot] ?? $configs['news'];
}

function tk_picture($image, $slot, $args = array())
{
    $data = tk_home_image_data($image);
    if (!$data['url']) {
        return '';
    }

    $config = tk_home_slot_config($slot);
    $alt = isset($args['alt']) ? (string) $args['alt'] : $data['alt'];
    $class = isset($args['class']) ? (string) $args['class'] : '';
    $loading = isset($args['loading']) ? (string) $args['loading'] : 'lazy';
    $fetchpriority = isset($args['fetchpriority']) ? (string) $args['fetchpriority'] : 'auto';
    $width = $data['width'] ?: (int) max($config['widths']);
    $height = $data['height'] ?: (int) round($width * 0.625);
    $sources = array('avif' => array(), 'webp' => array());

    if ($data['id']) {
        foreach ($sources as $format => $unused) {
            foreach ($config['widths'] as $candidate) {
                if ($candidate > $width && count($config['widths']) > 1) {
                    continue;
                }
                $relative = 'assets/dist/images/' . $config['directory'] . '/' . $data['id'] . '-' . $candidate . '.' . $format;
                if (file_exists(get_theme_file_path($relative))) {
                    $sources[$format][] = get_theme_file_uri($relative) . ' ' . $candidate . 'w';
                }
            }
        }
    }

    $img_attributes = array(
        'src' => esc_url($data['url']),
        'alt' => esc_attr($alt),
        'width' => (string) $width,
        'height' => (string) $height,
        'loading' => $loading,
        'decoding' => 'async',
        'fetchpriority' => $fetchpriority,
        'class' => esc_attr($class),
        'sizes' => esc_attr($config['sizes']),
    );
    if ($data['id']) {
        $fallback_srcset = wp_get_attachment_image_srcset($data['id'], 'full');
        if ($fallback_srcset) {
            $img_attributes['srcset'] = esc_attr($fallback_srcset);
        }
    }

    $html = '<picture>';
    if ($sources['avif']) {
        $html .= '<source type="image/avif" srcset="' . esc_attr(implode(', ', $sources['avif'])) . '" sizes="' . esc_attr($config['sizes']) . '">';
    }
    if ($sources['webp']) {
        $html .= '<source type="image/webp" srcset="' . esc_attr(implode(', ', $sources['webp'])) . '" sizes="' . esc_attr($config['sizes']) . '">';
    }
    $html .= '<img';
    foreach ($img_attributes as $name => $value) {
        $html .= ' ' . $name . '="' . $value . '"';
    }
    $html .= '></picture>';
    return $html;
}

function tk_home_picture($image, $slot, $args = array())
{
    return tk_picture($image, $slot, $args);
}

function tk_home_preload_hero()
{
    if (!is_front_page()) {
        return;
    }
    $data = tk_home_image_data(tk_home_field('wpcf-anhbanner-1'));
    if (!$data['url']) {
        return;
    }
    $config = tk_home_slot_config('hero');
    $srcset = array();
    foreach ($config['widths'] as $candidate) {
        $relative = 'assets/dist/images/home/' . $data['id'] . '-' . $candidate . '.avif';
        if ($data['id'] && file_exists(get_theme_file_path($relative))) {
            $srcset[] = get_theme_file_uri($relative) . ' ' . $candidate . 'w';
        }
    }
    echo '<link rel="preload" as="image" href="' . esc_url($data['url']) . '"';
    if ($srcset) {
        echo ' imagesrcset="' . esc_attr(implode(', ', $srcset)) . '" imagesizes="100vw" type="image/avif"';
    }
    echo ' fetchpriority="high">' . "\n";
}
add_action('wp_head', 'tk_home_preload_hero', 2);

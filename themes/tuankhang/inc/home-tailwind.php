<?php
/**
 * Tailwind homepage helpers.
 *
 * Presentation and responsive-media helpers for the homepage.
 */

if (!defined('ABSPATH')) {
    exit;
}

function tk_home_language()
{
    return 'vi';
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

function tk_home_hero_image()
{
    return tk_home_option('hero.image_id');
}

function tk_home_hero_secondary_image()
{
    return tk_home_option('hero.secondary_image_id', 2250);
}

function tk_home_logo($white = false)
{
    $option_key = $white ? 'brand.logo_white_id' : 'brand.logo_id';
    $attachment_id = absint(tk_site_option($option_key, 0));
    if ($attachment_id) {
        $url = wp_get_attachment_url($attachment_id);
        if ($url) {
            return $url;
        }
    }
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
        $is_current = in_array('current-menu-item', (array) $item->classes, true)
            || in_array('current-menu-ancestor', (array) $item->classes, true);
        echo '<li class="tk-menu-item">';
        echo '<a class="tk-menu-link' . ($is_current ? ' is-current' : '') . '" href="' . esc_url($item->url) . '" title="' . esc_attr($item->attr_title ?: $item->title) . '"' . ($is_current ? ' aria-current="page"' : '') . '>' . esc_html($item->title) . '</a>';
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
        $is_current = in_array('current-menu-item', (array) $item->classes, true)
            || in_array('current-menu-ancestor', (array) $item->classes, true);
        echo '<li class="border-b border-slate-100">';
        echo '<div class="flex items-center">';
        echo '<a class="flex min-h-11 flex-1 items-center py-2 font-semibold text-primary' . ($is_current ? ' is-current' : '') . '" href="' . esc_url($item->url) . '"' . ($is_current ? ' aria-current="page"' : '') . '>' . esc_html($item->title) . '</a>';
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
        'hero' => array('widths' => array(480, 768, 1200, 1600), 'sizes' => '(min-width: 1024px) 50vw, 100vw', 'directory' => 'home'),
        'hero-proof' => array('widths' => array(320, 480, 768), 'sizes' => '(min-width: 1024px) 22vw, 45vw', 'directory' => 'home', 'filename_suffix' => '-proof'),
        'story' => array('widths' => array(480, 768, 1200, 1600), 'sizes' => '(min-width: 768px) 50vw, 100vw', 'directory' => 'home'),
        'capability' => array('widths' => array(480, 768, 1200, 1600), 'sizes' => '(min-width: 1024px) 45vw, 100vw', 'directory' => 'home'),
        'news' => array('widths' => array(320, 480, 768), 'sizes' => '(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw', 'directory' => 'home'),
        'product' => array('widths' => array(320, 480, 768), 'sizes' => '(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw', 'directory' => 'home'),
        'featured-product' => array('widths' => array(320, 480, 768), 'sizes' => '(min-width: 1024px) 25vw, (min-width: 640px) 50vw, 50vw', 'directory' => 'home'),
        'featured-system' => array('widths' => array(320, 480, 768), 'sizes' => '(min-width: 1024px) 25vw, (min-width: 640px) 50vw, 50vw', 'directory' => 'home'),
        'project' => array('widths' => array(320, 480, 768), 'sizes' => '(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw', 'directory' => 'home'),
        'partner' => array('widths' => array(160, 320), 'sizes' => '160px', 'directory' => 'home'),
        'product-thumb' => array('widths' => array(160, 320), 'sizes' => '88px', 'directory' => 'products'),
        'product-card' => array('widths' => array(320, 480, 768), 'sizes' => '(min-width: 1024px) 220px, (min-width: 480px) 50vw, 100vw', 'directory' => 'products'),
        'product-listing-hero' => array('widths' => array(480, 768, 1024), 'sizes' => '(min-width: 1024px) 38vw, calc(100vw - 32px)', 'directory' => 'products'),
        'product-detail' => array('widths' => array(480, 768, 1024), 'sizes' => '(min-width: 1024px) 460px, 100vw', 'directory' => 'products'),
        'product-gallery' => array('widths' => array(480, 768, 1024), 'sizes' => '(min-width: 1024px) 42vw, calc(100vw - 32px)', 'directory' => 'products'),
        'product-content' => array('widths' => array(480, 768, 1024), 'sizes' => '(min-width: 1024px) 760px, calc(100vw - 32px)', 'directory' => 'products'),
        'post-thumb' => array('widths' => array(160, 320), 'sizes' => '88px', 'directory' => 'content'),
        'post-card' => array('widths' => array(320, 480, 768), 'sizes' => '(min-width: 1024px) 340px, (min-width: 640px) 40vw, 100vw', 'directory' => 'content'),
        'page-content' => array('widths' => array(480, 768, 1200), 'sizes' => '(min-width: 1024px) 850px, calc(100vw - 32px)', 'directory' => 'content'),
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
    $sizes = isset($args['sizes']) ? (string) $args['sizes'] : (string) $config['sizes'];
    $picture_class = isset($args['picture_class']) ? (string) $args['picture_class'] : '';
    $width = $data['width'] ?: (int) max($config['widths']);
    $height = $data['height'] ?: (int) round($width * 0.625);
    $sources = array('avif' => array(), 'webp' => array());

    if ($data['id']) {
        $filename_suffix = (string) ($config['filename_suffix'] ?? '');
        foreach ($sources as $format => $unused) {
            foreach ($config['widths'] as $candidate) {
                if ($candidate > $width && count($config['widths']) > 1) {
                    continue;
                }
                $relative = 'assets/dist/images/' . $config['directory'] . '/' . $data['id'] . $filename_suffix . '-' . $candidate . '.' . $format;
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
        'sizes' => esc_attr($sizes),
    );
    if ($data['id']) {
        $fallback_srcset = wp_get_attachment_image_srcset($data['id'], 'full');
        if ($fallback_srcset) {
            $img_attributes['srcset'] = esc_attr($fallback_srcset);
        }
    }

    $html = '<picture' . ($picture_class !== '' ? ' class="' . esc_attr($picture_class) . '"' : '') . '>';
    if ($sources['avif']) {
        $html .= '<source type="image/avif" srcset="' . esc_attr(implode(', ', $sources['avif'])) . '" sizes="' . esc_attr($sizes) . '">';
    }
    if ($sources['webp']) {
        $html .= '<source type="image/webp" srcset="' . esc_attr(implode(', ', $sources['webp'])) . '" sizes="' . esc_attr($sizes) . '">';
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

function tk_home_project_picture($image_id, $alt = '', $sizes = '')
{
    $image_id = absint($image_id);
    if (!$image_id) {
        return '';
    }
    return tk_picture($image_id, 'project', array(
        'alt' => $alt,
        'class' => 'h-full w-full object-cover',
        'picture_class' => 'tk-project-art',
        'sizes' => $sizes ?: '(min-width: 1024px) 40vw, (min-width: 768px) 50vw, 100vw',
    ));
}

function tk_home_preload_hero()
{
    if (!is_front_page()) {
        return;
    }
    $data = tk_home_image_data(tk_home_hero_image());
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
        echo ' imagesrcset="' . esc_attr(implode(', ', $srcset)) . '" imagesizes="(min-width: 1024px) 50vw, 100vw" type="image/avif"';
    }
    echo ' fetchpriority="high">' . "\n";
}
add_action('wp_head', 'tk_home_preload_hero', 2);

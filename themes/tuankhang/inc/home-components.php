<?php
/**
 * Small presentation helpers for the premium homepage.
 */

if (!defined('ABSPATH')) {
    exit;
}
function tk_home_render_cta($label, $url, $variant = 'primary', $extra_classes = '')
{
    if (!$label || !$url) {
        return;
    }
    $class = $variant === 'secondary' ? 'tk-cta tk-cta-secondary' : 'tk-cta tk-cta-primary';
    echo '<a class="' . esc_attr(trim($class . ' ' . $extra_classes)) . '" href="' . esc_url($url) . '">';
    echo '<span>' . esc_html($label) . '</span>';
    echo '<svg class="tk-cta-arrow size-4" aria-hidden="true" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 10h12M11 5l5 5-5 5"/></svg>';
    echo '</a>';
}

function tk_home_render_heading($eyebrow, $title, $description = '', $theme = 'light')
{
    $light = $theme === 'dark';
    echo '<header class="tk-section-heading' . ($light ? ' text-white' : '') . '" data-reveal>';
    if ($eyebrow) {
        echo '<p class="tk-eyebrow' . ($light ? ' text-sky-300' : '') . '">' . esc_html($eyebrow) . '</p>';
    }
    echo '<h2 class="tk-display-title' . ($light ? ' text-white' : '') . '">' . esc_html($title) . '</h2>';
    if ($description) {
        echo '<p class="mt-4 max-w-2xl text-pretty text-base leading-7 ' . ($light ? 'text-blue-100' : 'text-muted') . '">' . esc_html($description) . '</p>';
    }
    echo '</header>';
}

function tk_home_icon($name)
{
    $icons = array(
        'target' => '<circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="3"/><path d="m15 9 5-5M17 4h3v3"/>',
        'mission' => '<path d="M12 3v18M3 12h18"/><circle cx="12" cy="12" r="8"/><path d="m8.5 15.5 7-7"/>',
        'vision' => '<path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6S2.5 12 2.5 12Z"/><circle cx="12" cy="12" r="2.5"/>',
        'portfolio' => '<rect x="3" y="6" width="18" height="14" rx="2"/><path d="M8 6V4h8v2M3 11h18M9 11v2h6v-2"/>',
        'clinical' => '<path d="M12 21s7-4.35 7-11V5l-7-2-7 2v5c0 6.65 7 11 7 11Z"/><path d="M9 11h6M12 8v6"/>',
        'distribution' => '<path d="M3 7h12v10H3zM15 10h3l3 3v4h-6z"/><circle cx="7" cy="18" r="2"/><circle cx="18" cy="18" r="2"/>',
    );
    $paths = $icons[$name] ?? $icons['portfolio'];
    return '<svg class="size-7" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">' . $paths . '</svg>';
}

function tk_home_query_cards($query_args, $limit)
{
    $query_args = array_merge(array(
        'post_status' => 'publish',
        'posts_per_page' => max($limit * 3, $limit),
        'no_found_rows' => true,
        'ignore_sticky_posts' => true,
    ), $query_args);
    $query = new WP_Query($query_args);
    $items = array();

    foreach ($query->posts as $post) {
        $post_id = $post instanceof WP_Post ? $post->ID : absint($post);
        $image_id = get_post_thumbnail_id($post_id);
        if (!$post_id || !$image_id) {
            continue;
        }
        $items[] = array(
            'post_id' => $post_id,
            'image' => $image_id,
            'title' => get_the_title($post_id),
            'link' => get_permalink($post_id),
        );
        if (count($items) >= $limit) {
            break;
        }
    }
    return $items;
}

function tk_home_featured_products()
{
    $ids = function_exists('tk_product_featured_ids') ? tk_product_featured_ids() : array();
    if (!$ids) {
        return array();
    }
    return tk_home_query_cards(array(
        'post_type' => 'san-pham',
        'post__in' => array_map('intval', $ids),
        'orderby' => 'post__in',
    ), 4);
}

function tk_home_implant_systems()
{
    return tk_home_query_cards(array(
        'post_type' => 'san-pham',
        'orderby' => array('menu_order' => 'ASC', 'date' => 'DESC', 'ID' => 'DESC'),
        'tax_query' => array(array(
            'taxonomy' => 'danh-muc',
            'field' => 'slug',
            'terms' => array('implant'),
            'include_children' => true,
        )),
    ), 4);
}

function tk_home_latest_news()
{
    return tk_home_query_cards(array(
        'post_type' => 'post',
        'category_name' => 'tin-tuc',
        'orderby' => array('date' => 'DESC', 'ID' => 'DESC'),
    ), 3);
}

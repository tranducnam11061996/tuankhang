<?php
/**
 * Cache invalidation hooks for site-wide content managed outside normal posts.
 */

if (!defined('ABSPATH')) {
    exit;
}

function tk_performance_flush_page_cache()
{
    static $flushed = false;
    if ($flushed || !function_exists('wp_cache_clear_cache')) {
        return;
    }
    $flushed = true;
    wp_cache_clear_cache();
}

add_action('created_term', 'tk_performance_flush_page_cache');
add_action('edited_term', 'tk_performance_flush_page_cache');
add_action('delete_term', 'tk_performance_flush_page_cache');
add_action('wp_update_nav_menu', 'tk_performance_flush_page_cache');
add_action('customize_save_after', 'tk_performance_flush_page_cache');

function tk_performance_flush_acf_options($post_id)
{
    if ($post_id === 'options' || $post_id === 'option') {
        tk_performance_flush_page_cache();
    }
}
add_action('acf/save_post', 'tk_performance_flush_acf_options', 30);

<?php
/**
 * Runtime accessors for Theme Options.
 */

if (!defined('ABSPATH')) {
    exit;
}

function tk_theme_options($scope = 'site')
{
    static $cache = array();
    $scope = $scope === 'home' ? 'home' : 'site';

    if (array_key_exists($scope, $cache)) {
        return $cache[$scope];
    }

    $option_name = tk_theme_option_name($scope);
    $sentinel = new stdClass();
    $stored = get_option($option_name, $sentinel);
    $defaults = $scope === 'home' ? tk_theme_home_defaults() : tk_theme_site_defaults();

    if ($stored === $sentinel || !is_array($stored)) {
        $legacy_function = $scope === 'home' ? 'tk_theme_legacy_home_options' : 'tk_theme_legacy_site_options';
        $stored = function_exists($legacy_function) ? call_user_func($legacy_function) : array();
    }

    $cache[$scope] = tk_theme_array_replace_recursive($defaults, is_array($stored) ? $stored : array());
    return $cache[$scope];
}

function tk_theme_flush_options_cache()
{
    // Static request caches are intentionally refreshed on the next request.
    wp_cache_delete('tuankhang_site_options', 'options');
    wp_cache_delete('tuankhang_home_options', 'options');
}

function tk_theme_option($scope, $key, $default = '')
{
    $value = tk_theme_options($scope);
    $segments = is_array($key) ? $key : explode('.', (string) $key);

    foreach ($segments as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }
        $value = $value[$segment];
    }

    return $value;
}

function tk_site_option($key, $default = '')
{
    return tk_theme_option('site', $key, $default);
}

function tk_home_option($key, $default = '')
{
    return tk_theme_option('home', $key, $default);
}

function tk_theme_phone_uri($phone)
{
    return preg_replace('/[^0-9+]/', '', (string) $phone);
}

function tk_theme_zalo_url()
{
    $configured = trim((string) tk_site_option('social.zalo_url'));
    if ($configured !== '') {
        return $configured;
    }

    $phone = preg_replace('/\D+/', '', (string) tk_site_option('contact.hotline'));
    return $phone ? 'https://zalo.me/' . rawurlencode($phone) : '';
}

function tk_theme_attachment_url($attachment_id, $fallback = '')
{
    $attachment_id = absint($attachment_id);
    $url = $attachment_id ? wp_get_attachment_url($attachment_id) : '';
    return $url ?: $fallback;
}

function tk_theme_menu_id($location, $legacy_id = 0)
{
    $locations = get_nav_menu_locations();
    $menu_id = absint($locations[$location] ?? 0);
    return $menu_id ?: absint($legacy_id);
}

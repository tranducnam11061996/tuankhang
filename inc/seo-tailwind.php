<?php
/**
 * Lightweight SEO fallbacks for installations without a dedicated SEO plugin.
 */

if (!defined('ABSPATH')) {
    exit;
}

function tk_seo_plugin_is_active()
{
    return defined('WPSEO_VERSION')
        || defined('RANK_MATH_VERSION')
        || defined('SEOPRESS_VERSION')
        || defined('AIOSEO_VERSION')
        || class_exists('The_SEO_Framework\\Load');
}

function tk_seo_trim_description($value, $limit = 158)
{
    $value = html_entity_decode(wp_strip_all_tags((string) $value, true), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $value = trim((string) preg_replace('/\s+/u', ' ', $value));
    if ($value === '') {
        return '';
    }
    if (function_exists('mb_strlen') && mb_strlen($value, 'UTF-8') > $limit) {
        $value = mb_substr($value, 0, $limit + 1, 'UTF-8');
        $value = preg_replace('/\s+\S*$/u', '', $value);
        return rtrim((string) $value, " \t\n\r\0\x0B,.;:-") . '…';
    }
    return $value;
}

function tk_seo_description()
{
    $fallback = 'Tuấn Khang cung cấp thiết bị, vật liệu và giải pháp nha khoa chính hãng, đồng hành cùng bác sĩ và phòng khám trên toàn quốc.';

    if (is_front_page() || is_home()) {
        return $fallback;
    }
    if (is_post_type_archive('san-pham')) {
        return 'Khám phá danh mục thiết bị, vật liệu và giải pháp nha khoa chính hãng tại Tuấn Khang Medical.';
    }
    if (is_tax() || is_category() || is_tag()) {
        $term = get_queried_object();
        if ($term instanceof WP_Term) {
            $description = term_description($term);
            return tk_seo_trim_description($description ?: sprintf('Danh mục %s tại Tuấn Khang Medical.', $term->name));
        }
    }
    if (is_singular()) {
        $post = get_queried_object();
        if ($post instanceof WP_Post) {
            $description = has_excerpt($post) ? $post->post_excerpt : $post->post_content;
            return tk_seo_trim_description($description ?: $fallback);
        }
    }
    if (is_archive()) {
        $description = get_the_archive_description();
        return tk_seo_trim_description($description ?: sprintf('%s — thông tin mới nhất từ Tuấn Khang Medical.', get_the_archive_title()));
    }
    return $fallback;
}

function tk_seo_canonical_url()
{
    if (is_search() || is_404() || is_singular()) {
        return '';
    }

    $page = max(1, (int) get_query_var('paged'));
    if ($page > 1) {
        return get_pagenum_link($page);
    }
    if (is_front_page() || is_home()) {
        return home_url('/');
    }
    if (is_post_type_archive()) {
        $post_type = get_query_var('post_type');
        if (is_array($post_type)) {
            $post_type = reset($post_type);
        }
        return $post_type ? (string) get_post_type_archive_link($post_type) : '';
    }
    if (is_tax() || is_category() || is_tag()) {
        $term = get_queried_object();
        if ($term instanceof WP_Term) {
            $url = get_term_link($term);
            return is_wp_error($url) ? '' : $url;
        }
    }
    return is_archive() ? get_pagenum_link(1) : '';
}

function tk_seo_render_fallback_tags()
{
    if (tk_seo_plugin_is_active() || is_search() || is_404()) {
        return;
    }

    $description = tk_seo_description();
    if ($description !== '') {
        printf("<meta name=\"description\" content=\"%s\">\n", esc_attr($description));
    }
    $canonical = tk_seo_canonical_url();
    if ($canonical !== '') {
        printf("<link rel=\"canonical\" href=\"%s\">\n", esc_url($canonical));
    }
}
add_action('wp_head', 'tk_seo_render_fallback_tags', 4);

function tk_seo_noindex_non_public_pages($robots)
{
    if (!tk_seo_plugin_is_active() && (is_search() || is_404())) {
        $robots['noindex'] = true;
        $robots['follow'] = true;
    }
    return $robots;
}
add_filter('wp_robots', 'tk_seo_noindex_non_public_pages');

function tk_seo_disambiguate_duplicate_titles($parts)
{
    if (tk_seo_plugin_is_active() || !is_singular() || empty($parts['title'])) {
        return $parts;
    }
    $post = get_queried_object();
    if (!$post instanceof WP_Post || $post->post_title === '') {
        return $parts;
    }

    global $wpdb;
    $has_duplicate = (bool) $wpdb->get_var($wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type = %s AND post_title = %s AND ID <> %d LIMIT 1",
        $post->post_type,
        $post->post_title,
        $post->ID
    ));
    if ($has_duplicate) {
        $parts['title'] .= ' — ' . get_the_date('d/m/Y', $post);
    }
    return $parts;
}
add_filter('document_title_parts', 'tk_seo_disambiguate_duplicate_titles', 20);

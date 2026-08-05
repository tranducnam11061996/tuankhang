<?php
get_header();
the_post();

$page_id = get_the_ID();
$title = get_the_title($page_id);
$content_html = '';
if (is_page(63)) {
    $story = function_exists('get_field') ? get_field('wpcf-cau-chuyen-ve-tuan-khang', 61) : get_post_meta(61, 'wpcf-cau-chuyen-ve-tuan-khang', true);
    if ($story) $content_html .= apply_filters('the_content', $story);
}
$content_html .= apply_filters('the_content', get_the_content(null, false, $page_id));
$prepared = tk_content_prepare_article($content_html, $title);
$context = tk_content_detail_context($page_id, $prepared);

get_template_part('template-parts/content-detail', null, array(
    'post_id' => $page_id,
    'prepared' => $prepared,
    'context' => $context,
));
get_footer();

<?php
get_header();
the_post();

$post_id = get_the_ID();
$title = get_the_title($post_id);
$content_html = apply_filters('the_content', get_the_content(null, false, $post_id));
$prepared = tk_content_prepare_article($content_html, $title);
$context = tk_content_detail_context($post_id, $prepared);

get_template_part('template-parts/content-detail', null, array(
    'post_id' => $post_id,
    'prepared' => $prepared,
    'context' => $context,
));
get_footer();

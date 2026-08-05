<?php
if (!defined('ABSPATH')) exit;

$post_id = (int) ($args['post_id'] ?? get_the_ID());
$prepared = is_array($args['prepared'] ?? null) ? $args['prepared'] : array('html' => '', 'toc' => array(), 'reading_minutes' => 1);
$context = is_array($args['context'] ?? null) ? $args['context'] : tk_content_detail_context($post_id, $prepared);
$toc_items = is_array($prepared['toc'] ?? null) ? $prepared['toc'] : array();
$is_post = ($context['kind'] ?? '') === 'post';
?>
<main id="main-content" class="tk-content-context tk-content-detail">
    <?php tk_content_render_detail_hero($context); ?>
    <?php tk_content_sidebar_drawer($toc_items, true); ?>

    <section class="tk-content-reading" aria-label="<?php echo esc_attr(tk_site_text('Nội dung tài liệu', 'Document content')); ?>">
        <div class="tk-container">
            <?php tk_content_sidebar_button($toc_items, true); ?>
            <div class="tk-content-detail-layout">
                <aside class="tk-content-detail-rail" aria-label="<?php echo esc_attr($is_post ? tk_site_text('Chuyên mục và mục lục bài viết', 'Categories and article contents') : tk_site_text('Điều hướng và mục lục nội dung', 'Navigation and contents')); ?>">
                    <?php tk_content_sidebar('desktop', $toc_items, true); ?>
                </aside>

                <article class="tk-article-shell" aria-labelledby="content-detail-title">
                    <div class="tk-article-signal" aria-hidden="true"><span class="tk-article-signal-title" title="<?php echo esc_attr($context['title'] ?? ''); ?>"><?php echo esc_html($context['title'] ?? ''); ?></span><span class="tk-article-signal-line"></span></div>
                    <div class="tk-content tk-article-content"><?php echo wp_kses_post($prepared['html'] ?? ''); ?></div>
                    <?php tk_content_render_endcap($context); ?>
                </article>
            </div>

            <?php if ($is_post) : $related_ids = tk_content_related_post_ids($post_id); if ($related_ids) : ?>
                <section class="tk-content-related" aria-labelledby="related-posts-heading">
                    <div class="tk-content-related-head"><div><p><?php echo esc_html(tk_site_text('ĐỌC TIẾP / TUẤN KHANG JOURNAL', 'CONTINUE / TUAN KHANG JOURNAL')); ?></p><h2 id="related-posts-heading"><?php echo esc_html(tk_site_text('Các bài đăng khác', 'Related posts')); ?></h2></div><span><?php echo esc_html(sprintf('%02d', count($related_ids))); ?></span></div>
                    <div class="tk-content-related-grid"><?php foreach ($related_ids as $related_id) tk_content_post_card($related_id, true); ?></div>
                </section>
            <?php endif; endif; ?>
        </div>
    </section>
</main>

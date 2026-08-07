<?php
if (!defined('ABSPATH')) exit;
$items = isset($args['items']) && is_array($args['items']) ? $args['items'] : array();
if (!$items) return;
$term = get_term_by('slug', 'tin-tuc', 'category');
$view_all_url = $term instanceof WP_Term ? get_term_link($term) : home_url('/tin-tuc/');
?>
<section class="tk-home-section bg-surface">
    <div class="tk-container">
        <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between"><?php tk_home_render_heading('Kiến thức và hoạt động', 'Tin tức chuyên môn', 'Cập nhật công nghệ, hội thảo và hoạt động hợp tác dành cho cộng đồng nha khoa.'); ?><div data-reveal><?php tk_home_render_cta('Xem tất cả', is_wp_error($view_all_url) ? home_url('/tin-tuc/') : $view_all_url, 'secondary'); ?></div></div>
        <div class="tk-news-grid mt-10">
            <?php foreach ($items as $index => $item) : ?><article class="tk-news-card" data-reveal data-reveal-order="<?php echo esc_attr($index + 1); ?>"><a class="tk-news-media" href="<?php echo esc_url($item['link']); ?>"><?php echo tk_home_picture($item['image'], 'news', array('alt' => $item['title'], 'class' => 'h-full w-full object-cover')); ?></a><div class="tk-news-body"><p>Góc chuyên môn</p><h3><?php echo esc_html($item['title']); ?></h3><a class="tk-text-link mt-auto" href="<?php echo esc_url($item['link']); ?>">Xem chi tiết<span aria-hidden="true">→</span></a></div></article><?php endforeach; ?>
        </div>
    </div>
</section>

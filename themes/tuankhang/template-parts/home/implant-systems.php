<?php
if (!defined('ABSPATH')) exit;
$items = isset($args['items']) && is_array($args['items']) ? $args['items'] : array();
if (!$items) return;
$term = get_term_by('slug', 'implant', 'danh-muc');
$view_all_url = $term instanceof WP_Term ? get_term_link($term) : home_url('/implant/');
?>
<section class="tk-home-section bg-white">
    <div class="tk-container tk-product-showcase">
        <?php tk_home_render_heading('Giải pháp cấy ghép', 'Hệ thống Implant', 'Các hệ thống được lựa chọn theo tiêu chí ổn định, chính xác và phù hợp thực hành lâm sàng.'); ?>
        <div class="tk-product-showcase-grid grid grid-cols-2 items-stretch gap-3 sm:gap-5 lg:grid-cols-4">
            <?php foreach ($items as $index => $item) : ?><article class="tk-home-card tk-product-showcase-card" data-reveal data-reveal-order="<?php echo esc_attr(($index % 4) + 1); ?>"><a class="tk-home-card-media" href="<?php echo esc_url($item['link']); ?>"><?php echo tk_home_picture($item['image'], 'featured-system', array('alt' => $item['title'], 'class' => 'h-full w-full object-contain')); ?></a><div class="tk-home-card-body"><h3 class="tk-card-title"><?php echo esc_html($item['title']); ?></h3><a class="tk-text-link tk-product-showcase-action mt-auto" href="<?php echo esc_url($item['link']); ?>" aria-label="<?php echo esc_attr(sprintf('Xem chi tiết: %s', $item['title'])); ?>">Xem chi tiết<span aria-hidden="true">→</span></a></div></article><?php endforeach; ?>
        </div>
        <div class="tk-product-showcase-view-all" data-reveal><?php tk_home_render_cta('Xem tất cả', is_wp_error($view_all_url) ? home_url('/implant/') : $view_all_url, 'secondary'); ?></div>
    </div>
</section>

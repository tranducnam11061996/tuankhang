<?php
if (!defined('ABSPATH')) exit;
$items = isset($args['items']) && is_array($args['items']) ? $args['items'] : array();
if (!$items) return;
$archive_url = get_post_type_archive_link('san-pham') ?: home_url('/san-pham/');
?>
<section class="tk-home-section bg-surface">
    <div class="tk-container tk-product-showcase tk-featured-product-showcase">
        <?php tk_home_render_heading('Danh mục tuyển chọn', 'Sản phẩm nổi bật', 'Những sản phẩm được quan tâm trong hệ sinh thái thiết bị và vật liệu nha khoa Tuấn Khang.'); ?>
        <div class="tk-product-showcase-grid grid grid-cols-2 items-stretch gap-3 sm:gap-5 lg:grid-cols-4">
            <?php foreach ($items as $index => $item) : ?><article class="tk-home-card tk-product-card tk-product-showcase-card" data-reveal data-reveal-order="<?php echo esc_attr(($index % 4) + 1); ?>"><a class="tk-home-card-media" href="<?php echo esc_url($item['link']); ?>"><?php echo tk_home_picture($item['image'], 'featured-product', array('alt' => $item['title'], 'class' => 'h-full w-full object-contain')); ?></a><div class="tk-home-card-body"><h3 class="tk-card-title"><?php echo esc_html($item['title']); ?></h3><a class="tk-text-link tk-product-showcase-action mt-auto" href="<?php echo esc_url($item['link']); ?>" aria-label="<?php echo esc_attr(sprintf('Xem chi tiết: %s', $item['title'])); ?>">Xem chi tiết<span aria-hidden="true">→</span></a></div></article><?php endforeach; ?>
        </div>
        <div class="tk-product-showcase-view-all" data-reveal><?php tk_home_render_cta('Xem tất cả', $archive_url, 'secondary'); ?></div>
    </div>
</section>

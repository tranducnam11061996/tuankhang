<?php
get_header();

if (!have_posts()) {
    get_footer();
    return;
}

the_post();
$product_id = get_the_ID();
$title = get_the_title();
$term = tk_product_primary_term($product_id);
$short_description = (string) get_post_meta($product_id, 'wpcf-mo-ta-ngan', true);
$tagline = tk_product_premium_tagline($product_id, $short_description);
$gallery = tk_product_gallery_items($product_id);
$highlights = tk_product_premium_highlights($product_id);
$specs = tk_product_premium_specs($product_id);
$catalogue = tk_product_catalogue_data($product_id);
$video = tk_product_video_data($product_id);
$rich_content = tk_product_normalize_rich_content($product_id);
$hotline = (string) tk_home_field('wpcf-so-hotline');
$phone = preg_replace('/[^0-9+]/', '', $hotline);
$zalo_phone = preg_replace('/\D+/', '', $hotline);
$zalo_url = $zalo_phone ? 'https://zalo.me/' . rawurlencode($zalo_phone) : '';
$messenger_url = 'https://m.me/108890274790969';
$related_ids = tk_product_related_ids($product_id, 4);
$breadcrumbs = tk_product_breadcrumbs($product_id);
$section_nav = array();
$dialog_style_path = get_theme_file_path('/assets/dist/product-dialogs.min.css');
$dialog_style_url = is_file($dialog_style_path)
    ? add_query_arg('ver', (string) filemtime($dialog_style_path), get_theme_file_uri('/assets/dist/product-dialogs.min.css'))
    : '';

if ($short_description) {
    $section_nav[] = array('id' => 'product-overview', 'label' => tk_home_text('Tổng quan', 'Overview'));
}
if ($highlights) {
    $section_nav[] = array('id' => 'product-highlights', 'label' => tk_home_text('Điểm nổi bật', 'Highlights'));
}
if ($specs) {
    $section_nav[] = array('id' => 'product-specifications', 'label' => tk_home_text('Thông số', 'Specifications'));
}
if ($video) {
    $section_nav[] = array('id' => 'product-video', 'label' => tk_home_text('Video', 'Video'));
}
if ($rich_content) {
    $section_nav[] = array('id' => 'product-details', 'label' => tk_home_text('Chi tiết', 'Details'));
}
$section_nav[] = array('id' => 'product-consultation', 'label' => tk_home_text('Tư vấn', 'Consultation'));
?>

<main
    id="main-content"
    class="tk-product-detail-page"
    data-product-detail
    data-product-id="<?php echo esc_attr((string) $product_id); ?>"
    data-product-slug="<?php echo esc_attr((string) get_post_field('post_name', $product_id)); ?>"
    data-product-taxonomy="<?php echo esc_attr($term instanceof WP_Term ? $term->name : ''); ?>"
    data-product-dialog-style="<?php echo esc_url($dialog_style_url); ?>"
>
    <nav class="tk-product-breadcrumb" aria-label="<?php echo esc_attr(tk_home_text('Đường dẫn trang', 'Breadcrumb')); ?>">
        <div class="tk-container">
            <ol>
                <?php foreach ($breadcrumbs as $index => $item) : ?>
                    <li>
                        <?php if ($index) : ?>
                            <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 0 1 1.06-1.06l4.24 4.24a.75.75 0 0 1 0 1.06l-4.24 4.24a.75.75 0 0 1-1.08 0Z" clip-rule="evenodd"/></svg>
                        <?php endif; ?>
                        <?php if (!empty($item['url']) && !is_wp_error($item['url'])) : ?>
                            <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['label']); ?></a>
                        <?php else : ?>
                            <span aria-current="page"><?php echo esc_html($item['label']); ?></span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </nav>

    <article>
        <section class="tk-product-hero" aria-labelledby="site-page-title" data-product-hero>
            <div class="tk-product-hero-grid tk-container">
                <div class="tk-product-hero-copy">
                    <?php if ($term instanceof WP_Term) : ?>
                        <a class="tk-product-eyebrow" href="<?php echo esc_url(get_term_link($term)); ?>">
                            <span><?php echo esc_html($term->name); ?></span>
                            <svg aria-hidden="true" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m7 4 6 6-6 6"/></svg>
                        </a>
                    <?php else : ?>
                        <span class="tk-product-eyebrow"><?php echo esc_html(tk_home_text('Giải pháp nha khoa', 'Dental solution')); ?></span>
                    <?php endif; ?>

                    <h1 id="site-page-title"><?php echo esc_html($title); ?></h1>
                    <?php if ($tagline) : ?><p class="tk-product-tagline"><?php echo esc_html($tagline); ?></p><?php endif; ?>

                    <?php if ($specs) : ?>
                        <dl class="tk-product-hero-facts">
                            <?php foreach (array_slice($specs, 0, 3) as $spec) : ?>
                                <div><dt><?php echo esc_html($spec['label']); ?></dt><dd><?php echo esc_html($spec['value']); ?></dd></div>
                            <?php endforeach; ?>
                        </dl>
                    <?php endif; ?>

                    <div class="tk-product-hero-actions">
                        <button
                            type="button"
                            class="tk-product-cta tk-product-cta-primary"
                            data-product-modal-open
                            data-cta-source="hero"
                            data-tk-event="tk_product_cta_click"
                            data-tk-action="consultation"
                            data-tk-placement="hero"
                        >
                            <span><?php echo esc_html(tk_home_text('Nhận tư vấn chuyên môn', 'Request expert consultation')); ?></span>
                            <svg aria-hidden="true" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 10h12m-4-4 4 4-4 4"/></svg>
                        </button>
                        <?php if ($phone) : ?>
                            <a
                                class="tk-product-cta tk-product-cta-secondary"
                                href="tel:<?php echo esc_attr($phone); ?>"
                                rel="nofollow"
                                data-tk-event="tk_product_cta_click"
                                data-tk-action="hotline"
                                data-tk-placement="hero"
                            >
                                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.69 2.8a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.33 1.85.56 2.81.69A2 2 0 0 1 22 16.92Z"/></svg>
                                <span><?php echo esc_html(tk_home_text('Gọi chuyên gia', 'Call an expert')); ?></span>
                            </a>
                        <?php endif; ?>
                    </div>

                    <?php if ($catalogue) : ?>
                        <a
                            class="tk-product-catalogue-link"
                            href="<?php echo esc_url($catalogue['url']); ?>"
                            target="_blank"
                            rel="noopener"
                            data-tk-event="tk_product_catalogue_click"
                            data-tk-action="catalogue"
                            data-tk-placement="hero"
                        >
                            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6M12 18v-6m-3 3 3 3 3-3"/></svg>
                            <span><?php echo esc_html(tk_home_text('Xem catalogue / tài liệu kỹ thuật', 'View catalogue / technical document')); ?></span>
                        </a>
                    <?php endif; ?>

                    <p class="tk-product-advisory-note"><?php echo esc_html(tk_home_text('Đội ngũ Tuấn Khang hỗ trợ lựa chọn giải pháp phù hợp với nhu cầu lâm sàng và vận hành của phòng khám.', 'Tuan Khang supports product selection for your clinical and practice requirements.')); ?></p>
                </div>

                <div class="tk-product-gallery" data-product-gallery>
                    <?php if ($gallery) : ?>
                        <button
                            type="button"
                            class="tk-product-gallery-main"
                            data-product-gallery-zoom
                            aria-label="<?php echo esc_attr(sprintf(tk_home_text('Phóng to ảnh %s', 'Enlarge image of %s'), $title)); ?>"
                        >
                            <span class="tk-product-gallery-stage" data-product-gallery-stage>
                                <?php foreach ($gallery as $index => $image) : ?>
                                    <span class="tk-product-gallery-slide" data-product-gallery-slide="<?php echo esc_attr((string) $index); ?>"<?php echo $index ? ' hidden aria-hidden="true"' : ' aria-hidden="false"'; ?>>
                                        <?php
                                        echo tk_picture(
                                            $image,
                                            'product-gallery',
                                            array(
                                                'alt' => $image['alt'] ?: $title,
                                                'loading' => $index === 0 ? 'eager' : 'lazy',
                                                'fetchpriority' => $index === 0 ? 'high' : 'auto',
                                                'class' => 'tk-product-gallery-image',
                                            )
                                        );
                                        ?>
                                    </span>
                                <?php endforeach; ?>
                            </span>
                            <span class="tk-product-gallery-zoom-label">
                                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4m-5-8v6m-3-3h6"/></svg>
                                <?php echo esc_html(tk_home_text('Xem ảnh lớn', 'View larger')); ?>
                            </span>
                        </button>

                        <?php if (count($gallery) > 1) : ?>
                            <div class="tk-product-gallery-thumbs" role="group" aria-label="<?php echo esc_attr(tk_home_text('Chọn ảnh sản phẩm', 'Choose product image')); ?>">
                                <?php foreach ($gallery as $index => $image) : ?>
                                    <button
                                        type="button"
                                        class="tk-product-gallery-thumb"
                                        data-product-gallery-thumb="<?php echo esc_attr((string) $index); ?>"
                                        aria-pressed="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                                        aria-label="<?php echo esc_attr(sprintf(tk_home_text('Xem ảnh %d', 'View image %d'), $index + 1)); ?>"
                                    >
                                        <?php echo tk_picture($image, 'product-thumb', array('alt' => '', 'class' => 'tk-product-gallery-thumb-image')); ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php else : ?>
                        <div class="tk-product-gallery-empty">
                            <svg aria-hidden="true" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 12h36a4 4 0 0 1 4 4v32a4 4 0 0 1-4 4H14a4 4 0 0 1-4-4V16a4 4 0 0 1 4-4Z"/><circle cx="24" cy="25" r="5"/><path d="m12 44 12-11 9 8 7-6 12 11"/></svg>
                            <span><?php echo esc_html(tk_home_text('Hình ảnh đang được cập nhật', 'Image coming soon')); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="tk-product-proof" aria-label="<?php echo esc_attr(tk_home_text('Cam kết dịch vụ Tuấn Khang', 'Tuan Khang service commitments')); ?>">
            <div class="tk-container">
                <?php
                $proof_items = array(
                    array('title' => tk_home_text('Sản phẩm chính hãng', 'Authentic products'), 'description' => tk_home_text('Nguồn gốc minh bạch', 'Transparent sourcing'), 'icon' => 'shield'),
                    array('title' => tk_home_text('Tư vấn kỹ thuật', 'Technical advice'), 'description' => tk_home_text('Đồng hành cùng bác sĩ', 'Supporting clinicians'), 'icon' => 'clinical'),
                    array('title' => tk_home_text('Hỗ trợ 24/7', '24/7 support'), 'description' => tk_home_text('Phản hồi khi cần thiết', 'Responsive assistance'), 'icon' => 'support'),
                    array('title' => tk_home_text('Phân phối toàn quốc', 'Nationwide delivery'), 'description' => tk_home_text('Hà Nội & TP. Hồ Chí Minh', 'Hanoi & Ho Chi Minh City'), 'icon' => 'delivery'),
                );
                foreach ($proof_items as $proof) : ?>
                    <div class="tk-product-proof-item">
                        <?php if ($proof['icon'] === 'shield') : ?>
                            <svg class="tk-product-proof-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 3 5 6v5c0 5 3.1 8.3 7 10 3.9-1.7 7-5 7-10V6Z"/><path d="m9 12 2 2 4-5"/></svg>
                        <?php elseif ($proof['icon'] === 'clinical') : ?>
                            <svg class="tk-product-proof-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M8 3v4a4 4 0 0 0 8 0V3M5 3h3m8 0h3"/><path d="M12 11v3a5 5 0 0 0 10 0v-1"/><circle cx="21" cy="10" r="2"/></svg>
                        <?php elseif ($proof['icon'] === 'support') : ?>
                            <svg class="tk-product-proof-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 13a8 8 0 0 1 16 0v6a2 2 0 0 1-2 2h-3"/><path d="M4 13h3v6H4a2 2 0 0 1-2-2v-2a2 2 0 0 1 2-2Zm16 0h-3v6h3a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2Z"/></svg>
                        <?php else : ?>
                            <svg class="tk-product-proof-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M3 6h11v11H3Z"/><path d="M14 10h4l3 3v4h-7Z"/><circle cx="7" cy="19" r="2"/><circle cx="18" cy="19" r="2"/></svg>
                        <?php endif; ?>
                        <strong><?php echo esc_html($proof['title']); ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <nav class="tk-product-section-nav" data-product-section-nav aria-label="<?php echo esc_attr(tk_home_text('Điều hướng nội dung sản phẩm', 'Product content navigation')); ?>">
            <div class="tk-container">
                <?php foreach ($section_nav as $index => $nav_item) : ?>
                    <a href="#<?php echo esc_attr($nav_item['id']); ?>" data-product-section-link="<?php echo esc_attr($nav_item['id']); ?>"<?php echo $index === 0 ? ' class="is-active" aria-current="location"' : ''; ?>>
                        <?php echo esc_html($nav_item['label']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </nav>

        <div class="tk-product-body">
            <?php if ($short_description) : ?>
                <section id="product-overview" class="tk-product-section tk-product-overview" data-product-section data-reveal aria-labelledby="product-overview-title">
                    <div class="tk-container tk-product-section-grid">
                        <div class="tk-product-section-heading tk-product-section-heading-horizontal">
                            <span>01</span>
                            <div><p><?php echo esc_html(tk_home_text('Tổng quan', 'Overview')); ?></p><h2 id="product-overview-title"><?php echo esc_html(tk_home_text('Giải pháp được lựa chọn cho thực hành nha khoa hiện đại', 'A solution for modern dental practice')); ?></h2></div>
                        </div>
                        <div class="tk-product-overview-copy"><?php echo wp_kses_post($short_description); ?></div>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($highlights) : ?>
                <section id="product-highlights" class="tk-product-section tk-product-highlights" data-product-section data-reveal aria-labelledby="product-highlights-title">
                    <div class="tk-container">
                        <div class="tk-product-section-heading tk-product-section-heading-horizontal">
                            <span>02</span>
                            <div><p><?php echo esc_html(tk_home_text('Giá trị nổi bật', 'Key value')); ?></p><h2 id="product-highlights-title"><?php echo esc_html(tk_home_text('Điểm khác biệt của sản phẩm', 'What sets this product apart')); ?></h2></div>
                        </div>
                        <div class="tk-product-highlight-grid">
                            <?php foreach ($highlights as $index => $highlight) : ?>
                                <article class="tk-product-highlight-card" data-reveal-item>
                                    <span class="tk-product-highlight-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 3v18M3 12h18"/><circle cx="12" cy="12" r="8"/></svg>
                                    </span>
                                    <span class="tk-product-highlight-index"><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                                    <h3><?php echo esc_html($highlight['title']); ?></h3>
                                    <?php if ($highlight['description']) : ?><p><?php echo esc_html($highlight['description']); ?></p><?php endif; ?>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($specs) : ?>
                <section id="product-specifications" class="tk-product-section tk-product-specifications" data-product-section data-reveal aria-labelledby="product-specifications-title">
                    <div class="tk-container">
                        <div class="tk-product-spec-console">
                            <header class="tk-product-spec-intro">
                                <div class="tk-product-spec-kicker">
                                    <span aria-hidden="true">03</span>
                                    <p><?php echo esc_html(tk_home_text('Dữ liệu sản phẩm', 'Product data')); ?></p>
                                </div>
                                <h2 id="product-specifications-title"><?php echo esc_html(tk_home_text('Thông số & thông tin kỹ thuật', 'Specifications & technical information')); ?></h2>
                                <p class="tk-product-spec-description"><?php echo esc_html(tk_home_text('Thông tin nhận diện và nguồn gốc giúp bác sĩ lựa chọn giải pháp phù hợp với nhu cầu điều trị.', 'Identification and origin information helps clinicians select a solution suited to their treatment needs.')); ?></p>
                                <svg class="tk-product-spec-line" aria-hidden="true" viewBox="0 0 280 72" fill="none">
                                    <path d="M1 58h61l18-30 27 42 31-54 22 42h119" stroke="currentColor" stroke-width="1.5"/>
                                    <circle cx="80" cy="28" r="4" fill="currentColor"/>
                                    <circle cx="138" cy="16" r="4" fill="currentColor"/>
                                    <circle cx="160" cy="58" r="4" fill="currentColor"/>
                                </svg>
                            </header>

                            <div class="tk-product-spec-data">
                                <dl class="tk-product-spec-list">
                                    <?php foreach ($specs as $spec) : ?>
                                        <div class="tk-product-spec-card">
                                            <dt>
                                                <span class="tk-product-spec-icon" aria-hidden="true">
                                                    <?php if (($spec['type'] ?? '') === 'manufacturer') : ?>
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 21V9l6 3V8l6 4V4h6v17Z"/><path d="M7 21v-4h3v4m4 0v-4h3v4"/></svg>
                                                    <?php elseif (($spec['type'] ?? '') === 'origin') : ?>
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.3 2.5 3.5 5.5 3.5 9S14.3 18.5 12 21c-2.3-2.5-3.5-5.5-3.5-9S9.7 5.5 12 3Z"/></svg>
                                                    <?php elseif (($spec['type'] ?? '') === 'model') : ?>
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 5h10l6 6-9 9-7-7Z"/><circle cx="9" cy="10" r="1.5"/></svg>
                                                    <?php else : ?>
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M5 4h14v16H5Z"/><path d="M8 8h8M8 12h8M8 16h5"/></svg>
                                                    <?php endif; ?>
                                                </span>
                                                <span><?php echo esc_html($spec['label']); ?></span>
                                            </dt>
                                            <dd><?php echo esc_html($spec['value']); ?></dd>
                                        </div>
                                    <?php endforeach; ?>
                                </dl>

                                <div class="tk-product-spec-footer">
                                    <p><?php echo esc_html(tk_home_text('Cần thêm tài liệu để đánh giá sản phẩm?', 'Need more documentation to evaluate this product?')); ?></p>
                                    <?php if ($catalogue) : ?>
                                        <a
                                            class="tk-product-spec-action"
                                            href="<?php echo esc_url($catalogue['url']); ?>"
                                            target="_blank"
                                            rel="noopener"
                                            data-tk-event="tk_product_catalogue_click"
                                            data-tk-action="catalogue"
                                            data-tk-placement="specifications"
                                        >
                                            <span><?php echo esc_html(tk_home_text('Xem catalogue kỹ thuật', 'View technical catalogue')); ?></span>
                                            <svg aria-hidden="true" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 10h12m-4-4 4 4-4 4"/></svg>
                                        </a>
                                    <?php else : ?>
                                        <button
                                            type="button"
                                            class="tk-product-spec-action"
                                            data-product-modal-open
                                            data-cta-source="specifications"
                                            data-tk-event="tk_product_cta_click"
                                            data-tk-action="consultation"
                                            data-tk-placement="specifications"
                                        >
                                            <span><?php echo esc_html(tk_home_text('Yêu cầu thông tin kỹ thuật', 'Request technical information')); ?></span>
                                            <svg aria-hidden="true" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 10h12m-4-4 4 4-4 4"/></svg>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($video) : ?>
                <section id="product-video" class="tk-product-section tk-product-video" data-product-section data-reveal aria-labelledby="product-video-title">
                    <div class="tk-container">
                        <div class="tk-product-section-heading tk-product-section-heading-horizontal">
                            <span>04</span>
                            <div><p><?php echo esc_html(tk_home_text('Video', 'Demonstration')); ?></p></div>
                        </div>
                        <div class="tk-product-video-frame" data-product-video>
                            <button
                                type="button"
                                data-product-video-play
                                data-video-src="<?php echo esc_url($video['embed_url']); ?>"
                                data-video-provider="<?php echo esc_attr($video['provider']); ?>"
                                aria-label="<?php echo esc_attr(tk_home_text('Phát video giới thiệu sản phẩm', 'Play product introduction')); ?>"
                            >
                                <?php if (!empty($video['poster_url'])) : ?>
                                    <?php if (!empty($video['poster_id'])) : ?>
                                        <?php
                                        echo wp_get_attachment_image(
                                            (int) $video['poster_id'],
                                            'full',
                                            false,
                                            array(
                                                'class' => 'tk-product-video-poster',
                                                'alt' => '',
                                                'loading' => 'lazy',
                                                'decoding' => 'async',
                                                'fetchpriority' => 'low',
                                                'sizes' => '(min-width: 1200px) 1200px, calc(100vw - 32px)',
                                                'data-product-video-poster' => '',
                                                'data-video-poster-fallbacks' => '[]',
                                            )
                                        );
                                        ?>
                                    <?php else : ?>
                                        <img
                                            class="tk-product-video-poster"
                                            src="<?php echo esc_url($video['poster_url']); ?>"
                                            alt=""
                                            width="1280"
                                            height="720"
                                            loading="lazy"
                                            decoding="async"
                                            fetchpriority="low"
                                            data-product-video-poster
                                            data-video-poster-fallbacks="<?php echo esc_attr(wp_json_encode($video['poster_fallbacks'])); ?>"
                                        >
                                    <?php endif; ?>
                                <?php endif; ?>
                                <span class="tk-product-video-play-ui">
                                    <span class="tk-product-video-play-icon"><svg aria-hidden="true" viewBox="0 0 24 24" fill="currentColor"><path d="m9 7 8 5-8 5Z"/></svg></span>
                                    <span class="tk-product-video-play-caption"><?php echo esc_html(tk_home_text('Phát video giới thiệu sản phẩm', 'Play product introduction')); ?></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($rich_content) : ?>
                <section id="product-details" class="tk-product-section tk-product-details" data-product-section aria-labelledby="product-details-title">
                    <div class="tk-container">
                        <header class="tk-product-details-header">
                            <p><?php echo esc_html(tk_home_text('Thông tin chuyên sâu', 'In-depth information')); ?></p>
                            <h2 id="product-details-title"><?php echo esc_html(tk_home_text('Mô tả chi tiết', 'Product details')); ?></h2>
                        </header>
                        <div class="tk-product-rich-content"><?php echo $rich_content; ?></div>
                    </div>
                </section>
            <?php endif; ?>

            <section id="product-consultation" class="tk-product-consultation" data-product-section data-reveal aria-labelledby="product-consultation-title">
                <div class="tk-container">
                    <div class="tk-product-consultation-copy">
                        <p><?php echo esc_html(tk_home_text('Đồng hành chuyên môn', 'Clinical partnership')); ?></p>
                        <h2 id="product-consultation-title"><?php echo esc_html(tk_home_text('Cần một giải pháp phù hợp cho phòng khám của bạn?', 'Need the right solution for your practice?')); ?></h2>
                        <span class="tk-product-consultation-description">
                            <span><?php echo esc_html(tk_home_text('Chia sẻ nhu cầu của bạn. Đội ngũ Tuấn Khang sẽ tư vấn sản phẩm, tài liệu', 'Tell us what you need. Tuan Khang will recommend suitable products and documentation')); ?></span>
                            <span><?php echo esc_html(tk_home_text('và phương án hỗ trợ phù hợp.', 'with the right support.')); ?></span>
                        </span>
                    </div>
                    <div class="tk-product-consultation-actions">
                        <div class="tk-product-consultation-primary">
                            <button
                                type="button"
                                class="tk-product-consultation-form-trigger"
                                data-product-modal-open
                                data-cta-source="final"
                                data-tk-event="tk_product_cta_click"
                                data-tk-action="consultation"
                                data-tk-placement="final"
                            >
                                <span class="tk-product-consultation-form-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6.75 4.25h10.5A1.75 1.75 0 0 1 19 6v13.25H8.5A2.5 2.5 0 0 1 6 16.75V5a.75.75 0 0 1 .75-.75Z"/><path d="M9.25 4.25v15M12.25 8h3.75M12.25 11h3.75"/></svg>
                                </span>
                                <span><?php echo esc_html(tk_home_text('Trao đổi với chuyên gia', 'Speak with an expert')); ?></span>
                            </button>
                            <span class="tk-product-consultation-primary-divider" aria-hidden="true"></span>
                            <div class="tk-product-consultation-socials" role="group" aria-label="<?php echo esc_attr(tk_home_text('Kênh tư vấn trực tuyến', 'Online consultation channels')); ?>">
                                <?php if ($zalo_url) : ?>
                                    <a
                                        class="tk-product-consultation-social tk-product-consultation-social-zalo"
                                        href="<?php echo esc_url($zalo_url); ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        aria-label="<?php echo esc_attr(tk_home_text('Chat với Tuấn Khang qua Zalo, mở trong tab mới', 'Chat with Tuan Khang on Zalo, opens in a new tab')); ?>"
                                        data-tooltip="<?php echo esc_attr(tk_home_text('Chat qua Zalo', 'Chat on Zalo')); ?>"
                                        data-tk-event="tk_product_cta_click"
                                        data-tk-action="zalo_chat"
                                        data-tk-placement="final"
                                    >
                                        <svg viewBox="0 0 48 48" aria-hidden="true" focusable="false"><path d="M11 8.5h26a7 7 0 0 1 7 7v14a7 7 0 0 1-7 7H24l-7.5 4v-4H11a7 7 0 0 1-7-7v-14a7 7 0 0 1 7-7Z" fill="none" stroke="currentColor" stroke-width="2.5"/><text x="24" y="27.5" fill="currentColor" font-family="Arial, sans-serif" font-size="12" font-weight="700" text-anchor="middle">Zalo</text></svg>
                                    </a>
                                <?php endif; ?>
                                <a
                                    class="tk-product-consultation-social tk-product-consultation-social-messenger"
                                    href="<?php echo esc_url($messenger_url); ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    aria-label="<?php echo esc_attr(tk_home_text('Chat với Tuấn Khang qua Facebook Messenger, mở trong tab mới', 'Chat with Tuan Khang on Facebook Messenger, opens in a new tab')); ?>"
                                    data-tooltip="<?php echo esc_attr(tk_home_text('Chat Facebook', 'Chat on Facebook')); ?>"
                                    data-tk-event="tk_product_cta_click"
                                    data-tk-action="facebook_chat"
                                    data-tk-placement="final"
                                >
                                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M12 2C6.48 2 2 6.15 2 11.27c0 2.91 1.45 5.5 3.72 7.2V22l3.4-1.87c.91.25 1.88.39 2.88.39 5.52 0 10-4.15 10-9.25S17.52 2 12 2Zm.99 12.48-2.55-2.72-4.98 2.72 5.48-5.82 2.62 2.72 4.91-2.72-5.48 5.82Z"/></svg>
                                </a>
                            </div>
                        </div>
                        <?php if ($hotline) : ?>
                            <a class="tk-product-hotline" href="tel:<?php echo esc_attr($phone); ?>" data-tk-event="tk_product_cta_click" data-tk-action="hotline" data-tk-placement="final">
                                <span class="tk-product-hotline-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M7.2 3.5 4.8 4.7c-.8.4-1.2 1.3-1 2.1 1.5 6.6 6.8 11.9 13.4 13.4.9.2 1.8-.2 2.2-1l1.1-2.4-4.3-2-1.1 1.8c-.3.5-.9.7-1.4.4-2.9-1.4-5.3-3.8-6.7-6.7-.3-.5-.1-1.1.4-1.4l1.8-1.1Z"/></svg>
                                </span>
                                <span class="tk-product-hotline-copy">
                                    <small><?php echo esc_html(tk_home_text('Hotline hỗ trợ', 'Support hotline')); ?></small>
                                    <strong><?php echo esc_html($hotline); ?></strong>
                                </span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>
    </article>

    <?php if ($related_ids) : ?>
        <section class="tk-product-related" aria-labelledby="related-products-title">
            <div class="tk-container">
                <header>
                    <p><?php echo esc_html(tk_home_text('Tiếp tục khám phá', 'Continue exploring')); ?></p>
                    <h2 id="related-products-title"><?php echo esc_html(tk_home_text('Sản phẩm liên quan', 'Related products')); ?></h2>
                </header>
                <div class="tk-product-related-grid">
                    <?php foreach ($related_ids as $related_id) tk_product_card($related_id, false, 'h3'); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
</main>

<?php if ($gallery) : ?>
    <dialog class="tk-product-image-dialog" data-product-image-dialog aria-labelledby="product-image-dialog-title">
        <div class="tk-product-image-dialog-inner">
            <div class="tk-product-dialog-header">
                <h2 id="product-image-dialog-title"><?php echo esc_html($title); ?></h2>
                <button type="button" data-product-image-dialog-close aria-label="<?php echo esc_attr(tk_home_text('Đóng ảnh lớn', 'Close enlarged image')); ?>">
                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="tk-product-image-dialog-stage" data-product-image-dialog-stage></div>
        </div>
    </dialog>
<?php endif; ?>

<dialog class="tk-product-dialog" data-product-modal aria-labelledby="product-form-title">
    <section data-product-modal-panel>
        <div class="tk-product-dialog-header">
            <div>
                <p><?php echo esc_html(tk_home_text('Tư vấn sản phẩm', 'Product consultation')); ?></p>
                <h2 id="product-form-title"><?php echo esc_html(tk_home_text('Trao đổi với chuyên gia Tuấn Khang', 'Speak with a Tuan Khang expert')); ?></h2>
            </div>
            <button type="button" data-product-modal-close aria-label="<?php echo esc_attr(tk_home_text('Đóng biểu mẫu', 'Close form')); ?>">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <p class="tk-product-dialog-intro"><?php echo esc_html(tk_home_text('Để lại thông tin, đội ngũ của chúng tôi sẽ liên hệ và tư vấn theo nhu cầu thực tế của bạn.', 'Leave your details and our team will contact you with advice tailored to your needs.')); ?></p>
        <div class="tk-product-form" data-product-form-host></div>
        <template data-product-form-template>
            <?php echo shortcode_exists('contact-form-7') ? do_shortcode('[contact-form-7 id="14"]') : '<p>' . esc_html(tk_home_text('Biểu mẫu hiện chưa khả dụng.', 'The form is currently unavailable.')) . '</p>'; ?>
        </template>
    </section>
</dialog>

<div
    class="tk-product-mobile-bar"
    aria-label="<?php echo esc_attr(tk_home_text('Liên hệ nhanh', 'Quick contact')); ?>"
    data-product-mobile-bar
>
    <button
        type="button"
        data-product-modal-open
        data-cta-source="mobile-sticky"
        data-tk-event="tk_product_cta_click"
        data-tk-action="consultation"
        data-tk-placement="mobile-sticky"
    >
        <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/><path d="m22 6-10 7L2 6"/></svg>
        <span><?php echo esc_html(tk_home_text('Tư vấn', 'Consult')); ?></span>
    </button>
    <?php if ($phone) : ?>
        <a href="tel:<?php echo esc_attr($phone); ?>" rel="nofollow" data-tk-event="tk_product_cta_click" data-tk-action="hotline" data-tk-placement="mobile-sticky">
            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.69 2.8a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.33 1.85.56 2.81.69A2 2 0 0 1 22 16.92Z"/></svg>
            <span><?php echo esc_html(tk_home_text('Hotline', 'Hotline')); ?></span>
        </a>
    <?php endif; ?>
</div>

<?php get_footer(); ?>

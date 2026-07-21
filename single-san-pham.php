<?php
get_header();

if (!have_posts()) {
    get_footer();
    return;
}

the_post();
$product_id = get_the_ID();
$title = get_the_title();
$thumbnail_id = get_post_thumbnail_id($product_id);
$model = get_field('wpcf-model', $product_id);
$manufacturer = get_field('wpcf-hang-sx', $product_id);
$origin = get_field('wpcf-xuat-xu', $product_id);
$short_description = get_field('wpcf-mo-ta-ngan', $product_id);
$hotline = (string) tk_home_field('wpcf-so-hotline');
$phone = preg_replace('/[^0-9+]/', '', $hotline);
$related_ids = tk_product_related_ids($product_id, 4);
?>
<main id="main-content">
    <?php tk_product_banner($title, tk_product_breadcrumbs($product_id)); ?>

    <article class="py-10 md:py-12">
        <div class="tk-container">
            <div class="grid gap-8 lg:grid-cols-[minmax(0,2fr)_minmax(0,3fr)] lg:items-start">
                <div class="flex aspect-square items-center justify-center overflow-hidden border border-slate-200 bg-white p-3">
                    <?php if ($thumbnail_id) : ?>
                        <?php echo tk_picture($thumbnail_id, 'product-detail', array('alt' => $title, 'loading' => 'eager', 'fetchpriority' => 'high', 'class' => 'h-full w-full object-contain')); ?>
                    <?php else : ?>
                        <span class="text-slate-400"><?php echo esc_html(tk_home_text('Chưa có ảnh', 'No image')); ?></span>
                    <?php endif; ?>
                </div>

                <div class="min-w-0">
                    <h2 class="text-2xl font-bold leading-tight text-slate-800 md:text-[28px]"><?php echo esc_html($title); ?></h2>
                    <span class="mt-3 block h-1 w-14 rounded-full bg-primary" aria-hidden="true"></span>

                    <?php if ($model || $manufacturer || $origin || $short_description) : ?>
                        <div class="mt-6 space-y-2 text-base leading-7">
                            <?php if ($model) : ?><p><?php echo esc_html(tk_home_text('Model', 'Model')); ?>: <strong class="text-slate-900"><?php echo esc_html($model); ?></strong></p><?php endif; ?>
                            <?php if ($manufacturer) : ?><p><?php echo esc_html(tk_home_text('Hãng', 'Brand')); ?>: <strong class="text-slate-900"><?php echo esc_html($manufacturer); ?></strong></p><?php endif; ?>
                            <?php if ($origin) : ?><p><?php echo esc_html(tk_home_text('Xuất xứ', 'Origin')); ?>: <strong class="text-slate-900"><?php echo esc_html($origin); ?></strong></p><?php endif; ?>
                            <?php if ($short_description) : ?><div class="tk-product-content pt-2"><?php echo wp_kses_post($short_description); ?></div><?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <section class="mt-7 overflow-hidden rounded-xl border-2 border-primary" aria-labelledby="product-benefits-title">
                        <h3 id="product-benefits-title" class="bg-primary px-5 py-3 text-base font-bold uppercase text-white"><?php echo esc_html(tk_home_text('Lợi ích khi mua hàng tại Tuấn Khang', 'Benefits of buying from Tuan Khang')); ?></h3>
                        <ul class="space-y-3 px-5 py-5 text-base">
                            <?php
                            $benefits = array(
                                tk_home_text('Nhà cung cấp uy tín', 'Trusted supplier'),
                                tk_home_text('Sản phẩm chất lượng, giá cả hợp lý', 'Quality products at reasonable prices'),
                                tk_home_text('Bảo hành 12 tháng, dịch vụ chuyên nghiệp', '12-month warranty and professional service'),
                                tk_home_text('Hàng có sẵn, có thể giao ngay', 'In-stock products available for immediate delivery'),
                                tk_home_text('Ưu đãi khi mua hàng với số lượng lớn', 'Volume purchase incentives'),
                            );
                            foreach ($benefits as $benefit) : ?>
                                <li class="flex gap-3"><svg class="mt-0.5 size-5 shrink-0 text-accent" aria-hidden="true" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.414l-7.5 7.5a1 1 0 01-1.414 0l-3.5-3.5A1 1 0 015.704 9.29l2.793 2.793 6.793-6.793a1 1 0 011.414 0z" clip-rule="evenodd"/></svg><span><?php echo esc_html($benefit); ?></span></li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                </div>
            </div>

            <section class="mt-12" aria-labelledby="product-description-title">
                <h2 id="product-description-title" class="border-b-2 border-primary pb-3 text-xl font-bold uppercase text-primary md:text-2xl"><?php echo esc_html(tk_home_text('Mô tả chi tiết', 'Product details')); ?></h2>
                <div class="tk-product-content mt-6 text-base leading-7"><?php the_content(); ?></div>
            </section>
        </div>
    </article>

    <?php if ($related_ids) : ?>
        <section class="bg-slate-50 py-10 md:py-12" aria-labelledby="related-products-title">
            <div class="tk-container">
                <h2 id="related-products-title" class="text-center text-2xl font-bold uppercase text-primary md:text-[28px]"><?php echo esc_html(tk_home_text('Sản phẩm liên quan', 'Related products')); ?></h2>
                <span class="mx-auto mt-3 block h-1 w-14 rounded-full bg-accent" aria-hidden="true"></span>
                <div class="mt-7 grid grid-cols-1 gap-x-4 gap-y-8 min-[480px]:grid-cols-2 lg:grid-cols-4">
                    <?php foreach ($related_ids as $related_id) tk_product_card($related_id); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
</main>

<div data-product-modal class="tk-product-modal fixed inset-0 z-[80] flex items-center justify-center overflow-y-auto bg-slate-950/60 p-4" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="product-form-title" hidden>
    <section data-product-modal-panel class="relative my-auto w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl md:p-8">
        <button type="button" data-product-modal-close class="absolute right-3 top-3 flex size-11 items-center justify-center rounded-lg text-primary" aria-label="<?php echo esc_attr(tk_home_text('Đóng biểu mẫu', 'Close form')); ?>">
            <svg class="size-7" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
        <h2 id="product-form-title" class="pr-10 text-2xl font-bold uppercase text-primary"><?php echo esc_html(tk_home_text('Đăng ký tư vấn', 'Request consultation')); ?></h2>
        <div class="tk-product-form mt-6">
            <?php echo shortcode_exists('contact-form-7') ? do_shortcode('[contact-form-7 id="14"]') : '<p>' . esc_html(tk_home_text('Biểu mẫu hiện chưa khả dụng.', 'The form is currently unavailable.')) . '</p>'; ?>
        </div>
    </section>
</div>

<div class="fixed inset-x-0 bottom-0 z-40 grid h-14 grid-cols-2 border-t border-white/20 text-white shadow-2xl lg:hidden">
    <button type="button" data-product-modal-open class="flex min-h-11 items-center justify-center gap-2 bg-primary px-3 font-bold uppercase">
        <svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="m22 6-10 7L2 6"/></svg>
        <?php echo esc_html(tk_home_text('Đăng ký', 'Register')); ?>
    </button>
    <a class="flex min-h-11 items-center justify-center gap-2 bg-accent px-3 font-bold uppercase" href="tel:<?php echo esc_attr($phone); ?>" rel="nofollow">
        <svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6A19.79 19.79 0 012.12 4.18 2 2 0 014.11 2h3a2 2 0 012 1.72c.13.96.36 1.9.69 2.8a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.91.33 1.85.56 2.81.69A2 2 0 0122 16.92z"/></svg>
        <?php echo esc_html(tk_home_text('Hotline', 'Hotline')); ?>
    </a>
</div>

<?php get_footer(); ?>

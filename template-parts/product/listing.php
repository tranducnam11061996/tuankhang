<?php
if (!defined('ABSPATH')) {
    exit;
}

$title = tk_product_listing_title();
get_header();
?>
<main id="main-content">
    <?php tk_product_banner($title, tk_product_breadcrumbs()); ?>

    <section class="py-10 md:py-12">
        <div class="tk-container">
            <button type="button" data-product-filter-open aria-controls="product-filter-drawer" aria-expanded="false" class="mb-6 inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 font-bold uppercase text-white lg:hidden">
                <svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
                <?php echo esc_html(tk_home_text('Danh mục sản phẩm', 'Product categories')); ?>
            </button>

            <div class="grid gap-8 lg:grid-cols-[270px_minmax(0,1fr)]">
                <aside class="hidden lg:block" aria-label="<?php echo esc_attr(tk_home_text('Bộ lọc sản phẩm', 'Product navigation')); ?>">
                    <?php tk_product_sidebar('desktop'); ?>
                </aside>

                <div class="min-w-0">
                    <div class="border-b border-slate-200 pb-3">
                        <h2 class="text-2xl font-bold uppercase leading-tight text-slate-800 md:text-[28px]"><?php echo esc_html($title); ?></h2>
                        <span class="mt-3 block h-1 w-14 rounded-full bg-primary" aria-hidden="true"></span>
                    </div>

                    <?php if (have_posts()) : ?>
                        <div class="mt-6 grid grid-cols-1 gap-x-4 gap-y-8 min-[480px]:grid-cols-2 lg:grid-cols-4">
                            <?php while (have_posts()) : the_post(); ?>
                                <?php tk_product_card(get_the_ID()); ?>
                            <?php endwhile; ?>
                        </div>
                        <?php tk_product_pagination(); ?>
                    <?php else : ?>
                        <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 px-5 py-10 text-center">
                            <h3 class="text-lg font-bold text-primary"><?php echo esc_html(tk_home_text('Không tìm thấy sản phẩm', 'No products found')); ?></h3>
                            <p class="mt-2 text-slate-600"><?php echo esc_html(tk_home_text('Vui lòng thử danh mục hoặc từ khóa khác.', 'Please try another category or keyword.')); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<div data-product-filter-overlay class="tk-product-filter-overlay fixed inset-0 z-50 bg-slate-950/55 lg:hidden"></div>
<aside id="product-filter-drawer" data-product-filter-drawer aria-hidden="true" inert class="tk-product-filter-drawer fixed inset-y-0 left-0 z-[60] w-[min(90vw,380px)] overflow-y-auto bg-white p-5 shadow-2xl lg:hidden" aria-label="<?php echo esc_attr(tk_home_text('Danh mục sản phẩm', 'Product categories')); ?>">
    <div class="mb-5 flex items-center justify-between border-b border-slate-200 pb-4">
        <h2 class="text-lg font-bold uppercase text-primary"><?php echo esc_html(tk_home_text('Sản phẩm', 'Products')); ?></h2>
        <button type="button" data-product-filter-close class="flex size-11 items-center justify-center rounded-lg text-primary" aria-label="<?php echo esc_attr(tk_home_text('Đóng danh mục', 'Close categories')); ?>">
            <svg class="size-7" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
    </div>
    <?php tk_product_sidebar('mobile'); ?>
</aside>

<?php get_footer(); ?>

<?php
if (function_exists('tk_is_product_search') && tk_is_product_search()) {
    get_template_part('template-parts/product/listing');
    return;
}
get_header();
$title = tk_content_title();
tk_site_banner($title, tk_content_breadcrumbs());
tk_content_sidebar_drawer();
?>
<main id="main-content" class="py-10 md:py-14">
    <div class="tk-container">
        <?php tk_content_sidebar_button(); ?>
        <div class="grid gap-8 lg:grid-cols-[280px_minmax(0,1fr)] lg:gap-10">
            <aside class="hidden lg:block" aria-label="<?php echo esc_attr(tk_site_text('Chuyên mục tin tức', 'News categories')); ?>"><?php tk_content_sidebar(); ?></aside>
            <section class="min-w-0" aria-labelledby="search-heading">
                <h2 id="search-heading" class="mb-7 border-b border-slate-200 pb-4 text-2xl font-bold uppercase leading-tight text-primary md:text-[28px]"><?php echo esc_html($title); ?></h2>
                <?php if (have_posts()) : $card_index = 0; while (have_posts()) : the_post(); tk_content_post_card(get_the_ID(), false, $card_index++ === 0); endwhile; tk_content_pagination(); else : ?>
                    <div class="rounded-xl bg-slate-50 p-8 text-center"><p><?php echo esc_html(tk_site_text('Không tìm thấy kết quả phù hợp.', 'No matching results were found.')); ?></p><div class="mx-auto mt-5 max-w-xl"><?php get_search_form(); ?></div></div>
                <?php endif; ?>
            </section>
        </div>
    </div>
</main>
<?php get_footer(); ?>

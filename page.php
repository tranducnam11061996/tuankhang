<?php
get_header();
the_post();
$page_id = get_the_ID();
$title = get_the_title();
tk_site_banner($title, tk_content_breadcrumbs($page_id));
tk_content_sidebar_drawer();
?>
<main id="main-content" class="py-10 md:py-14">
    <div class="tk-container">
        <?php tk_content_sidebar_button(); ?>
        <div class="grid gap-8 lg:grid-cols-[280px_minmax(0,1fr)] lg:gap-10">
            <aside class="hidden lg:block" aria-label="<?php echo esc_attr(tk_site_text('Điều hướng nội dung', 'Content navigation')); ?>"><?php tk_content_sidebar(); ?></aside>
            <article class="min-w-0">
                <h2 class="mb-7 border-b border-slate-200 pb-4 text-2xl font-bold uppercase leading-tight text-primary md:text-[28px]"><?php echo esc_html($title); ?></h2>
                <div class="tk-content">
                    <?php if (is_page(63)) :
                        $story = function_exists('get_field') ? get_field('wpcf-cau-chuyen-ve-tuan-khang', 61) : get_post_meta(61, 'wpcf-cau-chuyen-ve-tuan-khang', true);
                        if ($story) echo apply_filters('the_content', $story);
                    endif; ?>
                    <?php the_content(); ?>
                </div>
            </article>
        </div>
    </div>
</main>
<?php get_footer(); ?>

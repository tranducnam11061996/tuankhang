<?php
get_header();
the_post();
$post_id = get_the_ID();
$title = get_the_title();
tk_site_banner($title, tk_content_breadcrumbs($post_id));
tk_content_sidebar_drawer();
?>
<main id="main-content" class="tk-content-context py-10 md:py-14">
    <div class="tk-container">
        <?php tk_content_sidebar_button(); ?>
        <div class="grid gap-8 lg:grid-cols-[280px_minmax(0,1fr)] lg:gap-10">
            <aside class="hidden lg:block" aria-label="<?php echo esc_attr(tk_site_text('Chuyên mục tin tức', 'News categories')); ?>"><?php tk_content_sidebar(); ?></aside>
            <article class="min-w-0">
                <header class="mb-7 border-b border-slate-200 pb-5">
                    <h2 class="text-2xl font-bold uppercase leading-tight text-primary md:text-[28px]"><?php echo esc_html($title); ?></h2>
                    <time class="mt-3 block text-sm text-slate-500" datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date(get_option('date_format'))); ?></time>
                </header>
                <div class="tk-content"><?php the_content(); ?></div>
            </article>
        </div>
        <?php $related_ids = tk_content_related_post_ids($post_id); if ($related_ids) : ?>
            <section class="mt-14 border-t border-slate-200 pt-10" aria-labelledby="related-posts-heading">
                <h2 id="related-posts-heading" class="tk-title"><?php echo esc_html(tk_site_text('Các bài đăng khác', 'Related posts')); ?></h2>
                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4"><?php foreach ($related_ids as $related_id) tk_content_post_card($related_id, true); ?></div>
            </section>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>

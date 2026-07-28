<?php
get_header();
$title = tk_content_title();
tk_site_banner($title, tk_content_breadcrumbs());
?>
<main id="main-content" class="py-14 md:py-20">
    <div class="tk-container text-center">
        <p class="text-7xl font-bold leading-none text-primary/15 md:text-9xl" aria-hidden="true">404</p>
        <h2 class="mt-5 text-2xl font-bold text-primary md:text-3xl"><?php echo esc_html(tk_site_text('Rất tiếc, liên kết không tồn tại.', 'Sorry, this page does not exist.')); ?></h2>
        <p class="mx-auto mt-4 max-w-2xl text-base leading-7 text-slate-600"><?php echo esc_html(tk_site_text('Vui lòng thử tìm kiếm lại hoặc quay về trang chủ.', 'Please try another search or return to the homepage.')); ?></p>
        <div class="mx-auto mt-7 max-w-xl"><?php get_search_form(); ?></div>
        <a class="tk-btn mt-7" href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html(tk_site_text('Về trang chủ', 'Back to homepage')); ?></a>
    </div>
</main>
<?php get_footer(); ?>

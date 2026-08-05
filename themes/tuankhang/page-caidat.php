<?php
/*
Template Name: Cài Đặt
*/
get_header();
the_post();
tk_site_banner(get_the_title(), tk_content_breadcrumbs(get_the_ID()));
?>
<main id="main-content" class="tk-content-context py-10 md:py-14">
    <div class="tk-container">
        <div class="tk-content"><?php the_content(); ?></div>
    </div>
</main>
<?php get_footer(); ?>

<?php get_header(); ?>
    <section id="body">
        <div id="product" class="page-body">

            <section class="bl-top-header">
                <div class="uk-container uk-container-center">
                    <div class="bl-head">
                        <h1 class="heading-1"><?php esc_html_e("Sản Phẩm", "tuankhang"); ?></h1>
                    </div>
                    <div class="bl-breadcrumb">
                        <ul class="uk-list uk-clearfix">
                            <li><a href="<?php echo get_home_url(); ?>"
                                   title="<?php esc_html_e("Trang Chủ", "tuankhang"); ?>"><?php esc_html_e("Trang Chủ", "tuankhang"); ?></a>
                            </li>

                            <li class="bl-active"><?php esc_html_e("Sản Phẩm", "tuankhang"); ?></a></li>
                        </ul>
                    </div>
                </div>
            </section><!-- bl-top-header -->
            <section class="bl-main-body">
                <div class="uk-container uk-container-center">
                    <div class="uk-grid uk-grid-medium">
                        <?php get_sidebar(); ?>
                        <div class="uk-width-large-3-4 uk-width-medium-3-4 uk-width-small-1-1">
                            <div class="bl-panel-head">
                                <h2 class="heading-1"><?php esc_html_e("Sản Phẩm", "tuankhang"); ?></h2>
                                <div class="prd-body-head"></div>
                            </div>
                            <div class="main-prod">
                                <div class="uk-grid uk-grid-small mt20">
                                    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                                        <?php include(TEMPLATEPATH . '/inc/1sanpham.php'); ?>
                                    <?php endwhile;
                                        wp_reset_postdata(); ?>
                                    <?php endif; ?>
                                </div>
                            </div><!-- main-prod -->
                            <div class="main-pagination">
                                <?php if (function_exists('wp_pagenavi')) {
                                    wp_pagenavi();
                                } ?>
                            </div>
                        </div><!-- uk-width-large-3-4 uk-width-medium-3-4 uk-width-small-1-1 -->
                    </div>
                </div>
            </section><!-- bl-main-body -->
        </div>
    </section><!-- #body -->
<?php get_footer(); ?>
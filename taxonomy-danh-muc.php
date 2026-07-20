<?php get_header(); ?>

    <section id="body">

        <div id="product" class="page-body">

            <?php

            $termId = get_queried_object()->term_id;

            $parentxx = get_queried_object()->parent;

            if ($parentxx) {

                $term_xx_link = get_term_link($parentxx);

                $term_xx_name = get_term($parentxx)->name;

            }

            $tendanhmuczz = get_term($termId)->name;

            $linkdanhmuczz = get_term_link($termId);

            ?>

            <section class="bl-top-header">

                <div class="uk-container uk-container-center">

                    <div class="bl-head">

                        <h1 class="heading-1"><?php echo $tendanhmuczz; ?></h1>

                    </div>

                    <div class="bl-breadcrumb">

                        <ul class="uk-list uk-clearfix">

                            <li><a href="<?php echo get_home_url(); ?>"
                                   title="<?php esc_html_e("Trang Chủ", "tuankhang"); ?>"><?php esc_html_e("Trang Chủ", "tuankhang"); ?></a>
                            </li>

                            <?php if ($term_xx_name) { ?>

                                <li class="bl-active"><a href="<?php echo $term_xx_link; ?>"
                                                         title="<?php echo $term_xx_name; ?>"><?php echo $term_xx_name; ?></a>
                                </li>

                            <?php } ?>

                            <li class="bl-active"><a href="<?php echo $linkdanhmuczz; ?>"
                                                     title="<?php echo $tendanhmuczz; ?>"><?php echo $tendanhmuczz; ?></a>
                            </li>

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

                                <h2 class="heading-1"><?php echo $tendanhmuczz; ?></h2>

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
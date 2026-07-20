<?php get_header(); ?>


    <section id="body">


        <div id="production" class="page-body">

            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                <?php

                $terms = get_the_terms($post->ID, 'danh-muc');
                foreach ($terms as $term) {
                    $abc = $term->term_id;
                    $abc1 = $term->name;
                    $term_link = get_term_link($term);
                };

                ?>

                <section class="bl-top-header">

                    <div class="uk-container uk-container-center">

                        <div class="bl-head">

                            <h1 class="heading-1"><?php the_title(); ?></h1>

                        </div>

                        <div class="bl-breadcrumb">

                            <ul class="uk-list uk-clearfix">

                                <li><a href="<?php echo get_home_url(); ?>"
                                       title="<?php esc_html_e("Trang Chủ", "tuankhang"); ?>"><?php esc_html_e("Trang Chủ", "tuankhang"); ?></a>
                                </li>

                                <li class="bl-active"><a href="<?php echo $term_link; ?>"
                                                         title="<?php echo $abc1 ?>"><?php echo $abc1 ?></a></li>

                                <li class="bl-active"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>

                            </ul>

                        </div>

                    </div>

                </section><!-- bl-top-header -->


                <section class="bl-main-body prd-detail">

                    <div class="uk-container uk-container-center">

                        <div class="uk-grid uk-grid-medium">

                            <div class="uk-width-large-4-4 uk-width-medium-4-4 uk-width-small-1-1">

                                <section class="detail uk-clearfix">

                                    <div class="uk-grid uk-grid-small">

                                        <div class="uk-width-large-2-5 uk-width-medium-2-5 uk-width-small-1-1 paddingleft0 paddingright10">

                                            <?php the_post_thumbnail('thumbnail'); ?>

                                        </div>

                                        <div class="uk-width-large-3-5 uk-width-medium-2-5 uk-width-small-1-1 paddingright15">

                                            <div class="panel-head">

                                                <h2 class="heading-2"><?php the_title(); ?></h2>

                                            </div>

                                            <div class="mt-2" style="font-size: 15px;" id="motanganx">

                                                <?php

                                                 $hangsanxuat = get_field('wpcf-hang-sx');
                                                 $model = get_field('wpcf-model');
                                                 $xuatxu = get_field('wpcf-xuat-xu');

                                                if ($model) {

                                                    ?>

                                                    <p><?php esc_html_e("Model", "tuankhang"); ?>:
                                                        <strong><?php echo $model; ?></strong></p>

                                                    <?php

                                                };

                                                if ($hangsanxuat) {

                                                    ?>

                                                    <p><?php esc_html_e("Hãng", "tuankhang"); ?>:
                                                        <strong><?php echo $hangsanxuat; ?></strong></p>

                                                    <?php

                                                };

                                                if ($xuatxu) {

                                                    ?>

                                                    <p><?php esc_html_e("Xuất Xứ", "tuankhang"); ?>:
                                                        <strong><?php echo $xuatxu; ?></strong></p>

                                                <?php } ?>

                                                <?php if (get_field('wpcf-mo-ta-ngan')): ?>
                                                 <p><?php the_field('wpcf-mo-ta-ngan'); ?></p>
                                                 <?php endif; ?>

                                            </div>

                                            <style>
                                                #motanganx img {
                                                    margin: 15px auto;
                                                }
                                            </style>
                                            <div class="policy">

                                                <h3 class="i-title"><i
                                                            class="fa fa-shield"></i> <?php esc_html_e("Lợi ích khi mua hàng tại Tuấn Khang", "tuankhang"); ?></h3>

                                                <ul>

                                                    <li xss="removed" style="list-style: none"><i class="fa fa-user"
                                                                                                  aria-hidden="true"></i>
                                                        <?php esc_html_e("Nhà cung cấp uy tín", "tuankhang"); ?>
                                                    </li>

                                                    <li xss="removed" style="list-style: none"><i
                                                                class="fa fa-product-hunt" aria-hidden="true"></i> <?php esc_html_e("Sản phẩm chất lượng, giá cả hợp lý", "tuankhang"); ?>
                                                    </li>

                                                    <li xss="removed" style="list-style: none"><i class="fa fa-repeat"
                                                                                                  aria-hidden="true"></i>
                                                        <?php esc_html_e("Thời gian bảo hành 12 tháng, dịch vụ chuyên nghiệp", "tuankhang"); ?>
                                                    </li>

                                                    <li xss="removed" style="list-style: none"><i class="fa fa-calendar"
                                                                                                  aria-hidden="true"></i>
                                                        <?php esc_html_e("Hàng có sẵn có thể giao ngay", "tuankhang"); ?>
                                                    </li>

                                                    <li xss="removed" style="list-style: none"><i class="fa fa-gift"
                                                                                                  aria-hidden="true"></i>
                                                        <?php esc_html_e("Ưu đãi khi mua hàng với số lượng lớn", "tuankhang"); ?>
                                                    </li>

                                                </ul>

                                            </div>

                                        </div>
                                    </div>


                                    <div class="tab-content">

                                        <ul class="uk-list uk-clearfix nav-tabs ulgachchan">

                                            <li class="uk-active"><span class="active"><?php esc_html_e("MÔ TẢ CHI TIẾT", "tuankhang"); ?></span></li>

                                        </ul>

                                        <div id="tabContent" class="uk-switcher tab-content">

                                            <div aria-hidden="false" class="uk-active">

                                                <article class="article detail-content">

                                                    <?php the_content(); ?>

                                                    <style>

                                                        #tabContent img {
                                                            width: 100%;
                                                            margin: 15px auto;
                                                        }

                                                        #tabContent {

                                                            font-size: 16px;

                                                            clear: both;

                                                            margin-top: 20px

                                                        }

                                                    </style>

                                                </article>

                                            </div>

                                        </div> <!-- tâb-menu -->

                                    </div><!-- tab-content -->


                                </section><!-- detail -->


                            </div><!-- uk-width-large-3-4 uk-width-medium-3-4 uk-width-small-1-1 -->

                        </div>

                    </div>

                </section><!-- bl-main-body -->


            <?php endwhile;
                wp_reset_postdata(); ?>

            <?php endif; ?>


            <section class="h-product">
                <?php
                $terms = get_the_terms($post->ID, 'danh-muc');
                foreach ($terms as $term) {
                    $abc = $term->term_id;
                    $abc1 = $term->name;
                    $term_link = get_term_link($term);
                };
                if ($abc) {
                    $args = array(
                        'post_type' => 'san-pham',
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'danh-muc',
                                'field' => 'id',
                                'terms' => $abc
                            )
                        ),
                        'post__not_in' => array($post->ID),
                        'showposts' => 4,
                        'orderby' => 'rand'
                    );
                    $cungloai = new WP_Query($args);
                    if ($cungloai->have_posts()) { ?>

                        <div class="uk-container uk-container-center">
                            <div class="h-panel-head">
                                <h3 class="heading-2"><a href="." title=""><?php esc_html_e("Sản phẩm liên quan", "tuankhang"); ?></a></h3>
                            </div>
                            <div class="panel-body main-prod">
                                <div class="uk-grid uk-grid-small mt20">

                                    <?php while ($cungloai->have_posts()) : $cungloai->the_post(); ?>
                                        <?php include(TEMPLATEPATH . '/inc/1sanpham.php'); ?>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div><!-- panel-body -->
                    <?php } else {
                        $args1 = array(
                            'post_type' => 'san-pham',
                            'tax_query' => array(
                                array('taxonomy' => 'danh-muc',
                                    'field' => 'id',
                                    'terms' => $abc,
                                    'operator' => 'NOT IN'
                                )
                            ),
                            'post__not_in' => array($post->ID),
                            'showposts' => 4,
                            'orderby' => 'rand'
                        );
                        $camera = new WP_Query($args1);
                        if ($camera->have_posts()): ?>
                            <div class="uk-container uk-container-center">
                                <div class="h-panel-head">
                                    <h3 class="heading-2"><a href="." title=""><?php esc_html_e("Nhà", "tuankhang"); ?>Sản phẩm khác</a></h3>
                                </div>
                                <div class="panel-body main-prod">
                                    <div class="uk-grid uk-grid-small mt20">

                                        <?php while ($camera->have_posts()) : $camera->the_post(); ?>
                                            <?php include(TEMPLATEPATH . '/inc/1sanpham.php'); ?>
                                        <?php endwhile; ?>

                                    </div>
                                </div>
                            </div><!-- panel-body -->
                        <?php endif;
                        wp_reset_query(); ?>
                    <?php } ?>
                    <?php wp_reset_query(); ?>
                <?php } ?>

            </section><!-- h-product -->

        </div><!-- .page-body -->

    </section>
    <div class="form-dangky-popup js-form-dangky-popup">
        <div class="form-inner">
            <div class="form-dangky">
                <?php echo do_shortcode("[contact-form-7 id='14' title='Form Đặt hàng' html_class='uk-form form dk-form']"); ?>
            </div>
            <span class="btn-close-popup js-btn-close-popup"><i class="fa fa-close"></i></span>
        </div>
    </div>

    <div class="list-btn-bot">
        <div class="btn-tu-van text-center js-btn-tu-van">
            <i class="fa fa-envelope"></i>
            <span><?php esc_html_e("Đăng ký", "tuankhang"); ?></span>
        </div>
        <!--    <div class="btn-hotline text-center">-->
        <a class="smooth btn-hotline"
           href="tel:<?php echo esc_attr(get_field('wpcf-so-hotline', 61)); ?>"
           title="" rel="nofollow,noindex">
            <i class="fa fa-phone"></i>
            <span><?php esc_html_e("Hotline", "tuankhang"); ?></span>
        </a>
        <!--    </div>-->

    </div>

<?php get_footer(); ?>
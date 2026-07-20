<?php get_header();
//global $post;
//
//$post_ID = (isset($_REQUEST['current_id']) && !empty($_REQUEST['current_id'])) ? $_REQUEST['current_id'] : $post->ID;
$post_ID = 61;
?>

    <section id="body">
        <div id="homepage" class="page-body">
            <section class="slide-1">
                <section class="main-slideshow">
                    <div class="uk-slidenav-position"
                         data-uk-slideshow="{autoplay: true, autoplayInterval: 2500, animation: 'fade'}">
                        <ul class="uk-slideshow">
                            <?php
                            for ($sliderdem = 1; $sliderdem <= 2; $sliderdem++) {
                                $getanhslider = 'wpcf-anhbanner-' . $sliderdem;
$img = get_field($getanhslider, 61);
$anhslidertrangchu = '';

if (is_array($img)) {
    $anhslidertrangchu = '<img src="'.esc_url($img['url']).'" alt="'.esc_attr($img['alt'] ?: 'Công ty TNHH Dược và Thiết bị y tế Tuấn Khang').'" width="1360" height="475">';
} elseif (is_string($img)) {
    $anhslidertrangchu = '<img src="'.esc_url($img).'" alt="Công ty TNHH Dược và Thiết bị y tế Tuấn Khang" width="1360" height="475">';
}
                                ?>
                                <li>
                                    <a href="#" title="Công ty TNHH Dược và Thiết bị y tế Tuấn Khang"
                                       class="image img-cover"><?php echo $anhslidertrangchu; ?></a>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                        <a href="#" class="uk-slidenav uk-slidenav-contrast uk-slidenav-previous"
                           data-uk-slideshow-item="previous"></a>
                        <a href="#" class="uk-slidenav uk-slidenav-contrast uk-slidenav-next"
                           data-uk-slideshow-item="next"></a>
                    </div>
                </section>
            </section>


            <?php
            $story_title = get_field('duytv_story_title', $post_ID);
            $story_image = get_field('duytv_story_image', $post_ID);
            $story_content = get_field('duytv_story_content', $post_ID);
            $story_link = get_field('duytv_story_link', $post_ID);

            if( !empty($story_title) || !empty($story_image) || !empty($story_content) || !empty($story_link) ){ ?>
                <!--                <section class="homepage-about">-->
                <!--                    <div class="uk-grid  uk-grid-collapse uk-grid uk-grid-width-large-1-2">-->
                <!--                        <section class="aboutus">-->
                <!--                            <div class="panel-body">-->
                <!--                                <article class="intro">-->
                <!--                                    <h3 class="title">--><?php //echo esc_html($story_title); ?><!--</h3>-->
                <!--                                    <div class="description" style="font-size: 12pt;color: #ffffff;line-height: 140%;"-->
                <!--                                         id="cauchuyentuankhangx">-->
                <!--                                        --><?php //echo ent2ncr($story_content); ?>
                <!--                                    </div>-->
                <!--                                    <div class="readmore" style="padding-top: 25px;padding-bottom: 10px;">-->
                <!--                                        <a href="--><?php //echo esc_url($story_link); ?><!--" class="btn-readmore">-->
                <!--                                            Xem thêm-->
                <!--                                        </a>-->
                <!--                                    </div>-->
                <!--                                    <div class="image">-->
                <!--                                        --><?php
//                                        if ( isset($story_image) && !empty($story_image) ) {
//                                            echo '<div class="image"><img src="' . esc_url($story_image["url"]) . '" width="' . esc_attr($story_image["width"]) . '" height="' . esc_attr($story_image["height"]) . '" alt="" /></div>';
//                                        }
//                                        ?>
                <!--                                    </div>-->
                <!--                                </article>-->
                <!--                            </div>-->
                <!--                        </section>-->
                <!--                    </div>-->
                <!--                </section>-->

                <section class="system-new story">
                    <div class="uk-container uk-container-center">
                        <div class="uk-grid uk-grid-small uk-grid-small-1-1 uk-grid-width-medium-1-2">
                            <div class="box-left">
                                <div class="info">
                                    <div class="panel-head">
                                        <h2 class="heading-2"><?php echo esc_html($story_title); ?></h2>
                                    </div>
                                    <div class="panel-body"><?php echo ent2ncr($story_content); ?></div>
                                    <div class="readmore" style="padding-top: 25px;padding-bottom: 10px;">
                                        <a href="<?php echo esc_url($story_link); ?>" class="btn-readmore">
                                            Xem thêm
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="box-right">
                                <div class="thumb">
                                    <?php
                                    if ( isset($story_image) && !empty($story_image) ) {
                                        echo '<div class="image"><img src="' . esc_url($story_image["url"]) . '" width="' . esc_attr($story_image["width"]) . '" height="' . esc_attr($story_image["height"]) . '" alt="" /></div>';
                                    }
                                    ?>
                                </div>
                            </div><!-- box-right -->
                        </div>
                    </div><!-- container -->
                </section>
                <?php
            }
            ?>
            <style>
                #cauchuyentuankhangx p {
                    margin: 20px auto 0px;
                    text-align: justify;
                }
            </style>

            <?php
            //            $info_title_1 = get_field('duytv_info_title_1', $post_ID);
            //            $info_des_1 = get_field('duytv_info_des_1', $post_ID);
            ?>
            <section class="h-info">
                <div class="uk-container uk-container-center">
                    <div class="dtv-list">
                        <?php
                        for ($infodem = 1; $infodem <= 3; $infodem++) {
                            $info_title = get_field('duytv_info_title_' . $infodem, $post_ID);
                            $info_des = get_field('duytv_info_des_' . $infodem, $post_ID);

                            if (!empty($info_title) || empty($info_des)) {
                                echo '<div class="dtv-list-item"><h3>' . esc_html($info_title) . '</h3><p>' . ent2ncr($info_des) . '</p></div>';
                            }
                        }
                        ?>
                    </div>
                </div><!-- panel-body -->
            </section><!-- h-info -->

            <section class="h-tin-tuc">
                <div class="uk-container uk-container-center">
                    <?php
                    $tintuc_name = get_field('duytv_news_name', $post_ID);
                    $tintuc_link_all = get_field('duytv_news_link_all', $post_ID);

                    if(!empty($tintuc_name)) echo '<div class="h-panel-head"><h3 class="heading-2">'.esc_html($tintuc_name).'</h3></div>';
                    ?>
                    <div class="tin-tuc-list grid">
                        <?php
                        for ($tindem = 1; $tindem <= 5; $tindem++) {
                            $tintuc_image = get_field('duytv_news_image_' . $tindem, $post_ID);
                            $tintuc_title = get_field('duytv_news_title_' . $tindem, $post_ID);
                            $tintuc_link = get_field('duytv_news_link_' . $tindem, $post_ID);

                            if( !empty($tintuc_title) || !empty($tintuc_image)){
                                ?>
                                <div class="grid-item item-<?php echo esc_attr($tindem); ?>">
                                    <div class="inner">
                                        <?php
                                        if ( isset($tintuc_image) && !empty($tintuc_image) ) {
                                            echo '<div class="image"><img src="' . esc_url($tintuc_image["url"]) . '" width="' . esc_attr($tintuc_image["width"]) . '" height="' . esc_attr($tintuc_image["height"]) . '" alt="" /></div>';
                                        }

                                        if ( isset($tintuc_title) && !empty($tintuc_title) ) {
                                            echo '<h3>';
                                            if ( isset($tintuc_title) && !empty($tintuc_title) ) echo '<a href="'.esc_url($tintuc_link).'">';
                                            echo esc_html($tintuc_title);
                                            echo '</h3>';
                                            if ( isset($tintuc_title) && !empty($tintuc_title) ) echo '</a>';
                                        }

                                        if ( isset($tintuc_link) && !empty($tintuc_link) ) {
                                            echo '<div class="view-more"><a href="'.esc_url($tintuc_link).'">Xem thêm</a></div>';
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <?php
                    if(isset($tintuc_link_all) && !empty($tintuc_link_all)){
                        echo '<div class="text-center"><a title="Sản phẩm" class="btn-all text-uppercase" href="'.esc_url($tintuc_link_all).'">Xem toàn bộ tin tức</a></div>';
                    }
                    ?>
                </div><!-- panel-body -->
            </section><!-- h-info -->

            <section class="h-product">
                <div class="uk-container uk-container-center">
                    <div class="h-panel-head">
                        <h3 class="heading-2"><a href="https://tuankhangmedical.com/san-pham" title="Sản phẩm">Sản phẩm
                                nổi
                                bật</a></h3>
                        <p></p>
                    </div>

                    <div id="product-solution" data-scroll="scroll2">
                        <div class="grid">
                            <div class="grid-sizer"></div>
                            <?php
                            for ($sanphamdem = 1; $sanphamdem <= 8; $sanphamdem++) {
                                $getlinklienketsanpham = 'wpcf-link-lien-ket-san-pham-' . $sanphamdem;
                                $gettensanpham = 'wpcf-ten-hien-thi-san-pham-' . $sanphamdem;
                                  $getanhsanpham = 'wpcf-anh-san-pham-' . $sanphamdem;
                            $linklienketsanpham = get_field($getlinklienketsanpham, 61);
$tensanphamtrangchu = get_field($gettensanpham, 61);
$img = get_field($getanhsanpham, 61);

$anhsanphamtrangchu = '';

if (is_array($img) && isset($img['url'])) {
    // ACF Image Array
    $anhsanphamtrangchu = '<img src="' . esc_url($img['url']) . '" alt="' . esc_attr($tensanphamtrangchu) . '" height="240">';
} elseif (is_string($img) && !empty($img)) {
    // ACF Image URL
    $anhsanphamtrangchu = '<img src="' . esc_url($img) . '" alt="' . esc_attr($tensanphamtrangchu) . '" height="240">';
}

if ( (!empty($anhsanphamtrangchu)) || (!empty($tensanphamtrangchu)) ) {
?>
                                <div class="grid-item">
                                    <div class="inner">
                                        <div class="image">
                                            <a href="<?php echo $linklienketsanpham; ?>"><?php echo $anhsanphamtrangchu; ?></a>
                                        </div>
                                        <h3><a title="<?php echo $tensanphamtrangchu; ?>"
                                               href="<?php echo $linklienketsanpham; ?>"><?php echo $tensanphamtrangchu; ?></a>
                                        </h3>
                                        <div class="view-more">
                                            <a title="<?php echo $tensanphamtrangchu; ?>"
                                               href="<?php echo $linklienketsanpham; ?>">Xem thêm</a>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <div class="text-center">
                            <a title="Sản phẩm" class="btn-all text-uppercase"
                               href="https://tuankhangmedical.com/san-pham">Xem
                                toàn bộ sản phẩm</a>
                        </div>
                    </div>
                </div><!-- panel-body -->
            </section><!-- h-product -->

            <section class="h-news h-project">
                <div class="uk-container uk-container-center">
                    <div class="h-panel-head">
                        <h3 class="heading-2">Dự án tiêu biểu</h3>
                        <p></p>
                    </div>
                    <div class="panel-body">
                        <ul class="uk-list uk-clearfix uk-grid uk-grid-medium list-news">
                            <?php
                            for ($duandem = 1; $duandem <= 6; $duandem++) {
                                $getlinklienketduan = 'wpcf-link-du-an-' . $duandem;
                                $gettenduan = 'wpcf-ten-du-an-' . $duandem;
                              $getanhduan = 'wpcf-hinh-anh-du-an-' . $duandem;
                                $linklienketduan = get_field($getlinklienketduan, 61);
if (empty($linklienketduan)) {
    $linklienketduan = '#';
}

$tenduantrangchu = get_field($gettenduan, 61);

$img = get_field($getanhduan, 61);
$anhduantrangchu = '';

if (is_array($img) && isset($img['url'])) {
    // ACF Image Array
    $anhduantrangchu = '<img src="' . esc_url($img['url']) . '" alt="' . esc_attr($tenduantrangchu) . '" width="383" height="250">';
} elseif (is_string($img) && !empty($img)) {
    // ACF Image URL
    $anhduantrangchu = '<img src="' . esc_url($img) . '" alt="' . esc_attr($tenduantrangchu) . '" width="383" height="250">';
}
?>
                                <li class="uk-width-large-1-3 uk-width-small-1-1 uk-width-meidum-1-3">
                                    <div class="panel-box">
                                        <div class="img-cover image">
                                            <?php echo $anhduantrangchu; ?>
                                        </div>
                                        <div class="heading-1 uk-text-center">
                                            <a style="color:#333;font-size:14px;" href="<?php echo $linklienketduan; ?>"
                                               title="<?php echo $tenduantrangchu; ?>"><?php echo $tenduantrangchu; ?></a>
                                        </div>
                                    </div>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div><!-- panel-body -->
                </div>
            </section><!-- h-news -->

            <section class="h-news home-box-office">
                <div class="uk-container uk-container-center">
                    <div class="h-panel-head">
                        <h3 class="heading-2">Hành trình thực hiện ước mơ</h3>
                    </div>
                    <div class="home-box-content zing-content text-center">
                    </div>
                    <div class="home-office-list">
                        <div class="home-office-item">
                            <p>
                                <span class="counter" data-count="5000">5000</span>+
                            </p>
                            <p>Khách Hàng</p>
                        </div>
                        <div class="home-office-item">
                            <p>
                                <span class="counter" data-count="30">30</span>+
                            </p>
                            <p>HÃNG SẢN XUẤT</p>
                        </div>
                        <div class="home-office-item">
                            <p>
                                <span class="counter" data-count="35">35</span>+
                            </p>
                            <p>CONTAINER MỖI NĂM</p>
                        </div>
                        <div class="home-office-item">
                            <p>
                                <span class="counter" data-count="24">24</span>/<span class="counter"
                                                                                      data-count="7">7</span>
                            </p>
                            <p>Hỗ trợ</p>
                        </div>
                        <div class="home-office-item">
                            <p>
                                <span class="counter" data-count="10">10</span>+
                            </p>
                            <p>Năm Kinh nghiệm</p>
                        </div>
                    </div>
                </div>
            </section>

            <script src="<?php echo bloginfo('template_directory'); ?>/js/counterup-min.js"></script>
            <script>
                $('.counter').counterUp({
                    delay: 10,
                    time: 1000
                });
            </script>


            <section class="h-partner">
                <div class="partner">
                    <div class="uk-container uk-container-center">
                        <div class="h-panel-head">
                            <h3 class="heading-2">Đối tác của TUẤN KHANG</h3>
                        </div>
                        <section class="partner-section">
                            <div class="uk-container uk-container-center">
                                <section class="panel-body">
                                    <div class="uk-slidenav-position">
                                        <div class="uk-slider-container">
                                            <ul class="uk-grid uk-grid-small uk-grid-width-1-2 uk-grid-width-small-1-3 uk-grid-width-medium-1-4 uk-grid-width-large-1-5 uk-grid-width-xlarge-1-6">
                                                <?php
                                                for ($doitacdem = 1; $doitacdem <= 10; $doitacdem++) {
                                                    $getanhdoitac = 'wpcf-doi-tac-' . $doitacdem;
                                          $anhdoitactrangchu = '';
$anh = get_field($getanhdoitac, 61);

if ($anh) {
    // Nếu là IMAGE ARRAY
    if (is_array($anh) && isset($anh['url'])) {
        $anhdoitactrangchu = '<img src="' . esc_url($anh['url']) . '" alt="Đối tác của Tuấn Khang" height="95">';
    }
    // Nếu là IMAGE ID
    elseif (is_numeric($anh)) {
        $url = wp_get_attachment_image_url($anh, 'full');
        if ($url) {
            $anhdoitactrangchu = '<img src="' . esc_url($url) . '" alt="Đối tác của Tuấn Khang" height="95">';
        }
    }
    // Nếu là IMAGE URL
    elseif (is_string($anh)) {
        $anhdoitactrangchu = '<img src="' . esc_url($anh) . '" alt="Đối tác của Tuấn Khang" height="95">';
    }
}

                                                    ?>
                                                    <li>
                                                        <div class="thumb">
                                                            <a class="image img-scaledown" href="#"
                                                               title="Đối tác của Tuấn Khang"><?php echo $anhdoitactrangchu; ?></a>
                                                        </div>
                                                    </li>
                                                    <?php
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div><!-- .slider -->
                                </section>
                            </div>
                        </section><!-- .partner-section -->
                    </div>
                </div>
            </section>
        </div>
    </section><!-- #body -->

<?php get_footer(); ?>
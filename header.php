<!DOCTYPE html>
<html lang="vi-VN" prefix="og: http://ogp.me/ns#">

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>

    <?php if (is_search()) { ?>
        <meta name="robots" content="noindex, nofollow"/>
    <?php } ?>

    <link href="<?php echo bloginfo('template_directory'); ?>/icon.jpg" rel="shortcut icon" type="image/x-icon"/>

    <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>">

    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes"/>

<!--    <title>--><?php //wp_title(''); ?><!--</title>-->

    <link rel='stylesheet' href='<?php echo bloginfo('template_directory'); ?>/css/core.css' type='text/css' media='all'
          property='stylesheet'/>
    <link rel='stylesheet' href='<?php echo bloginfo('template_directory'); ?>/css/font-awesome.min.css' type='text/css'
          media='all' property='stylesheet'/>
    <link rel='stylesheet' href='<?php echo bloginfo('template_directory'); ?>/css/uikit.modify.css' type='text/css'
          media='all' property='stylesheet'/>
    <link rel='stylesheet' href='<?php echo bloginfo('template_directory'); ?>/css/reset.css' type='text/css'
          media='all' property='stylesheet'/>
    <link rel='stylesheet' href='<?php echo bloginfo('template_directory'); ?>/css/flexslider.css' type='text/css'
          media='all' property='stylesheet'/>
    <link rel='stylesheet' href='<?php echo bloginfo('template_directory'); ?>/css/slick.css' type='text/css'
          media='all' property='stylesheet'/>
    <link rel='stylesheet' href='<?php echo bloginfo('template_directory'); ?>/css/slick-theme.css' type='text/css'
          media='all' property='stylesheet'/>
    <link rel='stylesheet' href='<?php echo bloginfo('template_directory'); ?>/css/library.css' type='text/css'
          media='all' property='stylesheet'/>
    <link rel='stylesheet' href='<?php echo bloginfo('template_directory'); ?>/css/style.css' type='text/css'
          media='all' property='stylesheet'/>
    <link rel='stylesheet' href='<?php echo bloginfo('template_directory'); ?>/css/owl.carousel.css' type='text/css'
          media='all' property='stylesheet'/>
    <link rel='stylesheet' href='<?php echo bloginfo('template_directory'); ?>/css/style-duytv.css' type='text/css'
          media='all' property='stylesheet'/>

    <script type='text/javascript' src='<?php echo bloginfo('template_directory'); ?>/js/jquery.js'></script>
    <script type='text/javascript'
            src='<?php echo bloginfo('template_directory'); ?>/js/jquery-migrate-1.2.1.min.js'></script>
    <script type='text/javascript' src='<?php echo bloginfo('template_directory'); ?>/js/uikit.min.js'></script>
    <script type='text/javascript' src='<?php echo bloginfo('template_directory'); ?>/js/owl.carousel.js'></script>
    <?php wp_head(); ?>
</head>
<body>

<!-- PC HEADER -->
<header class="pc-header uk-visible-large">
    <section class="upper" data-uk-sticky="">
        <div class="topbar">
            <div class="uk-container uk-container-center">
                <div class="uk-flex uk-flex-middle uk-flex-space-between">
                    <div class="uk-clearfix header-contact">
                        <div class="hotline">
                            <span class="label"><i
                                        class="lotus-icon-phone"></i> <?php esc_html_e("Hotline:", "tuankhang"); ?> </span>
                            <a href="tel:<?php echo get_post_meta(61, 'wpcf-so-hotline', true); ?>"
                               title="<?php esc_html_e("Hotline", "tuankhang"); ?>"><?php echo get_post_meta(61, 'wpcf-so-hotline', true); ?></a>
                        </div>
                        <div class="email">
                            <span class="label"><i
                                        class="fa fa-envelope-o"></i> <?php esc_html_e("Email", "tuankhang"); ?>: </span>
                            <a href="mailto:<?php echo get_post_meta(61, 'wpcf-email', true); ?>"
                               title="<?php esc_html_e("Email", "tuankhang"); ?>"><?php echo get_post_meta(61, 'wpcf-email', true); ?></a>
                        </div>
                    </div>
                        <div class="uk-clearfix header-toolbox duytv-language">
                            <?php

                            /*For display language switcher in any place add the code to your template if ( function_exists ( 'wpm_language_switcher' ) ) wpm_language_switcher ();
        Function accepts two parameters:
        $type – ‘list’, ‘dropdown’, ‘select’. Default – ‘list’.
        $show – ‘flag’, ‘name’, ‘both’. Default – ‘both’.*/
                            if (function_exists('wpm_language_switcher')) :
//                                $languages_current = wpm_get_language();
//                            var_dump($languages_current);
                                ?>
                            <div class="uk-button-dropdown pc-language" data-uk-dropdown="{mode:'click', pos : 'bottom-center'}">
                                <a class="uk-button btn open-language"
                                   title="<?php esc_html_e("Ngôn ngữ", "tuankhang"); ?>"><span><?php esc_html_e("Ngôn ngữ", "tuankhang"); ?></span></a>
                                <div class="uk-dropdown box">
                                    <?php wpm_language_switcher('list', 'name'); ?>
                                </div>
                            </div>
                            <?php
                            endif;/*Multiple Language*/
                            ?>
                            <div class="uk-button-dropdown pc-search"
                                 data-uk-dropdown="{mode:'click', pos : 'bottom-right'}">
                                <a class="uk-button btn open-search" title="<?php esc_html_e("Tìm kiếm", "tuankhang"); ?><"><span><?php esc_html_e("Tìm kiếm", "tuankhang"); ?></span></a>
                                <div class="uk-dropdown wrap-form">
                                    <form action="<?php bloginfo('siteurl'); ?>" method="get" class="uk-form form">
                                        <input type="text" name="s" id="s" class="uk-width-1-1 input-text"
                                               placeholder="<?php esc_html_e("Nhập từ khóa tìm kiếm ...", "tuankhang"); ?>"/>
                                        <button type="submit" class="btn-submit"><?php esc_html_e("Tìm", "tuankhang"); ?></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
        <div class="uk-container uk-container-center">
            <div class="uk-flex uk-flex-middle">
                <!-- Các trang về tour dung logo-1.png -->
                <?php
                $logo_url = get_template_directory_uri().'/image/logo.png';
                if (function_exists('wpm_language_switcher')){
                    $languages_current = wpm_get_language();
                    if( $languages_current == 'en' ) $logo_url = get_template_directory_uri().'/image/logo-en.png';
                }
                ?>
                <p class="logo"><a href="<?php bloginfo('siteurl'); ?>"
                                   title="<?php esc_html_e("Công ty TNHH Dược và Thiết bị y tế Tuấn Khang", "tuankhang"); ?>"><img
                                src="<?php echo esc_url($logo_url)?>" width="200"
                                height="139" alt="Công ty TNHH Dược và Thiết bị y tế Tuấn Khang"/></a><span
                            class="uk-hidden"><?php esc_html_e("Công ty TNHH Dược và Thiết bị y tế Tuấn Khang", "tuankhang"); ?></span>
                </p>
                <nav class="main-nav">

                    <ul class="uk-navbar-nav uk-clearfix main-menu">


                        <?php

                        $menuIDx = 25;
                        $primaryNavx = wp_get_nav_menu_items($menuIDx);
                        $id_parentx = 0;
                        foreach ($primaryNavx as $navItemx) {
                            if ($navItemx->menu_item_parent == $id_parentx) {
                                echo '<li> <a href="' . $navItemx->url . '" title="' . $navItemx->title . '">' . $navItemx->title . '</a>';
                                $sub = "";
                                foreach ($primaryNavx as $navItemx2) {
                                    if ($navItemx2->menu_item_parent == $navItemx->ID) {
                                        $sub .= '<li> <a href="' . $navItemx2->url . '" title="' . $navItemx2->title . '">' . $navItemx2->title . '</a>';

                                        $sub2 = "";
                                        foreach ($primaryNavx as $navItemx3) {
                                            if ($navItemx3->menu_item_parent == $navItemx2->ID) {
                                                $sub2 .= '<li> <a href="' . $navItemx3->url . '" title="' . $navItemx3->title . '">' . $navItemx3->title . '</a></li>';
                                            }
                                        }
                                        if(!empty($sub2)) $sub .= '<ul class="uk-list uk-clearfix submenu list-subchild">' . $sub2 . '</ul>';

                                        $sub .= '</li>';
                                    }
                                }
                                if(!empty($sub)) echo '<div class="dropdown-menu"><ul class="uk-list submenu">' . $sub . '</ul></div>';
                                echo '</li>';
                            }
                        }
                        ?>
                    </ul>
                </nav>
            </div>
        </div>
        <div class="upper-bg"></div>
    </section>
</header><!-- .pc-header -->

<!-- MOBILE HEADER -->

<section class="uk-hidden-large mobile-header" data-uk-sticky="">
    <section class="upper">
        <a class="open-offcanvas offcanvas" href="#" data-uk-offcanvas="{target:'#offcanvas'}">
            <span><?php esc_html_e("Menu", "tuankhang"); ?></span>
        </a>
        <?php
        $logo_url = get_template_directory_uri().'/image/logo.png';
        if (function_exists('wpm_language_switcher')){
            $languages_current = wpm_get_language();
            if( $languages_current == 'en' ) $logo_url = get_template_directory_uri().'/image/logo-en.png';
        }
        ?>
        <div class="logo">
            <a href="<?php bloginfo('siteurl'); ?>" title="<?php esc_html_e("Công ty TNHH Dược và Thiết bị y tế Tuấn Khang", "tuankhang"); ?>">
                <img src="<?php echo esc_url($logo_url); ?>" width="200" height="139" alt="<?php esc_html_e("Công ty TNHH Dược và Thiết bị y tế Tuấn Khang", "tuankhang"); ?>"/>
            </a>
        </div>
    </section>

</section>
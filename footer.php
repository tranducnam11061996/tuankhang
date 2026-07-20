<footer id="footer">
    <section class="ft-middle">
        <div class="uk-container uk-container-center">
            <div class="uk-grid uk-grid-medium">
                <div class="uk-width-large-1-4 uk-width-medium-1-2 uk-width-small-1-1">
                    <ul class="uk-list uk-clearfix">
                        <li class="ft-head ft-logo">
                            <?php
                            $logo_white_url = get_template_directory_uri().'/image/logo-white.png';
                            if (function_exists('wpm_language_switcher')){
                                $languages_current = wpm_get_language();
                                if( $languages_current == 'en' ) $logo_white_url = get_template_directory_uri().'/image/logo-white-en.png';
                            }
                            ?>
                            <a href="<?php bloginfo('siteurl'); ?>"
                               title="<?php esc_html_e("Công ty TNHH Dược và Thiết bị y tế Tuấn Khang", "tuankhang"); ?>"><img
                                        src="<?php echo esc_url($logo_white_url); ?>"
                                        width="200" height="139"
                                        alt="<?php esc_html_e("Công ty TNHH Dược và Thiết bị y tế Tuấn Khang", "tuankhang"); ?>"/></a><span
                                    class="uk-hidden"><?php esc_html_e("Công ty TNHH Dược và Thiết bị y tế Tuấn Khang", "tuankhang"); ?></span>
                        </li>
                        <li class="ft-address">
                            Trụ sở: <?php echo get_post_meta(61, 'wpcf-dia-chi-cong-ty', true); ?>
                        </li>
                        <li class="ft-web"><?php esc_html_e("Mã số thuế: 0108344655 - Ngày cấp: 28/06/2018 - Nơi cấp: Sở Kế Hoạch Và Đầu Tư Tp.Hà Nội", "tuankhang"); ?></li>
                        <li class="ft-phone">
                            <span> <?php esc_html_e("Hotline", "tuankhang"); ?>: <a
                                        href="tel:<?php echo get_post_meta(61, 'wpcf-so-hotline', true); ?>"
                                        title="Hà Nội"><?php echo get_post_meta(61, 'wpcf-so-hotline', true); ?></a></span>
                        </li>

                    </ul>
                </div>
                <div class="uk-width-large-1-4 uk-width-medium-1-2 uk-width-small-1-1">
                    <ul class="uk-list uk-clearfix">
                        <li class="ft-head">
                            <?php esc_html_e("Dịch vụ khách hàng", "tuankhang"); ?>
                        </li>
                        <?php

                        $menuIDxx = 29;
                        $primaryNavxx = wp_get_nav_menu_items($menuIDxx);
                        foreach ($primaryNavxx as $navItemxx) {
                            ?>
                            <li class="ft-check"><a
                                        href="<?php echo $navItemxx->url ?>"><?php echo $navItemxx->title ?></a></li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
                <div class="uk-width-large-1-4 uk-width-medium-1-2 uk-width-small-1-1">
                    <ul class="uk-list uk-clearfix">
                        <li class="ft-head">
                            <?php esc_html_e("Hệ thống chi nhánh", "tuankhang"); ?>
                        </li>
                        <li class="ft-address"><?php esc_html_e("[Hà Nội]: Số 23, ngõ 38 Phương Mai, Phường Kim Liên, Hà Nội.", "tuankhang"); ?></li>       
                        <li class="ft-address"><?php esc_html_e("[Hồ Chí Minh]: Số 1/1 Đường Hoàng Việt, Phường Tân Sơn Nhất, Hồ Chí Minh.", "tuankhang"); ?></li>
                    </ul>
                </div>
                <div class="uk-width-large-1-4 uk-width-medium-1-2 uk-width-small-1-1">
                    <ul class="uk-list uk-clearfix">
                        <li class="ft-head">
                            <?php esc_html_e("Bản Đồ", "tuankhang"); ?>
                        </li>
                        <li class="ft-maps">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.685746165307!2d105.83404037627878!3d21.005230488584314!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ac7919c6b95b%3A0x624a289a6ba5cdc!2zMjMgTmcuIDM4IFAuIFBoxrDGoW5nIE1haSwgS2ltIExpw6puLCDEkOG7kW5nIMSQYSwgSMOgIE7hu5lpLCBWaeG7h3QgTmFt!5e0!3m2!1svi!2s!4v1734943915820!5m2!1svi!2s"
                                    style="border:0; min-height: 220px; width:100%; height:100%;" allowfullscreen=""
                                    loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </li>
                    </ul>
                </div>
            </div><!-- uk-grid uk-grid-medium -->
            <div class="uk-grid uk-grid-large mt20">
                <div class="uk-width-large-1-3 uk-width-medium-1-3 uk-width-small-1-1">
                    <div class="ft-sosical">
                        <div class="sl-head">
                            <?php esc_html_e("Kết nối với chúng tôi qua", "tuankhang"); ?>
                        </div>
                        <div class="uk-flex uk-flex-middle sl-icon">
                            <a href="<?php echo get_post_meta(61, 'wpcf-link-fanpage', true); ?>" title="<?php esc_html_e("Fanpage Facebook", "tuankhang"); ?>">
                               <img src="<?php echo bloginfo('template_directory'); ?>/image/icon-1.png" alt="<?php esc_html_e("Fanpage Facebook", "tuankhang"); ?>" width="38" height="38" />
                            </a>
                            <a href="<?php echo get_post_meta(61, 'wpcf-link-twitter', true); ?>" title="<?php esc_html_e("Twitter", "tuankhang"); ?>">
                                <img src="<?php echo bloginfo('template_directory'); ?>/image/icon-2.png" alt="<?php esc_html_e("Twitter", "tuankhang"); ?>" width="37" height="37" />
                            </a>
                            <a href="<?php echo get_post_meta(61, 'wpcf-link-google-plus', true); ?>" title="<?php esc_html_e("Google Plus", "tuankhang"); ?>">
                               <img src="<?php echo bloginfo('template_directory'); ?>/image/icon-3.png" alt="<?php esc_html_e("Google Plus", "tuankhang"); ?>" width="36" height="36" />
                            </a>
                            <a href="<?php echo get_post_meta(61, 'wpcf-link-instagram', true); ?>" title="<?php esc_html_e("Instagram", "tuankhang"); ?>">
                               <img src="<?php echo bloginfo('template_directory'); ?>/image/icon-4.png" alt="<?php esc_html_e("Instagram", "tuankhang"); ?>" width="37" height="37" />
                            </a>
                            <a href="<?php echo get_post_meta(61, 'wpcf-link-kenh-youtube', true); ?>" title="<?php esc_html_e("Kênh Youtube", "tuankhang"); ?>">
                                <img src="<?php echo bloginfo('template_directory'); ?>/image/icon-5.png" alt="<?php esc_html_e("Kênh Youtube", "tuankhang"); ?>" width="38" height="38" />
                            </a>
                        </div>
                    </div><!-- ft-sosical -->
                </div>
                <div class="uk-width-large-1-3 uk-width-medium-1-3 uk-width-small-1-1">
                    <a style="display:block;max-width:200px;" rel="nofollow" target="_blank"
                       href="http://online.gov.vn/Home/WebDetails/84000" title="<?php esc_html_e("Đã Thông Báo Với Bộ Công Thương", "tuankhang"); ?>"><img
                                src="https://tuankhangmedical.com/wp-content/uploads/2021/08/bct.png"
                                alt="<?php esc_html_e("Đã Thông Báo Với Bộ Công Thương", "tuankhang"); ?>" width="160" height="60"/></a>
                </div>
            </div>
        </div>
    </section><!-- ft-middle -->
    <section class="ft-bottom">
        <div class="uk-container uk-container-center">
            <span><?php esc_html_e("© Copyright 2021 by TUẤN KHANG. All Rights Reserved.", "tuankhang"); ?></span>
        </div>
    </section><!-- ft-bottom -->
</footer>

<a class="ring-alo-phone uk-hidden-large" href="tel:<?php echo get_post_meta(61, 'wpcf-so-hotline', true); ?>"
   title="Hotline">
    <div class="animated infinite zoomIn ring-alo-ph-circle"></div>
    <div class="animated infinite pulse ring-alo-ph-circle-fill"></div>
    <div class="animated infinite tada ring-alo-ph-img-circle"></div>
</a>
<div id="offcanvas" class="uk-offcanvas offcanvas">
    <div class="uk-offcanvas-bar">
<!--        <form class="uk-search" action="#" data-uk-search="{}">-->
<!--            <input class="uk-search-field" type="search" name="s" id="s" placeholder="Tìm kiếm...">-->
<!--        </form>-->
        <ul class="uk-nav uk-nav-offcanvas uk-nav uk-nav-parent-icon" data-uk-nav>
            <?php
            $xxx_walker = new XXX_Nav_Walker;
            wp_nav_menu(array(
                'menu' => 'Menu Header PC',
                'container' => '',
                'items_wrap' => '%3$s',
                'walker' => $xxx_walker
            ));

            ?>
        </ul>
    </div>
</div><!-- #offcanvas -->

<script type='text/javascript' src='<?php echo bloginfo('template_directory'); ?>/js/slider.min.js'></script>
<script type='text/javascript' src='<?php echo bloginfo('template_directory'); ?>/js/slideshow.min.js'></script>
<script type='text/javascript' src='<?php echo bloginfo('template_directory'); ?>/js/slideset.min.js'></script>
<script type='text/javascript' src='<?php echo bloginfo('template_directory'); ?>/js/tooltip.min.js'></script>
<script type='text/javascript' src='<?php echo bloginfo('template_directory'); ?>/js/sticky.min.js'></script>
<script type='text/javascript' src='<?php echo bloginfo('template_directory'); ?>/js/switcher.min.js'></script>
<script type='text/javascript' src='<?php echo bloginfo('template_directory'); ?>/js/accordion.min.js'></script>
<script type='text/javascript' src='<?php echo bloginfo('template_directory'); ?>/js/lightbox.min.js'></script>
<script type='text/javascript' src='<?php echo bloginfo('template_directory'); ?>/js/scrollspy.min.js'></script>
<script type='text/javascript' src='<?php echo bloginfo('template_directory'); ?>/js/jquery.flexslider-min.js'></script>
<script type='text/javascript' src='<?php echo bloginfo('template_directory'); ?>/js/slick.min.js'></script>
<script type='text/javascript' src='<?php echo bloginfo('template_directory'); ?>/js/function.js'></script>
<script type='text/javascript' src='<?php echo bloginfo('template_directory'); ?>/js/library.js'></script>
<!-- Your Plugin chat code -->
<div id="fb-customer-chat" class="fb-customerchat"></div>
<script>
    var chatbox = document.getElementById('fb-customer-chat');
    chatbox.setAttribute("page_id", "108890274790969");
    chatbox.setAttribute("attribution", "biz_inbox");
    window.fbAsyncInit = function () {
        FB.init({
            xfbml: true,
            version: 'v11.0'
        });
    };

    (function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s);
        js.id = id;
        js.src = 'https://connect.facebook.net/vi_VN/sdk/xfbml.customerchat.js';
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>
<?php wp_footer(); ?>

<!-- Don't forget analytics -->

</body>
</html>

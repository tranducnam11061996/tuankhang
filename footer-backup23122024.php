	<footer id="footer">
	<section class="ft-middle">
		<div class="uk-container uk-container-center">
			<div class="uk-grid uk-grid-medium">
				<div class="uk-width-large-1-3 uk-width-medium-1-4 uk-width-small-1-1">
					<ul class="uk-list uk-clearfix">
						<li class="ft-head">
							Công ty TNHH Dược và Thiết bị Y tế Tuấn Khang
						</li>
						<li class="ft-address">
							Trụ sở: <?php echo get_post_meta( 61, 'wpcf-dia-chi-cong-ty', true ); ?>
                        </li>
						<li class="ft-web">Mã số thuế: 0108344655 - Ngày cấp: 28/06/2018 - Nơi cấp: Sở Kế Hoạch Và Đầu Tư Tp.Hà Nội</li>
                        <li class="ft-phone">
							<span>Hotline: <a href="tel:<?php echo get_post_meta( 61, 'wpcf-so-hotline', true ); ?>" title="Hà Nội"><?php echo get_post_meta( 61, 'wpcf-so-hotline', true ); ?></a></span>
						</li>
						
					</ul>
				</div>
				<div class="uk-width-large-1-4 uk-width-medium-1-4 uk-width-small-1-1">
						<ul class="uk-list uk-clearfix">
							<li class="ft-head">
								Văn Phòng Giao Dịch
							</li>
							<li class="ft-address">
								<?php echo get_post_meta( 61, 'wpcf-van-phong-giao-dich', true ); ?></li>
							<li class="ft-phone">
								<span>ĐT: <a href="tel:<?php echo get_post_meta( 61, 'wpcf-so-may-ban', true ); ?>" title="Văn Phòng Giao Dịch"><?php echo get_post_meta( 61, 'wpcf-so-may-ban', true ); ?></a></span>
							</li>
							<li class="ft-email">Email: <a href="mailto:<?php echo get_post_meta( 61, 'wpcf-email', true ); ?>" title="Tp Hồ chí minh"><?php echo get_post_meta( 61, 'wpcf-email', true ); ?></a></li>
						</ul>
				</div>

				<div class="uk-width-large-1-4 uk-width-medium-1-4 uk-width-small-1-1">
						<ul class="uk-list uk-clearfix">
							<li class="ft-head">
								Chính sách
							</li>
 <?php 

  $menuIDxx = 29; 
  $primaryNavxx = wp_get_nav_menu_items($menuIDxx); 
  foreach ( $primaryNavxx as $navItemxx ) {
?>
    <li class="ft-web"><a href="<?php echo $navItemxx->url ?>"><?php echo $navItemxx->title ?></a></li>
<?php
  }
 ?>
						</ul>
				</div>
			</div><!-- uk-grid uk-grid-medium -->
			<div class="uk-grid uk-grid-large mt20">
				<div class="uk-width-large-1-3 uk-width-medium-1-3 uk-width-small-1-1">
					<div class="ft-sosical">
						<div class="sl-head">
							Kết nối với chúng tôi qua
						</div>
						<div class="uk-flex uk-flex-middle sl-icon">
							<a href="<?php echo get_post_meta( 61, 'wpcf-link-fanpage', true ); ?>" title="Fanpage Facebook"><img src="<?php echo bloginfo('template_directory'); ?>/image/icon-1.png" alt="Fanpage"></a>
							<a href="<?php echo get_post_meta( 61, 'wpcf-link-twitter', true ); ?>" title="Twitter"><img src="<?php echo bloginfo('template_directory'); ?>/image/icon-2.png" alt="Twitter"></a>
							<a href="<?php echo get_post_meta( 61, 'wpcf-link-google-plus', true ); ?>" title="Google Plus"><img src="<?php echo bloginfo('template_directory'); ?>/image/icon-3.png" alt="Google Plus"></a>
							<a href="<?php echo get_post_meta( 61, 'wpcf-link-instagram', true ); ?>" title="Instagram"><img src="<?php echo bloginfo('template_directory'); ?>/image/icon-4.png" alt="Instagram"></a>
							<a href="<?php echo get_post_meta( 61, 'wpcf-link-kenh-youtube', true ); ?>" title="Kênh Youtube"><img src="<?php echo bloginfo('template_directory'); ?>/image/icon-5.png" alt="Kênh Youtube"></a>
						</div>
					</div><!-- ft-sosical -->
				</div>
                <div class="uk-width-large-1-3 uk-width-medium-1-3 uk-width-small-1-1">
					<a style="display:block;max-width:200px;" rel="nofollow" target="_blank" href="http://online.gov.vn/Home/WebDetails/84000" title="Đã Thông Báo Với Bộ Công Thương"><img src="https://tuankhangmedical.com/wp-content/uploads/2021/08/bct.png" alt="Đã Thông Báo Với Bộ Công Thương"/></a>
				</div>
			</div>
		</div>
	</section><!-- ft-middle -->
	<section class="ft-bottom">
		<div class="uk-container uk-container-center">
			<span>© Copyright 2021 by TUẤN KHANG. All Rights Reserved.</span>
		</div>
	</section><!-- ft-bottom -->
</footer>

<a class="ring-alo-phone uk-hidden-large" href="tel:<?php echo get_post_meta( 61, 'wpcf-so-hotline', true ); ?>" title="Hotline">
	<div class="animated infinite zoomIn ring-alo-ph-circle"></div>
	<div class="animated infinite pulse ring-alo-ph-circle-fill"></div>
	<div class="animated infinite tada ring-alo-ph-img-circle"></div>
</a>
	<div id="offcanvas" class="uk-offcanvas offcanvas">
	<div class="uk-offcanvas-bar">
		<form class="uk-search" action="#" data-uk-search="{}">
		    <input class="uk-search-field" type="search" name="s" id="s" placeholder="Tìm kiếm...">
        </form>		         
<ul class="uk-nav uk-nav-offcanvas uk-nav uk-nav-parent-icon" data-uk-nav>		         
<?php
 $xxx_walker = new XXX_Nav_Walker;
 wp_nav_menu( array(
  'menu'=>'Menu Header PC',
  'container'=>'',
  'items_wrap' => '%3$s',
  'walker' => $xxx_walker
 ) ); 

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
  window.fbAsyncInit = function() {
    FB.init({
      xfbml            : true,
      version          : 'v11.0'
    });
  };

  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = 'https://connect.facebook.net/vi_VN/sdk/xfbml.customerchat.js';
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));
</script>
	<?php wp_footer(); ?>
	
	<!-- Don't forget analytics -->

</body>
</html>
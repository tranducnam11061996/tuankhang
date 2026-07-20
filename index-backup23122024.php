<?php get_header(); ?>

	<section id="body">
				<div id="homepage" class="page-body">
	<section class="slide-1">
			<section class="main-slideshow">
		<div class="uk-slidenav-position" data-uk-slideshow="{autoplay: true, autoplayInterval: 2500, animation: 'fade'}">
			<ul class="uk-slideshow">
<?php
for ($sliderdem = 1; $sliderdem <= 2; $sliderdem++) {
$getanhslider = 'anhbanner-'.$sliderdem;
$anhslidertrangchu = types_render_field( $getanhslider, array( "alt" => "Công ty TNHH Dược và Thiết bị y tế Tuấn Khang", "id" => 61, width => "1360px" ,"height" => "475px") );
?> 
				<li>
					<a href="#" title="Công ty TNHH Dược và Thiết bị y tế Tuấn Khang" class="image img-cover"><?php echo $anhslidertrangchu; ?></a>
				</li>
<?php
}
?>
							</ul>
			<a href="#" class="uk-slidenav uk-slidenav-contrast uk-slidenav-previous" data-uk-slideshow-item="previous"></a>
			<a href="#" class="uk-slidenav uk-slidenav-contrast uk-slidenav-next" data-uk-slideshow-item="next"></a>
		</div>
	</section>
		</section>

	<section class="homepage-about">
		<div class="uk-grid  uk-grid-collapse uk-grid uk-grid-width-large-1-2">
												
			<section class="aboutus">
				<div class="panel-body">
					<article class="intro">
						<h3 class="title"><a href="#" title="CÂU CHUYỆN VỀ TUẤN KHANG">CÂU CHUYỆN VỀ TUẤN KHANG</a></h3>
						<div class="description" style="font-size: 12pt;color: #ffffff;line-height: 140%;" id="cauchuyentuankhangx">
                            <?php echo apply_filters('the_content', get_post_meta(61, 'wpcf-cau-chuyen-ve-tuan-khang', true)); ?>
                        </div>
						<div class="readmore" style="padding-top: 25px;padding-bottom: 10px;">
							<a href="https://tuankhangmedical.com/gioi-thieu" title="CÂU CHUYỆN VỀ TUẤN KHANG" class="btn-readmore">
								Xem thêm
							</a>
						</div>
					</article>
				</div>
			</section>
											</div>
	</section>
<style>
#cauchuyentuankhangx p {
    margin: 20px auto 0px;
    text-align: justify;
}
</style>



				
	<section class="h-product">
		<div class="uk-container uk-container-center">
			<div class="h-panel-head">
				<h3 class="heading-2"><a href="https://tuankhangmedical.com/san-pham" title="Sản phẩm">Sản phẩm</a></h3>
				<p></p>
			</div>

						<div id="product-solution" data-scroll="scroll2">
				<div class="grid">
					<div class="grid-sizer"></div>
<?php
for ($sanphamdem = 1; $sanphamdem <= 8; $sanphamdem++) {
$getlinklienketsanpham = 'wpcf-link-lien-ket-san-pham-'.$sanphamdem;
$gettensanpham = 'wpcf-ten-hien-thi-san-pham-'.$sanphamdem;
$getanhsanpham = 'anh-san-pham-'.$sanphamdem;
$linklienketsanpham = get_post_meta( 61, $getlinklienketsanpham, true );
$tensanphamtrangchu = get_post_meta( 61, $gettensanpham, true );
$anhsanphamtrangchu = types_render_field( $getanhsanpham, array( "alt" => $tensanphamtrangchu, "id" => 61, "height" => "240px") );
?>
					<div class="grid-item">
						<div class="inner">
                            <div class="image">
                                <a href="<?php echo $linklienketsanpham; ?>"><?php echo $anhsanphamtrangchu; ?></a>
                            </div>
							<h3><a title="<?php echo $tensanphamtrangchu; ?>" href="<?php echo $linklienketsanpham; ?>"><?php echo $tensanphamtrangchu; ?></a></h3>
							<div class="view-more">
								<a title="<?php echo $tensanphamtrangchu; ?>" href="<?php echo $linklienketsanpham; ?>">Xem thêm</a>
							</div>
						</div>
					</div>
<?php
}
?>                    
                </div>
				<div class="text-center">
					<a title="Sản phẩm" class="btn-all text-uppercase" href="https://tuankhangmedical.com/san-pham">Xem toàn bộ sản phẩm</a>
				</div>
			</div>
					</div><!-- panel-body -->
	</section><!-- h-product -->
	
                            <section class="system-new">
                <div class="uk-container uk-container-center">
                    <div class="uk-grid uk-grid-small uk-grid-small-1-1 uk-grid-width-medium-1-2">
                        <div class="box-left">
                            <div class="thumb">
                                <img src="<?php echo bloginfo('template_directory'); ?>/image/vnn-2.png" alt="Phân Phối">
                            </div>
                        </div>
                        <div class="box-right">
                            <div class="panel-head">
                                <h3 class="heading-2"><a href="#" title="Hệ thống phân phối">HỆ THỐNG PHÂN PHỐI</a></h3>

                            </div>
                            <div class="panel-body"><?php echo apply_filters('the_content', get_post_meta(61, 'wpcf-he-thong-phan-phoi', true)); ?></div>
                        </div><!-- box-right -->
                    </div>
                </div><!-- container -->
            </section>
    

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
                        <span class="counter" data-count="24">24</span>/<span class="counter" data-count="7">7</span>
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

				
	<section class="h-news h-project">
		<div class="uk-container uk-container-center">
			<div class="h-panel-head">
				<h3 class="heading-2">Dự án</h3>
				<p></p>
			</div>
						<div class="panel-body">
				<ul class="uk-list uk-clearfix uk-grid uk-grid-medium list-news">
<?php
for ($duandem = 1; $duandem <= 6; $duandem++) {
$getlinklienketduan = 'wpcf-link-du-an-'.$duandem;
$gettenduan = 'wpcf-ten-du-an-'.$duandem;
$getanhduan = 'hinh-anh-du-an-'.$duandem;
$linklienketduan = get_post_meta( 61, $getlinklienketduan, true );
if($linklienketduan == '') {$linklienketduan = '#';}
$tenduantrangchu = get_post_meta( 61, $gettenduan, true );
$anhduantrangchu = types_render_field( $getanhduan, array( "alt" => $tenduantrangchu, "id" => 61, "width" => "383px" , "height" => "250px" , "crop" => "true") );
?>              										
<li class="uk-width-large-1-3 uk-width-small-1-1 uk-width-meidum-1-3">
	<div class="panel-box">
		<div class="img-cover image">
			<?php echo $anhduantrangchu; ?>
		</div>
		<div class="heading-1 uk-text-center">
            <a style="color:#333;font-size:14px;" href="<?php echo $linklienketduan; ?>" title="<?php echo $tenduantrangchu; ?>"><?php echo $tenduantrangchu; ?></a>
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
$getanhdoitac = 'doi-tac-'.$doitacdem;
$anhdoitactrangchu = types_render_field( $getanhdoitac, array( "alt" => "Đối tác của Tuấn Khang", "id" => 61, "height" => "95px") );
?> 
<li>
    <div class="thumb">
        <a class="image img-scaledown" href="#" title="Đối tác của Tuấn Khang"><?php echo $anhdoitactrangchu; ?></a>
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
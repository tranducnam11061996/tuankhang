<div class="uk-width-large-1-4 uk-width-medium-1-4 uk-width-small-1-1">
	<section class="aside-1">
	   <div class="aside-panel aside-categories">
            <div class="as-head"><?php esc_html_e("Danh mục Sản phẩm", "tuankhang"); ?></div>
            <div class="as-panel-body">
            <div class="uk-accordion" data-uk-accordion>

<?php 
  $menuID = 24; 
  $primaryNav = wp_get_nav_menu_items($menuID); 
  $id_parent =0;
  foreach ( $primaryNav as $navItem ) {
    if($navItem -> menu_item_parent == $id_parent){
?>
    <h3 class="uk-accordion-title as-heading-2"><a href="<?php echo $navItem->url; ?>" title="<?php echo $navItem->title; ?>"><?php echo $navItem->title; ?></a></h3>
    <div class="uk-accordion-content as-body">
	  <ul class="uk-list uk-clearfix">
<?php
    foreach ( $primaryNav as $navItem2 ) { 
        if($navItem2 -> menu_item_parent == $navItem ->ID){
?>
            <li><a href="<?php echo $navItem2->url; ?>" title="<?php echo $navItem2->title; ?>"><?php echo $navItem2->title; ?></a></li>
<?php
        } 
      }
      echo '</ul>';
      echo '</div>';
    }
    
  }
 ?>

			</div><!--  danh mục chinh -->
            </div><!-- panell-body -->
        </div>


<div class="aside-panel aside-product">
<div class="as-head mt20">Sản phẩm nổi bật</div>
<div class="as-panel-body">

<?php $argsx = array(

'post_type' => 'san-pham',

'showposts' => 6,

'meta_key' => 'wpcf-noi-bat',

'meta_value' => '1'

);

$indexx = new WP_Query( $argsx ); 

while($indexx->have_posts()) : $indexx->the_post();?>

		
<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
<div class="uk-grid uk-grid-small">
	<div class="uk-width-large-1-2">
		<div class="as-thumb img-scaledown">
			<?php echo get_the_post_thumbnail($page->ID, 'medium'); ?> 
		</div>
	</div>
	<div class="uk-width-large-1-2">
		<div class="as-content">
			<span class="as-content-head"><?php the_title(); ?></span>
			<span class="as-content-price">Đánh Giá:</span>
			<span class="as-content-icon">
				<i class="fa fa-star" aria-hidden="true"></i>
				<i class="fa fa-star" aria-hidden="true"></i>
				<i class="fa fa-star" aria-hidden="true"></i>
				<i class="fa fa-star" aria-hidden="true"></i>
				<i class="fa fa-star" aria-hidden="true"></i>
			</span>
		</div>
	</div>
</div>
</a>
		
<?php endwhile; wp_reset_postdata(); ?>
		
		
	</div><!-- as-panel-body -->
</div>
</section>

<?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('Sidebar Widgets')) : else : ?>

<?php endif; ?>
</div>
<?php get_header(); ?>

	<section id="body">
				<div id="product" class="page-body">
           
	<section class="bl-top-header">
		<div class="uk-container uk-container-center">
			<div class="bl-head">
				<h1 class="heading-1"><?php the_title(); ?></h1>
			</div>

		</div>
	</section><!-- bl-top-header -->
	<section class="bl-main-body">
		<div class="uk-container uk-container-center">
			<div class="uk-grid uk-grid-medium">
                   
<?php get_sidebar(); ?>    

<div class="uk-width-large-3-4 uk-width-medium-3-4 uk-width-small-1-1">
	<div class="bl-panel-head">
		<h2 class="heading-1"><?php the_title(); ?></h2>
	</div>
	<section class="art-detail detail-content">
		<section class="panel-body" id="contenpagex">
         
			<article class="article detail-content">
				<div style="text-align: justify;">
                    <?php the_content(); ?>
                </div>		
            </article><!-- .article -->
		</section><!-- .panel-body -->
		<footer class="panel-foot">

		</footer>
	</section><!-- .art-detail -->	
</div>          
			</div>
		</div>
	</section><!-- bl-main-body -->

	<section class="h-product">
<?php
    $args = array(
    'post__not_in' => array($post->ID),
    'showposts' => 10
    );
    $cungloaitin = new WP_Query( $args );
    if( $cungloaitin->have_posts() ): 
?>
		<div class="uk-container uk-container-center">
			<div class="h-panel-head">
				<h3 class="heading-2"><a href="." title=""><?php esc_html_e("Các bài đăng khác", "tuankhang"); ?></a></h3>

			</div>
							<div class="panel-body main-prod">
					<div class="uk-grid uk-grid-small mt20">


<?php while ( $cungloaitin->have_posts() ) : $cungloaitin->the_post(); ?>     
<?php include (TEMPLATEPATH . '/inc/1sanpham.php' ); ?> 
<?php endwhile; ?>
					</div>
				</div>
					</div><!-- panel-body -->
 <?php endif; wp_reset_query(); ?>                   
	</section><!-- h-product -->
</div>    
	</section><!-- #body -->

<?php get_footer(); ?>
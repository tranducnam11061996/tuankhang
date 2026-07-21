<?php get_header(); ?>

	<section id="body">
				<div id="product" class="page-body">
       
	<section class="bl-top-header">
		<div class="uk-container uk-container-center">
			<div class="bl-head">
				<h1 class="heading-1"><?php single_cat_title(); ?></h1>
			</div>

		</div>
	</section><!-- bl-top-header -->
	<section class="bl-main-body">
		<div class="uk-container uk-container-center">
			<div class="uk-grid uk-grid-medium">
                   
<?php get_sidebar(); ?>    
    
				<div class="uk-width-large-3-4 uk-width-medium-3-4 uk-width-small-1-1">

							<div class="bl-panel-head">
								<h2 class="heading-1"><?php single_cat_title(); ?> :</h2>
								<div class="prd-body-head"></div>
							</div>
<?php if (have_posts()) : ?>                  
<?php while (have_posts()) : the_post(); ?>  
<div class="bl-panel-body">
	<div class="uk-grid uk-grid-small">
		<div class="uk-width-large-2-5 uk-width-medium-2-5 uk-width-small-1-2">
			<div class="bl-thumb img-scaledown">
				<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
			</div>
		</div>
		<div class="uk-width-large-3-5 uk-width-medium-3-5 uk-width-small-1-2">
			<div class="bl-description">
				<div class="des-head"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></div>
				<p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 50, '…')); ?></p>
			</div>
		</div>
	</div>
</div>
<?php endwhile; wp_reset_postdata();?>
<?php endif; ?>
<div class="main-pagination">
<?php if(function_exists('wp_pagenavi')) { wp_pagenavi();} ?>
</div>	
            </div>
			</div>
		</div>
	</section><!-- bl-main-body -->
</div>    
	</section><!-- #body -->

<?php get_footer(); ?>

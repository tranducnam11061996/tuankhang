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
<?php if (is_page( 'gioi-thieu' )) { ?> 
			<div class="description detail-content">
                    <?php
$content = get_field('wpcf-cau-chuyen-ve-tuan-khang', 61);
if ($content) {
    echo apply_filters('the_content', $content);
}
?>
				
            </div>
<?php } ?>             
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

</div>    
	</section><!-- #body -->

<?php get_footer(); ?>
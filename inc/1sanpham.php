<div class="uk-width-large-1-4 uk-width-meidum-1-3 uk-width-small-1-1 mb15">

	<div class="info-prod">

		<div class="prod-thumb img-zoomin ">

			<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="img-scaledown image">

                <?php echo get_the_post_thumbnail($page->ID, 'medium'); ?> 

            </a>

		</div>

		<div class="title">

			<a style="color:#333;" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>

		</div>

	</div><!-- info-prod -->

</div> <!-- khoi 1 -->
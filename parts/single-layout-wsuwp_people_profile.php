<?php while ( have_posts() ) : the_post(); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<section class="row halves gutter pad-top reverse">

		<div class="column one gutterless">

			<?php
    		if ( spine_has_featured_image() ) {
					$featured_image_src = spine_get_featured_image_src();
					/*?><figure class="featured-image" style="background-image: url('<?php echo esc_url( $featured_image_src ); ?>');"><?php spine_the_featured_image(); ?></figure><?php*/
					?><figure class="wsuwp-person-photo"><?php spine_the_featured_image(); ?></figure><?php
				}
			?>

		</div><!--/column-->

		<div class="column two">

			<?php get_template_part( 'articles/post', get_post_type() ); ?>

		</div><!--/column two-->

	</section>

	<section class="row single gutter pad-top">

		<div class="column one">

			<?php the_content(); ?>

		</div><!--/column-->

	</section>

	<?php get_template_part( 'articles/post', get_post_type() . '-additional' ); ?>

</div>

<?php endwhile; ?>
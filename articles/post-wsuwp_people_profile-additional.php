<?php
// Taxonomies to leverage as expertises.
$university_categories = wp_get_post_terms( get_the_ID(), 'wsuwp_university_category' );

// CV meta.
$cv_employment       = get_post_meta( get_the_ID(), '_wsuwp_profile_employment', true );
$cv_honors           = get_post_meta( get_the_ID(), '_wsuwp_profile_honors', true );
$cv_grants           = get_post_meta( get_the_ID(), '_wsuwp_profile_grants', true );
$cv_publications     = get_post_meta( get_the_ID(), '_wsuwp_profile_publications', true );
$cv_presentations    = get_post_meta( get_the_ID(), '_wsuwp_profile_presentations', true );
$cv_teaching         = get_post_meta( get_the_ID(), '_wsuwp_profile_teaching', true );
$cv_service          = get_post_meta( get_the_ID(), '_wsuwp_profile_service', true );
$cv_responsibilities = get_post_meta( get_the_ID(), '_wsuwp_profile_responsibilities', true );
$cv_societies        = get_post_meta( get_the_ID(), '_wsuwp_profile_societies', true );
$cv_professional_dev = get_post_meta( get_the_ID(), '_wsuwp_profile_experience', true );

if ( $university_categories ||
		 has_tag() ||
		 $cv_employment ||
		 $cv_honors ||
		 $cv_grants ||
		 $cv_publications ||
		 $cv_presentations ||
		 $cv_teaching ||
		 $cv_service ||
		 $cv_responsibilities ||
		 $cv_societies ||
		 $cv_professional_dev ) :
?>

<section class="row single pad-top">

	<div class="column one wsuwp-person-additional">

		<?php if ( ( $university_categories && ! is_wp_error( $university_categories ) ) || has_tag() ) : ?>
		<dl>
			<dt>
				<h2>Expertise</h2>
			</dt>
			<dd>
				<?php
				if ( $university_categories && ! is_wp_error( $university_categories ) ) {
					echo '<dl class="categorized">';
					//echo '<dt><span class="categorized-default">Categorized</span></dt>';
					foreach ( $university_categories as $category ) {
						$category = sanitize_term( $category, 'wsuwp_university_category' );
						echo '<dd><a href="' . esc_attr( get_term_link( $category, 'wsuwp_university_category' ) ) . '">' . esc_html( $category->name ) . '</a></dd>';
					}
					//echo '</dl>';
				}

				if ( has_tag() ) {
					echo '<dl class="tagged">';
					//echo '<dt><span class="tagged-default">Tagged</span></dt>';
					foreach ( get_the_tags() as $tag ) {
						echo '<dd><a href="' . esc_attr( get_tag_link( $tag->term_id ) ) . '">' . esc_html( $tag->name ) . '</a></dd>';
					}
					echo '</dl>';
				}
				?>
			</dd>
		</dl>
		<?php endif; ?>

		<?php if ( $cv_employment || $cv_honors || $cv_grants || $cv_publications || $cv_presentations || $cv_teaching || $cv_service || $cv_responsibilities || $cv_societies || $cv_professional_dev ) : ?>
		<dl>
			<dt>
				<h2>Curriculum Vitae</h2>
			</dt>
			<dd>
			<?php
			if ( $cv_employment ) {
				echo '<h3>Employment</h3>';
				echo wp_kses_post( wpautop( $cv_employment ) );
			}

			if ( $cv_honors ) {
				echo '<h3>Honors and Awards</h3>';
				echo wp_kses_post( wpautop( $cv_honors ) );
			}

			if ( $cv_grants ) {
				echo '<h3>Grants, Contracts, and Fund Generation</h3>';
				echo wp_kses_post( wpautop( $cv_grants ) );
				echo '<p class="key">Key to indicators or description of contributions to Grants, Contracts and Fund Generation: 1 = Provided the initial idea; 2 = Developed research/program design and hypotheses; 3 = Authored or co-authored grant application; 4 = Developed and/or managed budget; 5 = Managed personnel, partnerships, and project activities.</p>';
			}

			if ( $cv_publications ) {
				echo '<h3>Publications and Creative Work</h3>';
				echo wp_kses_post( wpautop( $cv_publications ) );
				echo '<p class="key">Key to indicators or description of contributions to Publications and Creative Work: 1 = Developed the initial idea; 2 = Obtained or provided funds or other resources; 3 = Collected data; 4 = Analyzed data; 5 = Wrote/created product; 6 = Edited product.</p>';
			}

			if ( $cv_presentations ) {
				echo '<h3>Presentations</h3>';
				echo wp_kses_post( wpautop( $cv_presentations ) );
			}

			if ( $cv_teaching ) {
				echo '<h3>University Instruction</h3>';
				echo wp_kses_post( wpautop( $cv_teaching ) );
			}

			if ( $cv_service ) {
				echo '<h3>Professional Service</h3>';
				echo wp_kses_post( wpautop( $cv_service ) );
			}

			if ( $cv_responsibilities ) {
				echo '<h3>Administrative Responsibility</h3>';
				echo wp_kses_post( wpautop( $cv_responsibilities ) );
			}

			if ( $cv_societies ) {
				echo '<h3>Professional and Scholarly Organization Affiliations</h3>';
				echo wp_kses_post( wpautop( $cv_societies ) );
			}

			if ( $cv_professional_dev ) {
				echo '<h3>Professional Developlment</h3>';
				echo wp_kses_post( wpautop( $cv_professional_dev ) );
			}
			?>
			</dd>
		</dl>
		<?php endif; ?>

	</div><!--/column-->

</section>

<?php endif; ?>

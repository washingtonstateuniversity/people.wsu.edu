<?php if ( ! is_singular() ) : ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php endif; ?>

	<header class="wsuwp-person-profile-header">
		<hgroup>
		<?php if ( is_single() ) : ?>
			<?php if (  'true' === spine_get_option( 'articletitle_show' ) ) : ?>
				<h1 class="wsuwp-person-name"><?php the_title(); ?></h1>
			<?php endif; ?>
		<?php else : ?>
			<h2 class="wsuwp-person-name">
				<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
			</h2>
		<?php endif; // is_single() or in_a_relationship() ?>
		</hgroup>
	</header>

	<div class="wsuwp-person-card">

		<?php
		// Not sure where to put this yet.
		/*if ( spine_has_featured_image() && ! is_singular() ) {
			?><figure class="wsuwp-person-photo"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'spine-thumbnail_size' ); ?></a></figure><?php
		}*/

		// Meta data (excluding additional bios and C.V.).
		$degrees    = get_post_meta( get_the_ID(), '_wsuwp_profile_degree', true );
		$title      = get_post_meta( get_the_ID(), '_wsuwp_profile_ad_title', true );
		$titles     = get_post_meta( get_the_ID(), '_wsuwp_profile_title', true );
		$phone      = get_post_meta( get_the_ID(), '_wsuwp_profile_ad_phone', true );
		$phone_ext  = get_post_meta( get_the_ID(), '_wsuwp_profile_ad_phone_ext', true );
		$alt_phone  = get_post_meta( get_the_ID(), '_wsuwp_profile_alt_phone', true );
		$office     = get_post_meta( get_the_ID(), '_wsuwp_profile_ad_office', true );
		$alt_office = get_post_meta( get_the_ID(), '_wsuwp_profile_alt_office', true );
		$address    = get_post_meta( get_the_ID(), '_wsuwp_profile_ad_address', true );
		$email      = get_post_meta( get_the_ID(), '_wsuwp_profile_ad_email', true );
		$alt_email  = get_post_meta( get_the_ID(), '_wsuwp_profile_alt_email', true );
		$cv         = get_post_meta( get_the_ID(), '_wsuwp_profile_cv', true );
		$website    = get_post_meta( get_the_ID(), '_wsuwp_profile_website', true );

		// Taxonomy data.
		$departments     = wp_get_post_terms( get_the_ID(), 'wsuwp_university_org' );
		$appointments    = wp_get_post_terms( get_the_ID(), 'appointment', array( 'fields' => 'names' ) );
		$classifications = wp_get_post_terms( get_the_ID(), 'classification', array( 'fields' => 'names' ) );
		$locations       = wp_get_post_terms( get_the_ID(), 'wsuwp_university_location', array( 'fields' => 'names' ) );

		if ( is_single() && $degrees && is_array( $degrees ) ) {
			?><ul class="wsuwp-person-education">
			<?php foreach ( $degrees as $degree ) { ?>
				<li class="degree"><?php echo esc_html( $degree ); ?></li>
			<?php } ?>
			</ul><?php
		}

		if ( is_single() && $classifications && ! is_wp_error( $classifications ) ) {
			?><p class="wsuwp-person-classifications">
			<?php
			foreach ( $classifications as $classification ) {
				echo esc_html( $classification );
			}
			?>
			</p><?php
		}

		if ( $title || $titles ) {
		?><p class="wsuwp-person-position">
		<?php
		// Show user-entered title(s) if viewing full profile.
		if ( is_single() && $titles ) {
			foreach ( $titles as $additional_title ) :
				?><span class="title"><?php echo esc_html( $additional_title ); ?></span><?php
			endforeach;
		} else {
			if ( $title ) {
				echo esc_html( $title );
			}
		}
		?>
		</p><?php
		}

		if ( $departments && ! is_wp_error( $departments ) && ( is_single() || is_search() || ( is_tax() && ! is_tax( 'wsuwp_university_org' ) ) ) ) {
			?><p class="wsuwp-person-department">
			<?php
			foreach ( $departments as $department ) {
				$dept = sanitize_term( $department, 'wsuwp_university_org' );
				?><span class="department"><a href="<?php echo esc_attr( get_term_link( $dept, 'wsuwp_university_org' ) ); ?>"><?php echo esc_html( $dept->name ); ?></a></span><?php
			}
			?>
			</p><?php
		}

		if ( $email || $alt_email ) {
			?><p class="contact wsuwp-person-email"><span class="dashicons dashicons-email"></span>
			<?php
			if ( $email ) { echo '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>'; }
			if ( is_single() && $email && $alt_email ) { echo ' | '; }
			if ( is_single() && $alt_email ) { echo '<a href="mailto:' . esc_attr( $alt_email ) . '">' . esc_html( $alt_email ) . '</a>'; }
			?>
			</p><?php
		}

		if ( $phone || $alt_phone ) {
			?><p class="contact wsuwp-person-phone"><span class="dashicons dashicons-phone"></span>
			<?php
			if ( $phone ) { echo esc_html( $phone ); }
			if ( is_single() && $phone && $alt_phone ) { echo ' | '; }
			if ( is_single() && $alt_phone ) { echo esc_html( $alt_phone ); }
			?>
			</p><?php
		}

		if ( $office || $alt_office || $locations ) {
			?><p class="contact wsuwp-person-office"><span class="dashicons dashicons-location"></span>
			<?php
			if ( $office ) { echo esc_html( $office ); }
			if ( is_single() && $office && $alt_office ) { echo ' | '; }
			if ( is_single() && $alt_office ) { echo esc_html( $alt_office ); }
			if ( $address ) { echo '<span class="address">' . esc_html( $address ) . '</span>'; }
			if ( $locations && ! is_wp_error( $locations ) ) {
				foreach ( $locations as $location ) {
					echo '<span class="address">' . esc_html( $location ) . '</span>';
				}
			}
			?>
			</p><?php
		}

		// Curriculum Vitae.
		if ( is_single() && $cv ) {
			?><p class="contact cv"><span class="dashicons dashicons-download"></span><a href="<?php echo esc_url( wp_get_attachment_url( $cv ) ); ?>">Curriculum Vitae</a></p><?php
		}

		// Website.
		if ( is_single() && $website ) {
			?><p class="contact website"><span class="dashicons dashicons-external"></span><a href="<?php echo esc_url( $website ); ?>">Website</a></p><?php
		}
		?>

	</div><!-- .wsuwp-person-card -->

<?php if ( ! is_singular() ) : ?>
</article>
<?php endif; ?>

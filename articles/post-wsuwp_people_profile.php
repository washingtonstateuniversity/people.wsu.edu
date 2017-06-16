<?php
// AD data.
$ad_title = get_post_meta( get_the_ID(), '_wsuwp_profile_ad_title', true );
$ad_email = get_post_meta( get_the_ID(), '_wsuwp_profile_ad_email', true );
$ad_phone = get_post_meta( get_the_ID(), '_wsuwp_profile_ad_phone', true );
$ad_phone_ext = get_post_meta( get_the_ID(), '_wsuwp_profile_ad_phone_ext', true );
$ad_office = get_post_meta( get_the_ID(), '_wsuwp_profile_ad_office', true );
$ad_address = get_post_meta( get_the_ID(), '_wsuwp_profile_ad_address', true );

// Override data.
$alt_title = get_post_meta( get_the_ID(), '_wsuwp_profile_title', true );
$alt_email = get_post_meta( get_the_ID(), '_wsuwp_profile_alt_email', true );
$alt_phone = get_post_meta( get_the_ID(), '_wsuwp_profile_alt_phone', true );
$alt_office = get_post_meta( get_the_ID(), '_wsuwp_profile_alt_office', true );
$alt_address = get_post_meta( get_the_ID(), '_wsuwp_profile_alt_address', true );

// Additional data.
$degrees = get_post_meta( get_the_ID(), '_wsuwp_profile_degree', true );
$photos = get_post_meta( get_the_ID(), '_wsuwp_profile_photos', true );
$website = get_post_meta( get_the_ID(), '_wsuwp_profile_website', true );

// Show the override data if it exists, otherwise show the AD data.
$titles = ( $alt_title ) ? $alt_title : array( $ad_title );
$email = ( $alt_email ) ? $alt_email : $ad_email;
$office = ( $alt_office ) ? $alt_office : $ad_office;
$phone = ( $alt_phone ) ? $alt_phone : $ad_phone;
$address = ( $alt_address ) ? $alt_address : $ad_address;

// Set up additional post classes.
$classes = array( 'wsu-person' );

// Define the URL of the primary photo.
$photo_url = false;
if ( $photos && is_array( $photos ) ) {
	$photo_url = wp_get_attachment_image_src( $photos[0] )[0];
} elseif ( has_post_thumbnail() ) {
	$photo_url = get_the_post_thumbnail_url();
}

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( $classes ); ?>>

	<div class="card">

		<header>

			<?php if ( is_single() ) { ?>
			<h1 class="name"><?php the_title(); ?></h1>
			<?php } else { ?>
			<h2 class="name">
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h2>
			<?php } ?>

			<?php if ( $degrees && is_array( $degrees ) ) { ?>
				<?php foreach ( $degrees as $degree ) { ?>
				<span class="degree"><?php echo esc_html( $degree ); ?></span>
				<?php } ?>
			<?php } ?>

		</header>

		<?php if ( $photo_url ) { ?>
		<figure class="photo">
			<img src="<?php echo esc_url( $photo_url ); ?>" alt="<?php the_title(); ?>" />
		</figure>
		<?php } ?>

		<div class="contact">

			<?php if ( $titles && is_array( $titles ) ) { ?>
				<?php foreach ( $titles as $title ) { ?>
				<span class="title"><?php echo esc_html( $title ); ?></span>
				<?php } ?>
			<?php } ?>

			<span class="email"><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></span>

			<span class="phone"><?php echo esc_html( $phone ); ?></span>

			<span class="office"><?php echo esc_html( $office ); ?></span>

			<?php if ( $address ) { ?>
			<span class="address"><?php echo esc_html( $address ); ?></span>
			<?php } ?>

			<?php if ( $website ) { ?>
			<span class="website"><a href="<?php echo esc_url( $website ); ?>"><?php echo esc_html( $website ); ?></a></span>
			<?php } ?>
		</div>

	</div>

	<?php if ( is_single() ) { ?>
	<div class="about">
		<?php the_content(); ?>
	</div>
	<?php } ?>

</article>

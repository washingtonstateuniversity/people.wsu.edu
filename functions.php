<?php

add_action( 'wp_enqueue_scripts', 'people_scripts_styles', 11 );
/**
 * Enqueue child theme Scripts and Styles
 */
function people_scripts_styles() {
	if ( 'wsuwp_people_profile' === get_post_type() && is_single() ) {
		wp_enqueue_script( 'wsuwp-people-profile-script', get_stylesheet_directory_uri() . '/js/profile.js', array( 'jquery' ), '', true );
	}
}
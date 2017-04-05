<?php

require_once( dirname( __FILE__ ) . '/includes/class-wsuwp-people-directory-theme.php' );
require_once( dirname( __FILE__ ) . '/includes/class-wsuwp-people-directory-roles.php' );

add_action( 'after_setup_theme', 'WSUWP_People_Directory_Theme' );
/**
 * Starts the main class controlling the theme.
 *
 * @since 0.1.0
 *
 * @return \WSUWP_People_Directory_Theme
 */
function WSUWP_People_Directory_Theme() {
	return WSUWP_People_Directory_Theme::get_instance();
}

add_action( 'after_setup_theme', 'WSUWP_People_Directory_Roles' );
/**
 * Starts the roles and capabilities functionality.
 *
 * @since 0.1.0
 *
 * @return \WSUWP_People_Directory_Roles
 */
function WSUWP_People_Directory_Roles() {
	return WSUWP_People_Directory_Roles::get_instance();
}

/**
 * Exposes the people post type through the REST API.
 *
 * @since 0.1.0
 */
add_filter( 'wsuwp_people_show_in_rest', '__return_true' );

/**
 * Disables the display components of the people plugin.
 *
 * @since 0.1.0
 */
add_filter( 'wsuwp_people_display', '__return_false' );

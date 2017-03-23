<?php

class WSUWP_People_Directory_Theme {
	/**
	 * @since 0.1.0
	 *
	 * @var WSUWP_People_Directory_Theme
	 */
	private static $instance;

	/**
	 * @since 0.1.0
	 *
	 * @var string String used for busting cache on scripts.
	 */
	var $script_version = '0.1.0';

	/**
	 * Maintain and return the one instance and initiate hooks when
	 * called the first time.
	 *
	 * @since 0.1.0
	 *
	 * @return \WSUWP_People_Directory_Theme
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new WSUWP_People_Directory_Theme();
			self::$instance->setup_hooks();
		}
		return self::$instance;
	}
	/**
	 * Setup hooks to include.
	 *
	 * @since 0.1.0
	 */
	public function setup_hooks() {
		add_filter( 'spine_child_theme_version', array( $this, 'theme_version' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 11 );
		add_action( 'init', array( $this, 'rewrite_rules' ), 11 );
		add_filter( 'post_type_link', array( $this, 'person_permalink'), 10, 2 );
	}

	/**
	 * Provide a theme version for use in cache busting.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	public function theme_version() {
		return $this->script_version;
	}

	/**
	 * Enqueue child theme Scripts and Styles
	 *
	 * @since 0.1.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'dashicons' );
		if ( 'wsuwp_people_profile' === get_post_type() && is_single() ) {
			wp_enqueue_script( 'wsuwp-people-profile-script', get_stylesheet_directory_uri() . '/js/profile.js', array( 'jquery' ), $this->script_version, true );
		}
	}

	/**
	 * Add rewrite rules for handling people and person views.
	 *
	 * @since 0.1.0
	 */
	public function rewrite_rules() {
		add_rewrite_tag( "%wsuwp_person%", '([^/]+)', WSUWP_People_Post_Type::$post_type_slug . '=' );
		add_permastruct( 'person', "/profile/%wsuwp_person%/", false );
	}

	/**
	 * Change the permalink structure for individual people posts.
	 *
	 * @since 0.1.0
	 *
	 * @param string $url  The post URL.
	 * @param object $post The post object.
	 */
	public function person_permalink( $url, $post ) {
		if ( 'wsuwp_people_profile' === get_post_type( $post ) ) {
			$url = get_site_url() . '/profile/' . $post->post_name . '/';
		}

		return $url;
	}
}

add_action( 'after_setup_theme', 'WSUWP_People_Directory_Theme' );
/**
 * Start things up.
 *
 * @return \WSUWP_People_Directory_Theme
 */
function WSUWP_People_Directory_Theme() {
	return WSUWP_People_Directory_Theme::get_instance();
}

// Expose the people post type through the REST API.
add_filter( 'wsuwp_people_show_in_rest', '__return_true' );

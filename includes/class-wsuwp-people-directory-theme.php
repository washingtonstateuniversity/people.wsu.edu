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
	public $script_version = '0.1.1';

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
		add_action( 'init', array( $this, 'rewrite_rules' ), 11 );
		add_filter( 'post_type_link', array( $this, 'person_permalink' ), 10, 2 );
		add_filter( 'wsuwp_people_get_organization_person_data', array( $this, 'get_person_by_id' ), 10, 2 );
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
	 * Add rewrite rules for handling people and person views.
	 *
	 * @since 0.1.0
	 */
	public function rewrite_rules() {
		if ( class_exists( 'WSUWP_People_Post_Type' ) ) {
			add_rewrite_tag( '%wsuwp_person%', '([^/]+)', WSUWP_People_Post_Type::$post_type_slug . '=' );
			add_permastruct( 'person', '/profile/%wsuwp_person%/', false );
		}
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

	/**
	 * Retrieves information about a person given their WSU network ID.
	 *
	 * @since 0.1.0
	 *
	 * @param $data
	 * @param $nid
	 *
	 * @return array
	 */
	public function get_person_by_id( $data, $nid ) {
		if ( false === function_exists( 'wsuwp_get_wsu_ad_by_login' ) ) {
			return array();
		}

		// Get data from the WSUWP SSO Authentication plugin.
		$nid_data = wsuwp_get_wsu_ad_by_login( $nid );

		$return_data = array(
			'given_name' => '',
			'surname' => '',
			'title' => '',
			'office' => '',
			'street_address' => '',
			'telephone_number' => '',
			'email' => '',
			'confirm_ad_hash' => '',
		);

		if ( isset( $nid_data['givenname'][0] ) ) {
			$return_data['given_name'] = $nid_data['givenname'][0];
		}

		if ( isset( $nid_data['sn'][0] ) ) {
			$return_data['surname'] = $nid_data['sn'][0];
		}

		if ( isset( $nid_data['title'][0] ) ) {
			$return_data['title'] = $nid_data['title'][0];
		}

		if ( isset( $nid_data['physicaldeliveryofficename'][0] ) ) {
			$return_data['office'] = $nid_data['physicaldeliveryofficename'][0];
		}

		if ( isset( $nid_data['streetaddress'][0] ) ) {
			$return_data['street_address'] = $nid_data['streetaddress'][0];
		}

		if ( isset( $nid_data['telephonenumber'][0] ) ) {
			$return_data['telephone_number'] = $nid_data['telephonenumber'][0];
		}

		if ( isset( $nid_data['mail'][0] ) ) {
			$return_data['email'] = $nid_data['mail'][0];
		}

		return $return_data;
	}
}

<?php

class WSUWP_People_Directory_Roles {
	/**
	 * @since 0.1.0
	 *
	 * @var WSUWP_People_Directory_Roles
	 */
	private static $instance;

	/**
	 * @since 0.1.0
	 *
	 * @var array Names for custom VALS roles.
	 */
	private static $roles = array(
		'owner' => 'wsuwp_people_profile_owner',
		'unit_admin' => 'wsuwp_people_unit_admin',
	);

	/**
	 * Maintain and return the one instance and initiate hooks when
	 * called the first time.
	 *
	 * @since 0.1.0
	 *
	 * @return \WSUWP_People_Directory_Roles
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new WSUWP_People_Directory_Roles();
			self::$instance->setup_hooks();
		}
		return self::$instance;
	}

	/**
	 * Adds the hooks used to create and manage roles and capabilities.
	 *
	 * @since 0.1.0
	 */
	public function setup_hooks() {
		add_action( 'after_switch_theme', array( $this, 'add_roles' ) );
		add_action( 'switch_theme', array( $this, 'remove_roles' ) );
		add_action( 'init', array( $this, 'map_role_capabilities' ), 12 );
	}

	/**
	 * Adds custom roles (called on theme activation).
	 *
	 * @since 0.1.0
	 */
	public static function add_roles() {
		add_role(
			self::$roles['owner'],
			'Profile Owner',
			array(
				'edit_profiles' => true,
				'edit_published_profiles' => true,
				'read' => true,
				'upload_files' => true,
			)
		);

		add_role(
			self::$roles['unit_admin'],
			'Unit Admin',
			array(
				'create_profiles' => true,
				'edit_others_profiles' => true,
				'edit_profiles' => true,
				'edit_published_profiles' => true,
				'publish_profiles' => true,
				'read' => true,
				'upload_files' => true,
			)
		);
	}

	/**
	 * Removes custom roles (called on theme deactivation).
	 *
	 * @since 0.1.0
	 */
	public static function remove_roles() {
		remove_role( self::$roles['owner'] );
		remove_role( self::$roles['unit_admin'] );
	}

	/**
	 * Maps the custom roles' capabilities to the people post type.
	 *
	 * @since 0.1.0
	 */
	public function map_role_capabilities() {
		$user = wp_get_current_user();

		if ( empty( array_intersect( self::$roles, $user->roles ) ) ) {
			return;
		}

		$people = get_post_type_object( 'wsuwp_people_profile' );

		if ( $people ) {
			$people->cap->edit_posts = 'edit_profiles';
			$people->cap->edit_others_posts = 'edit_others_profiles';
			$people->cap->publish_posts = 'publish_profiles';
			$people->cap->edit_published_posts = 'edit_published_profiles';
			$people->cap->create_posts = 'create_profiles';
		}

		$taxonomies = get_taxonomies( array(), 'objects' );

		if ( $taxonomies ) {
			foreach ( $taxonomies as $taxonomy ) {
				$taxonomy->cap->assign_terms = 'edit_profiles';
			}
		}
	}
}

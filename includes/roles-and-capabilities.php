<?php

namespace WSU\Theme\People\Roles_And_Capabilities;

add_action( 'after_switch_theme', 'WSU\Theme\People\Roles_And_Capabilities\add_roles' );
add_action( 'switch_theme', 'WSU\Theme\People\Roles_And_Capabilities\remove_roles' );
add_action( 'init', 'WSU\Theme\People\Roles_And_Capabilities\map_role_capabilities', 12 );
add_action( 'init', 'WSU\Theme\People\Roles_And_Capabilities\register_wsu_orgs_for_users' );
add_action( 'admin_enqueue_scripts', 'WSU\Theme\People\Roles_And_Capabilities\admin_enqueue_scripts', 11 );
add_action( 'personal_options', 'WSU\Theme\People\Roles_And_Capabilities\extend_user_profile' );
add_action( 'personal_options_update', 'WSU\Theme\People\Roles_And_Capabilities\save_user_organization' );
add_action( 'edit_user_profile_update', 'WSU\Theme\People\Roles_And_Capabilities\save_user_organization' );
add_filter( 'user_has_cap', 'WSU\Theme\People\Roles_And_Capabilities\unit_administration', 10, 4 );
add_action( 'pre_get_posts', 'WSU\Theme\People\Roles_And_Capabilities\filter_list_tables' );
add_filter( 'views_edit-wsuwp_people_profile', 'WSU\Theme\People\Roles_And_Capabilities\people_views' );
add_action( 'admin_menu', 'WSU\Theme\People\Roles_And_Capabilities\profile_owner_admin_menu' );
add_action( 'parent_file', 'WSU\Theme\People\Roles_And_Capabilities\profile_owner_menu_item' );

/**
 * Returns an array of names for custom roles.
 *
 * @since 0.1.0
 *
 * @return array
 */
function roles() {
	return array(
		'owner' => 'wsuwp_people_profile_owner',
		'unit_admin' => 'wsuwp_people_unit_admin',
	);
}

/**
 * Adds custom roles (called on theme activation).
 *
 * @since 0.1.0
 */
function add_roles() {
	add_role(
		roles()['owner'],
		'Profile Owner',
		array(
			'edit_profiles' => true,
			'edit_published_profiles' => true,
			'read' => true,
			'upload_files' => true,
		)
	);

	add_role(
		roles()['unit_admin'],
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
function remove_roles() {
	remove_role( roles()['owner'] );
	remove_role( roles()['unit_admin'] );
}

/**
 * Maps the custom roles' capabilities to the people post type.
 *
 * @since 0.1.0
 */
function map_role_capabilities() {
	$user = wp_get_current_user();

	if ( empty( array_intersect( roles(), $user->roles ) ) ) {
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

/**
 * Registers the University Organizations taxonomy for the user object.
 *
 * @since 0.1.0
 */
function register_wsu_orgs_for_users() {
	register_taxonomy_for_object_type( 'wsuwp_university_org', 'user' );
}

/**
 * Enqueues stylesheets for the Edit User page.
 *
 * @since 0.1.2
 *
 * @param string $hook_suffix
 */
function admin_enqueue_scripts( $hook_suffix ) {
	if ( 'user-edit.php' !== $hook_suffix ) {
		return;
	}

	wp_enqueue_style( 'select2' );
	wp_enqueue_style( 'wsuwp-select2' );
	wp_enqueue_script( 'select2' );
	wp_enqueue_script( 'wsuwp-select2' );
}

/**
 * Adds an area to the "edit user/profile" page for associating users with a University Organization.
 *
 * @since 0.1.0
 *
 * @param object $user The user object currently being edited.
 */
function extend_user_profile( $user ) {

	// This only needs to be added for users with the Unit Admin role...
	if ( ! in_array( roles()['unit_admin'], (array) $user->roles, true ) ) {
		return;
	}

	// And only when they aren't editing their own profiles.
	if ( wp_get_current_user()->ID === $user->ID ) {
		return;
	}

	$university_organizations = get_taxonomy( 'wsuwp_university_org' );

	if ( ! current_user_can( $university_organizations->cap->assign_terms ) ) {
		return;
	}

	$dropdown_args = array(
		'class' => 'taxonomy-select2',
		'echo' => false,
		'hide_empty' => false,
		'hierarchical' => true,
		'id' => 'wsuwp_university_org',
		'name' => 'wsuwp_university_org[]',
		'taxonomy' => 'wsuwp_university_org',
	);

	wp_nonce_field( 'save-user-org', '_user_org_nonce' );

	$dropdown = wp_dropdown_categories( $dropdown_args );
	$dropdown = str_replace( '<select', '<select multiple="multiple"', $dropdown );
	$dropdown = str_replace( '&nbsp;', '', $dropdown );

	$selected_terms = wp_get_object_terms( $user->ID, 'wsuwp_university_org' );

	if ( $selected_terms && ! is_wp_error( $selected_terms ) ) {
		foreach ( $selected_terms as $term ) {
			$dropdown = str_replace( 'value="' . esc_attr( $term->term_id ) . '"', 'value="' . esc_attr( $term->term_id ) . '" selected="selected"', $dropdown );
		}
	}

	$allowed = array(
		'select' => array(
			'class' => array(),
			'name' => array(),
			'id' => array(),
			'multiple' => array(),
		),
		'option' => array(
			'class' => array(),
			'value' => array(),
			'selected' => array(),
		),
	);

	?>

	<tr>
		<th scope="row"><label for="wsuwp_university_org">Administrator For</label></th>
		<td>
			<?php echo wp_kses( $dropdown, $allowed ); ?>
		</td>
	</tr>

	<?php
}

/**
 * Adds selected terms to the user.
 *
 * @since 0.1.0
 *
 * @param int $user_id The ID of the user to save the additional data for.
 */
function save_user_organization( $user_id ) {
	if ( ! isset( $_POST['_user_org_nonce'] ) || ! wp_verify_nonce( $_POST['_user_org_nonce'], 'save-user-org' ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return;
	}

	if ( isset( $_POST['wsuwp_university_org'] ) ) {
		$terms = array_map( 'absint', $_POST['wsuwp_university_org'] );

		wp_set_object_terms( $user_id, $terms, 'wsuwp_university_org' );
		clean_object_term_cache( $user_id, 'wsuwp_university_org' );
	} else {
		wp_set_object_terms( $user_id, '', 'wsuwp_university_org' );
	}
}

/**
 * Filters a Unit Admin's ability to edit people posts.
 *
 * @param array $allcaps
 * @param array $caps
 * @param array $args
 * @param WP_User $user
 *
 * @return array
 */
function unit_administration( $allcaps, $caps, $args, $user ) {
	if ( 'edit_post' !== $args[0] ) {
		return $allcaps;
	}

	if ( ! in_array( roles()['unit_admin'], $user->roles, true ) ) {
		return $allcaps;
	}

	$terms_args = array(
		'fields' => 'ids',
	);
	$user_orgs = wp_get_object_terms( $user->ID, 'wsuwp_university_org', $terms_args );
	$post_orgs = wp_get_post_terms( $args[2], 'wsuwp_university_org', $terms_args );

	if ( empty( array_intersect( $user_orgs, $post_orgs ) ) ) {
		$allcaps['edit_others_profiles'] = false;
	}

	return $allcaps;
}

/**
 * Filters a user's view of the media library and people list table.
 *
 * @since 0.1.0
 *
 * @param WP_Query $query
 */
function filter_list_tables( $query ) {
	if ( ! is_admin() ) {
		return;
	}

	$user = wp_get_current_user();

	if ( empty( array_intersect( roles(), $user->roles ) ) ) {
		return;
	}

	// Show users with either custom role only their media.
	if ( 'attachment' === $query->query['post_type'] ) {
		$query->set( 'author', $user->ID );
	}

	if ( ! in_array( roles()['unit_admin'], $user->roles, true ) ) {
		return;
	}

	// Show Unit Admins only the people posts they share University Organizations with.
	if ( 'wsuwp_people_profile' === $query->query['post_type'] ) {
		$terms_args = array(
			'fields' => 'ids',
		);
		$user_orgs = wp_get_object_terms( $user->ID, 'wsuwp_university_org', $terms_args );

		if ( is_array( $user_orgs ) ) {
			$query->set( 'tax_query', array(
				array(
					'taxonomy' => 'wsuwp_university_org',
					'field' => 'id',
					'terms' => $user_orgs,
				),
			) );
		}
	}
}

/**
 * Modifies the list table view links for Unit Admins.
 *
 * @since 0.1.0
 *
 * @param array $views
 *
 * @return array
 */
function people_views( $views ) {
	$user = wp_get_current_user();

	if ( ! in_array( roles()['unit_admin'], $user->roles, true ) ) {
		return $views;
	}

	unset( $views['all'] );
	unset( $views['publish'] );
	unset( $views['trash'] );

	$organizations = wp_get_object_terms( $user->ID, 'wsuwp_university_org', array(
		'fields' => 'ids',
	) );

	$people_query_args = array(
		'post_type' => 'wsuwp_people_profile',
		'posts_per_page' => -1,
		'tax_query' => array(
			array(
				'taxonomy' => 'wsuwp_university_org',
				'terms' => $organizations,
			),
		),
	);

	$people = new \WP_Query( $people_query_args );
	$current = ( 1 < count( $_GET ) ) ? '' : ' class="current"'; //@codingStandardsIgnoreLine

	$views = array(
		'all' => '<a href="edit.php?post_type=wsuwp_people_profile"' . $current . '>All <span class="count">(' . $people->found_posts . ')</span></a>',
	) + $views;

	return $views;
}

/**
 * Return the post edit link for a Profile Owner's profile.
 *
 * @since 0.1.2
 *
 * @return string|false
 */
function get_wsu_profile_edit_link() {
	$user = wp_get_current_user();

	if ( ! in_array( roles()['owner'], $user->roles, true ) ) {
		return false;
	}

	$profile = get_posts( array(
		'posts_per_page' => 1,
		'post_type' => 'wsuwp_people_profile',
		'meta_key' => '_wsuwp_profile_ad_nid',
		'meta_value' => $user->user_login,
	) );

	if ( $profile ) {
		return get_edit_post_link( $profile[0]->ID, '' );
	}

	return false;
}

/**
 * Modifies the admin menu for Profile Owners.
 *
 * @since 0.1.2
 */
function profile_owner_admin_menu() {
	$profile_link = get_wsu_profile_edit_link();

	if ( $profile_link ) {
		global $menu;

		remove_menu_page( 'edit.php?post_type=wsuwp_people_profile' );

		$profiles_override = array(
			6 => array(
				'Profiles',
				'read',
				$profile_link,
				'',
				'menu-top menu-icon-wsuwp_people_profile',
				'menu-posts-wsuwp_people_profile',
				'dashicons-groups',
			),
		);

		array_splice( $menu, 2, 0, $profiles_override );
	}
}

/**
 * Sets the modified Profiles admin menu item as current
 * when a Profile Owner is editing their profile.
 *
 * @since 0.1.2
 *
 * @param string
 *
 * @return string
 */
function profile_owner_menu_item( $file ) {
	$profile_link = get_wsu_profile_edit_link();

	if ( $profile_link && 'wsuwp_people_profile' === get_current_screen()->id ) {
		return $profile_link;
	}

	return $file;
}

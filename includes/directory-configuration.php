<?php

namespace WSU\Theme\People\Directory_Configuration;

/**
 * Disables the secondary components of the people plugin.
 *
 * @since 0.1.0
 */
add_filter( 'wsuwp_people_is_main_site', '__return_true' );

add_filter( 'wsuwp_people_default_rewrite_slug', 'WSU\Theme\People\Directory_Configuration\rewrite_arguments' );
add_filter( 'wsuwp_people_get_organization_person_data', 'WSU\Theme\People\Directory_Configuration\get_person_by_id', 10, 2 );

/**
 * Filter the rewrite arguments passed to register_post_type by the people directory.
 *
 * @param array|bool $rewrite False by default. Array if previously filtered.
 *
 * @return array
 */
function rewrite_arguments( $rewrite ) {
	return array(
		'slug' => 'profile',
		'with_front' => false,
	);
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
function get_person_by_id( $data, $nid ) {
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

<?php

include_once __DIR__ . '/includes/directory-configuration.php';
include_once __DIR__ . '/includes/roles-and-capabilities.php';

add_filter( 'spine_child_theme_version', 'people_theme_version' );
/**
 * @since 0.1.0
 *
 * @var string String used for busting cache on scripts.
 */
function people_theme_version() {
	return '0.1.2';
}

<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load the source-controlled ACF schema before ACF scans local JSON paths.
 */
function tuankhang_core_acf_json_load_paths( $paths ) {
	$paths[] = TUANKHANG_CORE_PATH . 'acf-json';

	return array_values( array_unique( $paths ) );
}
add_filter( 'acf/settings/load_json', 'tuankhang_core_acf_json_load_paths' );

/**
 * Keep the site accessible if ACF is unavailable while making the dependency
 * problem visible to administrators.
 */
function tuankhang_core_acf_dependency_notice() {
	if ( function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	echo '<div class="notice notice-error"><p>'
		. esc_html__( 'Tuan Khang Core requires Advanced Custom Fields. Content types remain available, but custom fields cannot be edited until ACF is active.', 'tuankhang-core' )
		. '</p></div>';
}
add_action( 'admin_notices', 'tuankhang_core_acf_dependency_notice' );

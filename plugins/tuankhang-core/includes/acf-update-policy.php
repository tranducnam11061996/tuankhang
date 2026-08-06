<?php
/**
 * Keep the bundled ACF PRO installation pinned to the reviewed version.
 *
 * This policy deliberately lives outside ACF so the vendor package remains
 * unchanged and can still be audited or replaced manually.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TUANKHANG_CORE_ACF_UPDATE_POLICY_VERSION', '1.0.0' );
define( 'TUANKHANG_CORE_ACF_PRO_BASENAME', 'advanced-custom-fields-pro/acf.php' );

/**
 * Prevent ACF PRO from registering its remote updater and Updates screen.
 *
 * @return bool
 */
function tuankhang_core_disable_acf_pro_updates() {
	return false;
}
add_filter( 'acf/settings/show_updates', 'tuankhang_core_disable_acf_pro_updates', PHP_INT_MAX );

/**
 * Remove only ACF PRO update data while preserving updates for other plugins.
 *
 * @param mixed $transient WordPress plugin update transient.
 * @return mixed
 */
function tuankhang_core_strip_acf_pro_update_from_transient( $transient ) {
	if ( ! is_object( $transient ) ) {
		return $transient;
	}

	foreach ( array( 'response', 'no_update' ) as $bucket ) {
		if ( isset( $transient->{$bucket} ) && is_array( $transient->{$bucket} ) ) {
			unset( $transient->{$bucket}[ TUANKHANG_CORE_ACF_PRO_BASENAME ] );
		}
	}

	return $transient;
}
add_filter( 'site_transient_update_plugins', 'tuankhang_core_strip_acf_pro_update_from_transient', PHP_INT_MAX );
add_filter( 'pre_set_site_transient_update_plugins', 'tuankhang_core_strip_acf_pro_update_from_transient', PHP_INT_MAX );

/**
 * Reject automatic updates for ACF PRO without affecting other plugins.
 *
 * @param bool|null $update Whether WordPress should update the plugin.
 * @param object    $item   Plugin update data.
 * @return bool|null
 */
function tuankhang_core_disable_acf_pro_auto_update( $update, $item ) {
	$plugin = is_object( $item ) && isset( $item->plugin ) ? (string) $item->plugin : '';

	if ( TUANKHANG_CORE_ACF_PRO_BASENAME === $plugin ) {
		return false;
	}

	return $update;
}
add_filter( 'auto_update_plugin', 'tuankhang_core_disable_acf_pro_auto_update', PHP_INT_MAX, 2 );

/**
 * Keep ACF PRO out of WordPress' persisted auto-update allowlist.
 *
 * @param mixed $plugins Plugin basenames selected for automatic updates.
 * @return mixed
 */
function tuankhang_core_remove_acf_pro_from_auto_update_option( $plugins ) {
	if ( ! is_array( $plugins ) ) {
		return $plugins;
	}

	return array_values( array_diff( $plugins, array( TUANKHANG_CORE_ACF_PRO_BASENAME ) ) );
}
add_filter( 'pre_update_site_option_auto_update_plugins', 'tuankhang_core_remove_acf_pro_from_auto_update_option', PHP_INT_MAX );

/**
 * Hide the auto-update control for ACF PRO in the Plugins list table.
 *
 * @param string $html        Auto-update control markup.
 * @param string $plugin_file Plugin basename.
 * @return string
 */
function tuankhang_core_hide_acf_pro_auto_update_control( $html, $plugin_file ) {
	if ( TUANKHANG_CORE_ACF_PRO_BASENAME === $plugin_file ) {
		return '';
	}

	return $html;
}
add_filter( 'plugin_auto_update_setting_html', 'tuankhang_core_hide_acf_pro_auto_update_control', PHP_INT_MAX, 2 );

/**
 * Clear update information cached before this policy was installed.
 */
function tuankhang_core_maybe_apply_acf_update_policy() {
	$option_name = 'tuankhang_core_acf_update_policy_version';

	if ( TUANKHANG_CORE_ACF_UPDATE_POLICY_VERSION === get_option( $option_name ) ) {
		return;
	}

	$updates = get_site_transient( 'update_plugins' );
	if ( is_object( $updates ) ) {
		set_site_transient( 'update_plugins', tuankhang_core_strip_acf_pro_update_from_transient( $updates ) );
	}

	delete_transient( 'acf_plugin_updates' );
	delete_transient( 'acf_plugin_info_pro' );

	$auto_updates          = (array) get_site_option( 'auto_update_plugins', array() );
	$filtered_auto_updates = tuankhang_core_remove_acf_pro_from_auto_update_option( $auto_updates );
	if ( $filtered_auto_updates !== $auto_updates ) {
		update_site_option( 'auto_update_plugins', $filtered_auto_updates );
	}

	update_option( $option_name, TUANKHANG_CORE_ACF_UPDATE_POLICY_VERSION, false );
}
add_action( 'init', 'tuankhang_core_maybe_apply_acf_update_policy', 1 );

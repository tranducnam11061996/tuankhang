<?php
/**
 * Plugin Name:       Tuan Khang Core
 * Description:       Registers Tuan Khang content types and version-controlled ACF fields.
 * Version:           1.4.1
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Requires Plugins:  advanced-custom-fields
 * Author:            Tuan Khang
 * Text Domain:       tuankhang-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TUANKHANG_CORE_VERSION', '1.4.1' );
define( 'TUANKHANG_CORE_FILE', __FILE__ );
define( 'TUANKHANG_CORE_PATH', plugin_dir_path( __FILE__ ) );

require_once TUANKHANG_CORE_PATH . 'includes/content-types.php';
require_once TUANKHANG_CORE_PATH . 'includes/acf-schema.php';
require_once TUANKHANG_CORE_PATH . 'includes/acf-home-premium.php';
require_once TUANKHANG_CORE_PATH . 'includes/acf-product-premium.php';
require_once TUANKHANG_CORE_PATH . 'includes/sales-automation.php';
require_once TUANKHANG_CORE_PATH . 'includes/product-slug-validation.php';
require_once TUANKHANG_CORE_PATH . 'includes/comments-policy.php';
require_once TUANKHANG_CORE_PATH . 'includes/migrations.php';

register_activation_hook( TUANKHANG_CORE_FILE, 'tuankhang_core_schedule_rewrite_flush' );
register_deactivation_hook( TUANKHANG_CORE_FILE, 'tuankhang_core_schedule_rewrite_flush' );

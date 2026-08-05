<?php
/**
 * Disable comments and pingbacks independently from the active theme.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tuankhang_core_comments_are_closed() {
	return false;
}
add_filter( 'comments_open', 'tuankhang_core_comments_are_closed', 100 );
add_filter( 'pings_open', 'tuankhang_core_comments_are_closed', 100 );
add_filter( 'feed_links_show_comments_feed', 'tuankhang_core_comments_are_closed', 100 );

function tuankhang_core_hide_comment_queries( $comments ) {
	return array();
}
add_filter( 'comments_pre_query', 'tuankhang_core_hide_comment_queries', 100 );

function tuankhang_core_close_post_comment_data( $data ) {
	$data['comment_status'] = 'closed';
	$data['ping_status']    = 'closed';

	return $data;
}
add_filter( 'wp_insert_post_data', 'tuankhang_core_close_post_comment_data', 100 );

function tuankhang_core_remove_comment_support() {
	foreach ( get_post_types( array(), 'names' ) as $post_type ) {
		remove_post_type_support( $post_type, 'comments' );
		remove_post_type_support( $post_type, 'trackbacks' );
	}
}
add_action( 'init', 'tuankhang_core_remove_comment_support', 100 );

function tuankhang_core_reject_comment_submission() {
	wp_die(
		esc_html__( 'Comments are disabled on this site.', 'tuankhang-core' ),
		'',
		array( 'response' => 403 )
	);
}
add_action( 'pre_comment_on_post', 'tuankhang_core_reject_comment_submission', 1 );

function tuankhang_core_disable_pingback_methods( $methods ) {
	unset( $methods['pingback.ping'], $methods['pingback.extensions.getPingbacks'] );

	return $methods;
}
add_filter( 'xmlrpc_methods', 'tuankhang_core_disable_pingback_methods' );

function tuankhang_core_remove_pingback_header( $headers ) {
	unset( $headers['X-Pingback'] );

	return $headers;
}
add_filter( 'wp_headers', 'tuankhang_core_remove_pingback_header' );

function tuankhang_core_remove_comment_rest_routes( $endpoints ) {
	foreach ( array_keys( $endpoints ) as $route ) {
		if ( 0 === strpos( $route, '/wp/v2/comments' ) ) {
			unset( $endpoints[ $route ] );
		}
	}

	return $endpoints;
}
add_filter( 'rest_endpoints', 'tuankhang_core_remove_comment_rest_routes', 100 );

function tuankhang_core_block_comment_feeds() {
	if ( ! is_comment_feed() ) {
		return;
	}

	wp_die(
		esc_html__( 'Comment feeds are disabled on this site.', 'tuankhang-core' ),
		'',
		array( 'response' => 404 )
	);
}
add_action( 'template_redirect', 'tuankhang_core_block_comment_feeds', 1 );

function tuankhang_core_remove_comments_admin_ui() {
	remove_menu_page( 'edit-comments.php' );
	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
}
add_action( 'admin_menu', 'tuankhang_core_remove_comments_admin_ui', 100 );
add_action( 'wp_dashboard_setup', 'tuankhang_core_remove_comments_admin_ui', 100 );

function tuankhang_core_remove_comments_admin_bar_node( $admin_bar ) {
	$admin_bar->remove_node( 'comments' );
}
add_action( 'admin_bar_menu', 'tuankhang_core_remove_comments_admin_bar_node', 100 );

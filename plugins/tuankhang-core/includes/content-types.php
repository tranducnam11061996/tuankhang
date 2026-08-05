<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the product post type and its hierarchical taxonomy.
 *
 * These arguments intentionally mirror the legacy Toolset Types registration
 * so existing post IDs, URLs, menus, queries and template hierarchy continue
 * to work without a content migration.
 */
function tuankhang_core_register_content_types() {
	register_post_type(
		'san-pham',
		array(
			'labels'              => array(
				'name'               => 'Các sản phẩm',
				'singular_name'      => 'Sản phẩm',
				'menu_name'          => 'Sản phẩm',
				'name_admin_bar'     => 'Sản phẩm',
				'add_new'            => 'Nhập sản phẩm mới',
				'add_new_item'       => 'Thêm sản phẩm mới',
				'edit_item'          => 'Sửa sản phẩm',
				'new_item'           => 'Sản phẩm mới',
				'view_item'          => 'Xem sản phẩm',
				'view_items'         => 'Xem các sản phẩm',
				'search_items'       => 'Tìm kiếm sản phẩm',
				'not_found'          => 'Không tìm thấy sản phẩm',
				'not_found_in_trash' => 'Không tìm thấy sản phẩm trong Thùng rác',
				'all_items'          => 'Tất cả sản phẩm',
				'archives'           => 'Kho sản phẩm',
			),
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'show_in_rest'        => false,
			'exclude_from_search' => false,
			'query_var'           => true,
			'can_export'          => true,
			'has_archive'         => true,
			'hierarchical'        => false,
			'menu_position'       => 2,
			'menu_icon'           => 'dashicons-cart',
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
			'taxonomies'          => array( 'danh-muc' ),
			'rewrite'             => array(
				'slug'       => 'san-pham',
				'with_front' => true,
				'feeds'      => true,
				'pages'      => true,
			),
		)
	);

	register_taxonomy(
		'danh-muc',
		array( 'san-pham' ),
		array(
			'labels'            => array(
				'name'              => 'Danh mục sản phẩm',
				'singular_name'     => 'Danh mục',
				'menu_name'         => 'Danh mục sản phẩm',
				'all_items'         => 'Tất cả danh mục',
				'edit_item'         => 'Sửa danh mục',
				'view_item'         => 'Xem danh mục',
				'update_item'       => 'Cập nhật danh mục',
				'add_new_item'      => 'Thêm danh mục mới',
				'new_item_name'     => 'Tên danh mục mới',
				'parent_item'       => 'Danh mục cha',
				'parent_item_colon' => 'Danh mục cha:',
				'search_items'      => 'Tìm kiếm danh mục',
				'not_found'         => 'Không tìm thấy danh mục',
			),
			'public'            => true,
			'publicly_queryable'=> true,
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => false,
			'show_in_rest'      => false,
			'query_var'         => true,
			'rewrite'           => array(
				'slug'         => 'danh-muc',
				'with_front'   => true,
				'hierarchical' => false,
			),
		)
	);
}
add_action( 'init', 'tuankhang_core_register_content_types', 0 );

/**
 * Mark rewrite rules for a deferred flush after all rewrite filters are ready.
 */
function tuankhang_core_schedule_rewrite_flush() {
	update_option( 'tuankhang_core_flush_rewrite', '1', false );
}

/**
 * Flush only once per plugin version, after Remove Taxonomy Base Slug has run.
 */
function tuankhang_core_maybe_flush_rewrite_rules() {
	$registered_version = get_option( 'tuankhang_core_rewrite_version' );
	$scheduled          = get_option( 'tuankhang_core_flush_rewrite' );

	if ( TUANKHANG_CORE_VERSION === $registered_version && '1' !== $scheduled ) {
		return;
	}

	flush_rewrite_rules( false );
	update_option( 'tuankhang_core_rewrite_version', TUANKHANG_CORE_VERSION, false );
	delete_option( 'tuankhang_core_flush_rewrite' );
}
add_action( 'init', 'tuankhang_core_maybe_flush_rewrite_rules', 99 );

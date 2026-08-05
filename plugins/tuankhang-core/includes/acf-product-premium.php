<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add optional presentation fields for the premium product detail template.
 *
 * Fixed slots are intentional: they keep the schema compatible with ACF Free
 * while allowing editors to progressively enrich any product.
 */
function tuankhang_core_register_premium_product_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$fields = array(
		array(
			'key' => 'field_tk_product_tagline',
			'label' => 'Thông điệp ngắn',
			'name' => 'tk_product_tagline',
			'type' => 'text',
			'instructions' => 'Một câu ngắn dành cho phần mở đầu sản phẩm. Có thể để trống để website dùng dữ liệu hiện có.',
			'required' => 0,
			'wrapper' => array( 'width' => '100' ),
			'default_value' => '',
			'allow_in_bindings' => 0,
		),
	);

	for ( $index = 1; $index <= 5; $index++ ) {
		$fields[] = array(
			'key' => 'field_tk_product_gallery_image_' . $index,
			'label' => 'Ảnh thư viện ' . $index,
			'name' => 'tk_product_gallery_image_' . $index,
			'type' => 'image',
			'required' => 0,
			'return_format' => 'array',
			'preview_size' => 'thumbnail',
			'library' => 'all',
			'wrapper' => array( 'width' => '20' ),
			'allow_in_bindings' => 0,
		);
	}

	for ( $index = 1; $index <= 4; $index++ ) {
		$fields[] = array(
			'key' => 'field_tk_product_highlight_title_' . $index,
			'label' => 'Điểm nổi bật ' . $index . ' - Tiêu đề',
			'name' => 'tk_product_highlight_title_' . $index,
			'type' => 'text',
			'required' => 0,
			'wrapper' => array( 'width' => '40' ),
			'default_value' => '',
			'allow_in_bindings' => 0,
		);
		$fields[] = array(
			'key' => 'field_tk_product_highlight_description_' . $index,
			'label' => 'Điểm nổi bật ' . $index . ' - Mô tả',
			'name' => 'tk_product_highlight_description_' . $index,
			'type' => 'textarea',
			'required' => 0,
			'wrapper' => array( 'width' => '60' ),
			'rows' => 3,
			'new_lines' => '',
			'allow_in_bindings' => 0,
		);
	}

	for ( $index = 1; $index <= 8; $index++ ) {
		$fields[] = array(
			'key' => 'field_tk_product_spec_label_' . $index,
			'label' => 'Thông số ' . $index . ' - Nhãn',
			'name' => 'tk_product_spec_label_' . $index,
			'type' => 'text',
			'required' => 0,
			'wrapper' => array( 'width' => '40' ),
			'default_value' => '',
			'allow_in_bindings' => 0,
		);
		$fields[] = array(
			'key' => 'field_tk_product_spec_value_' . $index,
			'label' => 'Thông số ' . $index . ' - Giá trị',
			'name' => 'tk_product_spec_value_' . $index,
			'type' => 'text',
			'required' => 0,
			'wrapper' => array( 'width' => '60' ),
			'default_value' => '',
			'allow_in_bindings' => 0,
		);
	}

	$fields[] = array(
		'key' => 'field_tk_product_catalogue',
		'label' => 'Catalogue / tài liệu kỹ thuật',
		'name' => 'tk_product_catalogue',
		'type' => 'file',
		'required' => 0,
		'return_format' => 'array',
		'library' => 'all',
		'mime_types' => 'pdf',
		'wrapper' => array( 'width' => '50' ),
		'allow_in_bindings' => 0,
	);
	$fields[] = array(
		'key' => 'field_tk_product_video_url',
		'label' => 'Video sản phẩm',
		'name' => 'tk_product_video_url',
		'type' => 'url',
		'instructions' => 'URL YouTube hoặc Vimeo. Video chỉ tải sau khi người dùng bấm phát.',
		'required' => 0,
		'wrapper' => array( 'width' => '50' ),
		'default_value' => '',
		'allow_in_bindings' => 0,
	);

	acf_add_local_field_group(
		array(
			'key' => 'group_tk_product_premium_v1',
			'title' => 'Sản phẩm Premium',
			'fields' => $fields,
			'location' => array(
				array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'san-pham',
					),
				),
			),
			'position' => 'normal',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => array(),
			'active' => true,
			'show_in_rest' => 0,
		)
	);
}
add_action( 'acf/init', 'tuankhang_core_register_premium_product_fields' );

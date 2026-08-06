<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add optional presentation fields for the premium product detail template.
 *
 * Dynamic rows use ACF PRO repeaters so editors can add, reorder and remove
 * product presentation data without maintaining empty fixed slots.
 */
function tuankhang_core_register_premium_product_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' )
		|| ! function_exists( 'acf_get_field_type' )
		|| ! acf_get_field_type( 'repeater' ) ) {
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

	$fields[] = array(
		'key' => 'field_tk_product_gallery',
		'label' => 'Ảnh thư viện',
		'name' => 'tk_product_gallery',
		'type' => 'repeater',
		'instructions' => 'Thêm, kéo để sắp xếp hoặc xóa ảnh trong thư viện sản phẩm.',
		'required' => 0,
		'collapsed' => 'field_tk_product_gallery_image',
		'min' => 0,
		'max' => 0,
		'layout' => 'row',
		'button_label' => 'Thêm Ảnh thư viện',
		'sub_fields' => array(
			array(
				'key' => 'field_tk_product_gallery_image',
				'label' => 'Ảnh',
				'name' => 'tk_product_gallery_image',
				'type' => 'image',
				'required' => 1,
				'return_format' => 'id',
				'preview_size' => 'thumbnail',
				'library' => 'all',
				'wrapper' => array( 'width' => '100' ),
			),
		),
	);

	$fields[] = array(
		'key' => 'field_tk_product_highlights',
		'label' => 'Điểm nổi bật',
		'name' => 'tk_product_highlights',
		'type' => 'repeater',
		'instructions' => 'Thêm các giá trị nổi bật và kéo để thay đổi thứ tự hiển thị.',
		'required' => 0,
		'collapsed' => 'field_tk_product_highlight_title',
		'min' => 0,
		'max' => 0,
		'layout' => 'row',
		'button_label' => 'Thêm Đặc Điểm Nổi Bật',
		'sub_fields' => array(
			array(
				'key' => 'field_tk_product_highlight_title',
				'label' => 'Tiêu đề',
				'name' => 'tk_product_highlight_title',
				'type' => 'text',
				'required' => 1,
				'wrapper' => array( 'width' => '40' ),
				'default_value' => '',
			),
			array(
				'key' => 'field_tk_product_highlight_description',
				'label' => 'Mô tả',
				'name' => 'tk_product_highlight_description',
				'type' => 'textarea',
				'required' => 0,
				'wrapper' => array( 'width' => '60' ),
				'rows' => 3,
				'new_lines' => '',
			),
		),
	);

	$fields[] = array(
		'key' => 'field_tk_product_specs',
		'label' => 'Thông số tùy chỉnh',
		'name' => 'tk_product_specs',
		'type' => 'repeater',
		'instructions' => 'Bổ sung các thông số ngoài Model, Hãng sản xuất và Xuất xứ.',
		'required' => 0,
		'collapsed' => 'field_tk_product_spec_label',
		'min' => 0,
		'max' => 0,
		'layout' => 'table',
		'button_label' => 'Thêm Thông Số',
		'sub_fields' => array(
			array(
				'key' => 'field_tk_product_spec_label',
				'label' => 'Nhãn',
				'name' => 'tk_product_spec_label',
				'type' => 'text',
				'required' => 1,
				'wrapper' => array( 'width' => '40' ),
				'default_value' => '',
			),
			array(
				'key' => 'field_tk_product_spec_value',
				'label' => 'Giá trị',
				'name' => 'tk_product_spec_value',
				'type' => 'text',
				'required' => 1,
				'wrapper' => array( 'width' => '60' ),
				'default_value' => '',
			),
		),
	);

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
	$fields[] = array(
		'key' => 'field_tk_product_video_poster',
		'label' => 'Ảnh đại diện video',
		'name' => 'tk_product_video_poster',
		'type' => 'image',
		'instructions' => 'Ảnh ngang tỷ lệ 16:9, khuyến nghị tối thiểu 1280 × 720 px. Nếu để trống, website sẽ tự lấy thumbnail từ YouTube hoặc Vimeo.',
		'required' => 0,
		'return_format' => 'id',
		'preview_size' => 'medium',
		'library' => 'all',
		'wrapper' => array( 'width' => '50' ),
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

<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register editable copy for the premium homepage without touching legacy keys.
 */
function tuankhang_core_register_premium_home_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$fields = array();
	$add_text = static function ( $name, $label, $width = 50 ) use ( &$fields ) {
		$fields[] = array(
			'key' => 'field_' . $name,
			'label' => $label,
			'name' => $name,
			'type' => 'text',
			'required' => 0,
			'wrapper' => array( 'width' => (string) $width ),
			'default_value' => '',
			'allow_in_bindings' => 0,
		);
	};
	$add_textarea = static function ( $name, $label, $width = 100 ) use ( &$fields ) {
		$fields[] = array(
			'key' => 'field_' . $name,
			'label' => $label,
			'name' => $name,
			'type' => 'textarea',
			'required' => 0,
			'wrapper' => array( 'width' => (string) $width ),
			'rows' => 4,
			'new_lines' => '',
			'allow_in_bindings' => 0,
		);
	};
	$add_url = static function ( $name, $label, $width = 50 ) use ( &$fields ) {
		$fields[] = array(
			'key' => 'field_' . $name,
			'label' => $label,
			'name' => $name,
			'type' => 'url',
			'required' => 0,
			'wrapper' => array( 'width' => (string) $width ),
			'default_value' => '',
			'allow_in_bindings' => 0,
		);
	};

	$add_text( 'tk_home_hero_eyebrow', 'Hero - Dòng giới thiệu', 100 );
	$add_text( 'tk_home_hero_title', 'Hero - Tiêu đề', 100 );
	$add_textarea( 'tk_home_hero_description', 'Hero - Mô tả' );
	$fields[] = array(
		'key' => 'field_tk_home_hero_image',
		'label' => 'Hero - Hình ảnh',
		'name' => 'tk_home_hero_image',
		'type' => 'image',
		'required' => 0,
		'return_format' => 'array',
		'preview_size' => 'medium',
		'library' => 'all',
		'wrapper' => array( 'width' => '100' ),
		'allow_in_bindings' => 0,
	);
	$fields[] = array(
		'key' => 'field_tk_home_hero_secondary_image',
		'label' => 'Hero - Hình ảnh đội ngũ / năng lực',
		'name' => 'tk_home_hero_secondary_image',
		'type' => 'image',
		'instructions' => 'Ảnh phụ dùng trong collage hero. Nếu để trống, website dùng ảnh HAIDEC 2026 đã chọn sẵn.',
		'required' => 0,
		'return_format' => 'array',
		'preview_size' => 'medium',
		'library' => 'all',
		'wrapper' => array( 'width' => '100' ),
		'allow_in_bindings' => 0,
	);
	$add_text( 'tk_home_hero_primary_label', 'Hero - Nhãn CTA chính' );
	$add_url( 'tk_home_hero_primary_url', 'Hero - URL CTA chính' );
	$add_text( 'tk_home_hero_secondary_label', 'Hero - Nhãn CTA phụ' );
	$add_url( 'tk_home_hero_secondary_url', 'Hero - URL CTA phụ' );

	$add_text( 'tk_home_capability_eyebrow', 'Năng lực - Dòng giới thiệu', 100 );
	$add_text( 'tk_home_capability_title', 'Năng lực - Tiêu đề', 100 );
	$add_textarea( 'tk_home_capability_description', 'Năng lực - Mô tả' );
	for ( $index = 1; $index <= 3; $index++ ) {
		$add_text( 'tk_home_capability_title_' . $index, 'Năng lực ' . $index . ' - Tiêu đề', 50 );
		$add_url( 'tk_home_capability_link_' . $index, 'Năng lực ' . $index . ' - URL', 50 );
		$add_textarea( 'tk_home_capability_description_' . $index, 'Năng lực ' . $index . ' - Mô tả' );
	}

	for ( $index = 1; $index <= 5; $index++ ) {
		$add_text( 'tk_home_metric_value_' . $index, 'Số liệu ' . $index . ' - Giá trị', 34 );
		$add_text( 'tk_home_metric_suffix_' . $index, 'Số liệu ' . $index . ' - Hậu tố', 16 );
		$add_text( 'tk_home_metric_label_' . $index, 'Số liệu ' . $index . ' - Nhãn', 50 );
	}

	$add_text( 'tk_home_cta_title', 'CTA cuối trang - Tiêu đề', 100 );
	$add_textarea( 'tk_home_cta_description', 'CTA cuối trang - Mô tả' );
	$add_text( 'tk_home_cta_label', 'CTA cuối trang - Nhãn nút' );
	$add_url( 'tk_home_cta_url', 'CTA cuối trang - URL nút' );

	acf_add_local_field_group(
		array(
			'key' => 'group_tk_home_premium_v1',
			'title' => 'Homepage Premium - Nội dung thương hiệu',
			'fields' => $fields,
			'location' => array(
				array(
					array(
						'param' => 'page_template',
						'operator' => '==',
						'value' => 'page-caidat.php',
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
add_action( 'acf/init', 'tuankhang_core_register_premium_home_fields' );

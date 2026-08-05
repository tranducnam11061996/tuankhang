<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Forward a successfully emailed product consultation to an optional CRM
 * webhook. No external request is made until the endpoint is configured.
 */
function tuankhang_core_forward_product_lead( $contact_form ) {
	if ( ! is_object( $contact_form ) || ! method_exists( $contact_form, 'id' ) || 14 !== (int) $contact_form->id() ) {
		return;
	}
	if ( ! defined( 'TUANKHANG_LEAD_WEBHOOK_URL' ) || ! TUANKHANG_LEAD_WEBHOOK_URL ) {
		return;
	}
	if ( ! class_exists( 'WPCF7_Submission' ) ) {
		return;
	}

	$submission = WPCF7_Submission::get_instance();
	if ( ! $submission ) {
		return;
	}
	$posted = (array) $submission->get_posted_data();
	$product_id = isset( $posted['tk_product_id'] ) ? absint( $posted['tk_product_id'] ) : 0;
	if ( ! $product_id || 'san-pham' !== get_post_type( $product_id ) ) {
		return;
	}

	$text = static function ( $key, $limit = 500 ) use ( $posted ) {
		$value = isset( $posted[ $key ] ) && ! is_array( $posted[ $key ] ) ? sanitize_text_field( $posted[ $key ] ) : '';
		return function_exists( 'mb_substr' ) ? mb_substr( $value, 0, $limit ) : substr( $value, 0, $limit );
	};

	$payload = array(
		'event' => 'product_consultation',
		'submitted_at' => gmdate( 'c' ),
		'lead' => array(
			'fullname' => $text( 'fullname', 150 ),
			'phone' => $text( 'phone', 50 ),
			'city' => $text( 'city', 100 ),
			'message' => isset( $posted['message'] ) && ! is_array( $posted['message'] )
				? ( function_exists( 'mb_substr' )
					? mb_substr( sanitize_textarea_field( $posted['message'] ), 0, 2000 )
					: substr( sanitize_textarea_field( $posted['message'] ), 0, 2000 ) )
				: '',
		),
		'product' => array(
			'id' => $product_id,
			'title' => get_the_title( $product_id ),
			'slug' => get_post_field( 'post_name', $product_id ),
			'url' => get_permalink( $product_id ),
		),
		'attribution' => array(
			'cta_source' => $text( 'tk_cta_source', 80 ),
			'page_url' => esc_url_raw( $text( 'tk_page_url', 500 ) ),
			'utm_source' => $text( 'tk_utm_source', 100 ),
			'utm_medium' => $text( 'tk_utm_medium', 100 ),
			'utm_campaign' => $text( 'tk_utm_campaign', 150 ),
		),
	);
	$payload = apply_filters( 'tuankhang_lead_webhook_payload', $payload, $contact_form, $submission );

	$headers = array( 'Content-Type' => 'application/json; charset=utf-8' );
	if ( defined( 'TUANKHANG_LEAD_WEBHOOK_TOKEN' ) && TUANKHANG_LEAD_WEBHOOK_TOKEN ) {
		$headers['Authorization'] = 'Bearer ' . sanitize_text_field( TUANKHANG_LEAD_WEBHOOK_TOKEN );
	}

	wp_remote_post(
		esc_url_raw( TUANKHANG_LEAD_WEBHOOK_URL ),
		array(
			'headers' => $headers,
			'body' => wp_json_encode( $payload ),
			'timeout' => 2,
			'blocking' => false,
			'data_format' => 'body',
		)
	);
}
add_action( 'wpcf7_mail_sent', 'tuankhang_core_forward_product_lead', 10, 1 );

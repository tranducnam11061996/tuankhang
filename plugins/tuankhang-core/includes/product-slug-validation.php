<?php
/**
 * Prevent styled Mathematical Alphanumeric Unicode in public content titles and slugs.
 *
 * Mathematical Alphanumeric Symbols are separate Unicode code points, not font styles.
 * Keeping them in titles produces inconsistent typography and percent-encoded permalinks.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Post types protected by the Unicode content validator.
 *
 * @return string[]
 */
function tuankhang_core_unicode_validated_post_types() {
	return array( 'post', 'san-pham' );
}

/**
 * Check whether a value contains Mathematical Alphanumeric Symbols.
 *
 * @param string $value Source text.
 * @return bool
 */
function tuankhang_core_has_styled_unicode( $value ) {
	return 1 === preg_match( '/[\x{1D400}-\x{1D7FF}]/u', (string) $value );
}

/**
 * Normalize Mathematical Latin letters and digits without the intl extension.
 *
 * The ranges retain alphabet offsets even where Unicode uses a legacy Letterlike
 * Symbol code point for an individual character. Normalizer remains the primary
 * implementation and also covers Mathematical Greek symbols.
 *
 * @param string $value Source text.
 * @return string
 */
function tuankhang_core_normalize_styled_unicode_fallback( $value ) {
	if ( ! function_exists( 'mb_ord' ) || ! function_exists( 'mb_chr' ) ) {
		return (string) $value;
	}

	$uppercase_starts = array(
		0x1D400, 0x1D434, 0x1D468, 0x1D49C, 0x1D4D0, 0x1D504,
		0x1D538, 0x1D56C, 0x1D5A0, 0x1D5D4, 0x1D608, 0x1D63C, 0x1D670,
	);
	$lowercase_starts = array(
		0x1D41A, 0x1D44E, 0x1D482, 0x1D4B6, 0x1D4EA, 0x1D51E,
		0x1D552, 0x1D586, 0x1D5BA, 0x1D5EE, 0x1D622, 0x1D656, 0x1D68A,
	);
	$digit_starts = array( 0x1D7CE, 0x1D7D8, 0x1D7E2, 0x1D7EC, 0x1D7F6 );
	$characters   = preg_split( '//u', (string) $value, -1, PREG_SPLIT_NO_EMPTY );

	if ( ! is_array( $characters ) ) {
		return (string) $value;
	}

	$output = '';
	foreach ( $characters as $character ) {
		$codepoint = mb_ord( $character, 'UTF-8' );
		$replacement = null;

		foreach ( $uppercase_starts as $start ) {
			if ( $codepoint >= $start && $codepoint <= $start + 25 ) {
				$replacement = chr( 65 + $codepoint - $start );
				break;
			}
		}

		if ( null === $replacement ) {
			foreach ( $lowercase_starts as $start ) {
				if ( $codepoint >= $start && $codepoint <= $start + 25 ) {
					$replacement = chr( 97 + $codepoint - $start );
					break;
				}
			}
		}

		if ( null === $replacement ) {
			foreach ( $digit_starts as $start ) {
				if ( $codepoint >= $start && $codepoint <= $start + 9 ) {
					$replacement = chr( 48 + $codepoint - $start );
					break;
				}
			}
		}

		$output .= null === $replacement ? $character : $replacement;
	}

	return $output;
}

/**
 * Compatibility-normalize styled Unicode while preserving normal Unicode text.
 *
 * @param string $value Source text.
 * @return string
 */
function tuankhang_core_normalize_styled_unicode( $value ) {
	$value = (string) $value;

	if ( ! tuankhang_core_has_styled_unicode( $value ) ) {
		return $value;
	}

	if ( class_exists( 'Normalizer' ) ) {
		$normalized = Normalizer::normalize( $value, Normalizer::FORM_KC );
		if ( is_string( $normalized ) ) {
			return $normalized;
		}
	}

	return tuankhang_core_normalize_styled_unicode_fallback( $value );
}

/**
 * Extract the default-language title before generating a permalink.
 *
 * @param string $title Multilingual or plain title.
 * @return string
 */
function tuankhang_core_default_language_title( $title ) {
	$title = (string) $title;

	if ( function_exists( 'wpm_translate_string' ) ) {
		$language = function_exists( 'wpm_get_default_language' ) ? wpm_get_default_language() : 'vi';
		$translated = wpm_translate_string( $title, $language );
		if ( is_string( $translated ) && '' !== trim( $translated ) ) {
			return $translated;
		}
	}

	if ( preg_match( '/\[:vi\](.*?)\[:\]/su', $title, $matches ) ) {
		return $matches[1];
	}

	return preg_replace( '/\[:[a-z_-]+\]|\[:\]/iu', ' ', $title );
}

/**
 * Build an ASCII-friendly WordPress slug from a normalized display title.
 *
 * @param string $title   Display title.
 * @param int    $post_id Optional post ID used by the final fallback.
 * @return string
 */
function tuankhang_core_build_clean_content_slug( $title, $post_id = 0 ) {
	$display_title = tuankhang_core_default_language_title( $title );
	$slug          = sanitize_title( wp_strip_all_tags( $display_title ) );

	if ( '' === $slug ) {
		$slug = 'noi-dung-' . ( $post_id ? absint( $post_id ) : wp_generate_password( 8, false, false ) );
	}

	return $slug;
}

/**
 * Mark which fields were changed during the current save request.
 *
 * @param string $field Field name.
 */
function tuankhang_core_mark_unicode_normalized( $field ) {
	if ( empty( $GLOBALS['tuankhang_core_unicode_normalized'] ) || ! is_array( $GLOBALS['tuankhang_core_unicode_normalized'] ) ) {
		$GLOBALS['tuankhang_core_unicode_normalized'] = array();
	}

	$GLOBALS['tuankhang_core_unicode_normalized'][ sanitize_key( $field ) ] = true;
}

/**
 * Stop a save rather than persisting a styled code point we cannot normalize.
 *
 * This branch is a fail-safe for PHP installations without a usable Unicode
 * normalizer. The production/local runtime uses intl and should never reach it.
 */
function tuankhang_core_abort_failed_unicode_normalization() {
	wp_die(
		esc_html__( 'Không thể chuẩn hóa ký tự Unicode tạo kiểu trong tiêu đề. Vui lòng thay ký tự đó bằng chữ thông thường rồi lưu lại.', 'tuankhang-core' ),
		esc_html__( 'Tiêu đề không hợp lệ', 'tuankhang-core' ),
		array( 'response' => 422 )
	);
}

/**
 * Normalize titles and dirty slugs immediately before WordPress writes a post.
 *
 * @param array $data                Slashed, sanitized post data.
 * @param array $postarr             Slashed post array.
 * @param array $unsanitized_postarr Original slashed post array.
 * @param bool  $update              Whether this is an update.
 * @return array
 */
function tuankhang_core_validate_content_unicode( $data, $postarr, $unsanitized_postarr, $update ) {
	unset( $unsanitized_postarr, $update );

	$post_type = isset( $data['post_type'] ) ? (string) $data['post_type'] : '';
	if ( ! in_array( $post_type, tuankhang_core_unicode_validated_post_types(), true ) ) {
		return $data;
	}

	$post_id = isset( $postarr['ID'] ) ? absint( $postarr['ID'] ) : 0;
	$title   = isset( $data['post_title'] ) ? wp_unslash( (string) $data['post_title'] ) : '';
	$slug    = isset( $data['post_name'] ) ? rawurldecode( wp_unslash( (string) $data['post_name'] ) ) : '';
	$title_was_styled = tuankhang_core_has_styled_unicode( $title );
	$slug_was_styled  = tuankhang_core_has_styled_unicode( $slug );

	if ( $title_was_styled ) {
		$title = tuankhang_core_normalize_styled_unicode( $title );
		if ( tuankhang_core_has_styled_unicode( $title ) ) {
			tuankhang_core_abort_failed_unicode_normalization();
		}

		$data['post_title'] = wp_slash( $title );
		tuankhang_core_mark_unicode_normalized( 'title' );
	}

	if ( $slug_was_styled || ( $title_was_styled && '' === trim( $slug ) ) ) {
		$clean_slug  = tuankhang_core_build_clean_content_slug( $title, $post_id );
		$post_status = isset( $data['post_status'] ) ? (string) $data['post_status'] : 'draft';
		$post_parent = isset( $data['post_parent'] ) ? absint( $data['post_parent'] ) : 0;

		$data['post_name'] = wp_unique_post_slug(
			$clean_slug,
			$post_id,
			$post_status,
			$post_type,
			$post_parent
		);
		tuankhang_core_mark_unicode_normalized( 'slug' );

		if ( 'san-pham' === $post_type ) {
			$GLOBALS['tuankhang_core_product_slug_normalized'] = true;
		}
	}

	return $data;
}
add_filter( 'wp_insert_post_data', 'tuankhang_core_validate_content_unicode', 99, 4 );

/**
 * Backward-compatible styled-Unicode detector.
 *
 * @param string $value Source text.
 * @return bool
 */
function tuankhang_core_product_slug_has_styled_unicode( $value ) {
	return tuankhang_core_has_styled_unicode( $value );
}

/**
 * Backward-compatible normalizer.
 *
 * @param string $value Source text.
 * @return string
 */
function tuankhang_core_normalize_slug_unicode( $value ) {
	return tuankhang_core_normalize_styled_unicode( $value );
}

/**
 * Backward-compatible validation entry point.
 *
 * @param array $data                Slashed, sanitized post data.
 * @param array $postarr             Slashed post array.
 * @param array $unsanitized_postarr Original slashed post array.
 * @param bool  $update              Whether this is an update.
 * @return array
 */
function tuankhang_core_validate_product_slug( $data, $postarr, $unsanitized_postarr, $update ) {
	return tuankhang_core_validate_content_unicode( $data, $postarr, $unsanitized_postarr, $update );
}

/**
 * Append normalized fields to the post-save redirect for confirmation.
 *
 * @param string $location Redirect URL.
 * @param int    $post_id  Saved post ID.
 * @return string
 */
function tuankhang_core_unicode_redirect_notice( $location, $post_id ) {
	unset( $post_id );

	$fields = empty( $GLOBALS['tuankhang_core_unicode_normalized'] )
		? array()
		: array_keys( $GLOBALS['tuankhang_core_unicode_normalized'] );

	if ( empty( $fields ) ) {
		return $location;
	}

	return add_query_arg( 'tk_unicode_normalized', implode( ',', $fields ), $location );
}
add_filter( 'redirect_post_location', 'tuankhang_core_unicode_redirect_notice', 10, 2 );

/**
 * Confirm server-side normalization after an editor save.
 */
function tuankhang_core_unicode_admin_notice() {
	if ( empty( $_GET['tk_unicode_normalized'] ) ) {
		return;
	}

	$fields = array_filter( array_map( 'sanitize_key', explode( ',', wp_unslash( $_GET['tk_unicode_normalized'] ) ) ) );
	$allowed = array_intersect( $fields, array( 'title', 'slug' ) );

	if ( empty( $allowed ) ) {
		return;
	}

	if ( array( 'title', 'slug' ) === array_values( $allowed ) || ( in_array( 'title', $allowed, true ) && in_array( 'slug', $allowed, true ) ) ) {
		$message = __( 'Tiêu đề và đường dẫn đã được tự động chuyển về ký tự Unicode thông thường.', 'tuankhang-core' );
	} elseif ( in_array( 'title', $allowed, true ) ) {
		$message = __( 'Tiêu đề đã được tự động chuyển về ký tự Unicode thông thường.', 'tuankhang-core' );
	} else {
		$message = __( 'Đường dẫn đã được tự động làm sạch ký tự Unicode tạo kiểu.', 'tuankhang-core' );
	}

	echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $message ) . '</p></div>';
}
add_action( 'admin_notices', 'tuankhang_core_unicode_admin_notice' );

/**
 * Load realtime validation on classic post and product editor screens.
 *
 * @param string $hook_suffix Current admin page.
 */
function tuankhang_core_enqueue_content_unicode_validation( $hook_suffix ) {
	if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->post_type, tuankhang_core_unicode_validated_post_types(), true ) ) {
		return;
	}

	$handle = 'tuankhang-core-content-unicode-validation';
	wp_enqueue_script(
		$handle,
		plugins_url( 'assets/js/product-slug-validation.js', TUANKHANG_CORE_FILE ),
		array(),
		TUANKHANG_CORE_VERSION,
		true
	);
	wp_localize_script(
		$handle,
		'tuankhangContentUnicodeValidation',
		array(
			'warning'       => __( 'Tiêu đề hoặc đường dẫn đang chứa ký tự Unicode tạo kiểu. Hệ thống sẽ chuyển chúng về chữ thông thường trước khi lưu.', 'tuankhang-core' ),
			'titlePreview'  => __( 'Tiêu đề sau khi chuẩn hóa:', 'tuankhang-core' ),
			'slugPreview'   => __( 'Đường dẫn sạch dự kiến:', 'tuankhang-core' ),
			'normalized'    => __( 'Đã chuyển ký tự tạo kiểu về chữ thông thường.', 'tuankhang-core' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'tuankhang_core_enqueue_content_unicode_validation' );

/**
 * Backward-compatible enqueue entry point.
 *
 * @param string $hook_suffix Current admin page.
 */
function tuankhang_core_enqueue_product_slug_validation( $hook_suffix ) {
	tuankhang_core_enqueue_content_unicode_validation( $hook_suffix );
}

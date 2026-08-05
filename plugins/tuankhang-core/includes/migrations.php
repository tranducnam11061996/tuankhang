<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TUANKHANG_CORE_DB_VERSION', '1.0.0' );
define( 'TUANKHANG_CORE_CONTENT_UNICODE_DB_VERSION', '1.0.0' );
define( 'TUANKHANG_CORE_COMMENTS_DB_VERSION', '1.1.0' );

/**
 * Resolve the local ACF field-name-to-key map.
 *
 * @return array<string,string>
 */
function tuankhang_core_get_acf_field_key_map() {
	$map = array();

	if ( ! function_exists( 'acf_get_local_fields' ) ) {
		return $map;
	}

	foreach ( acf_get_local_fields() as $field ) {
		if ( ! empty( $field['name'] ) && ! empty( $field['key'] ) ) {
			$map[ $field['name'] ] = $field['key'];
		}
	}

	return $map;
}

/**
 * Convert the two legacy Toolset banner URLs to existing attachment IDs.
 *
 * The update is deliberately limited to the known values and post IDs. If an
 * attachment or local file does not match, the migration stops without being
 * marked complete so it can be retried safely.
 *
 * @return bool
 */
function tuankhang_core_migrate_banner_values() {
	$uploads = wp_get_upload_dir();
	$items   = array(
		'wpcf-anhbanner-1' => array(
			'legacy_url'    => 'https://tuankhangmedical.com/wp-content/uploads/2025/02/tong.jpg',
			'attachment_id' => 1488,
			'file'          => '2025/02/tong.jpg',
		),
		'wpcf-anhbanner-2' => array(
			'legacy_url'    => 'https://tuankhangmedical.com/wp-content/uploads/2025/04/myq.png',
			'attachment_id' => 1557,
			'file'          => '2025/04/myq.png',
		),
	);

	foreach ( $items as $meta_key => $item ) {
		$attachment = get_post( $item['attachment_id'] );
		$attached   = get_post_meta( $item['attachment_id'], '_wp_attached_file', true );
		$file_path  = trailingslashit( $uploads['basedir'] ) . $item['file'];

		if ( ! $attachment || 'attachment' !== $attachment->post_type || $item['file'] !== $attached || ! is_file( $file_path ) ) {
			error_log( sprintf( 'Tuan Khang Core migration: attachment validation failed for %s.', $meta_key ) );
			return false;
		}

		foreach ( array( 61, 1342 ) as $post_id ) {
			$current = get_post_meta( $post_id, $meta_key, true );
			if ( $item['legacy_url'] === $current ) {
				update_post_meta( $post_id, $meta_key, (string) $item['attachment_id'] );
			}
		}
	}

	return true;
}

/**
 * Add missing ACF reference meta without changing existing content values.
 *
 * @param array<string,string> $field_map Field names mapped to ACF keys.
 */
function tuankhang_core_add_acf_reference_meta( $field_map ) {
	global $wpdb;

	if ( empty( $field_map ) ) {
		return;
	}

	$meta_names   = array_keys( $field_map );
	$placeholders = implode( ',', array_fill( 0, count( $meta_names ), '%s' ) );
	$sql          = "SELECT DISTINCT pm.post_id, pm.meta_key
		FROM {$wpdb->postmeta} pm
		INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
		WHERE p.post_type <> 'revision'
		AND pm.meta_key IN ($placeholders)";
	$rows         = $wpdb->get_results( $wpdb->prepare( $sql, $meta_names ) );

	foreach ( $rows as $row ) {
		$field_key = $field_map[ $row->meta_key ];
		$reference = '_' . $row->meta_key;
		if ( $field_key !== get_post_meta( (int) $row->post_id, $reference, true ) ) {
			update_post_meta( (int) $row->post_id, $reference, $field_key );
		}
	}
}

/**
 * Run the Toolset-to-ACF compatibility migration once.
 */
function tuankhang_core_maybe_migrate() {
	if ( TUANKHANG_CORE_DB_VERSION === get_option( 'tuankhang_core_db_version' ) ) {
		return;
	}

	$field_map = tuankhang_core_get_acf_field_key_map();
	if ( empty( $field_map ) || ! tuankhang_core_migrate_banner_values() ) {
		return;
	}

	tuankhang_core_add_acf_reference_meta( $field_map );
	update_option( 'tuankhang_core_db_version', TUANKHANG_CORE_DB_VERSION, false );
}
add_action( 'acf/init', 'tuankhang_core_maybe_migrate', 20 );

/**
 * Return the reviewed permalink targets for legacy public posts.
 *
 * @return array<int,string>
 */
function tuankhang_core_content_unicode_slug_targets() {
	return array(
		646  => 'chuong-trinh-toa-dam-ket-noi-hoi-ngo-chuyen-gia-cay-ghep-implant-mien-bac',
		857  => 'dentech-china-trien-lam-thiet-bi-nha-khoa-quoc-te-trung-quoc-lan-thu-26-ngay-14-10-2023-17-10-2023',
		888  => 'ids-2023-trien-lam-nha-khoa-quoc-te-ngay-14-03-2023-18-03-2023',
		910  => 'buoi-le-ky-ket-va-chuyen-giao-cong-nghe-cay-ghep-implant-xam-lan-toi-thieu-my-q-implant-korea-giua-tuan-khang-medical-bach-quyet-dental-bs-nguyen-huy-thong',
		919  => 'hoi-thao-implant-xam-lan-toi-thieu-trong-cay-ghep-nha-khoa-hien-dai-4-0-ngay-16-08-2022',
		1187 => 'hoi-nghi-khoa-hoc-va-trien-lam-rang-ham-mat-quoc-te-videc-2024',
		1310 => 'trien-lam-quoc-te-trung-quoc-ve-thiet-bi-cong-nghe-san-pham-nha-khoa-dentech-china-2024',
	);
}

/**
 * Return the exact legacy slugs retained for backward-compatible redirects.
 *
 * @return array<int,string>
 */
function tuankhang_core_content_unicode_old_slugs() {
	return array(
		646  => 'chuong-trinh-toa-dam-ket-noi-%f0%9d%90%87%f0%9d%90%8e%cc%a3%cc%82%f0%9d%90%88-%f0%9d%90%8d%f0%9d%90%86%f0%9d%90%8e%cc%a3%cc%82-%f0%9d%90%82%f0%9d%90%87%f0%9d%90%94%f0%9d%90%98%f0%9d%90%84%cc%82',
		857  => '%f0%9d%90%83%f0%9d%90%84%f0%9d%90%8d%f0%9d%90%93%f0%9d%90%84%f0%9d%90%82%f0%9d%90%87-%f0%9d%90%82%f0%9d%90%87%f0%9d%90%88%f0%9d%90%8d%f0%9d%90%80-trien-lam-thiet-bi-nha-khoa-quoc-te-trung-quoc-lan-thu',
		888  => '%f0%9d%90%88%f0%9d%90%83%f0%9d%90%92-%f0%9d%9f%90%f0%9d%9f%8e%f0%9d%9f%90%f0%9d%9f%91-trien-lam-nha-khoa-quoc-te-ngay-14-03-2023-18-03-2023',
		910  => '%f0%9d%90%81%f0%9d%90%ae%f0%9d%90%a8%cc%82%cc%89%f0%9d%90%a2-%f0%9d%90%8b%f0%9d%90%9e%cc%82%cc%83-%f0%9d%90%8a%f0%9d%90%b2-%f0%9d%90%8a%f0%9d%90%9e%cc%82%f0%9d%90%ad-%f0%9d%90%af',
		919  => 'hoi-thao-%f0%9d%91%b0%f0%9d%92%8e%f0%9d%92%91%f0%9d%92%8d%f0%9d%92%82%f0%9d%92%8f%f0%9d%92%95-%f0%9d%91%bf%f0%9d%92%82%cc%82%f0%9d%92%8e-%f0%9d%91%b3%f0%9d%92%82%cc%82%f0%9d%92%8f',
		1187 => 'hoi-nghi-khoa-hoc-va-trien-lam-rang-ham-mat-quoc-te-%f0%9d%90%95%f0%9d%90%88%f0%9d%90%83%f0%9d%90%84%f0%9d%90%82-%f0%9d%9f%90%f0%9d%9f%8e%f0%9d%9f%90%f0%9d%9f%92',
		1310 => 'trien-lam-quoc-te-trung-quoc-ve-thiet-bi-cong-nghe-san-pham-nha-khoa-%f0%9d%90%83%f0%9d%90%84%f0%9d%90%8d%f0%9d%90%93%f0%9d%90%84%f0%9d%90%82%f0%9d%90%87-%f0%9d%90%82%f0%9d%90%87%f0%9d%90%88',
	);
}

/**
 * Load published posts and products containing styled Unicode.
 *
 * @return array<int,object>
 */
function tuankhang_core_get_public_styled_unicode_posts() {
	global $wpdb;

	$rows = $wpdb->get_results(
		"SELECT ID, post_type, post_status, post_title, post_name, post_parent
		FROM {$wpdb->posts}
		WHERE post_status = 'publish'
		AND post_type IN ('post', 'san-pham')
		ORDER BY ID"
	);

	if ( ! is_array( $rows ) ) {
		return array();
	}

	return array_values(
		array_filter(
			$rows,
			function ( $row ) {
				return tuankhang_core_has_styled_unicode( $row->post_title )
					|| tuankhang_core_has_styled_unicode( rawurldecode( $row->post_name ) );
			}
		)
	);
}

/**
 * Replace reviewed homepage links after the canonical post slugs change.
 *
 * @param array<int,array<string,string>> $changed_slugs Changed slug data.
 * @return bool
 */
function tuankhang_core_update_homepage_unicode_links( $changed_slugs ) {
	$meta_keys = array( 'duytv_news_link_3', 'duytv_news_link_4', 'duytv_news_link_5' );

	foreach ( $meta_keys as $meta_key ) {
		$current = get_post_meta( 61, $meta_key, true );
		if ( ! is_string( $current ) || '' === $current ) {
			continue;
		}

		$updated = $current;
		foreach ( $changed_slugs as $change ) {
			$old_url = home_url( '/' . $change['old_slug'] . '/' );
			$new_url = home_url( '/' . $change['new_slug'] . '/' );
			$updated = str_replace( $old_url, $new_url, $updated );
		}

		if ( $updated !== $current && false === update_post_meta( 61, $meta_key, $updated ) ) {
			return false;
		}
	}

	return true;
}

/**
 * Verify the public database state before marking the migration complete.
 *
 * @param array<int,array<string,string>> $changed_slugs Changed slug data.
 * @return bool
 */
function tuankhang_core_verify_content_unicode_migration( $changed_slugs ) {
	global $wpdb;

	$rows = $wpdb->get_results(
		"SELECT ID, post_title, post_name
		FROM {$wpdb->posts}
		WHERE post_status = 'publish'
		AND post_type IN ('post', 'san-pham')"
	);

	if ( ! is_array( $rows ) ) {
		return false;
	}

	foreach ( $rows as $row ) {
		if ( tuankhang_core_has_styled_unicode( $row->post_title )
			|| tuankhang_core_has_styled_unicode( rawurldecode( $row->post_name ) ) ) {
			return false;
		}
	}

	foreach ( $changed_slugs as $post_id => $change ) {
		$current_slug = $wpdb->get_var(
			$wpdb->prepare( "SELECT post_name FROM {$wpdb->posts} WHERE ID = %d", $post_id )
		);
		if ( $change['new_slug'] !== $current_slug ) {
			return false;
		}

		$old_slug_exists = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT meta_id FROM {$wpdb->postmeta}
				WHERE post_id = %d AND meta_key = '_wp_old_slug' AND meta_value = %s
				LIMIT 1",
				$post_id,
				$change['old_slug']
			)
		);
		if ( ! $old_slug_exists ) {
			return false;
		}
	}

	$old_urls = array();
	foreach ( $changed_slugs as $change ) {
		$old_urls[] = home_url( '/' . $change['old_slug'] . '/' );
	}

	foreach ( array( 'duytv_news_link_3', 'duytv_news_link_4', 'duytv_news_link_5' ) as $meta_key ) {
		$current = get_post_meta( 61, $meta_key, true );
		foreach ( $old_urls as $old_url ) {
			if ( is_string( $current ) && false !== strpos( $current, $old_url ) ) {
				return false;
			}
	}
	}

	return true;
}

/**
 * Normalize public titles and reviewed slugs once, independently from ACF.
 */
function tuankhang_core_maybe_migrate_content_unicode() {
	global $wpdb;

	$version_option = 'tuankhang_core_content_unicode_db_version';
	$lock_option    = 'tuankhang_core_content_unicode_migration_lock';

	if ( TUANKHANG_CORE_CONTENT_UNICODE_DB_VERSION === get_option( $version_option ) ) {
		return;
	}

	$lock_time = (int) get_option( $lock_option, 0 );
	if ( $lock_time && time() - $lock_time < 300 ) {
		return;
	}

	if ( $lock_time ) {
		delete_option( $lock_option );
	}

	if ( ! add_option( $lock_option, time(), '', false ) ) {
		return;
	}

	$slug_targets  = tuankhang_core_content_unicode_slug_targets();
	$old_slugs     = tuankhang_core_content_unicode_old_slugs();
	$changed_slugs = array();
	$success       = false;

	$wpdb->query( 'START TRANSACTION' );

	try {
		$rows = tuankhang_core_get_public_styled_unicode_posts();

		foreach ( $rows as $row ) {
			$post_id     = (int) $row->ID;
			$clean_title = tuankhang_core_normalize_styled_unicode( $row->post_title );

			if ( tuankhang_core_has_styled_unicode( $clean_title ) ) {
				throw new RuntimeException( 'Title normalization failed for post ' . $post_id . '.' );
			}

			$update = array( 'post_title' => $clean_title );
			$format = array( '%s' );

			if ( false === $wpdb->update( $wpdb->posts, $update, array( 'ID' => $post_id ), $format, array( '%d' ) ) ) {
				throw new RuntimeException( 'Database update failed for post ' . $post_id . ': ' . $wpdb->last_error );
			}

			clean_post_cache( $post_id );
		}

		foreach ( $slug_targets as $post_id => $new_slug ) {
			if ( ! isset( $old_slugs[ $post_id ] ) ) {
				throw new RuntimeException( 'No reviewed legacy slug exists for post ' . $post_id . '.' );
			}

			$post = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT ID, post_type, post_status, post_name, post_parent FROM {$wpdb->posts} WHERE ID = %d",
					$post_id
				)
			);
			if ( ! $post || 'post' !== $post->post_type || 'publish' !== $post->post_status ) {
				throw new RuntimeException( 'Reviewed public post is unavailable: ' . $post_id . '.' );
			}

			$old_slug = $old_slugs[ $post_id ];
			if ( $old_slug === $post->post_name ) {
				$unique_slug = wp_unique_post_slug(
					$new_slug,
					$post_id,
					$post->post_status,
					$post->post_type,
					(int) $post->post_parent
				);
				if ( $new_slug !== $unique_slug ) {
					throw new RuntimeException( 'Reviewed slug is no longer unique for post ' . $post_id . '.' );
				}

				if ( false === $wpdb->update(
					$wpdb->posts,
					array( 'post_name' => $new_slug ),
					array( 'ID' => $post_id ),
					array( '%s' ),
					array( '%d' )
				) ) {
					throw new RuntimeException( 'Slug update failed for post ' . $post_id . ': ' . $wpdb->last_error );
				}
			} elseif ( $new_slug !== $post->post_name ) {
				throw new RuntimeException( 'Post ' . $post_id . ' has an unexpected permalink.' );
			}

			if ( ! in_array( $old_slug, get_post_meta( $post_id, '_wp_old_slug', false ), true )
				&& false === add_post_meta( $post_id, '_wp_old_slug', $old_slug, false ) ) {
				throw new RuntimeException( 'Could not preserve the old slug for post ' . $post_id . '.' );
			}

			$changed_slugs[ $post_id ] = array(
				'old_slug' => $old_slug,
				'new_slug' => $new_slug,
			);
			clean_post_cache( $post_id );
		}

		if ( ! tuankhang_core_update_homepage_unicode_links( $changed_slugs ) ) {
			throw new RuntimeException( 'Could not update reviewed homepage links.' );
		}

		if ( ! tuankhang_core_verify_content_unicode_migration( $changed_slugs ) ) {
			throw new RuntimeException( 'Post-migration Unicode verification failed.' );
		}

		$wpdb->query( 'COMMIT' );
		$success = true;
	} catch ( Throwable $error ) {
		$wpdb->query( 'ROLLBACK' );
		error_log( 'Tuan Khang Core content Unicode migration: ' . $error->getMessage() );
	}

	delete_option( $lock_option );

	if ( $success ) {
		update_option( $version_option, TUANKHANG_CORE_CONTENT_UNICODE_DB_VERSION, false );
	}
}
add_action( 'init', 'tuankhang_core_maybe_migrate_content_unicode', 5 );

/**
 * Close comments and pingbacks without deleting historical comment rows.
 */
function tuankhang_core_maybe_migrate_comments_policy() {
	global $wpdb;

	$version_option = 'tuankhang_core_comments_db_version';
	$lock_option    = 'tuankhang_core_comments_migration_lock';

	if ( TUANKHANG_CORE_COMMENTS_DB_VERSION === get_option( $version_option ) ) {
		return;
	}

	$lock_time = (int) get_option( $lock_option, 0 );
	if ( $lock_time && time() - $lock_time < 300 ) {
		return;
	}

	if ( $lock_time ) {
		delete_option( $lock_option );
	}

	if ( ! add_option( $lock_option, time(), '', false ) ) {
		return;
	}

	$option_values = array(
		'default_comment_status' => 'closed',
		'default_ping_status'    => 'closed',
		'default_pingback_flag'  => '0',
	);
	$comments_before = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->comments}" );
	$success         = false;

	$wpdb->query( 'START TRANSACTION' );

	try {
		foreach ( $option_values as $option_name => $option_value ) {
			update_option( $option_name, $option_value );
		}

		$updated = $wpdb->query(
			"UPDATE {$wpdb->posts}
			SET comment_status = 'closed', ping_status = 'closed'
			WHERE comment_status <> 'closed' OR ping_status <> 'closed'"
		);

		if ( false === $updated ) {
			throw new RuntimeException( 'Could not close existing post comments: ' . $wpdb->last_error );
		}

		foreach ( $option_values as $option_name => $option_value ) {
			if ( $option_value !== (string) get_option( $option_name ) ) {
				throw new RuntimeException( 'Could not update option ' . $option_name . '.' );
			}
		}

		$open_posts     = (int) $wpdb->get_var(
			"SELECT COUNT(*) FROM {$wpdb->posts}
			WHERE comment_status <> 'closed' OR ping_status <> 'closed'"
		);
		$comments_after = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->comments}" );

		if ( 0 !== $open_posts ) {
			throw new RuntimeException( 'Post comment statuses remain open after migration.' );
		}

		if ( $comments_before !== $comments_after ) {
			throw new RuntimeException( 'Historical comment count changed during migration.' );
		}

		if ( false === $wpdb->query( 'COMMIT' ) ) {
			throw new RuntimeException( 'Could not commit the comments policy migration.' );
		}

		$success = true;
	} catch ( Throwable $error ) {
		$wpdb->query( 'ROLLBACK' );
		error_log( 'Tuan Khang Core comments migration: ' . $error->getMessage() );
	}

	delete_option( $lock_option );
	wp_cache_delete( 'alloptions', 'options' );
	wp_cache_delete( 'notoptions', 'options' );
	foreach ( array_keys( $option_values ) as $option_name ) {
		wp_cache_delete( $option_name, 'options' );
	}

	if ( $success ) {
		update_option( $version_option, TUANKHANG_CORE_COMMENTS_DB_VERSION, false );
	}
}
add_action( 'init', 'tuankhang_core_maybe_migrate_comments_policy', 6 );

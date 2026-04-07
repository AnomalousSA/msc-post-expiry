<?php
/**
 * Uninstall MSC Post Expiry.
 *
 * @package MSCPE
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'mscpe_options' );

// Unschedule cron events.
global $mscpe_hook;
$mscpe_hook = 'mscpe_process_expired_posts';
$mscpe_next = wp_next_scheduled( $mscpe_hook );

while ( $mscpe_next ) {
	wp_unschedule_event( $mscpe_next, $mscpe_hook );
	$mscpe_next = wp_next_scheduled( $mscpe_hook );
}

// Delete post meta data.
global $wpdb;
global $mscpe_pattern;
$mscpe_pattern = $wpdb->esc_like( 'mscpe_' ) . '%';
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE %s",
		$mscpe_pattern
	)
);

<?php
/**
 * Uninstall MSC Post Expiry.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'mscpe_options' );

if ( true ) {
	$hook = 'msc-post-expiry_cron_event';
	$next = wp_next_scheduled( $hook );

	while ( $next ) {
		wp_unschedule_event( $next, $hook );
		$next = wp_next_scheduled( $hook );
	}
}

if ( true ) {
	global $wpdb;
	$pattern = $wpdb->esc_like( 'msc-post-expiry_' ) . '%';
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE %s",
			$pattern
		)
	);
}

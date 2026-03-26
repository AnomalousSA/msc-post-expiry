<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

delete_option( 'mscpe_options' );

global $wpdb;
$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key IN ('_mscpe_expiry_timestamp','_mscpe_expiry_processed','_mscpe_expiry_log')" );

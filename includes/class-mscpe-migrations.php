<?php
/**
 * Database migrations for MSC Post Expiry.
 *
 * Handles creation and versioning of custom tables.
 *
 * @package MSCPE
 */

namespace MSCPE;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manages database migrations for MSC Post Expiry.
 */
class Migrations {

	/**
	 * Current migration version.
	 */
	const MIGRATION_VERSION = '1.2.0';

	/**
	 * Option key for tracking migration version.
	 */
	const VERSION_OPTION = 'mscpe_db_version';

	/**
	 * Runs migrations if needed.
	 *
	 * @return void
	 */
	public static function run_migrations() {
		$current_version = get_option( self::VERSION_OPTION, '0.0.0' );

		if ( version_compare( $current_version, self::MIGRATION_VERSION, '>=' ) ) {
			return;
		}

		self::create_rules_table();
		self::create_analytics_table();

		update_option( self::VERSION_OPTION, self::MIGRATION_VERSION );
	}

	/**
	 * Creates the rules table.
	 *
	 * @return void
	 */
	private static function create_rules_table() {
		global $wpdb;

		$table_name      = $wpdb->prefix . 'mscpe_rules';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table_name} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			name varchar(255) NOT NULL DEFAULT '',
			description text NOT NULL,
			enabled tinyint(1) NOT NULL DEFAULT 1,
			condition_type varchar(50) NOT NULL DEFAULT '',
			condition_config longtext NOT NULL,
			action_type varchar(50) NOT NULL DEFAULT '',
			action_config longtext NOT NULL,
			created_at bigint(20) unsigned NOT NULL DEFAULT 0,
			updated_at bigint(20) unsigned NOT NULL DEFAULT 0,
			PRIMARY KEY (id),
			KEY enabled (enabled),
			KEY condition_type (condition_type)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Creates the analytics table.
	 *
	 * @return void
	 */
	private static function create_analytics_table() {
		global $wpdb;

		$table_name      = $wpdb->prefix . 'mscpe_analytics';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table_name} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			post_id bigint(20) unsigned NOT NULL DEFAULT 0,
			action varchar(50) NOT NULL DEFAULT '',
			category_id bigint(20) unsigned NOT NULL DEFAULT 0,
			author_id bigint(20) unsigned NOT NULL DEFAULT 0,
			views_before_expiry int(11) NOT NULL DEFAULT 0,
			age_days int(11) NOT NULL DEFAULT 0,
			status varchar(20) NOT NULL DEFAULT 'success',
			created_at bigint(20) unsigned NOT NULL DEFAULT 0,
			PRIMARY KEY (id),
			KEY post_id (post_id),
			KEY action (action),
			KEY category_id (category_id),
			KEY author_id (author_id),
			KEY created_at (created_at),
			KEY status (status)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Drops all custom tables (used on uninstall).
	 *
	 * @return void
	 */
	public static function drop_tables() {
		global $wpdb;

		$tables = array(
			$wpdb->prefix . 'mscpe_rules',
			$wpdb->prefix . 'mscpe_analytics',
		);

		foreach ( $tables as $table ) {
			$wpdb->query( "DROP TABLE IF EXISTS {$table}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}

		delete_option( self::VERSION_OPTION );
	}
}

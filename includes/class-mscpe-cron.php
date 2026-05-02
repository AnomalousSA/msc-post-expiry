<?php
/**
 * Cron processing class for MSC Post Expiry.
 *
 * @package MSCPE
 */

namespace MSCPE;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles scheduled post expiry processing via WordPress cron.
 */
class Cron {

	const CRON_HOOK          = 'mscpe_process_expired_posts';
	const CRON_INTERVAL      = 300; // 5 minutes in seconds.
	const LOG_DIR            = 'msc-post-expiry-logs';
	const LOG_RETENTION_DAYS = 30;

	/**
	 * Main plugin instance.
	 *
	 * @var Plugin
	 */
	private $plugin;

	/**
	 * Constructor.
	 *
	 * @param Plugin $plugin Plugin instance.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		// Hook the cron handler.
		add_action( self::CRON_HOOK, array( $this, 'process_expired_posts' ) );
	}

	/**
	 * Register the cron event on plugin activation.
	 */
	public function register_cron_event() {
		// Only register if not already scheduled.
		if ( wp_next_scheduled( self::CRON_HOOK ) ) {
			return;
		}

		wp_schedule_event( time(), 'mscpe_5min', self::CRON_HOOK );
	}

	/**
	 * Unregister the cron event on plugin deactivation.
	 */
	public function unregister_cron_event() {
		$timestamp = wp_next_scheduled( self::CRON_HOOK );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, self::CRON_HOOK );
		}
	}

	/**
	 * Process expired posts.
	 *
	 * This is the main cron handler that finds and processes all expired posts.
	 */
	public function process_expired_posts() {
		$this->log_event( '=== Cron job started at ' . current_time( 'Y-m-d H:i:s' ) . ' ===' );

		// Check if module is enabled.
		$module_enabled = $this->plugin->get_option( 'module_enabled', 1 );
		$this->log_event( 'Module enabled value: ' . wp_json_encode( $module_enabled ) . ' (type: ' . gettype( $module_enabled ) . ')' );

		// Handle both integer 1 and string '1' as enabled.
		$is_enabled = ! empty( $module_enabled ) || $module_enabled === '1' || $module_enabled === 1;
		if ( ! $is_enabled ) {
			$this->log_event( 'Module is disabled, skipping cron processing.' );
			return;
		}

		/**
		 * Fires before processing expired posts.
		 */
		do_action( 'mscpe_before_process_expired_posts' );

		$expired_posts   = $this->get_expired_posts();
		$processed_count = 0;

		$this->log_event( 'Found ' . count( $expired_posts ) . ' expired posts to process.' );

		if ( empty( $expired_posts ) ) {
			$this->log_event( 'No expired posts found.' );
			do_action( 'mscpe_after_process_expired_posts', 0 );
			$this->cleanup_old_logs();
			return;
		}

		$expiry_action = (string) $this->plugin->get_option( 'expiry_action', 'trash' );
		$this->log_event( 'Expiry action: ' . $expiry_action );

		foreach ( $expired_posts as $post_id ) {
			$this->log_event( 'Processing post ID: ' . $post_id );
			$result = $this->expire_post( $post_id, $expiry_action );
			if ( $result ) {
				++$processed_count;
				$this->log_event( 'Successfully expired post ID: ' . $post_id );
			} else {
				$this->log_event( 'Failed to expire post ID: ' . $post_id );
			}
		}

		$this->log_event( sprintf( 'Processed %d expired posts.', $processed_count ) );

		/**
		 * Fires after processing expired posts.
		 *
		 * @param int $processed_count Number of posts processed.
		 */
		do_action( 'mscpe_after_process_expired_posts', $processed_count );

		// Clean up old logs.
		$this->cleanup_old_logs();
	}

	/**
	 * Get all posts that have expired.
	 *
	 * Uses PHP-based datetime comparison for reliable time-zone aware
	 * and time-only expiry comparisons.
	 *
	 * @return array<int> Array of post IDs.
	 */
	private function get_expired_posts() {
		global $wpdb;

		$this->log_event( '=== Starting get_expired_posts() ===' );

		// Skip cache during debugging to ensure fresh results.
		$use_cache = ! ( defined( 'WP_DEBUG' ) && WP_DEBUG );
		$cache_key = 'mscpe_expired_posts_' . current_time( 'Y-m-d-H' );

		if ( $use_cache ) {
			$cached_results = wp_cache_get( $cache_key, 'mscpe' );
			if ( false !== $cached_results ) {
				$this->log_event( 'Returning cached results: ' . count( $cached_results ) . ' posts' );
				return $cached_results;
			}
		}

		// Get current timestamp (WordPress timezone-aware).
		$current_timestamp = current_time( 'timestamp' );
		$current_datetime = current_time( 'Y-m-d H:i:s' );

		$this->log_event( 'Current timestamp: ' . $current_timestamp . ' | Datetime: ' . $current_datetime );

		// Get configured post types.
		$post_types     = (array) $this->plugin->get_option( 'post_types', array( 'post', 'page' ) );
		$post_type_mode = (string) $this->plugin->get_option( 'post_type_mode', 'include' );

		$this->log_event( 'Post types config: ' . implode( ', ', $post_types ) . ' | Mode: ' . $post_type_mode );

		// Determine which post types to query.
		$all_post_types = get_post_types( array( 'public' => true ) );
		if ( 'include' === $post_type_mode ) {
			$target_post_types = $post_types;
		} else {
			$target_post_types = array_diff( $all_post_types, $post_types );
		}

		$this->log_event( 'Target post types: ' . implode( ', ', (array) $target_post_types ) );

		if ( empty( $target_post_types ) ) {
			$this->log_event( 'No target post types, returning empty array.' );
			return array();
		}

		// Sanitize post types.
		$target_post_types = array_map( 'sanitize_key', (array) $target_post_types );

		// Collect all expired post IDs across all target post types.
		$all_expired_posts = array();

		foreach ( $target_post_types as $post_type ) {
			$this->log_event( 'Querying post type: ' . $post_type );

			// Query posts that have expiry_date meta set.
			// We do PHP-based datetime comparison for reliable time comparisons.
			$post_ids = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$wpdb->prepare(
					"
					SELECT DISTINCT p.ID
					FROM {$wpdb->posts} p
					INNER JOIN {$wpdb->postmeta} pm_date ON p.ID = pm_date.post_id AND pm_date.meta_key = 'mscpe_expiry_date'
					LEFT JOIN {$wpdb->postmeta} pm_time ON p.ID = pm_time.post_id AND pm_time.meta_key = 'mscpe_expiry_time'
					WHERE p.post_type = %s
					AND p.post_status = 'publish'
					LIMIT 200
					",
					$post_type
				)
			);

			$this->log_event( 'Query for ' . $post_type . ' returned: ' . count( $post_ids ) . ' posts (before PHP filtering)' );

			if ( ! empty( $post_ids ) ) {
				// Filter posts in PHP to properly compare datetimes.
				foreach ( $post_ids as $post_id ) {
					$expiry_date = get_post_meta( $post_id, 'mscpe_expiry_date', true );
					$expiry_time = get_post_meta( $post_id, 'mscpe_expiry_time', true );

					// Build full expiry datetime.
					$expiry_time_value = $expiry_time ? $expiry_time : '00:00:00';
					$expiry_datetime   = $expiry_date . ' ' . $expiry_time_value;

					// Convert to timestamp for comparison.
					$expiry_timestamp = strtotime( $expiry_datetime );

					$this->log_event( '  Checking post ID: ' . $post_id . ' | Expiry: ' . $expiry_datetime . ' | Expiry ts: ' . $expiry_timestamp . ' | Current ts: ' . $current_timestamp );

					// Check if expiry timestamp is in the past or now.
					if ( $expiry_timestamp && $expiry_timestamp <= $current_timestamp ) {
						$all_expired_posts[] = $post_id;
						$this->log_event( '    -> POST IS EXPIRED (ts <= current)', $expiry_timestamp );
					}
				}
			}
		}

		// Convert to integers and remove duplicates.
		$all_expired_posts = array_unique( array_map( 'intval', $all_expired_posts ) );

		$this->log_event( 'Total expired posts after PHP filtering and dedup: ' . count( $all_expired_posts ) );

		// Cache results for 1 hour (only if not debugging).
		if ( $use_cache ) {
			wp_cache_set( $cache_key, $all_expired_posts, 'mscpe', HOUR_IN_SECONDS );
		}

		return $all_expired_posts;
	}

	/**
	 * Expire a single post.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $action Expiry action (trash, delete, draft).
	 * @return bool True if successful, false otherwise.
	 */
	private function expire_post( $post_id, $action ) {
		$this->log_event( '--- expire_post() called for post ID: ' . $post_id . ' with action: ' . $action );

		$post = get_post( $post_id );

		if ( ! $post ) {
			$this->log_event( 'Post ID ' . $post_id . ' not found (get_post returned null).' );
			return false;
		}

		$this->log_event( 'Post found: ' . $post->post_title . ' | Status: ' . $post->post_status );

		/**
		 * Fires before expiring a post.
		 *
		 * @param int    $post_id Post ID.
		 * @param string $action Expiry action.
		 */
		do_action( 'mscpe_before_expire_post', $post_id, $action );

		$result = false;

		switch ( $action ) {
			case 'trash':
				$this->log_event( 'Executing wp_trash_post() for post ID: ' . $post_id );
				$result = wp_trash_post( $post_id );
				$this->log_event( 'wp_trash_post() result: ' . wp_json_encode( $result ) );
				break;

			case 'delete':
				$this->log_event( 'Executing wp_delete_post() with delete=true for post ID: ' . $post_id );
				$result = wp_delete_post( $post_id, true );
				$this->log_event( 'wp_delete_post() result: ' . wp_json_encode( $result ) );
				break;

			case 'draft':
				$this->log_event( 'Executing wp_update_post() to set draft for post ID: ' . $post_id );
				$result = wp_update_post(
					array(
						'ID'          => $post_id,
						'post_status' => 'draft',
					)
				);
				$this->log_event( 'wp_update_post() to draft result: ' . wp_json_encode( $result ) );
				break;

			case 'private':
				$this->log_event( 'Executing wp_update_post() to set private for post ID: ' . $post_id );
				$result = wp_update_post(
					array(
						'ID'          => $post_id,
						'post_status' => 'private',
					)
				);
				$this->log_event( 'wp_update_post() to private result: ' . wp_json_encode( $result ) );
				break;

			case 'category':
				$this->log_event( 'Executing category action for post ID: ' . $post_id );
				if ( 'post' !== get_post_type( $post_id ) ) {
					$this->log_event( 'Category action only works for posts, but post type is: ' . get_post_type( $post_id ) );
					return false;
				}
				$cat = absint( $this->plugin->get_option( 'expiry_category', 0 ) );
				$this->log_event( 'Expiry category ID: ' . $cat );
				$result = $cat > 0 && false !== wp_set_post_categories( $post_id, array( $cat ), false ) ? $post_id : false;
				$this->log_event( 'wp_set_post_categories() result: ' . wp_json_encode( $result ) );
				break;

			default:
				$this->log_event( 'Invalid action "' . $action . '" for post ID ' . $post_id . '.' );
				return false;
		}

		if ( $result ) {
			// Delete post meta after successful expiry.
			delete_post_meta( $post_id, 'mscpe_expiry_date' );
			delete_post_meta( $post_id, 'mscpe_expiry_time' );

			$this->log_event( 'Post ID ' . $post_id . ' expired successfully with action "' . $action . '". Meta cleared.' );

			/**
			 * Fires after expiring a post.
			 *
			 * @param int    $post_id Post ID.
			 * @param string $action Expiry action.
			 * @param mixed  $result Result of the action.
			 */
			do_action( 'mscpe_after_expire_post', $post_id, $action, $result );

			return true;
		}

		$this->log_event( 'Failed to expire post ID ' . $post_id . ' with action "' . $action . '".' );
		return false;
	}

	/**
	 * Log an event.
	 *
	 * Uses WP_Filesystem for reliable file operations.
	 *
	 * @param string $message Log message.
	 */
	private function log_event( $message ) {
		if ( ! (bool) apply_filters( 'mscpe_enable_logging', true ) ) {
			return;
		}

		$log_dir  = wp_upload_dir();
		$log_path = $log_dir['basedir'] . '/' . self::LOG_DIR;

		// Use WP_Filesystem for all file operations.
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
		}

		if ( empty( $wp_filesystem ) ) {
			return;
		}

		// Create log directory if it doesn't exist.
		if ( ! $wp_filesystem->is_dir( $log_path ) ) {
			$wp_filesystem->mkdir( $log_path, 0755 );

			// Copy index.php for security.
			$index_source = MSCPE_PLUGIN_DIR . 'includes/index-log.php';
			$index_dest   = $log_path . '/index.php';
			if ( $wp_filesystem->exists( $index_source ) ) {
				$wp_filesystem->copy( $index_source, $index_dest );
			}
		}

		$log_file  = $log_path . '/msc-post-expiry.log';
		$timestamp = current_time( 'Y-m-d H:i:s' );
		$log_entry = sprintf( "[%s] %s\n", $timestamp, $message );

		// Read existing content.
		$existing_content = $wp_filesystem->exists( $log_file ) ? $wp_filesystem->get_contents( $log_file ) : '';
		if ( false === $existing_content ) {
			$existing_content = '';
		}

		// Append new content.
		$new_content = $existing_content . $log_entry;

		// Write back.
		$wp_filesystem->put_contents( $log_file, $new_content, FS_CHMOD_FILE );
	}

	/**
	 * Clean up old log files.
	 */
	private function cleanup_old_logs() {
		$log_dir  = wp_upload_dir();
		$log_path = $log_dir['basedir'] . '/' . self::LOG_DIR;

		if ( ! is_dir( $log_path ) ) {
			return;
		}

		$retention_days = (int) apply_filters( 'mscpe_cron_log_retention_days', self::LOG_RETENTION_DAYS );
		$cutoff_time    = time() - ( $retention_days * DAY_IN_SECONDS );

		// Use WP_Filesystem for file operations.
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
		}

		if ( empty( $wp_filesystem ) ) {
			return;
		}

		$files = $wp_filesystem->dirlist( $log_path );

		if ( ! $files ) {
			return;
		}

		foreach ( $files as $file_name => $file_info ) {
			if ( '.' === $file_name || '..' === $file_name ) {
				continue;
			}

			$file_path = $log_path . '/' . $file_name;

			// Check if file is older than retention period.
			if ( 'f' === $file_info['type'] ) {
				$file_time = filemtime( $file_path );
				if ( $file_time && $file_time < $cutoff_time ) {
					wp_delete_file( $file_path );
				}
			}
		}
	}
}

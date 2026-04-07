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
		// Check if module is enabled.
		if ( ! $this->plugin->get_option( 'module_enabled', 1 ) ) {
			return;
		}

		/**
		 * Fires before processing expired posts.
		 */
		do_action( 'mscpe_before_process_expired_posts' );

		$expired_posts   = $this->get_expired_posts();
		$processed_count = 0;

		if ( empty( $expired_posts ) ) {
			$this->log_event( 'No expired posts found.' );
			do_action( 'mscpe_after_process_expired_posts', 0 );
			return;
		}

		$expiry_action = (string) $this->plugin->get_option( 'expiry_action', 'trash' );

		foreach ( $expired_posts as $post_id ) {
			$result = $this->expire_post( $post_id, $expiry_action );
			if ( $result ) {
				++$processed_count;
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
	 * @return array<int> Array of post IDs.
	 */
	private function get_expired_posts() {
		global $wpdb;

		// Check cache first.
		$cache_key      = 'mscpe_expired_posts_' . current_time( 'Y-m-d-H' );
		$cached_results = wp_cache_get( $cache_key, 'mscpe' );
		if ( false !== $cached_results ) {
			return $cached_results;
		}

		// Get current date and time.
		$current_datetime                    = current_time( 'mysql' );
		list( $current_date, $current_time ) = explode( ' ', $current_datetime );

		// Get configured post types.
		$post_types     = (array) $this->plugin->get_option( 'post_types', array( 'post', 'page' ) );
		$post_type_mode = (string) $this->plugin->get_option( 'post_type_mode', 'include' );

		// Determine which post types to query.
		$all_post_types = get_post_types( array( 'public' => true ) );
		if ( 'include' === $post_type_mode ) {
			$target_post_types = $post_types;
		} else {
			$target_post_types = array_diff( $all_post_types, $post_types );
		}

		if ( empty( $target_post_types ) ) {
			return array();
		}

		// Sanitize post types.
		$target_post_types = array_map( 'sanitize_key', $target_post_types );

		// Collect all expired post IDs across all target post types.
		$all_expired_posts = array();

		foreach ( $target_post_types as $post_type ) {
			$post_ids = $wpdb->get_col(
				$wpdb->prepare(
					"
					SELECT DISTINCT p.ID
					FROM {$wpdb->posts} p
					INNER JOIN {$wpdb->postmeta} pm_date ON p.ID = pm_date.post_id
					LEFT JOIN {$wpdb->postmeta} pm_time ON p.ID = pm_time.post_id AND pm_time.meta_key = %s
					WHERE p.post_type = %s
					AND p.post_status = %s
					AND pm_date.meta_key = %s
					AND (
						pm_date.meta_value < %s
						OR (
							pm_date.meta_value = %s
							AND COALESCE(pm_time.meta_value, '00:00') <= %s
						)
					)
					LIMIT 50
					",
					array(
						'mscpe_expiry_time',
						$post_type,
						'publish',
						'mscpe_expiry_date',
						$current_date,
						$current_date,
						$current_time,
					)
				)
			);
			if ( ! empty( $post_ids ) ) {
				$all_expired_posts = array_merge( $all_expired_posts, $post_ids );
			}
		}

		// Convert to integers and remove duplicates.
		$all_expired_posts = array_unique( array_map( 'intval', $all_expired_posts ) );

		// Cache results for 1 hour.
		wp_cache_set( $cache_key, $all_expired_posts, 'mscpe', HOUR_IN_SECONDS );

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
		$post = get_post( $post_id );

		if ( ! $post ) {
			$this->log_event( sprintf( 'Post ID %d not found.', $post_id ) );
			return false;
		}

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
				$result = wp_trash_post( $post_id );
				break;

			case 'delete':
				$result = wp_delete_post( $post_id, true );
				break;

			case 'draft':
				$result = wp_update_post(
					array(
						'ID'          => $post_id,
						'post_status' => 'draft',
					)
				);
				break;

			default:
				$this->log_event( sprintf( 'Invalid action "%s" for post ID %d.', $action, $post_id ) );
				return false;
		}

		if ( $result ) {
			// Delete post meta after successful expiry.
			delete_post_meta( $post_id, 'mscpe_expiry_date' );
			delete_post_meta( $post_id, 'mscpe_expiry_time' );

			$this->log_event( sprintf( 'Post ID %d expired with action "%s".', $post_id, $action ) );

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

		$this->log_event( sprintf( 'Failed to expire post ID %d with action "%s".', $post_id, $action ) );
		return false;
	}

	/**
	 * Log an event.
	 *
	 * @param string $message Log message.
	 */
	private function log_event( $message ) {
		if ( ! (bool) apply_filters( 'mscpe_enable_logging', true ) ) {
			return;
		}

		$log_dir  = wp_upload_dir();
		$log_path = $log_dir['basedir'] . '/' . self::LOG_DIR;

		// Create log directory if it doesn't exist.
		if ( ! is_dir( $log_path ) ) {
			wp_mkdir_p( $log_path );
		}

		$log_file  = $log_path . '/msc-post-expiry.log';
		$timestamp = current_time( 'Y-m-d H:i:s' );
		$log_entry = sprintf( "[%s] %s\n", $timestamp, $message );

		// Use WP_Filesystem for file operations.
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
		}

		if ( ! empty( $wp_filesystem ) ) {
			$wp_filesystem->put_contents( $log_file, $log_entry, FILE_APPEND );
		}
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

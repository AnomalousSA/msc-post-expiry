<?php
/**
 * Multi-step workflows for MSC Post Expiry.
 *
 * @package MSCPE
 */

namespace MSCPE;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manages workflows.
 */
class Workflows {

	/**
	 * Meta key for workflow assignment.
	 */
	const META_KEY_WORKFLOW_ID = '_mscpe_workflow_id';

	/**
	 * Meta key for tracking workflow state.
	 */
	const META_KEY_WORKFLOW_STATE = '_mscpe_workflow_state';

	/**
	 * Meta key for workflow step completion timestamps.
	 */
	const META_KEY_STEP_TIMESTAMP = '_mscpe_workflow_step_ts';

	/**
	 * Cron hook for processing delayed workflow steps.
	 */
	const CRON_HOOK = 'mscpe_process_workflow_steps';

	/**
	 * Main plugin instance.
	 *
	 * @var Plugin
	 */
	private $plugin;

	/**
	 * Constructor.
	 *
	 * @param Plugin $plugin Main plugin instance.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		add_action( self::CRON_HOOK, array( $this, 'process_delayed_steps' ) );
	}

	/**
	 * Gets all workflows from the database.
	 *
	 * @return array<int,array>
	 */
	public function get_workflows() {
		global $wpdb;

		$table   = $wpdb->prefix . 'mscpe_workflows';
		$results = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,PluginCheck.Security.DirectDB.UnescapedDBParameter
			"SELECT * FROM {$table} ORDER BY id ASC", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			ARRAY_A
		);

		if ( empty( $results ) ) {
			return array();
		}

		$workflows = array();
		foreach ( $results as $row ) {
			$workflow_id              = (int) $row['id'];
			$workflows[ $workflow_id ] = array(
				'id'          => $workflow_id,
				'name'        => $row['name'],
				'description' => $row['description'],
				'enabled'     => (int) $row['enabled'],
				'created_at'  => (int) $row['created_at'],
				'updated_at'  => (int) $row['updated_at'],
				'steps'       => $this->get_workflow_steps( $workflow_id ),
			);
		}

		return $workflows;
	}

	/**
	 * Gets a single workflow by ID.
	 *
	 * @param int $workflow_id Workflow ID.
	 * @return array|null
	 */
	public function get_workflow( $workflow_id ) {
		global $wpdb;

		$table = $wpdb->prefix . 'mscpe_workflows';
		$row   = $wpdb->get_row( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,PluginCheck.Security.DirectDB.UnescapedDBParameter
			$wpdb->prepare(
				"SELECT * FROM {$table} WHERE id = %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$workflow_id
			),
			ARRAY_A
		);

		if ( ! $row ) {
			return null;
		}

		return array(
			'id'          => (int) $row['id'],
			'name'        => $row['name'],
			'description' => $row['description'],
			'enabled'     => (int) $row['enabled'],
			'created_at'  => (int) $row['created_at'],
			'updated_at'  => (int) $row['updated_at'],
			'steps'       => $this->get_workflow_steps( (int) $row['id'] ),
		);
	}

	/**
	 * Gets steps for a workflow.
	 *
	 * @param int $workflow_id Workflow ID.
	 * @return array<int,array>
	 */
	public function get_workflow_steps( $workflow_id ) {
		global $wpdb;

		$table   = $wpdb->prefix . 'mscpe_workflow_steps';
		$results = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,PluginCheck.Security.DirectDB.UnescapedDBParameter
			$wpdb->prepare(
				"SELECT * FROM {$table} WHERE workflow_id = %d ORDER BY step_order ASC", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$workflow_id
			),
			ARRAY_A
		);

		if ( empty( $results ) ) {
			return array();
		}

		$steps = array();
		foreach ( $results as $row ) {
			$steps[] = array(
				'id'            => (int) $row['id'],
				'workflow_id'   => (int) $row['workflow_id'],
				'step_order'    => (int) $row['step_order'],
				'action_type'   => $row['action_type'],
				'action_config' => json_decode( $row['action_config'], true ) ?: array(),
				'delay_days'    => (int) $row['delay_days'],
				'created_at'    => (int) $row['created_at'],
			);
		}

		return $steps;
	}

	/**
	 * Creates a new workflow.
	 *
	 * @param array $data Workflow data.
	 * @return int|false Workflow ID on success, false on failure.
	 */
	public function create_workflow( $data ) {
		global $wpdb;

		$table = $wpdb->prefix . 'mscpe_workflows';

		$result = $wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$table,
			array(
				'name'        => sanitize_text_field( $data['name'] ),
				'description' => sanitize_textarea_field( $data['description'] ),
				'enabled'     => ! empty( $data['enabled'] ) ? 1 : 0,
				'created_at'  => time(),
				'updated_at'  => time(),
			),
			array( '%s', '%s', '%d', '%d', '%d' )
		);

		if ( false === $result ) {
			return false;
		}

		$workflow_id = (int) $wpdb->insert_id;

		if ( ! empty( $data['steps'] ) && is_array( $data['steps'] ) ) {
			$this->save_workflow_steps( $workflow_id, $data['steps'] );
		}

		return $workflow_id;
	}

	/**
	 * Updates an existing workflow.
	 *
	 * @param int   $workflow_id Workflow ID.
	 * @param array $data        Workflow data.
	 * @return bool
	 */
	public function update_workflow( $workflow_id, $data ) {
		global $wpdb;

		$table = $wpdb->prefix . 'mscpe_workflows';

		$result = $wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			$table,
			array(
				'name'        => sanitize_text_field( $data['name'] ),
				'description' => sanitize_textarea_field( $data['description'] ),
				'enabled'     => ! empty( $data['enabled'] ) ? 1 : 0,
				'updated_at'  => time(),
			),
			array( 'id' => $workflow_id ),
			array( '%s', '%s', '%d', '%d' ),
			array( '%d' )
		);

		if ( false === $result ) {
			return false;
		}

		if ( isset( $data['steps'] ) && is_array( $data['steps'] ) ) {
			$this->delete_workflow_steps( $workflow_id );
			$this->save_workflow_steps( $workflow_id, $data['steps'] );
		}

		return true;
	}

	/**
	 * Deletes a workflow and its steps.
	 *
	 * @param int $workflow_id Workflow ID.
	 * @return bool
	 */
	public function delete_workflow( $workflow_id ) {
		global $wpdb;

		$this->delete_workflow_steps( $workflow_id );

		$table  = $wpdb->prefix . 'mscpe_workflows';
		$result = $wpdb->delete( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			$table,
			array( 'id' => $workflow_id ),
			array( '%d' )
		);

		return false !== $result;
	}

	/**
	 * Saves workflow steps.
	 *
	 * @param int   $workflow_id Workflow ID.
	 * @param array $steps       Steps data.
	 * @return void
	 */
	private function save_workflow_steps( $workflow_id, $steps ) {
		global $wpdb;

		$table = $wpdb->prefix . 'mscpe_workflow_steps';

		foreach ( $steps as $index => $step ) {
			$action_type = isset( $step['action_type'] ) ? sanitize_key( $step['action_type'] ) : '';
			if ( empty( $action_type ) ) {
				continue;
			}

			$action_config = isset( $step['action_config'] ) ? $step['action_config'] : array();
			if ( is_array( $action_config ) ) {
				$action_config = wp_json_encode( $action_config );
			}

			$wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$table,
				array(
					'workflow_id'   => $workflow_id,
					'step_order'    => $index,
					'action_type'   => $action_type,
					'action_config' => $action_config,
					'delay_days'    => isset( $step['delay_days'] ) ? absint( $step['delay_days'] ) : 0,
					'created_at'    => time(),
				),
				array( '%d', '%d', '%s', '%s', '%d', '%d' )
			);
		}
	}

	/**
	 * Deletes all steps for a workflow.
	 *
	 * @param int $workflow_id Workflow ID.
	 * @return void
	 */
	private function delete_workflow_steps( $workflow_id ) {
		global $wpdb;

		$wpdb->delete( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prefix . 'mscpe_workflow_steps',
			array( 'workflow_id' => $workflow_id ),
			array( '%d' )
		);
	}

	/**
	 * Executes a workflow for a post.
	 *
	 * @param int $post_id     Post ID.
	 * @param int $workflow_id Workflow ID.
	 * @return bool
	 */
	public function execute_workflow( $post_id, $workflow_id ) {
		$workflow = $this->get_workflow( $workflow_id );
		if ( ! $workflow || empty( $workflow['enabled'] ) || empty( $workflow['steps'] ) ) {
			return false;
		}

		// Sort steps by order.
		$steps = $workflow['steps'];
		usort(
			$steps,
			function ( $a, $b ) {
				return ( $a['step_order'] ?? 0 ) - ( $b['step_order'] ?? 0 );
			}
		);

		// Execute first step immediately.
		$first_step = $steps[0];
		$result     = $this->execute_step( $post_id, $first_step );

		if ( ! $result ) {
			return false;
		}

		// If there are more steps with delays, schedule them.
		if ( count( $steps ) > 1 ) {
			$remaining_steps = array_slice( $steps, 1 );
			$this->schedule_delayed_steps( $post_id, $remaining_steps );
		}

		return true;
	}

	/**
	 * Assigns a workflow to a post.
	 *
	 * @param int $post_id     Post ID.
	 * @param int $workflow_id Workflow ID.
	 * @return void
	 */
	public function assign_workflow_to_post( $post_id, $workflow_id ) {
		if ( $workflow_id > 0 ) {
			update_post_meta( $post_id, self::META_KEY_WORKFLOW_ID, $workflow_id );
		} else {
			delete_post_meta( $post_id, self::META_KEY_WORKFLOW_ID );
		}
	}

	/**
	 * Gets the workflow assigned to a post.
	 *
	 * @param int $post_id Post ID.
	 * @return int
	 */
	public function get_post_workflow_id( $post_id ) {
		return (int) get_post_meta( $post_id, self::META_KEY_WORKFLOW_ID, true );
	}

	/**
	 * Executes a single workflow step.
	 *
	 * @param int   $post_id Post ID.
	 * @param array $step    Step data.
	 * @return bool
	 */
	public function execute_step( $post_id, $step ) {
		$action_type   = isset( $step['action_type'] ) ? $step['action_type'] : '';
		$action_config = isset( $step['action_config'] ) ? $step['action_config'] : array();

		switch ( $action_type ) {
			case 'draft':
				return ! is_wp_error( wp_update_post( array( 'ID' => $post_id, 'post_status' => 'draft' ), true ) );

			case 'private':
				return ! is_wp_error( wp_update_post( array( 'ID' => $post_id, 'post_status' => 'private' ), true ) );

			case 'trash':
				return false !== wp_trash_post( $post_id );

			case 'category':
				if ( 'post' !== get_post_type( $post_id ) ) {
					return false;
				}
				$cat_id = isset( $action_config['category_id'] ) ? absint( $action_config['category_id'] ) : 0;
				if ( $cat_id <= 0 ) {
					return false;
				}
				return false !== wp_set_post_categories( $post_id, array( $cat_id ), false );

			case 'email':
				return $this->action_email( $post_id, $action_config );

			case 'redirect':
				$redirect_url = isset( $action_config['redirect_url'] ) ? $action_config['redirect_url'] : '';
				if ( empty( $redirect_url ) ) {
					return false;
				}
				update_post_meta( $post_id, '_mscpe_expiry_redirect_url', $redirect_url );
				return true;

			case 'noindex':
				update_post_meta( $post_id, SEO::META_KEY_NOINDEX, 1 );
				return true;

			case 'delay':
				return true;

			default:
				return false;
		}
	}

	/**
	 * Schedules delayed workflow steps for cron processing.
	 *
	 * @param int   $post_id Post ID.
	 * @param array $steps   Remaining steps.
	 * @return void
	 */
	private function schedule_delayed_steps( $post_id, $steps ) {
		$state = array(
			'post_id'      => $post_id,
			'steps'        => $steps,
			'current_step' => 0,
			'started_at'   => time(),
		);

		update_post_meta( $post_id, self::META_KEY_WORKFLOW_STATE, $state );

		foreach ( $steps as $step ) {
			$delay_days = isset( $step['delay_days'] ) ? absint( $step['delay_days'] ) : 0;

			if ( $delay_days > 0 ) {
				$scheduled_time = time() + ( $delay_days * DAY_IN_SECONDS );
				update_post_meta( $post_id, self::META_KEY_STEP_TIMESTAMP, $scheduled_time );
				wp_schedule_single_event( $scheduled_time, self::CRON_HOOK, array( $post_id ) );
				break;
			}

			// Execute immediate steps.
			$result = $this->execute_step( $post_id, $step );
			if ( ! $result ) {
				$this->clear_workflow_state( $post_id );
				break;
			}
		}
	}

	/**
	 * Processes delayed workflow steps via cron.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function process_delayed_steps( $post_id ) {
		$state = get_post_meta( $post_id, self::META_KEY_WORKFLOW_STATE, true );
		if ( empty( $state ) || ! is_array( $state ) ) {
			return;
		}

		$post = get_post( $post_id );
		if ( ! $post ) {
			$this->clear_workflow_state( $post_id );
			return;
		}

		$steps = isset( $state['steps'] ) ? $state['steps'] : array();
		if ( empty( $steps ) ) {
			$this->clear_workflow_state( $post_id );
			return;
		}

		$current_step = isset( $state['current_step'] ) ? (int) $state['current_step'] : 0;

		for ( $i = $current_step; $i < count( $steps ); ++$i ) {
			$step       = $steps[ $i ];
			$delay_days = isset( $step['delay_days'] ) ? absint( $step['delay_days'] ) : 0;

			if ( $delay_days > 0 ) {
				$step_ts = (int) get_post_meta( $post_id, self::META_KEY_STEP_TIMESTAMP, true );
				if ( $step_ts > time() ) {
					wp_schedule_single_event( $step_ts, self::CRON_HOOK, array( $post_id ) );
					return;
				}
			}

			$result = $this->execute_step( $post_id, $step );
			if ( ! $result ) {
				$this->clear_workflow_state( $post_id );
				return;
			}

			$state['current_step'] = $i + 1;
			update_post_meta( $post_id, self::META_KEY_WORKFLOW_STATE, $state );

			if ( isset( $steps[ $i + 1 ] ) ) {
				$next_delay = isset( $steps[ $i + 1 ]['delay_days'] ) ? absint( $steps[ $i + 1 ]['delay_days'] ) : 0;
				if ( $next_delay > 0 ) {
					$scheduled_time = time() + ( $next_delay * DAY_IN_SECONDS );
					update_post_meta( $post_id, self::META_KEY_STEP_TIMESTAMP, $scheduled_time );
					wp_schedule_single_event( $scheduled_time, self::CRON_HOOK, array( $post_id ) );
					return;
				}
			}
		}

		$this->clear_workflow_state( $post_id );
	}

	/**
	 * Clears workflow state for a post.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	private function clear_workflow_state( $post_id ) {
		delete_post_meta( $post_id, self::META_KEY_WORKFLOW_STATE );
		delete_post_meta( $post_id, self::META_KEY_STEP_TIMESTAMP );
	}

	/**
	 * Action: Send email notification.
	 *
	 * @param int   $post_id Post ID.
	 * @param array $config  Action config.
	 * @return bool
	 */
	private function action_email( $post_id, $config ) {
		$post = get_post( $post_id );
		if ( ! $post ) {
			return false;
		}

		$recipients_str = isset( $config['recipients'] ) ? $config['recipients'] : 'author';
		$custom_email   = isset( $config['custom_email'] ) ? $config['custom_email'] : '';

		$recipients = array();

		if ( 'author' === $recipients_str || 'both' === $recipients_str ) {
			$author_email = get_the_author_meta( 'email', $post->post_author );
			if ( $author_email ) {
				$recipients[] = $author_email;
			}
		}

		if ( 'admin' === $recipients_str || 'both' === $recipients_str ) {
			$admin_email = get_option( 'admin_email' );
			if ( $admin_email ) {
				$recipients[] = $admin_email;
			}
		}

		if ( ! empty( $custom_email ) ) {
			$recipients[] = $custom_email;
		}

		if ( empty( $recipients ) ) {
			return false;
		}

		$edit_link = get_edit_post_link( $post_id, 'raw' );
		$subject   = sprintf(
			/* translators: %s is the post title */
			__( 'Post Expired: %s', 'msc-post-expiry' ),
			$post->post_title
		);
		$body = sprintf(
			/* translators: 1: Post title, 2: Edit link */
			__( "The following post has expired and its workflow step was executed:\n\nPost: %1\$s\n\nEdit post: %2\$s", 'msc-post-expiry' ),
			$post->post_title,
			$edit_link
		);

		return wp_mail( $recipients, $subject, $body );
	}

	/**
	 * Cancels any pending workflow for a post.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function cancel_workflow( $post_id ) {
		$this->clear_workflow_state( $post_id );
	}

	/**
	 * Gets available action types for workflow steps.
	 *
	 * @return array<string,string>
	 */
	public static function get_action_types() {
		return array(
			'draft'    => __( 'Change to Draft', 'msc-post-expiry' ),
			'private'  => __( 'Change to Private', 'msc-post-expiry' ),
			'trash'    => __( 'Move to Trash', 'msc-post-expiry' ),
			'category' => __( 'Move to Category', 'msc-post-expiry' ),
			'email'    => __( 'Send Email', 'msc-post-expiry' ),
			'redirect' => __( 'Set Redirect URL', 'msc-post-expiry' ),
			'noindex'  => __( 'Add Noindex', 'msc-post-expiry' ),
			'delay'    => __( 'Wait/Delay', 'msc-post-expiry' ),
		);
	}
}

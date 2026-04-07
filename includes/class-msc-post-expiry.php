<?php
/**
 * Main bootstrap class for MSC Post Expiry.
 *
 * @package MSCPE
 */

namespace MSCPE;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class.
 */
class Plugin {

	const OPTION_KEY = 'mscpe_options';

	/**
	 * Singleton instance.
	 *
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
	 * Module instance.
	 *
	 * @var Module|null
	 */
	private $module = null;

	/**
	 * Settings instance.
	 *
	 * @var Settings
	 */
	private $settings;

	/**
	 * Cron instance.
	 *
	 * @var Cron
	 */
	private $cron;

	/**
	 * Analytics instance.
	 *
	 * @var object|null
	 */
	private $analytics = null;

	/**
	 * Admin analytics instance.
	 *
	 * @var object|null
	 */
	private $admin_analytics = null;

	/**
	 * Get singleton instance.
	 *
	 * @return Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Activate plugin.
	 */
	public static function activate() {
		$options = get_option( self::OPTION_KEY );
		if ( ! is_array( $options ) ) {
			update_option( self::OPTION_KEY, self::default_options() );
		}

		// Register cron event.
		if ( ! wp_next_scheduled( Cron::CRON_HOOK ) ) {
			wp_schedule_event( time(), 'mscpe_5min', Cron::CRON_HOOK );
		}
	}

	/**
	 * Deactivate plugin.
	 */
	public static function deactivate() {
		// Unregister cron event.
		$timestamp = wp_next_scheduled( Cron::CRON_HOOK );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, Cron::CRON_HOOK );
		}
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->settings = new Settings( $this );
		$this->module   = new Module( $this );
		$this->cron     = new Cron( $this );
	}

	/**
	 * Default options.
	 *
	 * @return array<string,mixed>
	 */
	public static function default_options() {
		return array(
			'module_enabled' => 1,
			'post_types'     => array( 'post', 'page' ),
			'post_type_mode' => 'include',
			'expiry_action'  => 'trash',
		);
	}

	/**
	 * Option getter.
	 *
	 * @param string $key Key.
	 * @param mixed  $default_value Default value.
	 * @return mixed
	 */
	public function get_option( $key, $default_value = null ) {
		$options = wp_parse_args( get_option( self::OPTION_KEY, array() ), self::default_options() );
		return array_key_exists( $key, $options ) ? $options[ $key ] : $default_value;
	}

	/**
	 * Save merged options.
	 *
	 * @param array<string,mixed> $new_options New values.
	 * @return bool
	 */
	public function update_options( $new_options ) {
		$current = wp_parse_args( get_option( self::OPTION_KEY, array() ), self::default_options() );
		$merged  = array_merge( $current, $new_options );
		return (bool) update_option( self::OPTION_KEY, $merged );
	}

	/**
	 * Whether pro plugin is active.
	 *
	 * @return bool
	 */
	public function is_pro_active() {
		return (bool) apply_filters( 'mscpe_pro_active', false );
	}

	/**
	 * Feature switch helper.
	 *
	 * @param string $feature Feature key.
	 * @return bool
	 */
	public function has_feature( $feature ) {
		$map = array(
			'analytics'         => false,
			'admin_analytics'   => false,
			'cron'              => true,
			'meta_registration' => true,
			'bulk_actions'      => false,
			'shortcode'         => false,
			'ajax'              => false,
		);

		return ! empty( $map[ $feature ] );
	}
}

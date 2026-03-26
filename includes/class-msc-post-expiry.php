<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MSC_Post_Expiry {
    const OPTION_KEY = 'mscpe_options';

    /** @var MSC_Post_Expiry|null */
    private static $instance = null;

    /** @var array<string,mixed> */
    private $options = array();

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function default_options() {
        return array(
            'module_enabled'   => 1,
            'post_types'       => array( 'post' ),
            'default_action'   => 'draft',
            'archive_category' => 0,
        );
    }

    public static function activate() {
        $stored = get_option( self::OPTION_KEY, array() );
        if ( ! is_array( $stored ) ) {
            $stored = array();
        }
        update_option( self::OPTION_KEY, wp_parse_args( $stored, self::default_options() ) );

        if ( ! wp_next_scheduled( MSC_Post_Expiry_Module::CRON_HOOK ) ) {
            wp_schedule_event( time() + MINUTE_IN_SECONDS, 'mscpe_every_fifteen_minutes', MSC_Post_Expiry_Module::CRON_HOOK );
        }
    }

    public static function deactivate() {
        $next = wp_next_scheduled( MSC_Post_Expiry_Module::CRON_HOOK );
        if ( $next ) {
            wp_unschedule_event( $next, MSC_Post_Expiry_Module::CRON_HOOK );
        }
    }

    private function __construct() {
        $this->options = wp_parse_args( get_option( self::OPTION_KEY, array() ), self::default_options() );

        add_filter( 'cron_schedules', array( $this, 'add_cron_schedule' ) );

        new MSC_Post_Expiry_Settings( $this );

        if ( ! $this->is_pro_active() ) {
            new MSC_Post_Expiry_Module( $this );
        }
    }

    public function add_cron_schedule( $schedules ) {
        if ( ! isset( $schedules['mscpe_every_fifteen_minutes'] ) ) {
            $schedules['mscpe_every_fifteen_minutes'] = array(
                'interval' => 15 * MINUTE_IN_SECONDS,
                'display'  => __( 'Every 15 Minutes (Micro Site Care: Post Expiry)', 'msc-post-expiry' ),
            );
        }
        return $schedules;
    }

    public function is_pro_active() {
        return (bool) apply_filters( 'mscpe_pro_active', false );
    }

    public function get_options() {
        return $this->options;
    }

    public function get_option( $key, $default = null ) {
        return array_key_exists( $key, $this->options ) ? $this->options[ $key ] : $default;
    }

    public function update_options( $new_options ) {
        $this->options = wp_parse_args( $new_options, self::default_options() );
        update_option( self::OPTION_KEY, $this->options );
    }
}

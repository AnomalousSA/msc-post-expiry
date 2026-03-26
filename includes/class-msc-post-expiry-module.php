<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MSC_Post_Expiry_Module {
    const META_KEY_EXPIRY    = '_mscpe_expiry_timestamp';
    const META_KEY_PROCESSED = '_mscpe_expiry_processed';
    const META_KEY_LOG       = '_mscpe_expiry_log';
    const CRON_HOOK          = 'mscpe_process_expired_posts';

    /** @var MSC_Post_Expiry */
    private $plugin;

    public function __construct( $plugin ) {
        $this->plugin = $plugin;

        add_action( 'init', array( $this, 'register_meta' ) );
        add_action( 'add_meta_boxes', array( $this, 'register_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_meta_box' ) );
        add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );
        add_action( self::CRON_HOOK, array( $this, 'process_expired_posts' ) );
    }

    public function register_meta() {
        $post_types = (array) $this->plugin->get_option( 'post_types', array( 'post' ) );

        foreach ( $post_types as $post_type ) {
            register_post_meta(
                $post_type,
                self::META_KEY_EXPIRY,
                array(
                    'type'          => 'integer',
                    'single'        => true,
                    'show_in_rest'  => true,
                    'default'       => 0,
                    'auth_callback' => static function () {
                        return current_user_can( 'edit_posts' );
                    },
                )
            );

            register_post_meta(
                $post_type,
                self::META_KEY_PROCESSED,
                array(
                    'type'          => 'boolean',
                    'single'        => true,
                    'show_in_rest'  => false,
                    'default'       => false,
                    'auth_callback' => static function () {
                        return current_user_can( 'edit_posts' );
                    },
                )
            );
        }
    }

    public function register_meta_box() {
        if ( ! $this->is_enabled() ) {
            return;
        }

        $post_types = (array) $this->plugin->get_option( 'post_types', array( 'post' ) );

        foreach ( $post_types as $post_type ) {
            add_meta_box(
                'mscpe-post-expiry',
                __( 'Post Expiry', 'msc-post-expiry' ),
                array( $this, 'render_meta_box' ),
                $post_type,
                'side'
            );
        }
    }

    public function render_meta_box( $post ) {
        wp_nonce_field( 'mscpe_save_expiry_meta', 'mscpe_expiry_nonce' );
        $ts          = absint( get_post_meta( $post->ID, self::META_KEY_EXPIRY, true ) );
        $local_value = '';

        if ( $ts > 0 ) {
            $local_value = wp_date( 'Y-m-d\\TH:i', $ts + ( (int) get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
        }

        echo '<p><label for="mscpe-expiry-datetime"><strong>' . esc_html__( 'Expiry date/time', 'msc-post-expiry' ) . '</strong></label></p>';
        echo '<input type="datetime-local" id="mscpe-expiry-datetime" name="mscpe_expiry_datetime" value="' . esc_attr( $local_value ) . '" style="width:100%;" />';
        echo '<p class="description">' . esc_html__( 'Leave empty to disable expiry for this post.', 'msc-post-expiry' ) . '</p>';
    }

    public function save_meta_box( $post_id ) {
        if ( ! $this->is_enabled() || ! isset( $_POST['mscpe_expiry_nonce'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mscpe_expiry_nonce'] ) ), 'mscpe_save_expiry_meta' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $raw = isset( $_POST['mscpe_expiry_datetime'] ) ? sanitize_text_field( wp_unslash( $_POST['mscpe_expiry_datetime'] ) ) : '';
        if ( '' === trim( $raw ) ) {
            delete_post_meta( $post_id, self::META_KEY_EXPIRY );
            delete_post_meta( $post_id, self::META_KEY_PROCESSED );
            return;
        }

        $timestamp = strtotime( $raw );
        if ( false === $timestamp ) {
            return;
        }

        $gmt_timestamp = $timestamp - ( (int) get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
        update_post_meta( $post_id, self::META_KEY_EXPIRY, (int) $gmt_timestamp );
        update_post_meta( $post_id, self::META_KEY_PROCESSED, 0 );
    }

    public function enqueue_editor_assets() {
        if ( ! $this->is_enabled() ) {
            return;
        }

        $screen = get_current_screen();
        if ( ! $screen || ! in_array( $screen->post_type, (array) $this->plugin->get_option( 'post_types', array( 'post' ) ), true ) ) {
            return;
        }

        wp_enqueue_script(
            'mscpe-expiry-sidebar',
            MSCPE_PLUGIN_URL . 'assets/js/expiry-sidebar.js',
            array( 'wp-components', 'wp-compose', 'wp-data', 'wp-edit-post', 'wp-element', 'wp-plugins' ),
            MSCPE_PLUGIN_VERSION,
            true
        );

        wp_localize_script(
            'mscpe-expiry-sidebar',
            'mscpeExpiryConfig',
            array(
                'metaKey' => self::META_KEY_EXPIRY,
                'title'   => __( 'Post Expiry', 'msc-post-expiry' ),
                'help'    => __( 'Set a date/time to automatically apply the default expiry action to this post.', 'msc-post-expiry' ),
            )
        );
    }

    public function process_expired_posts() {
        if ( ! $this->is_enabled() ) {
            return;
        }

        $post_types = (array) $this->plugin->get_option( 'post_types', array( 'post' ) );
        $now        = time();

        $query = new WP_Query(
            array(
                'post_type'      => $post_types,
                'post_status'    => array( 'publish', 'future', 'private' ),
                'posts_per_page' => 50,
                'fields'         => 'ids',
                'meta_query'     => array(
                    array(
                        'key'     => self::META_KEY_EXPIRY,
                        'value'   => $now,
                        'compare' => '<=',
                        'type'    => 'NUMERIC',
                    ),
                ),
            )
        );

        if ( empty( $query->posts ) ) {
            return;
        }

        foreach ( $query->posts as $post_id ) {
            if ( (bool) get_post_meta( $post_id, self::META_KEY_PROCESSED, true ) ) {
                continue;
            }

            $action = (string) $this->plugin->get_option( 'default_action', 'draft' );
            $action = apply_filters( 'mscpe_post_expiry_action', $action, $post_id );

            $result = false;
            if ( 'archive_category' === $action && 'post' === get_post_type( $post_id ) ) {
                $archive_category = absint( $this->plugin->get_option( 'archive_category', 0 ) );
                if ( $archive_category > 0 ) {
                    $result = (bool) wp_set_post_categories( $post_id, array( $archive_category ), false );
                }
            } else {
                $result = ! is_wp_error(
                    wp_update_post( array( 'ID' => $post_id, 'post_status' => 'draft' ), true )
                );
            }

            if ( $result ) {
                update_post_meta( $post_id, self::META_KEY_PROCESSED, 1 );
                $this->append_log( $post_id, $action );
                do_action( 'mscpe_post_expired', $post_id, $action );
            }
        }

        wp_reset_postdata();
    }

    private function append_log( $post_id, $action ) {
        $log   = get_post_meta( $post_id, self::META_KEY_LOG, true );
        $log   = is_array( $log ) ? $log : array();
        $log[] = array( 'time' => gmdate( 'c' ), 'action' => sanitize_key( $action ) );
        if ( count( $log ) > 20 ) {
            $log = array_slice( $log, -20 );
        }
        update_post_meta( $post_id, self::META_KEY_LOG, $log );
    }

    private function is_enabled() {
        return (bool) $this->plugin->get_option( 'module_enabled', 1 );
    }
}

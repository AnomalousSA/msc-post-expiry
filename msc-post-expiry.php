<?php
/**
 * Plugin Name: Micro Site Care: Post Expiry
 * Plugin URI:  https://anomalous.co.za
 * Description: Set per-post expiry dates to automatically unpublish content when it becomes outdated.
 * Version:     0.1.0
 * Author:      Anomalous Developers
 * Author URI:  https://anomalous.co.za
 * Text Domain: msc-post-expiry
 * Domain Path: /languages
 * Requires at least: 5.9
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'MSCPE_PLUGIN_VERSION', '0.1.0' );
define( 'MSCPE_PLUGIN_FILE', __FILE__ );
define( 'MSCPE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MSCPE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once MSCPE_PLUGIN_DIR . 'includes/class-msc-post-expiry-module.php';
require_once MSCPE_PLUGIN_DIR . 'includes/class-msc-post-expiry-settings.php';
require_once MSCPE_PLUGIN_DIR . 'includes/class-msc-post-expiry.php';

register_activation_hook( __FILE__, array( 'MSC_Post_Expiry', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'MSC_Post_Expiry', 'deactivate' ) );

add_action(
    'plugins_loaded',
    static function () {
        load_plugin_textdomain( 'msc-post-expiry', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
        MSC_Post_Expiry::instance();
    }
);

<?php
/**
 * Plugin Name: MSC Post Expiry
 * Plugin URI: https://anomalous.co.za
 * Description: Schedule and process post expiry actions.
 * Version: 1.0.0
 * Author: Anomalous Developers
 * Author URI: https://anomalous.co.za
 * Text Domain: msc-post-expiry
 * Domain Path: /languages
 * Requires at least: 5.9
 * Tested up to: 6.8
 * Requires PHP: 7.4
 * License: GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Current plugin version.
 */
define( 'MSCPE_PLUGIN_VERSION', '1.0.0' );

/**
 * Absolute path to the main plugin file.
 */
define( 'MSCPE_PLUGIN_FILE', __FILE__ );

/**
 * Absolute path to the plugin directory.
 */
define( 'MSCPE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * URL to the plugin directory.
 */
define( 'MSCPE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once MSCPE_PLUGIN_DIR . 'includes/class-msc-post-expiry-module.php';
require_once MSCPE_PLUGIN_DIR . 'includes/class-msc-post-expiry-settings.php';
require_once MSCPE_PLUGIN_DIR . 'includes/class-msc-post-expiry.php';

if ( false ) {
	require_once MSCPE_PLUGIN_DIR . 'includes/class-msc-post-expiry-analytics.php';
}

if ( false ) {
	require_once MSCPE_PLUGIN_DIR . 'includes/class-msc-post-expiry-admin-analytics.php';
}

register_activation_hook(
	__FILE__,
	array( 'MSC_Post_Expiry\\Plugin', 'activate' )
);

register_deactivation_hook(
	__FILE__,
	array( 'MSC_Post_Expiry\\Plugin', 'deactivate' )
);

add_action(
	'plugins_loaded',
	static function () {
		// Load translation files from the plugin languages directory.
		load_plugin_textdomain(
			'msc-post-expiry',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages'
		);

		MSC_Post_Expiry\Plugin::instance();
	}
);

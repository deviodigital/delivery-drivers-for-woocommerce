<?php

/**
 * The plugin bootstrap file
 *
 * @link              http://www.deviodigital.com
 * @since             1.0.0
 * @package           DDWC
 *
 * @wordpress-plugin
 * Plugin Name:       Delivery Drivers for WooCommerce
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Plugin URI:        http://www.deviodigital.com
 * Version:           1.0.0
 * Author:            WP Dispensary
 * Author URI:        https://www.wpdispensary.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ddwc
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'DDWC_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ddwc-activator.php
 */
function activate_ddwc() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ddwc-activator.php';
	Delivery_Drivers_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ddwc-deactivator.php
 */
function deactivate_ddwc() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ddwc-deactivator.php';
	Delivery_Drivers_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ddwc' );
register_deactivation_hook( __FILE__, 'deactivate_ddwc' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ddwc.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ddwc() {

	$plugin = new DDWC();
	$plugin->run();

}
run_ddwc();

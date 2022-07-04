<?php

/**
 * The plugin bootstrap file
 *
 * @package DDWC
 * @author  Devio Digital <contact@deviodigital.com>
 * @license GPL-2.0+ http://www.gnu.org/licenses/gpl-2.0.txt
 * @link    https://www.deviodigital.com
 *
 * @wordpress-plugin
 * Plugin Name:          Delivery Drivers for WooCommerce
 * Plugin URI:           https://www.deviodigital.com
 * Description:          Streamline your mobile workforce & increase your bottom line
 * Version:              3.3.1
 * Author:               Devio Digital
 * Author URI:           https://www.deviodigital.com
 * License:              GPL-2.0+
 * License URI:          http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:          delivery-drivers-for-woocommerce
 * Domain Path:          /languages
 * WC requires at least: 3.5.0
 * WC tested up to:      6.6.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Current plugin version.
 */
define( 'DDWC_VERSION', '3.3.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ddwc-activator.php
 * 
 * @return void
 */
function activate_ddwc() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-ddwc-activator.php';
    Delivery_Drivers_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ddwc-deactivator.php
 * 
 * @return void
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
 * @since  1.0.0
 * @return void
 */
function run_ddwc() {

    $plugin = new DDWC();
    $plugin->run();

}
run_ddwc();

// Create variable for settings link filter.
$plugin_name = plugin_basename( __FILE__ );

/**
 * Add settings link on plugin page
 *
 * @param array $links an array of links related to the plugin.
 * 
 * @since  1.0.3
 * @return array updatead array of links related to the plugin.
 */
function ddwc_settings_link( $links ) {
    // Pro link.
    $pro_link = '<a href="https://deviodigital.com/product/delivery-drivers-for-woocommerce-pro" target="_blank" style="font-weight:700;">' . esc_attr__( 'Go Pro', 'delivery-drivers-for-woocommerce' ) . '</a>';
    // Settings link.
    $settings_link = '<a href="admin.php?page=wc-settings&tab=ddwc">' . esc_html__( 'Settings', 'delivery-drivers-for-woocommerce' ) . '</a>';

    array_unshift( $links, $settings_link );
    if ( ! function_exists( 'ddwc_pro_all_settings' ) ) {
        array_unshift( $links, $pro_link );
    }
    return $links;
}
add_filter( "plugin_action_links_$plugin_name", 'ddwc_settings_link' );

/**
 * Check DDWC Pro version number.
 *
 * If the DDWC Pro version number is less than what's defined below, there will
 * be a notice added to the admin screen letting the user know there's a new
 * version of the DDWC Pro plugin available.
 *
 * @since  2.9
 * @return void
 */
function ddwc_check_pro_version() {
    // Only run if DDWC Pro is active.
    if ( function_exists( 'ddwc_pro_all_settings' ) ) {
        // Check if DDWC Pro version is defined.
        if ( ! defined( 'DDWC_PRO_VERSION' ) ) {
            define( 'DDWC_PRO_VERSION', 0 ); // default to zero.
        }
        // Set pro version number.
        $pro_version = DDWC_PRO_VERSION;
        if ( '0' == $pro_version || $pro_version < '2.3' ) {
            add_action( 'admin_notices', 'ddwc_update_ddwc_pro_notice' );
        }
    }
}
add_action( 'admin_init', 'ddwc_check_pro_version' );

/**
 * Error notice - Runs if DDWC Pro is out of date.
 *
 * @see    ddwc_check_pro_version()
 * @since  2.9
 * @return string
 */
function ddwc_update_ddwc_pro_notice() {
    $ddwc_orders = '<a href="https://www.deviodigital.com/my-account/orders/" target="_blank">' . esc_html__( 'Orders', 'delivery-drivers-for-woocommerce' ) . '</a>';
    $error       = sprintf( __( 'There is a new version of DDWC Pro available. Download your copy from the %1$s page on Devio Digital.', 'delivery-drivers-for-woocommerce' ), $ddwc_orders );
    echo '<div class="notice notice-info"><p>' . $error . '</p></div>';
}

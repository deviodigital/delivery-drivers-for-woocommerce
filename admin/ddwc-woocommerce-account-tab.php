<?php

/**
 * WooCommerce Account Tab - Drivers
 *
 * @link       https://www.deviodigital.com
 * @since      1.0.0
 *
 * @package    DDWC
 * @subpackage DDWC/admin
 */

/**
 * Register new endpoint to use inside My Account page.
 *
 * @see https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
 */
function ddwc_endpoints() {
	add_rewrite_endpoint( 'driver-dashboard', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'ddwc_endpoints' );

/**
 * Add new query var.
 *
 * @param array $vars
 * @return array
 *
 * @since 1.2
 */
add_filter( 'woocommerce_get_query_vars', function ( $vars ) {
  foreach ( ['driver-dashboard'] as $e ) {
    $vars[$e] = $e;
  }
  return $vars;
} );

// Flush rewrite rules.
function ddwc_flush_rewrite_rules() {
	add_rewrite_endpoint( 'driver-dashboard', EP_ROOT | EP_PAGES );
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ddwc_flush_rewrite_rules' );

/**
 * Insert the new endpoint into the My Account menu.
 *
 * @param array $items
 * @return array
 */
function ddwc_my_account_menu_items( $items ) {
	// Get customer-logout menu item.
	$logout = $items['customer-logout'];
	// Remove the customer-logout menu item.
	unset( $items['customer-logout'] );
	// Set user roles.
	$roles = array( 'administrator', 'driver' );
	// Check user role.
	if ( ddwc_check_user_roles( apply_filters( 'ddwc_my_account_check_user_role_array', $roles ) ) ) {
		// Insert the driver-dashboard endpoint.
		$items['driver-dashboard'] = apply_filters( 'ddwc_my_account_menu_item_driver_dashboard', esc_attr__( 'Driver Dashboard', 'ddwc' ) );
	}
	// Insert back the customer-logout item.
	$items['customer-logout'] = $logout;

	return $items;
}
add_filter( 'woocommerce_account_menu_items', 'ddwc_my_account_menu_items' );

/**
 * Endpoint HTML content.
 */
function ddwc_endpoint_content() {
	echo do_shortcode( '[ddwc_dashboard]' );
}
add_action( 'woocommerce_account_driver-dashboard_endpoint', 'ddwc_endpoint_content', 99, 1 );

/**
 * Change endpoint title.
 *
 * @param string $title
 * @return string
 */
function ddwc_endpoint_title( $title ) {
	// Change title on driver dashboard page.
	if ( is_wc_endpoint_url( 'driver-dashboard' ) && in_the_loop() ) {
		$title = apply_filters( 'ddwc_my_account_endpoint_title_driver_dashboard', esc_attr__( 'Driver Dashboard', 'ddwc-pro' ) );
	}
	return $title;
}
add_filter( 'the_title', 'ddwc_endpoint_title', 10, 1 );

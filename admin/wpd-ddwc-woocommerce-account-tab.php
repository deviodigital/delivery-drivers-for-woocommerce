<?php

/**
 * WooCommerce Account Tab - Drivers
 *
 * @link       https://www.wpdispensary.com
 * @since      1.0.0
 *
 * @package    WPD_DDWC
 * @subpackage WPD_DDWC/admin
 */
 
/**
 * Register new endpoint to use inside My Account page.
 *
 * @see https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
 */
function wpd_ddwc_endpoints() {
	add_rewrite_endpoint( 'driver-dashboard', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'wpd_ddwc_endpoints' );

/**
 * Add new query var.
 *
 * @param array $vars
 * @return array
 */
function wpd_ddwc_query_vars( $vars ) {
	$vars[] = 'driver-dashboard';

	return $vars;
}
add_filter( 'query_vars', 'wpd_ddwc_query_vars', 0 );

function wpd_ddwc_flush_rewrite_rules() {
	add_rewrite_endpoint( 'driver-dashboard', EP_ROOT | EP_PAGES );
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'wpd_ddwc_flush_rewrite_rules' );

/**
 * Insert the new endpoint into the My Account menu.
 *
 * @param array $items
 * @return array
 */
function wpd_ddwc_my_account_menu_items( $items ) {
	// Remove the logout menu item.
	$logout = $items['customer-logout'];
	unset( $items['customer-logout'] );

	// Insert your custom endpoint.
	$items['driver-dashboard'] = __( 'Delivery Drivers', 'woocommerce' );

	// Insert back the logout item.
	$items['customer-logout'] = $logout;

	return $items;
}
add_filter( 'woocommerce_account_menu_items', 'wpd_ddwc_my_account_menu_items' );

/**
 * Endpoint HTML content.
 */
function wpd_ddwc_endpoint_content() {
	echo do_shortcode( '[wpd_ddwc_dashboard]' );
}

add_action( 'woocommerce_account_driver-dashboard_endpoint', 'wpd_ddwc_endpoint_content' );

/*
 * Change endpoint title.
 *
 * @param string $title
 * @return string
 */
function wpd_ddwc_endpoint_title( $title ) {
	global $wp_query;

	$is_endpoint = isset( $wp_query->query_vars['driver-dashboard'] );

	if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
		// New page title.
		$title = __( 'Driver Dashboard', 'woocommerce' );

		remove_filter( 'the_title', 'wpd_ddwc_endpoint_title' );
	}

	return $title;
}

add_filter( 'the_title', 'wpd_ddwc_endpoint_title' );

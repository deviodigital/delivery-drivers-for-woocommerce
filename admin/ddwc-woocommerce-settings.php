<?php

/**
 * Custom Class for Woocommerce Settings
 *
 * @link       https://www.deviodigital.com
 * @since      1.0.0
 *
 * @package    DDWC
 * @subpackage DDWC/admin
 */
class Delivery_Drivers_WooCommerce_Settings {
	/**
	 * Bootstraps the class and hooks required actions & filters.
	 */
	public static function init() {
	   add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
	   add_action( 'woocommerce_settings_tabs_ddwc', __CLASS__ . '::settings_tab' );
	   add_action( 'woocommerce_update_options_ddwc', __CLASS__ . '::update_settings' );
	   // Add custom type.
	   add_action( 'woocommerce_admin_field_custom_type', __CLASS__ . '::output_custom_type', 10, 1 );
	}

	public static function output_custom_type( $value ) {
	 	// You can output the custom type in any format you'd like.
		echo $value['desc'];
	}

	/**
	 * Add a new settings tab to the WooCommerce settings tabs array.
	 *
	 * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
	 * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
	 */
	public static function add_settings_tab( $settings_tabs ) {
	   $settings_tabs['ddwc'] = __( 'Delivery Drivers', 'ddwc' );
	   return $settings_tabs;
	}

	/**
	 * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
	 *
	 * @uses woocommerce_admin_fields()
	 * @uses self::get_settings()
	 */
	public static function settings_tab() {
	   woocommerce_admin_fields( self::get_settings() );
	}

	/**
	 * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
	 *
	 * @uses woocommerce_update_options()
	 * @uses self::get_settings()
	 */
	public static function update_settings() {
	   woocommerce_update_options( self::get_settings() );
	}

	/**
	 * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
	 *
	 * @return array Array of settings for @see woocommerce_admin_fields() function.
	 */
	public static function get_settings() {

		// Get loop of all Pages.
		$args = array(
			'sort_column'  => 'post_title',
			'hierarchical' => 1,
			'post_type'    => 'page',
			'post_status'  => 'publish'
		);
		$pages = get_pages( $args );

		// Create data array.
		$pages_array = array( 'none' => '' );

		// Loop through pages.
		foreach ( $pages as $page ) {
			$pages_array[ $page->ID ] = $page->post_title;
		}

		$settings = array(
			// Section title.
			'ddwc_settings_section_title' => array(
			    'name' => __( 'Delivery Drivers for WooCommerce', 'ddwc' ),
			    'type' => 'title',
			    'desc' => __( 'Brought to you by <a href="https://www.deviodigital.com" target="_blank">Devio Digital</a>', 'ddwc' ),
			    'id'   => 'ddwc_settings_section_title'
			),
            // Dispatch phone number.
            'dispatch_phone_number' => array(
                'name' => __( 'Dispatch phone number', 'ddwc' ),
                'type' => 'text',
                'desc' => __( 'Allow your drivers to call if they have questions about an order.', 'ddwc' ),
                'id'   => 'ddwc_settings_dispatch_phone_number'
            ),
            // Google Maps API key.
            'google_maps_api_key' => array(
                'name' => __( 'Google Maps API key', 'ddwc' ),
                'type' => 'text',
                'desc' => __( 'Add a map to the order directions for your drivers.', 'ddwc' ),
                'id'   => 'ddwc_settings_google_maps_api_key'
            ),
            // Google Maps API key.
            'driver_ratings' => array(
                'name' => __( 'Driver Ratings', 'ddwc' ),
                'type' => 'select',
                'desc' => __( 'Add driver details and delivery star rating to order details page.', 'ddwc' ),
				'id'   => 'ddwc_settings_driver_ratings',
				'options' => array(
					'yes' => 'Yes',
					'no'  => 'No',
				),
            ),
            // Driver phone number.
            'driver_phone_number' => array(
                'name' => __( 'Driver phone number', 'ddwc' ),
                'type' => 'select',
                'desc' => __( 'Add a button for customers to call driver in the driver details.', 'ddwc' ),
				'id'   => 'ddwc_settings_driver_phone_number',
				'options' => array(
					'yes' => 'Yes',
					'no'  => 'No',
				),
            ),
			// Section End.
			'section_end' => array(
			    'type' => 'sectionend',
			    'id'   => 'ddwc_settings_section_end'
			),
		);
		return apply_filters( 'ddwc_woocommerce_settings', $settings );

	}
}
Delivery_Drivers_WooCommerce_Settings::init();

/**
 * Redirect users to custom URL based on their role after login
 *
 * @param string $redirect
 * @param object $user
 * @return string
 */
function wc_custom_user_redirect( $redirect, $user ) {
	// Get the first of all the roles assigned to the user
	$role      = $user->roles[0];
	$dashboard = apply_filters( 'ddwc_driver_dashboard_login_redirect', get_permalink( wc_get_page_id( 'myaccount' ) ) . '/driver-dashboard/' );
	if ( 'driver' == $role ) {
		// Redirect delivery drivers to the dashboard.
		$redirect = $dashboard;
	} else {
		// Redirect any other role to the previous visited page or, if not available, to the home
		$redirect = wp_get_referer() ? wp_get_referer() : home_url();
	}
	return $redirect;
}
add_filter( 'woocommerce_login_redirect', 'wc_custom_user_redirect', 10, 2 );

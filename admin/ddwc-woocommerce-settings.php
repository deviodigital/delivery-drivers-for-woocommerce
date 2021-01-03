<?php

/**
 * Custom Class for Woocommerce Settings
 *
 * @link       https://www.deviodigital.com
 * @since      1.0.0
 *
 * @package    DDWC
 * @subpackage DDWC/admin
 * @author     Devio Digital <contact@deviodigital.com>
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
	 * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Delivery Drivers tab.
	 * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Delivery Drivers tab.
	 */
	public static function add_settings_tab( $settings_tabs ) {
		$settings_tabs['ddwc'] = esc_attr__( 'Delivery Drivers', 'delivery-drivers-for-woocommerce' );
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

		// Go Pro.
		$go_pro = '';

		// Go Pro link if DDWC Pro plugin isn't active.
		if ( ! function_exists( 'ddwc_pro_all_settings' ) ) {
			$go_pro = ' | <a href="https://www.deviodigital.com/product/delivery-drivers-for-woocommerce-pro" target="_blank" style="font-weight:700;">' . esc_attr__( 'Go Pro', 'delivery-drivers-for-woocommerce' ) . '</a>';
		}

		$settings = array(
			// Section title.
			'ddwc_settings_section_title' => array(
				'name' => esc_attr__( 'Delivery Drivers for WooCommerce', 'delivery-drivers-for-woocommerce' ),
				'type' => 'title',
				'desc' => esc_attr__( 'Brought to you by', 'delivery-drivers-for-woocommerce' ) . ' <a href="https://www.deviodigital.com/" target="_blank">Devio Digital</a>' . $go_pro . ' | <a href="https://www.deviodigital.com/documentation/" target="_blank">' . esc_attr__( 'Documentation', 'delivery-drivers-for-woocommerce' ) . '</a>',
				'id'   => 'ddwc_settings_section_title'
			),
			// Dispatch phone number.
			'dispatch_phone_number' => array(
				'name' => esc_attr__( 'Dispatch phone number', 'delivery-drivers-for-woocommerce' ),
				'type' => 'text',
				'desc' => esc_attr__( 'Allow your drivers to call if they have questions about an order.', 'delivery-drivers-for-woocommerce' ),
				'id'   => 'ddwc_settings_dispatch_phone_number'
			),
			// Google Maps API key.
			'google_maps_api_key' => array(
				'name' => esc_attr__( 'Google Maps API key', 'delivery-drivers-for-woocommerce' ),
				'type' => 'text',
				'desc' => esc_attr__( 'Add a map to the order directions for your drivers.', 'delivery-drivers-for-woocommerce' ),
				'id'   => 'ddwc_settings_google_maps_api_key'
			),
			// Google Maps address type.
			'google_maps_geocode' => array(
				'name'     => esc_attr__( 'Google Maps Geocode', 'delivery-drivers-for-woocommerce' ),
				'desc_tip' => esc_attr__( 'Update your drivers\' order details page to use a customers latitutde and longitude for directions', 'delivery-drivers-for-woocommerce' ),
				'id'       => 'ddwc_settings_google_maps_geocode',
				'type'     => 'checkbox',
				'css'      => 'min-width:300px;',
				'desc'     => esc_attr__( 'Enable Google Maps geocode for latitude and longitude', 'delivery-drivers-for-woocommerce' ),
			),
			// Google Maps mode.
			'google_maps_mode' => array(
				'name'    => esc_attr__( 'Google Maps Mode', 'delivery-drivers-for-woocommerce' ),
				'type'    => 'select',
				'desc'    => esc_attr__( 'Select the method of travel you would like used in your drivers map.', 'delivery-drivers-for-woocommerce' ),
				'id'      => 'ddwc_settings_google_maps_mode',
				'options' => array(
					'driving'   => esc_attr__( 'Driving', 'delivery-drivers-for-woocommerce' ),
					'walking'   => esc_attr__( 'Walking', 'delivery-drivers-for-woocommerce' ),
					'bicycling' => esc_attr__( 'Bicycling', 'delivery-drivers-for-woocommerce' )
				),
			),
			// Driver ratings.
			'driver_ratings' => array(
				'name'    => esc_attr__( 'Driver ratings', 'delivery-drivers-for-woocommerce' ),
				'type'    => 'select',
				'desc'    => esc_attr__( 'Add driver details with delivery star ratings to order details page.', 'delivery-drivers-for-woocommerce' ),
				'id'      => 'ddwc_settings_driver_ratings',
				'options' => array(
					'yes' => esc_attr__( 'Yes', 'delivery-drivers-for-woocommerce' ),
					'no'  => esc_attr__( 'No', 'delivery-drivers-for-woocommerce' )
				),
			),
			// Driver phone number.
			'driver_phone_number' => array(
				'name'    => esc_attr__( 'Driver phone number', 'delivery-drivers-for-woocommerce' ),
				'type'    => 'select',
				'desc'    => esc_attr__( 'Add a button for customers to call driver in the driver details.', 'delivery-drivers-for-woocommerce' ),
				'id'      => 'ddwc_settings_driver_phone_number',
				'options' => array(
					'yes' => esc_attr__( 'Yes', 'delivery-drivers-for-woocommerce' ),
					'no'  => esc_attr__( 'No', 'delivery-drivers-for-woocommerce' )
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
 * Redirect drivers to the Driver Dashboard after login.
 *
 * @param string $redirect
 * @param object $user
 * @return string
 */
function ddwc_custom_user_redirect( $redirect, $user ) {
	// Get the first of all the roles assigned to the user.
	$user_role = $user->roles[0];
	// Dashboard URL for driver's login redirect.
	$dashboard = apply_filters( 'ddwc_driver_dashboard_login_redirect', get_permalink( wc_get_page_id( 'myaccount' ) ) . '/driver-dashboard/' );
	// Redirect page ID.
	$redirect_page_id = url_to_postid( $redirect );
	// Checkout page ID.
	$checkout_page_id = wc_get_page_id( 'checkout' );
	// Redirect normally if user is on checkout page.
	if ( $redirect_page_id == $checkout_page_id ) {
		return $redirect;
	}
	// Redirect delivery drivers to the dashboard.
	if ( 'driver' == $user_role ) {
		$redirect = $dashboard;
	}

	return $redirect;
}
add_filter( 'woocommerce_login_redirect', 'ddwc_custom_user_redirect', 10, 2 );

/**
 * Remove name from formatted addresses
 *
 * This filter is used to remove first and last name from formatted address, which is
 * used in the Driver Dashboard Google Maps display of the customer's address.
 *
 * @access      public
 * @since       2.3
 * @return      string
 */
function ddwc_custom_order_formatted_address( $address ) {
	// Check if $address is array.
	if ( is_array( $address ) ) {
		unset( $address['first_name'] );
		unset( $address['last_name'] );
	}
	return $address;
}

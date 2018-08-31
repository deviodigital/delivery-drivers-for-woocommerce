<?php

/**
 * Custom functions for Wommerce Settings
 *
 * @link       https://www.wpdispensary.com
 * @since      1.0.0
 *
 * @package    DDWC
 * @subpackage DDWC/admin
 */

/**
 * WP Dispensary Details Settings
 *
 * Related to WooCOmmerce Settings API.
 *
 * @since  1.1
 */
class Delivery_Drivers_WooCommerce_Settings {
	/**
	* Bootstraps the class and hooks required actions & filters.
	*
	*/
	public static function init() {
	   add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
	   add_action( 'woocommerce_settings_tabs_ddwc', __CLASS__ . '::settings_tab' );
	   add_action( 'woocommerce_update_options_ddwc', __CLASS__ . '::update_settings' );
	   //add custom type.
	   add_action( 'woocommerce_admin_field_custom_type', __CLASS__ . '::output_custom_type', 10, 1 );
	}

	public static function output_custom_type( $value ) {
	 	//you can output the custom type in any format you'd like.
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
			   'name' => __( 'Delivery Drivers', 'ddwc' ),
			   'type' => 'title',
			   'desc' => 'Brought to you by <a href="http://www.deviodigital.com" target="_blank">Devio Digital</a>',
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


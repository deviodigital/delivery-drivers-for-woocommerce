<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.wpdispensary.com
 * @since      1.0.0
 *
 * @package    WPD_DDWC
 * @subpackage WPD_DDWC/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    WPD_DDWC
 * @subpackage WPD_DDWC/includes
 * @author     WP Dispensary <deviodigital@gmail.com>
 */
class WPD_DDWC_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		/**
		 * Create Delivery Driver user role
		 * 
		 * @since 1.0
		 */
		add_role( 'driver', 'Delivery Driver', array( 'read' => true, 'edit_posts' => false, 'delete_posts' => false ) );

	}

}

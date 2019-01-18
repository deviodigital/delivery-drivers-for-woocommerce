<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.deviodigital.com
 * @since      1.0.0
 *
 * @package    DDWC
 * @subpackage DDWC/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    DDWC
 * @subpackage DDWC/includes
 * @author     Devio Digital <deviodigital@gmail.com>
 */
class Delivery_Drivers_Activator {

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

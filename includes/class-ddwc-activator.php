<?php

/**
 * Fired during plugin activation
 *
 * @package    DDWC
 * @subpackage DDWC/includes
 * @author     Devio Digital <contact@deviodigital.com>
 * @link       https://www.deviodigital.com
 * @since      1.0.0
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @package    DDWC
 * @subpackage DDWC/includes
 * @author     Devio Digital <contact@deviodigital.com>
 * @since      1.0.0
 */
class Delivery_Drivers_Activator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since  1.0.0
     * @return void
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

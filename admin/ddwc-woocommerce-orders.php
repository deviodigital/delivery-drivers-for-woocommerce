<?php

/**
 * Custom functions for Wommerce Orders
 *
 * @link       https://www.deviodigital.com
 * @since      1.0.0
 *
 * @package    DDWC
 * @subpackage DDWC/admin
 */

/**
 * Registering Out for Delivery post status
 */
function ddwc_register_out_for_delivery_order_status() {
    register_post_status( 'wc-out-for-delivery', array(
        'label'                     => 'Out for Delivery',
        'public'                    => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list'    => true,
        'exclude_from_search'       => false,
        'label_count'               => _n_noop( 'Out for Delivery <span class="count">(%s)</span>', 'Out for Delivery <span class="count">(%s)</span>' )
    ) );
}
add_action( 'init', 'ddwc_register_out_for_delivery_order_status' );

/**
 * Registering Out for Delivery post status
 */
function ddwc_register_driver_assigned_order_status() {
    register_post_status( 'wc-driver-assigned', array(
        'label'                     => 'Driver Assigned',
        'public'                    => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list'    => true,
        'exclude_from_search'       => false,
        'label_count'               => _n_noop( 'Driver Assigned <span class="count">(%s)</span>', 'Driver Assigned <span class="count">(%s)</span>' )
    ) );
}
add_action( 'init', 'ddwc_register_driver_assigned_order_status' );

/**
 * Add Out for Delivery Status to list.
 */
function ddwc_add_custom_order_statuses( $order_statuses ) {

    $new_order_statuses = array();

    foreach ( $order_statuses as $key => $status ) {

        $new_order_statuses[ $key ] = $status;

        if ( 'wc-processing' === $key ) {
            $new_order_statuses['wc-driver-assigned'] = 'Driver Assigned';
            $new_order_statuses['wc-out-for-delivery'] = 'Out for Delivery';
        }
    }

    return $new_order_statuses;
}
add_filter( 'wc_order_statuses', 'ddwc_add_custom_order_statuses' );

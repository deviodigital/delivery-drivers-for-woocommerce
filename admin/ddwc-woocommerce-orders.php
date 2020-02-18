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
 * Registering Returned post status
 *
 * @since 2.5
 */
function ddwc_register_order_returned_order_status() {
    register_post_status( 'wc-order-returned', array(
        'label'                     => esc_attr__( 'Order Returned', 'ddwc' ),
        'public'                    => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list'    => true,
        'exclude_from_search'       => false,
        'label_count'               => _n_noop( 'Returned <span class="count">(%s)</span>', 'Returned <span class="count">(%s)</span>' )
    ) );
}
add_action( 'init', 'ddwc_register_order_returned_order_status' );

/**
 * Registering Out for Delivery post status
 *
 * @since 1.0
 */
function ddwc_register_out_for_delivery_order_status() {
    register_post_status( 'wc-out-for-delivery', array(
        'label'                     => esc_attr__( 'Out for Delivery', 'ddwc' ),
        'public'                    => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list'    => true,
        'exclude_from_search'       => false,
        'label_count'               => _n_noop( 'Out for Delivery <span class="count">(%s)</span>', 'Out for Delivery <span class="count">(%s)</span>' )
    ) );
}
add_action( 'init', 'ddwc_register_out_for_delivery_order_status' );

/**
 * Registering Driver Assigned post status
 *
 * @since 1.0
 */
function ddwc_register_driver_assigned_order_status() {
    register_post_status( 'wc-driver-assigned', array(
        'label'                     => esc_attr__( 'Driver Assigned', 'ddwc' ),
        'public'                    => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list'    => true,
        'exclude_from_search'       => false,
        'label_count'               => _n_noop( 'Driver Assigned <span class="count">(%s)</span>', 'Driver Assigned <span class="count">(%s)</span>' )
    ) );
}
add_action( 'init', 'ddwc_register_driver_assigned_order_status' );

/**
 * Add Custom Statuses to the order status list.
 *
 * @param array $order_statuses
 *
 * @return array
 *
 * @since 1.0
 */
function ddwc_add_custom_order_statuses( $order_statuses ) {
    // Create new status array.
    $new_order_statuses = array();
    // Loop though statuses.
    foreach ( $order_statuses as $key => $status ) {
        // Add status to our new statuses.
        $new_order_statuses[ $key ] = $status;
        // Add our custom statuses.
        if ( 'wc-processing' === $key ) {
            $new_order_statuses['wc-driver-assigned']  = esc_attr__( 'Driver Assigned', 'ddwc' );
            $new_order_statuses['wc-out-for-delivery'] = esc_attr__( 'Out for Delivery', 'ddwc' );
            $new_order_statuses['wc-order-returned']   = esc_attr__( 'Order Returned', 'ddwc' );
        }
    }

    return $new_order_statuses;
}
add_filter( 'wc_order_statuses', 'ddwc_add_custom_order_statuses' );

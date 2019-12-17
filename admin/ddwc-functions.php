<?php
/**
 * Helper functions.
 *
 * @link       https://www.deviodigital.com
 * @since      2.0
 *
 * @package    DDWC
 * @subpackage DDWC/admin
 */

/**
 * Change order statuses
 *
 * @since 2.0
 */
function ddwc_driver_dashboard_change_statuses() {

	// Get the order.
	$order_id = filter_input( INPUT_GET, 'orderid' );
	$order    = wc_get_order( $order_id );

	// Out for delivery + note.
	$out_for_delivery = filter_input( INPUT_POST, 'outfordelivery' );
	$driver_note      = filter_input( INPUT_POST, 'outfordeliverymessage' );

	// Redirect URL.
	$redirect_url = get_option( 'woocommerce_myaccount_page_id' ) . 'driver-dashboard/?orderid=' . $order_id;

	do_action( 'ddwc_driver_dashboard_change_statuses_top' );

	// Update order status if marked OUT FOR DELIVERY by Driver.
	if ( isset( $out_for_delivery ) ) {

		// Update order status.
		$order->update_status( 'out-for-delivery' );

		// Add driver note (if added).
		if ( isset( $driver_note ) && ! empty( $driver_note ) ) {
			// The text for the note.
			$note = esc_html__( 'Driver Note', 'ddwc' ) . ': ' . esc_html( $driver_note );
			// Add the note
			$order->add_order_note( $note );
			// Save the data
			$order->save();
		}

		// Run additional functions.
		do_action( 'ddwc_email_customer_order_status_out_for_delivery' );

		// Redirect so the new order details show on the page.
		wp_redirect( get_permalink( apply_filters( 'ddwc_driver_dashboard_change_status_out_for_delivery_url', $redirect_url, $order_id ) ) );

	}

	// Variables for order returned.
	$order_returned = filter_input( INPUT_POST, 'orderreturned' );
	$driver_note    = filter_input( INPUT_POST, 'ordermessage' );

	// Update order status if marked RETURNED by Driver.
	if ( isset( $order_returned ) ) {

		// Update order status.
		$order->update_status( 'order-returned' );

		// Add driver note (if added).
		if ( isset( $driver_note ) && ! empty( $driver_note ) ) {
			// The text for the note.
			$note = esc_html__( 'Driver Note', 'ddwc' ) . ': ' . esc_html( $driver_note );
			// Add the note
			$order->add_order_note( $note );
			// Save the data
			$order->save();
		}

		// Run additional functions.
		do_action( 'ddwc_email_admin_order_status_returned' );

		// Redirect so the new order details show on the page.
		wp_redirect( get_permalink( apply_filters( 'ddwc_driver_dashboard_change_status_returned_url', $redirect_url, $order_id ) ) );

	}

	$order_completed = filter_input( INPUT_POST, 'ordercompleted' );
	$driver_note     = filter_input( INPUT_POST, 'ordermessage' );

	// Update order status if marked COMPLETED by Driver.
	if ( isset( $order_completed ) ) {

		// Update order status.
		$order->update_status( 'completed' );

		// Add driver note (if added).
		if ( isset( $driver_note ) && ! empty( $driver_note ) ) {
			// The text for the note.
			$note = esc_html__( 'Driver Note', 'ddwc' ) . ': ' . esc_html( $driver_note );
			// Add the note
			$order->add_order_note( $note );
			// Save the data
			$order->save();
		}

		// Run additional functions.
		do_action( 'ddwc_email_admin_order_status_completed' );

		// Redirect so the new order details show on the page.
		wp_redirect( get_permalink( apply_filters( 'ddwc_driver_dashboard_change_status_completed_url', $redirect_url, $order_id ) ) );

	}

	do_action( 'ddwc_driver_dashboard_change_statuses_bottom' );

}
add_action( 'ddwc_driver_dashboard_change_status_forms_top', 'ddwc_driver_dashboard_change_statuses' );

/**
 * Change order status forms
 * 
 * Displayed on the driver dashboard, allowing the driver to change
 * the status of an order as they deliver to customer.
 * 
 * @since 2.0
 */
function ddwc_driver_dashboard_change_status_forms() {

	// Get the order ID.
	$order_id     = filter_input( INPUT_GET, 'orderid' );
	$order        = wc_get_order( $order_id );
	$order_data   = $order->get_data();
	$order_status = $order_data['status'];

	do_action( 'ddwc_driver_dashboard_change_status_forms_top' );

	// Create variable.
	$change_status = '';

	if ( 'driver-assigned' == $order_status ) {
		$change_status  = '<h4>' . esc_html__( 'Change Status', 'ddwc' ) . '</h4>';
		$change_status .= '<form method="post">';
		$change_status .= '<p><strong>' . esc_html__( 'Message for shop manager / administrator (optional)', 'ddwc' ) . '</strong></p>';
		$change_status .= '<input type="text" name="outfordeliverymessage" value="" placeholder="' . esc_html__( 'Add a message to the order', 'ddwc' ) . '" class="ddwc-ofdmsg" />';
		$change_status .= '<input type="hidden" name="outfordelivery" value="out-for-delivery" />';
		$change_status .= '<input type="submit" value="' . esc_html__( 'Out for Delivery', 'ddwc' ) . '" class="button ddwc-change-status" />';
		$change_status .= wp_nonce_field( 'ddwc_out_for_delivery_nonce_action', 'ddwc_out_for_delivery_nonce_field' ) . '</form>';
	}

	if ( 'out-for-delivery' == $order_status ) {
		$change_status  = '<h4>' . esc_html__( 'Change Status', 'ddwc' ) . '</h4>';
		$change_status .= '<form method="post">';
		$change_status .= '<p><strong>' . esc_html__( 'Message for shop manager / administrator (optional)', 'ddwc' ) . '</strong></p>';
		$change_status .= '<input type="text" name="ordermessage" value="" placeholder="' . esc_html__( 'Add a message to the order', 'ddwc' ) . '" class="ddwc-ofdmsg" />';
		$change_status .= '<input type="submit" name="orderreturned" value="' . esc_html__( 'Returned', 'ddwc' ) . '" class="button ddwc-change-status order-returned" />';
		$change_status .= '<input type="submit" name="ordercompleted" value="' . esc_html__( 'Completed', 'ddwc' ) . '" class="button ddwc-change-status" />';
		$change_status .= wp_nonce_field( 'ddwc_order_completed_nonce_action', 'ddwc_order_completed_nonce_field' ) . '</form>';
	}

	do_action( 'ddwc_driver_dashboard_change_status_forms_bottom' );

	echo apply_filters( 'ddwc_driver_dashboard_change_status', $change_status, $order_status );

}

/**
 * Checks if a particular user has one or more roles.
 *
 * Returns true on first matching role. Returns false if no roles match.
 *
 * @uses get_userdata()
 * @uses wp_get_current_user()
 *
 * @param array|string $roles Role name (or array of names).
 * @param int $user_id (Optional) The ID of a user. Defaults to the current user.
 * @return bool
 */
function ddwc_check_user_roles( $roles, $user_id = null ) {

    if ( is_numeric( $user_id ) )
        $user = get_userdata( $user_id );
    else
        $user = wp_get_current_user();

    if ( empty( $user ) )
        return false;

    $user_roles = (array) $user->roles;

    foreach ( (array) $roles as $role ) {
        if ( in_array( $role, $user_roles ) )
            return true;
    }

    return false;
}

/**
 * Delivery driver average rating
 *
 * @param int $driver_id
 * @return void
 * @since 2.5
 */
function ddwc_driver_rating( $driver_id ) {
	/**
	 * Args for Orders with Driver ID attached
	 */
	$args = array(
		'post_type'      => 'shop_order',
		'posts_per_page' => -1,
		'post_status'    => 'any',
		'meta_key'       => 'ddwc_driver_id',
		'meta_value'     => $driver_id
	);

	/**
	* Get Orders with Driver ID attached
	*/
	$assigned_orders = get_posts( $args );

	$order_count   = 0;
	$driver_rating = 0;

	/**
	* If Orders have Driver ID attached
	*/
	if ( $assigned_orders ) {
		// Loop through orders.
		foreach ( $assigned_orders as $driver_order ) {

			// Get an instance of the WC_Order object.
			$order = wc_get_order( $driver_order->ID );

			// Get the order data.
			$order_data   = $order->get_data();
			$order_id     = $order_data['id'];
			$order_status = $order_data['status'];

			// Only run if the order is Completed.
			if ( 'completed' === $order_status ) {
				$order_rating = get_post_meta( $order_id, 'ddwc_delivery_rating', TRUE );
				// Display driver rating.
				if ( ! empty( $order_rating ) ) {
					$order_count++; // potential ratings.
					$driver_rating = $driver_rating + $order_rating;
				}
			}
		}
	}

	// Set defaults.
	$average_rating      = NULL;
	$driver_rating_final = '';

	if ( 0 != $driver_rating ) {
		// Average rating.
		$average_rating      = $driver_rating / $order_count;
		$average_rating      = round( $average_rating, 1 );
		// Driver rating final.
		$driver_rating_final = $average_rating . '/5';
	}

	return $driver_rating_final;
}

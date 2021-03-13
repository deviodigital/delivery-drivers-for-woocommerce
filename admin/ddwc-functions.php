<?php
/**
 * Helper functions.
 *
 * @link       https://www.deviodigital.com
 * @since      2.0
 *
 * @package    DDWC
 * @subpackage DDWC/admin
 * @author     Devio Digital <contact@deviodigital.com>
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
	$redirect_url = apply_filters( 'ddwc_driver_dashboard_change_statuses_redirect_url', get_option( 'woocommerce_myaccount_page_id' ) . 'driver-dashboard/?orderid=' . $order_id );

	do_action( 'ddwc_driver_dashboard_change_statuses_top' );

	// Update order status if marked OUT FOR DELIVERY by Driver.
	if ( isset( $out_for_delivery ) ) {

		// Update order status.
		$order->update_status( 'out-for-delivery' );

		// Add driver note (if added).
		if ( isset( $driver_note ) && ! empty( $driver_note ) ) {
			// The text for the note.
			$note = esc_attr__( 'Driver Note', 'delivery-drivers-for-woocommerce' ) . ': ' . esc_html( $driver_note );
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
			$note = esc_attr__( 'Driver Note', 'delivery-drivers-for-woocommerce' ) . ': ' . esc_html( $driver_note );
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
			$note = esc_attr__( 'Driver Note', 'delivery-drivers-for-woocommerce' ) . ': ' . esc_html( $driver_note );
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
add_action( 'wp_loaded', 'ddwc_driver_dashboard_change_statuses' );

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

	// Change status form if status is "driver assigned".
	if ( 'driver-assigned' == $order_status ) {
		$change_status  = '<h4>' . esc_attr__( 'Change Status', 'delivery-drivers-for-woocommerce' ) . '</h4>';
		$change_status .= '<form method="post">';
		$change_status .= '<p><strong>' . esc_attr__( 'Message for shop manager / administrator (optional)', 'delivery-drivers-for-woocommerce' ) . '</strong></p>';
		$change_status .= '<input type="text" name="outfordeliverymessage" value="" placeholder="' . esc_attr__( 'Add a message to the order', 'delivery-drivers-for-woocommerce' ) . '" class="ddwc-ofdmsg" />';
		$change_status .= '<input type="hidden" name="outfordelivery" value="out-for-delivery" />';
		$change_status .= '<input type="submit" value="' . esc_attr__( 'Out for Delivery', 'delivery-drivers-for-woocommerce' ) . '" class="button ddwc-change-status" />';
		$change_status .= wp_nonce_field( 'ddwc_out_for_delivery_nonce_action', 'ddwc_out_for_delivery_nonce_field' ) . '</form>';
	}

	// Change status form if status is "out for delivery".
	if ( 'out-for-delivery' == $order_status ) {
		$change_status  = '<h4>' . esc_attr__( 'Change Status', 'delivery-drivers-for-woocommerce' ) . '</h4>';
		$change_status .= '<form method="post">';
		$change_status .= '<p><strong>' . esc_attr__( 'Message for shop manager / administrator (optional)', 'delivery-drivers-for-woocommerce' ) . '</strong></p>';
		$change_status .= '<input type="text" name="ordermessage" value="" placeholder="' . esc_attr__( 'Add a message to the order', 'delivery-drivers-for-woocommerce' ) . '" class="ddwc-ofdmsg" />';
		$change_status .= '<input type="submit" name="orderreturned" value="' . esc_attr__( 'Returned', 'delivery-drivers-for-woocommerce' ) . '" class="button ddwc-change-status order-returned" />';
		$change_status .= '<input type="submit" name="ordercompleted" value="' . esc_attr__( 'Completed', 'delivery-drivers-for-woocommerce' ) . '" class="button ddwc-change-status" />';
		$change_status .= wp_nonce_field( 'ddwc_order_completed_nonce_action', 'ddwc_order_completed_nonce_field' ) . '</form>';
	}

	do_action( 'ddwc_driver_dashboard_change_status_forms_bottom' );

	// Display order status form.
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
	// Set user.
    if ( is_numeric( $user_id ) ) {
        $user = get_userdata( $user_id );
	} else {
        $user = wp_get_current_user();
	}
	// Bail if no user.
    if ( empty( $user ) ) {
        return false;
	}
	// Get user roles.
    $user_roles = (array) $user->roles;
	// Loop through user roles.
    foreach ( (array) $roles as $role ) {
        if ( in_array( $role, $user_roles ) ) {
			return true;
		}
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
	$order_count     = 0;
	$driver_rating   = 0;

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
	// Star icon.
	$star = '<i class="fas fa-star"></i>';

	if ( 0 != $driver_rating ) {
		// Average rating.
		$average_rating = $driver_rating / $order_count;
		$average_rating = round( $average_rating, 1 );
		// Driver rating final.
		$driver_rating_final = str_repeat( $star, $average_rating ); 
	}

	return $driver_rating_final;
}

/**
 * Delivery Adddress Google Map Geocode
 *
 * @param string $delivery_address
 * @since 2.7
 */
function ddwc_delivery_address_google_map_geocode( $delivery_address ) {

	// Prepare the delivery address for Google Maps geocode.
	$delivery_address = str_replace( ' ', '+', $delivery_address );

	// Get delivery address details from Google Maps.
	$geocode = file_get_contents( 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $delivery_address . '&key=' . get_option( 'ddwc_settings_google_maps_api_key' ) );
	$output  = json_decode( $geocode );

	// Get the delivery address latitude and longitude.
	$latitude  = $output->results[0]->geometry->location->lat;
	$longitude = $output->results[0]->geometry->location->lng;

	// Delivery address (lat/lng).
	$delivery_address = $latitude . ',' . $longitude;

	// Error messages.
	if ( 'OK' != $output->status ) {
		// Remove the map via it's filter.
		add_filter( 'ddwc_delivery_address_google_map', '__return_false' );

		// Default error message.
		$error_message = __( 'The delivery address is returning NULL.', 'delivery-drivers-for-woocommerce' );

		// Google Maps error message.
		if ( NULL != $output ) {
			$error_message = $output->error_message;
		}
		// Display an error message.
		echo '<p class="ddwc-map-api-error-msg">' . esc_html( $error_message ) . '</p>';
	}

	return $delivery_address;
}

/**
 * Driver table for administrators
 * 
 * This function is used in the driver_dashboard in order to display a table of all 
 * drivers, along with specific details about each driver.
 * 
 * @since  3.0
 * @return string
 */
function ddwc_driver_dashboard_admin_drivers_table() {
	// Driver args.
	$args = array(
		'role'    => 'driver',
		'orderby' => 'user_nicename',
		'order'   => 'ASC'
	);

	// Filter args.
	$args = apply_filters( 'ddwc_driver_dashboard_admin_drivers_args', $args );

	// Get users.
	$drivers = get_users( $args );

	/**
	 * If Orders have Driver ID attached
	 */
	if ( $drivers ) {

		$thead = array(
			esc_attr__( 'Name', 'delivery-drivers-for-woocommerce' ),
			esc_attr__( 'Status', 'delivery-drivers-for-woocommerce' ),
			esc_attr__( 'Rating', 'delivery-drivers-for-woocommerce' ),
			esc_attr__( 'Contact', 'delivery-drivers-for-woocommerce' ),
		);

		$thead = apply_filters( 'ddwc_driver_dashboard_admin_table_thead', $thead );

		// Drivers table title.
		$drivers_table  = '<h3 class="ddwc delivery-drivers">' . __( 'Delivery Drivers', 'delivery-drivers-for-woocommerce' ) . '</h3>';

		// Drivers table start.
		$drivers_table .= '<table class="ddwc-dashboard delivery-drivers">';

		// Drivers table head.
		$drivers_table .= '<thead><tr>';

		// Loop through $thead.
		foreach ( $thead as $row ) {
			// Add td to thead.
			$drivers_table .= '<td>' . $row . '</td>';
		}

		// End drivers table thead.
		$drivers_table .= '</tr></thead>';

		// Drivers table tbody.
		$drivers_table .= '<tbody>';

		// Loop through drivers.
		foreach ( $drivers as $driver ) {
			// Driver unavailable.
			$availability = '<span class="driver-status unavailable">' . esc_attr__( 'Unavailable', 'delivery-drivers-for-woocommerce' ) . '</span>';

			// Driver available.
			if ( get_user_meta( $driver->ID, 'ddwc_driver_availability', true ) ) {
				$availability = '<span class="driver-status available">' . esc_attr__( 'Available', 'delivery-drivers-for-woocommerce' ) . '</span>';
			}

			// Driver rating.
			$driver_rating_final = ddwc_driver_rating( $driver->ID );

			// Driver phone number.
			$driver_number = get_user_meta( $driver->ID, 'billing_phone', true );

			// Empty var.
			$phone_number = '';

			// Driver phone number button.
			if ( $driver_number ) {
				$phone_number = '<a href="tel:' . esc_html( $driver_number ) . '" class="button ddwc-button"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path d="M20 22.621l-3.521-6.795c-.008.004-1.974.97-2.064 1.011-2.24 1.086-6.799-7.82-4.609-8.994l2.083-1.026-3.493-6.817-2.106 1.039c-7.202 3.755 4.233 25.982 11.6 22.615.121-.055 2.102-1.029 2.11-1.033z"/></svg></a>';
			}

			// Get driver userdata.
			$user_info = get_userdata( $driver->ID );

			// Driver email address.
			$driver_email = $user_info->user_email;

			// Empty var.
			$email_address = '';

			// Driver email address button.
			if ( $driver_email ) {
				$email_address = '<a href="mailto:' . esc_html( $driver_email ) . '" class="button ddwc-button"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path d="M0 3v18h24v-18h-24zm6.623 7.929l-4.623 5.712v-9.458l4.623 3.746zm-4.141-5.929h19.035l-9.517 7.713-9.518-7.713zm5.694 7.188l3.824 3.099 3.83-3.104 5.612 6.817h-18.779l5.513-6.812zm9.208-1.264l4.616-3.741v9.348l-4.616-5.607z"/></svg></a>';
			}

			if ( get_user_meta( $driver->ID, 'ddwc_driver_picture', true ) ) {
				$driver_pic = get_user_meta( $driver->ID, 'ddwc_driver_picture', true );
				$driver_img = '<a href="' . $driver_pic['url'] . '"><img src="' . $driver_pic['url'] . '" alt="' . esc_html( $driver->display_name ) . '" /></a>';
			}

			$tbody = array(
				$driver_img . '<span class="driver-name">' . esc_html( $driver->display_name ) . '</span> <a href="' . admin_url( 'user-edit.php?user_id=' . $driver->ID ) . '">(' . __( 'edit', 'delivery-drivers-for-woocommerce' ) . ')</a>',
				$availability,
				$driver_rating_final,
				$email_address . $phone_number,
			);

			$tbody = apply_filters( 'ddwc_driver_dashboard_admin_table_tbody', $tbody );

			// Drivers tbody tr.
			$drivers_table .= '<tr>';

			// Loop through $tbody.
			foreach ( $tbody as $row ) {
				// Add td to tbody.
				$drivers_table .= '<td>' . $row . '</td>';
			}

			// End drivers table tbody tr.
			$drivers_table .= '</tr>';
		}

		// End drivers table tbody.
		$drivers_table .= '</tbody>';

		// End drivers table.
		$drivers_table .= '</table>';
	}

	do_action( 'ddwc_admin_drivers_table_before' );

	// Display table.
	echo $drivers_table;

	do_action( 'ddwc_admin_drivers_table_after' );
}

/**
 * WooCommerce Store Address.
 * 
 * @return string
 */
function ddwc_woocommerce_store_address() {
	// The store address.
	$store_address   = get_option( 'woocommerce_store_address' );
	$store_address_2 = get_option( 'woocommerce_store_address_2' );
	$store_city      = get_option( 'woocommerce_store_city' );
	$store_postcode  = get_option( 'woocommerce_store_postcode' );

	// The store country/state.
	$store_raw_country = get_option( 'woocommerce_default_country' );

	// Split the store country/state.
	$split_country = explode( ':', $store_raw_country );

	// Check to see if State & Country are available.
	if ( false == strpos( $store_raw_country, ':' ) ) {
		// Store country only.
		$store_country = $split_country[0];
		$store_state   = '';
	} else {
		// Store country and state separated.
		$store_country = $split_country[0];
		$store_state   = $split_country[1];
	}

	// Create store address.
	$store_address = $store_address . ' ' . $store_address_2 . ' ' . $store_city . ' ' . $store_state . ' ' . $store_postcode . ' ' . $store_country;

	// Filter the store address.
	$store_address = apply_filters( 'ddwc_driver_dashboard_store_address', $store_address );

	return $store_address;
}

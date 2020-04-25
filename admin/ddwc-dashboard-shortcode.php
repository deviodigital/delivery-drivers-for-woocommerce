<?php
/**
 * The Delivery Driver Dashboard Shortcode.
 *
 * @link       https://www.deviodigital.com
 * @since      1.0.0
 *
 * @package    DDWC
 * @subpackage DDWC/admin
 */
function ddwc_dashboard_shortcode() {

	// Check if user is logged in.
	if ( is_user_logged_in() ) {
		// Get the user ID.
		$user_id = get_current_user_id();

		// Get the order ID.
		$order_id = filter_input( INPUT_GET, 'orderid' );

		// Get the user object.
		$user_meta = get_userdata( $user_id );

		// If user_id doesn't equal zero.
		if ( 0 != $user_id ) {

			// Get all the user roles as an array.
			$user_roles = $user_meta->roles;

			// Check if the role you're interested in, is present in the array.
			if ( in_array( 'driver', $user_roles, true ) ) {

				if ( isset( $order_id ) && ( '' != $order_id ) ) {
					$driver_id = get_post_meta( $order_id, 'ddwc_driver_id', true );
				}

				// Display order info if ?orderid is set and driver is assigned.
				if ( isset( $order_id ) && ( '' != $order_id ) && ( $driver_id == $user_id ) ) {

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

					// Get an instance of the WC_Order object
					$order = wc_get_order( $order_id );

					// Get the order data.
					$order_data = $order->get_data();

					// Specific order data.
					$order_id             = $order_data['id'];
					$order_parent_id      = $order_data['parent_id'];
					$order_status         = $order_data['status'];
					$order_currency       = $order_data['currency'];
					$order_version        = $order_data['version'];
					$order_payment_method = $order_data['payment_method'];
					$order_date_created   = $order_data['date_created']->date( apply_filters( 'ddwc_date_format', get_option( 'date_format' ) ) );
					$order_time_created   = $order_data['date_created']->date( apply_filters( 'ddwc_time_format', get_option( 'time_format' ) ) );
					$order_discount_total = $order_data['discount_total'];
					$order_discount_tax   = $order_data['discount_tax'];
					$order_shipping_total = $order_data['shipping_total'];
					$order_shipping_tax   = $order_data['shipping_tax'];
					$order_cart_tax       = $order_data['cart_tax'];
					$order_total          = $order_data['total'];
					$order_total_tax      = $order_data['total_tax'];
					$order_customer_id    = $order_data['customer_id'];
					$order_shipping_addr  = $order_data['shipping']['address_1'];
					$order_shipping_fname = $order_data['shipping']['first_name'];
					$order_shipping_lname = $order_data['shipping']['last_name'];
					$order_billing_fname  = $order_data['billing']['first_name'];
					$order_billing_lname  = $order_data['billing']['last_name'];
					$order_billing_phone  = $order_data['billing']['phone'];

					echo '<div class="ddwc-orders">';

					// Display order number.
					if ( isset( $order_id ) ) {
						echo '<h3 class="ddwc">' . esc_attr__( 'Order #', 'ddwc' ) . apply_filters( 'ddwc_order_number', $order_id ) . ' <span class="' . esc_attr( $order_status ) . '">' . wc_get_order_status_name( $order_status ) . '</span></h3>';
					}

					// Display a button to call the customers phone number.
					echo '<p>';

						$phone_customer = '';
						$phone_dispatch = '';

						// Call Customer button.
						if ( isset( $order_billing_phone ) ) {
							$phone_customer = '<a href="tel:' . esc_html( $order_billing_phone ) . '" class="button ddwc-button customer">' . esc_attr__( 'Call Customer', 'ddwc' ) . '</a> ';
						}

						// Call Dispatch button.
						if ( false !== get_option( 'ddwc_settings_dispatch_phone_number' ) && '' !== get_option( 'ddwc_settings_dispatch_phone_number' ) ) {
							$phone_dispatch = '<a href="tel:' . get_option( 'ddwc_settings_dispatch_phone_number' ) . '" class="button ddwc-button dispatch">' . esc_attr__( 'Call Dispatch', 'ddwc' ) . '</a> ';
						}

						// Call buttons.
						$phone_numbers = apply_filters( 'ddwc_driver_dashboard_phone_numbers', $phone_dispatch . $phone_customer );

						echo $phone_numbers;

					echo '</p>';

					echo '<h4>' . esc_attr__( 'Delivery Address', 'ddwc' ) . '</h4>';

					// Plain text delivery address.
					if ( '' == get_option( 'ddwc_settings_google_maps_api_key' ) ) {
						$plain_address = '<p>';
						if ( isset( $order_shipping_addr ) && '' !== $order_shipping_addr ) {
							add_filter( 'woocommerce_order_formatted_shipping_address' , 'ddwc_custom_order_formatted_address' );
							$plain_address   .= $order->get_formatted_shipping_address();
							$delivery_address = str_replace( '<br/>', ' ', $order->get_formatted_shipping_address() );
						} else {
							add_filter( 'woocommerce_order_formatted_billing_address' , 'ddwc_custom_order_formatted_address' );
							$plain_address   .= $order->get_formatted_billing_address();
							$delivery_address = str_replace( '<br/>', ' ', $order->get_formatted_billing_address() );
						}
						$plain_address  .= '</p>';
						$directions_link = 'https://www.google.com/maps/search/?api=1&query=' . $delivery_address;
						$directions_text = esc_attr__( 'Get Directions', 'ddwc' );
						$plain_address  .= '<p><a target="_blank" href="' . apply_filters( 'ddwc_delivery_address_directions_link', $directions_link, $delivery_address ) . '" class="button">' . apply_filters( 'ddwc_delivery_address_directions_text', $directions_text ) . '</a></p>';

						// Display the plain text delivery address.
						echo apply_filters( 'ddwc_delivery_address_plain_text', $plain_address, $delivery_address );
					}

					/**
					 * Display a Google Map with the customers address if an API key is added to
					 * the WooCommerce Settings page.
					 */
					if ( false !== get_option( 'ddwc_settings_google_maps_api_key' ) && '' !== get_option( 'ddwc_settings_google_maps_api_key' ) ) {
						// Use the Shipping address if available.
						if ( isset( $order_shipping_addr ) && '' !== $order_shipping_addr ) {
							add_filter( 'woocommerce_order_formatted_shipping_address' , 'ddwc_custom_order_formatted_address' );
							$delivery_address = str_replace( '<br/>', ' ', $order->get_formatted_shipping_address() );
						} else {
							add_filter( 'woocommerce_order_formatted_billing_address' , 'ddwc_custom_order_formatted_address' );
							$delivery_address = str_replace( '<br/>', ' ', $order->get_formatted_billing_address() );
						}

						// Check if the Google Maps Geocode checkbox has been selected.
						if ( 'yes' == get_option( 'ddwc_settings_google_maps_geocode' ) ) {
							// Google Maps Delivery Address Geocode.
							$delivery_address = ddwc_delivery_address_google_map_geocode( $delivery_address );
						}

						// Map mode, based on WooCommerce settings.
						if ( NULL !== get_option( 'ddwc_settings_google_maps_mode' ) ) {
							$mode = get_option( 'ddwc_settings_google_maps_mode' );
						} else {
							$mode = esc_attr__( 'driving', 'ddwc' );
						}

						// Map mode.
						$map_mode = '&mode=' . apply_filters( 'ddwc_delivery_address_google_map_mode', $mode );

						// Create the Google Map.
						$google_map = '<iframe width="600" height="450" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/directions?origin=' . apply_filters( 'ddwc_google_maps_origin_address', $store_address ) . '&destination=' . apply_filters( 'ddwc_google_maps_delivery_address', $delivery_address ) . '&key=' . get_option( 'ddwc_settings_google_maps_api_key' ) . $map_mode . '" allowfullscreen></iframe>';

						// Display the Google Map with delivery address.
						echo apply_filters( 'ddwc_delivery_address_google_map', $google_map, $delivery_address, $store_address );
					}

					echo '<h4>' . esc_attr__( 'Order details', 'ddwc' ) . '</h4>';

					do_action( 'ddwc_driver_dashboard_order_details_table_before' );

					// Get payment gateway details.
					$payment_gateway = wc_get_payment_gateway_by_order( $order_id );

					echo '<table class="ddwc-dashboard">';
					echo '<tbody>';

					do_action( 'ddwc_driver_dashboard_order_details_table_tbody_top' );

					// Display customer.
					if ( '' !== $order_shipping_fname ) {
						echo '<tr><td><strong>' . esc_attr__( 'Customer', 'ddwc' ) . '</strong></td><td>' . $order_shipping_fname . ' ' . $order_shipping_lname . '</td></tr>';
					} elseif ( '' !== $order_billing_fname ) {
						echo '<tr><td><strong>' . esc_attr__( 'Customer', 'ddwc' ) . '</strong></td><td>' . $order_billing_fname . ' ' . $order_billing_lname . '</td></tr>';
					} else {
						// Do nothing.
					}

					// Display payment method details.
					if ( isset( $payment_gateway ) && FALSE !== $payment_gateway ) {
						$payment_method = '<tr><td><strong>' . esc_attr__( 'Payment method', 'ddwc' ) . '</strong></td><td>' . $payment_gateway->title . '</td></tr>';
						echo apply_filters( 'ddwc_driver_dashboard_payment_method', $payment_method );
					}

					// Display order date.
					if ( isset( $order_date_created ) ) {
						echo '<tr><td><strong>' . esc_attr__( 'Order date', 'ddwc' ) . '</strong></td><td>' . esc_html( $order_date_created ) . ' - ' . esc_html( $order_time_created ) . '</td></tr>';
					}

					do_action( 'ddwc_driver_dashboard_order_details_table_tbody_bottom' );

					echo '</tbody>';
					echo '</table>';

					do_action( 'ddwc_driver_dashboard_order_details_table_after' );

					do_action( 'ddwc_driver_dashboard_order_table_before' );

					// Set up total title.
					$total_title = '<td>' . esc_attr__( 'Total', 'ddwc' ) . '</td>';

					echo '<table class="ddwc-dashboard">';
					echo '<thead><tr><td>' . esc_attr__( 'Product', 'ddwc' ) . '</td><td>' . esc_attr__( 'Qty', 'ddwc' ) . '</td>' . apply_filters( 'ddwc_driver_dashboard_total_title', $total_title ) . '</tr></thead>';
					echo '<tbody>';

					do_action( 'ddwc_driver_dashboard_order_table_tbody_top' );

					// get an instance of the WC_Order object.
					$order_items     = wc_get_order( $order_id );
					$currency_code   = $order_items->get_currency();
					$currency_symbol = get_woocommerce_currency_symbol( $currency_code );

					if ( ! empty( $order_items ) ) {
						// The loop to get the order items which are WC_Order_Item_Product objects since WC 3+
						foreach( $order_items->get_items() as $item_id=>$item_product ) {
							//Get the WC_Product object
							$product  = $item_product->get_product();
							// Get the product quantity.
							$quantity = $item_product->get_quantity();
							// Get the product details.
							$name        = $product->get_name();
							$price       = $product->get_price();
							$qtty        = $quantity;
							$qtty_price  = $qtty * $price;
							$price       = '<td>' . $currency_symbol . number_format( $qtty_price, 2 ) . '</td>';
							$total_price = apply_filters( 'ddwc_driver_dashboard_order_item_price', $price );
							/**
							 * @todo add thumbnail image next to the product name.
							 */
							echo '<tr><td>' . $name . '</td><td>' . $qtty . '</td>' . $total_price . '</tr>';
						}
					} else {
						// Do nothing.
					}

					do_action( 'ddwc_driver_dashboard_order_table_tbody_before_delivery' );

					// Delivery Total.
					$delivery_total = '<tr class="delivery-charge"><td colspan="2"><strong>' . esc_attr__( 'Delivery', 'ddwc' ) . '</strong></td><td class="total">' . $currency_symbol . number_format((float)$order_shipping_total, 2, '.', ',' ) . '</td></tr>';

					echo apply_filters( 'ddwc_driver_dashboard_delivery_total', $delivery_total );

					do_action( 'ddwc_driver_dashboard_order_table_tbody_before_total' );

					// Order total.
					$order_total = '<tr class="order-total"><td colspan="2"><strong>' . esc_attr__( 'Order total', 'ddwc' ) . '</strong></td><td class="total">' . $currency_symbol . $order_total . '</td></tr>';

					echo apply_filters( 'ddwc_driver_dashboard_order_total', $order_total );

					do_action( 'ddwc_driver_dashboard_order_table_tbody_bottom' );

					echo '</tbody>';
					echo '</table>';

					do_action( 'ddwc_driver_dashboard_order_table_after' );

					// Change status forms.
					apply_filters( 'ddwc_driver_dashboard_change_status_forms', ddwc_driver_dashboard_change_status_forms() );

					echo '</div>';

				} else {

					do_action( 'ddwc_driver_dashboard_top' );

					/**
					 * Args for Orders with Driver ID attached
					 */
					$args = array(
						'post_type'      => 'shop_order',
						'posts_per_page' => -1,
						'post_status'    => 'any',
						'meta_key'       => 'ddwc_driver_id',
						'meta_value'     => $user_id
					);

					/**
					 * Get Orders with Driver ID attached
					 */
					$assigned_orders = get_posts( $args );

					/**
					 * If Orders have Driver ID attached
					 */
					if ( $assigned_orders ) {

						do_action( 'ddwc_assigned_orders_title_before' );

						echo '<h3 class="ddwc assigned-orders">' . esc_attr__( 'Assigned Orders', 'ddwc' ) . '</h3>';

						do_action( 'ddwc_assigned_orders_table_before' );

						$total_title = '<td>' . esc_attr__( 'Total', 'ddwc' ) . '</td>';

						echo '<table class="ddwc-dashboard">';
						echo '<thead><tr><td>' . esc_attr__( 'ID', 'ddwc' ) . '</td><td>' . esc_attr__( 'Date', 'ddwc' ) . '</td><td>' . esc_attr__( 'Status', 'ddwc' ) . '</td>' . apply_filters( 'ddwc_driver_dashboard_assigned_orders_total_title', $total_title ) . '</tr></thead>';
						echo '<tbody>';
						foreach ( $assigned_orders as $driver_order ) {

							// Get an instance of the WC_Order object.
							$order = wc_get_order( $driver_order->ID );

							// Get the order data.
							$order_data           = $order->get_data();
							$currency_code        = $order_data['currency'];
							$currency_symbol      = get_woocommerce_currency_symbol( $currency_code );
							$order_id             = $order_data['id'];
							$order_parent_id      = $order_data['parent_id'];
							$order_status         = $order_data['status'];
							$order_currency       = $order_data['currency'];
							$order_version        = $order_data['version'];
							$order_payment_method = $order_data['payment_method'];
							$order_date_created   = $order_data['date_created']->date( apply_filters( 'ddwc_date_format', get_option( 'date_format' ) ) );
							$order_discount_total = $order_data['discount_total'];
							$order_discount_tax   = $order_data['discount_tax'];
							$order_shipping_total = $order_data['shipping_total'];
							$order_shipping_tax   = $order_data['shipping_tax'];
							$order_cart_tax       = $order_data['cart_tax'];
							$order_total          = $order_data['total'];
							$order_total_tax      = $order_data['total_tax'];
							$order_customer_id    = $order_data['customer_id'];

							// Statuses for driver.
							$statuses = array(
								'processing',
								'driver-assigned',
								'out-for-delivery',
								'order-returned'
							);

							// Filter the statuses.
							$statuses = apply_filters( 'ddwc_driver_dashboard_assigned_orders_statuses', $statuses );

							// Add orders to table if order status matches item in $statuses array.
							if ( in_array( $order_status, $statuses ) ) {
								echo '<tr>';
								echo '<td><a href="' . esc_url( apply_filters( 'ddwc_driver_dashboard_assigned_orders_order_details_url', '?orderid=' . $driver_order->ID, $driver_order->ID ) ) . '">' . esc_html( apply_filters( 'ddwc_order_number', $driver_order->ID ) ) . '</a></td>';
								echo '<td>' . $order_date_created . '</td>';
								echo '<td>' . wc_get_order_status_name( $order_status ) . '</td>';

								if ( isset( $order_total ) ) {
									$order_total = '<td>'  . $currency_symbol . $order_total . '</td>';
									echo apply_filters( 'ddwc_driver_dashboard_assigned_orders_total', $order_total, $driver_order->ID );
								} else {
									echo '<td>-</td>';
								}

								echo '</tr>';
							}
						}
						echo '</tbody>';
						echo '</table>';

						do_action( 'ddwc_assigned_orders_table_after' );

						echo '<h4 class="ddwc assigned-orders">' . esc_attr__( 'Completed Orders', 'ddwc' ) . '</h4>';

						do_action( 'ddwc_completed_orders_table_before' );

						$total_title = '<td>' . esc_attr__( 'Total', 'ddwc' ) . '</td>';

						echo '<table class="ddwc-dashboard">';
						echo '<thead><tr><td>' . esc_attr__( 'ID', 'ddwc' ) . '</td><td>' . esc_attr__( 'Date', 'ddwc' ) . '</td><td>' . esc_attr__( 'Status', 'ddwc' ) . '</td>' . apply_filters( 'ddwc_driver_dashboard_completed_orders_total_title', $total_title ) . '</tr></thead>';
						echo do_action( 'ddwc_driver_dashboard_completed_orders_before_tbody' );
						echo '<tbody>';
						echo do_action( 'ddwc_driver_dashboard_completed_orders_tbody_top' );
						foreach ( $assigned_orders as $driver_order ) {

							// Get an instance of the WC_Order object.
							$order = wc_get_order( $driver_order->ID );

							// Get order data.
							$order_data           = $order->get_data();
							$currency_code        = $order_data['currency'];
							$currency_symbol      = get_woocommerce_currency_symbol( $currency_code );
							$order_id             = $order_data['id'];
							$order_parent_id      = $order_data['parent_id'];
							$order_status         = $order_data['status'];
							$order_currency       = $order_data['currency'];
							$order_version        = $order_data['version'];
							$order_payment_method = $order_data['payment_method'];
							$order_date_created   = $order_data['date_created']->date( apply_filters( 'ddwc_date_format', get_option('date_format') ) );
							$order_discount_total = $order_data['discount_total'];
							$order_discount_tax   = $order_data['discount_tax'];
							$order_shipping_total = $order_data['shipping_total'];
							$order_shipping_tax   = $order_data['shipping_tax'];
							$order_cart_tax       = $order_data['cart_tax'];
							$order_total          = $order_data['total'];
							$order_total_tax      = $order_data['total_tax'];
							$order_customer_id    = $order_data['customer_id'];

							if ( 'completed' === $order_status ) {
								echo '<tr>';
								echo '<td><a href="?orderid=' . $driver_order->ID . '">' . apply_filters( 'ddwc_order_number', $driver_order->ID ) . '</a></td>';
								echo '<td>' . $order_date_created . '</td>';
								echo '<td>' . wc_get_order_status_name( $order_status ) . '</td>';

								if ( isset( $order_total ) ) {
									$order_total = '<td>'  . $currency_symbol . $order_total . '</td>';
									echo apply_filters( 'ddwc_driver_dashboard_completed_orders_total', $order_total, $driver_order->ID );
								} else {
									echo '<td>-</td>';
								}

								echo '</tr>';
							} else {
								// Do nothing.
							}
						}
						echo do_action( 'ddwc_driver_dashboard_completed_orders_tbody_bottom' );
						echo '</tbody>';
						echo do_action( 'ddwc_driver_dashboard_completed_orders_after_tbody' );
						echo '</table>';

						do_action( 'ddwc_completed_orders_table_after' );

					} else {

						do_action( 'ddwc_assigned_orders_empty_before' );

						// Message - No assigned orders.
						$empty  = '<h3 class="ddwc assigned-orders">' . esc_attr__( 'Assigned Orders', 'ddwc' ) . '</h3>';
						$empty .= '<p>' . esc_attr__( 'You do not have any assigned orders.', 'ddwc' ) . '</p>';

						echo apply_filters( 'ddwc_assigned_orders_empty', $empty );

						do_action( 'ddwc_assigned_orders_empty_after' );
					}

					do_action( 'ddwc_driver_dashboard_bottom' );

				}
			} elseif ( ddwc_check_user_roles( array( 'administrator' ) ) ) {

				/**
				 * Driver Dashboard for Administrator
				 *
				 * @since 2.6
				 */

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
						esc_attr__( 'Name', 'ddwc' ),
						esc_attr__( 'Status', 'ddwc' ),
						esc_attr__( 'Rating', 'ddwc' ),
						esc_attr__( 'Contact', 'ddwc' ),
					);

					$thead = apply_filters( 'ddwc_driver_dashboard_admin_table_thead', $thead );

					// Drivers table title.
					$drivers_table  = '<h3 class="ddwc delivery-drivers">' . __( 'Delivery Drivers', 'ddwc' ) . '</h3>';

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
						$availability = '<span class="driver-status unavailable">' . esc_attr__( 'Unavailable', 'ddwc' ) . '</span>';

						// Driver available.
						if ( get_user_meta( $driver->ID, 'ddwc_driver_availability', true ) ) {
							$availability = '<span class="driver-status available">' . esc_attr__( 'Available', 'ddwc' ) . '</span>';
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

						$tbody = array(
							esc_html( $driver->display_name ) . ' <a href="' . admin_url( 'user-edit.php?user_id=' . $driver->ID ) . '">(' . __( 'edit', 'ddwc' ) . ')</a>',
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

			} else {

				// Set the Access Denied page text.
				$access_denied = '<h3 class="ddwc access-denied">' . esc_attr__( 'Access Denied', 'ddwc' ) . '</h3><p>' . esc_attr__( 'Sorry, but you are not able to view this page.', 'ddwc' ) . '</p>';

				// Return the Access Denied text, filtered.
				return apply_filters( 'ddwc_access_denied', $access_denied );
			}

		} else {
			// Do nothing.
		}
	} else {
		// Display login form.
		apply_filters( 'ddwc_dashboard_login_form', wp_login_form() );
	}
}
add_shortcode( 'ddwc_dashboard', 'ddwc_dashboard_shortcode' );

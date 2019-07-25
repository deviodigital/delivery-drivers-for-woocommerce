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

		// Get the user object.
		$user_meta = get_userdata( $user_id );

		// If user_id doesn't equal zero.
		if ( 0 != $user_id ) {

			// Get all the user roles as an array.
			$user_roles = $user_meta->roles;

			// Check if the role you're interested in, is present in the array.
			if ( in_array( 'driver', $user_roles, true ) ) {

				if ( isset( $_GET['orderid'] ) && ( '' != $_GET['orderid'] ) ) {
					$driver_id = get_post_meta( $_GET['orderid'], 'ddwc_driver_id', true );
				}

				// Display order info if ?orderid is set and driver is assigned.
				if ( isset( $_GET['orderid'] ) && ( '' != $_GET['orderid'] ) && ( $driver_id == $user_id ) ) {

					// The store address.
					$store_address     = get_option( 'woocommerce_store_address' );
					$store_address_2   = get_option( 'woocommerce_store_address_2' );
					$store_city        = get_option( 'woocommerce_store_city' );
					$store_postcode    = get_option( 'woocommerce_store_postcode' );

					// The store country/state.
					$store_raw_country = get_option( 'woocommerce_default_country' );

					// Split the store country/state
					$split_country = explode( ":", $store_raw_country );

					// Store country and state separated
					$store_country = $split_country[0];
					$store_state   = $split_country[1];

					// Create store address.
					$store_address = $store_address .  ' ' . $store_address_2 . ' ' . $store_city . ' ' . $store_state . ' ' . $store_postcode . ' ' . $store_country;

					// Get an instance of the WC_Order object
					$order = wc_get_order( $_GET['orderid'] );

					$order_data = $order->get_data(); // The Order data

					$order_id                   = $order_data['id'];
					$order_parent_id            = $order_data['parent_id'];
					$order_status               = $order_data['status'];
					$order_currency             = $order_data['currency'];
					$order_version              = $order_data['version'];
					$order_payment_method       = $order_data['payment_method'];
					$order_payment_method_title = $order_data['payment_method_title'];
					$order_date_created         = $order_data['date_created']->date('m-d-y');
					$order_time_created         = $order_data['date_created']->date('h:i a');
					$order_discount_total       = $order_data['discount_total'];
					$order_discount_tax         = $order_data['discount_tax'];
					$order_shipping_total       = $order_data['shipping_total'];
					$order_shipping_tax         = $order_data['shipping_tax'];
					$order_cart_tax             = $order_data['cart_tax'];
					$order_total                = $order_data['total'];
					$order_total_tax            = $order_data['total_tax'];
					$order_customer_id          = $order_data['customer_id'];
					$order_billing_first_name   = $order_data['billing']['first_name'];
					$order_billing_last_name    = $order_data['billing']['last_name'];
					$order_billing_company      = $order_data['billing']['company'];
					$order_billing_address_1    = $order_data['billing']['address_1'];
					$order_billing_address_2    = $order_data['billing']['address_2'];
					$order_billing_city         = $order_data['billing']['city'];
					$order_billing_state        = $order_data['billing']['state'];
					$order_billing_postcode     = $order_data['billing']['postcode'];
					$order_billing_country      = $order_data['billing']['country'];
					$order_billing_email        = $order_data['billing']['email'];
					$order_billing_phone        = $order_data['billing']['phone'];
					$order_shipping_first_name  = $order_data['shipping']['first_name'];
					$order_shipping_last_name   = $order_data['shipping']['last_name'];
					$order_shipping_company     = $order_data['shipping']['company'];
					$order_shipping_address_1   = $order_data['shipping']['address_1'];
					$order_shipping_address_2   = $order_data['shipping']['address_2'];
					$order_shipping_city        = $order_data['shipping']['city'];
					$order_shipping_state       = $order_data['shipping']['state'];
					$order_shipping_postcode    = $order_data['shipping']['postcode'];
					$order_shipping_country     = $order_data['shipping']['country'];

					echo '<div class="ddwc-orders">';

					// Display order number.
					if ( isset( $order_id ) ) {
						echo '<h3 class="ddwc">' . __( 'Order #', 'ddwc' ) . apply_filters( 'ddwc_order_number', $order_id ) . ' <span class="' . $order_status . '">' . wc_get_order_status_name( $order_status ) . '</span></h3>';
					}

					// Display a button to call the customers phone number.
					echo '<p>';
					if ( isset( $order_billing_phone ) ) {
						echo '<a href="tel:' . $order_billing_phone . '" class="button ddwc-button customer">' . __( 'Call Customer', 'ddwc' ) . '</a> ';
					}

					// Display a button to call the dispatch number if it's set in the Settings page. 
					if ( false !== get_option( 'ddwc_settings_dispatch_phone_number' ) && '' !== get_option( 'ddwc_settings_dispatch_phone_number' ) ) {
						echo '<a href="tel:' . get_option( 'ddwc_settings_dispatch_phone_number' ) . '" class="button ddwc-button dispatch">' . __( 'Call Dispatch', 'ddwc' ) . '</a>';
					}

					echo '</p>';

					echo '<h4>' . __( 'Delivery Address', 'ddwc' ) . '</h4>';

					// Plain text delivery address.
					if ( '' == get_option( 'ddwc_settings_google_maps_api_key' ) ) {
						$plain_address = '<p>';
						if ( isset( $order_shipping_address_1 ) && '' !== $order_shipping_address_1 ) {
							$plain_address   .= $order->get_formatted_shipping_address();
							$delivery_address = $order_shipping_address_1 .  ' ' . $order_shipping_address_2 . ' ' . $order_shipping_city . ' ' . $order_shipping_state . ' ' . $order_shipping_postcode . ' ' . $order_shipping_country;
						} else {
							$plain_address   .= $order->get_formatted_billing_address();
							$delivery_address = $order_billing_address_1 .  ' ' . $order_billing_address_2 . ' ' . $order_billing_city . ' ' . $order_billing_state . ' ' . $order_billing_postcode . ' ' . $order_billing_country;
						}
						$plain_address  .= '</p>';
						$directions_link = 'https://www.google.com/maps/search/?api=1&query=' . $delivery_address;
						$directions_text = __( 'Get Directions', 'ddwc' );
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
						if ( isset( $order_shipping_address_1 ) && '' !== $order_shipping_address_1 ) {
							$delivery_address = $order_shipping_address_1 .  ' ' . $order_shipping_address_2 . ' ' . $order_shipping_city . ' ' . $order_shipping_state . ' ' . $order_shipping_postcode . ' ' . $order_shipping_country;
						} else {
							$delivery_address = $order_billing_address_1 .  ' ' . $order_billing_address_2 . ' ' . $order_billing_city . ' ' . $order_billing_state . ' ' . $order_billing_postcode . ' ' . $order_billing_country;
						}
						// Create the Google Map.
						$google_map = '<iframe width="600" height="450" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/directions?origin=' . apply_filters( 'ddwc_google_maps_origin_address', $store_address ) . '&destination=' . apply_filters( 'ddwc_google_maps_delivery_address', $delivery_address ) . '&key=' . get_option( 'ddwc_settings_google_maps_api_key' ) . '" allowfullscreen></iframe>';

						// Display the Google Map with delivery address.
						echo apply_filters( 'ddwc_delivery_address_google_map', $google_map, $delivery_address, $store_address );
					}

					echo '<h4>' . __( 'Order details', 'ddwc' ) . '</h4>';

					// Get payment gateway details.
					$payment_gateway = wc_get_payment_gateway_by_order( $order_id );

					echo '<table class="ddwc-dashboard">';
					echo '<tbody>';
					// Display customer.
					if ( '' !== $order_shipping_first_name ) {
						echo '<tr><td><strong>' . __( 'Customer', 'ddwc' ) . '</strong></td><td>' . $order_shipping_first_name . ' ' . $order_shipping_last_name . '</td></tr>';
					} elseif ( '' !== $order_billing_first_name ) {
						echo '<tr><td><strong>' . __( 'Customer', 'ddwc' ) . '</strong></td><td>' . $order_billing_first_name . ' ' . $order_billing_last_name . '</td></tr>';
					} else {
						// Do nothing.
					}
					// Display payment method details.
					if ( isset( $payment_gateway ) && FALSE !== $payment_gateway ) {
						$payment_method = '<tr><td><strong>' . __( 'Payment method', 'ddwc' ) . '</strong></td><td>' . $payment_gateway->title . '</td></tr>';
						echo apply_filters( 'ddwc_driver_dashboard_payment_method', $payment_method );
					}
					// Display order date.
					if ( isset( $order_date_created ) ) {
						echo '<tr><td><strong>' . __( 'Order date', 'ddwc' ) . '</strong></td><td>' . $order_date_created . ' - ' . $order_time_created . '</td></tr>';
					}
					echo '</tbody>';
					echo '</table>';

					do_action( 'ddwc_driver_dashboard_order_table_before' );

					// Set up total title.
					$total_title = '<td>' . __( 'Total', 'ddwc' ) . '</td>';

					echo '<table class="ddwc-dashboard">';
					echo '<thead><tr><td>' . __( 'Product', 'ddwc' ) . '</td><td>' . __( 'Qty', 'ddwc' ) . '</td>' . apply_filters( 'ddwc_driver_dashboard_total_title', $total_title ) . '</tr></thead>';
					echo '<tbody>';

					do_action( 'ddwc_driver_dashboard_order_table_tbody_top' );

					// get an instance of the WC_Order object.
					$order_items     = wc_get_order( $_GET['orderid'] );
					$currency_code   = $order_items->get_currency();
					$currency_symbol = get_woocommerce_currency_symbol( $currency_code );

					if ( ! empty( $order_items ) ) {
						// The loop to get the order items which are WC_Order_Item_Product objects since WC 3+
						foreach( $order_items->get_items() as $item_id=>$item_product ) {
							//Get the product ID
							$product_id = $item_product->get_product_id();
							//Get the WC_Product object
							$product  = $item_product->get_product();
							// Get the product quantity.
							$quantity = $item_product->get_quantity();

							$sku         = $product->get_sku();
							$name        = $product->get_name();
							$price       = $product->get_price();
							$qtty        = $quantity;
							$qtty_price  = $qtty * $price;
							$price       = '<td>' .$currency_symbol . number_format( $qtty_price, 2 ) . '</td>';
							$total_price = apply_filters( 'ddwc_driver_dashboard_order_item_price', $price );

							echo '<tr><td>' . $name . '</td><td>' . $qtty . '</td>' . $total_price . '</tr>';
						}
					} else {
						// Do nothing.
					}

					do_action( 'ddwc_driver_dashboard_order_table_tbody_before_delivery' );

					// Delivery Total.
					$delivery_total = '<tr class="delivery-charge"><td colspan="2"><strong>' . __( 'Delivery', 'ddwc' ) . '</strong></td><td class="total">' . $currency_symbol . number_format((float)$order_shipping_total, 2, '.', ',' ) . '</td></tr>';

					echo apply_filters( 'ddwc_driver_dashboard_delivery_total', $delivery_total );

					do_action( 'ddwc_driver_dashboard_order_table_tbody_before_total' );

					// Order total.
					$order_total = '<tr class="order-total"><td colspan="2"><strong>' . __( 'Order total', 'ddwc' ) . '</strong></td><td class="total">' . $currency_symbol . $order_total . '</td></tr>';

					echo apply_filters( 'ddwc_driver_dashboard_order_total', $order_total );

					do_action( 'ddwc_driver_dashboard_order_table_tbody_bottom' );

					echo '</tbody>';
					echo '</table>';

					do_action( 'ddwc_driver_dashboard_order_table_after' );

					// Change status forms.
					apply_filters( 'ddwc_driver_dashboard_change_status_forms', ddwc_driver_dashboard_change_status_forms() );

					echo '</div>';

				} else {

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
						echo '<h3 class="ddwc assigned-orders">' . __( 'Assigned Orders', 'ddwc' ) . '</h3>';

						do_action( 'ddwc_assigned_orders_table_before' );

						$total_title = '<td>' . __( 'Total', 'ddwc' ) . '</td>';

						echo '<table class="ddwc-dashboard">';
						echo '<thead><tr><td>' . __( 'ID', 'ddwc' ) . '</td><td>' . __( 'Date', 'ddwc' ) . '</td><td>' . __( 'Status', 'ddwc' ) . '</td>' . apply_filters( 'ddwc_driver_dashboard_assigned_orders_total_title', $total_title ) . '</tr></thead>';
						echo '<tbody>';
						foreach ( $assigned_orders as $driver_order ) {

							// Get an instance of the WC_Order object.
							$order = wc_get_order( $driver_order->ID );

							$order_data = $order->get_data(); // The Order data.

							// print_r( $order_data );

							$currency_code   = $order_data['currency'];
							$currency_symbol = get_woocommerce_currency_symbol( $currency_code );

							// print_r( $order_data );

							$order_id                   = $order_data['id'];
							$order_parent_id            = $order_data['parent_id'];
							$order_status               = $order_data['status'];
							$order_currency             = $order_data['currency'];
							$order_version              = $order_data['version'];
							$order_payment_method       = $order_data['payment_method'];
							$order_payment_method_title = $order_data['payment_method_title'];
							$order_payment_method       = $order_data['payment_method'];
							$order_payment_method       = $order_data['payment_method'];
							$order_date_created         = $order_data['date_created']->date('m-d-Y');
							$order_discount_total       = $order_data['discount_total'];
							$order_discount_tax         = $order_data['discount_tax'];
							$order_shipping_total       = $order_data['shipping_total'];
							$order_shipping_tax         = $order_data['shipping_tax'];
							$order_cart_tax             = $order_data['cart_tax'];
							$order_total                = $order_data['total'];
							$order_total_tax            = $order_data['total_tax'];
							$order_customer_id          = $order_data['customer_id'];
							$order_billing_first_name   = $order_data['billing']['first_name'];
							$order_billing_last_name    = $order_data['billing']['last_name'];
							$order_billing_company      = $order_data['billing']['company'];
							$order_billing_address_1    = $order_data['billing']['address_1'];
							$order_billing_address_2    = $order_data['billing']['address_2'];
							$order_billing_city         = $order_data['billing']['city'];
							$order_billing_state        = $order_data['billing']['state'];
							$order_billing_postcode     = $order_data['billing']['postcode'];
							$order_billing_country      = $order_data['billing']['country'];
							$order_billing_email        = $order_data['billing']['email'];
							$order_billing_phone        = $order_data['billing']['phone'];
							$order_shipping_first_name  = $order_data['shipping']['first_name'];
							$order_shipping_last_name   = $order_data['shipping']['last_name'];
							$order_shipping_company     = $order_data['shipping']['company'];
							$order_shipping_address_1   = $order_data['shipping']['address_1'];
							$order_shipping_address_2   = $order_data['shipping']['address_2'];
							$order_shipping_city        = $order_data['shipping']['city'];
							$order_shipping_state       = $order_data['shipping']['state'];
							$order_shipping_postcode    = $order_data['shipping']['postcode'];
							$order_shipping_country     = $order_data['shipping']['country'];

							if ( 'processing' === $order_status || 'driver-assigned' === $order_status || 'out-for-delivery' === $order_status ) {
								echo '<tr>';
								echo '<td><a href="?orderid=' . $driver_order->ID . '">' . apply_filters( 'ddwc_order_number', $driver_order->ID ) . '</a></td>';
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

						echo '<h4 class="ddwc assigned-orders">' . __( 'Completed Orders', 'ddwc' ) . '</h4>';

						do_action( 'ddwc_completed_orders_table_before' );

						$total_title = '<td>' . __( 'Total', 'ddwc' ) . '</td>';

						echo '<table class="ddwc-dashboard">';
						echo '<thead><tr><td>' . __( 'ID', 'ddwc' ) . '</td><td>' . __( 'Date', 'ddwc' ) . '</td><td>' . __( 'Status', 'ddwc' ) . '</td>' . apply_filters( 'ddwc_driver_dashboard_completed_orders_total_title', $total_title ) . '</tr></thead>';
						echo '<tbody>';
						echo do_action( 'ddwc_driver_dashboard_completed_orders_tbody_top' );
						foreach ( $assigned_orders as $driver_order ) {

							// Get an instance of the WC_Order object.
							$order = wc_get_order( $driver_order->ID );

							// Get order data.
							$order_data      = $order->get_data();
							$currency_code   = $order_data['currency'];
							$currency_symbol = get_woocommerce_currency_symbol( $currency_code );

							$order_id                   = $order_data['id'];
							$order_parent_id            = $order_data['parent_id'];
							$order_status               = $order_data['status'];
							$order_currency             = $order_data['currency'];
							$order_version              = $order_data['version'];
							$order_payment_method       = $order_data['payment_method'];
							$order_payment_method_title = $order_data['payment_method_title'];
							$order_payment_method       = $order_data['payment_method'];
							$order_payment_method       = $order_data['payment_method'];
							$order_date_created         = $order_data['date_created']->date('m-d-Y');
							$order_discount_total       = $order_data['discount_total'];
							$order_discount_tax         = $order_data['discount_tax'];
							$order_shipping_total       = $order_data['shipping_total'];
							$order_shipping_tax         = $order_data['shipping_tax'];
							$order_cart_tax             = $order_data['cart_tax'];
							$order_total                = $order_data['total'];
							$order_total_tax            = $order_data['total_tax'];
							$order_customer_id          = $order_data['customer_id'];
							$order_billing_first_name   = $order_data['billing']['first_name'];
							$order_billing_last_name    = $order_data['billing']['last_name'];
							$order_billing_company      = $order_data['billing']['company'];
							$order_billing_address_1    = $order_data['billing']['address_1'];
							$order_billing_address_2    = $order_data['billing']['address_2'];
							$order_billing_city         = $order_data['billing']['city'];
							$order_billing_state        = $order_data['billing']['state'];
							$order_billing_postcode     = $order_data['billing']['postcode'];
							$order_billing_country      = $order_data['billing']['country'];
							$order_billing_email        = $order_data['billing']['email'];
							$order_billing_phone        = $order_data['billing']['phone'];
							$order_shipping_first_name  = $order_data['shipping']['first_name'];
							$order_shipping_last_name   = $order_data['shipping']['last_name'];
							$order_shipping_company     = $order_data['shipping']['company'];
							$order_shipping_address_1   = $order_data['shipping']['address_1'];
							$order_shipping_address_2   = $order_data['shipping']['address_2'];
							$order_shipping_city        = $order_data['shipping']['city'];
							$order_shipping_state       = $order_data['shipping']['state'];
							$order_shipping_postcode    = $order_data['shipping']['postcode'];
							$order_shipping_country     = $order_data['shipping']['country'];

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
						$empty  = '<h3 class="ddwc assigned-orders">' . __( 'Assigned Orders', 'ddwc' ) . '</h3>';
						$empty .= '<p>' . __( 'You do not have any assigned orders.', 'ddwc' ) . '</p>';

						echo apply_filters( 'ddwc_assigned_orders_empty', $empty );

						do_action( 'ddwc_assigned_orders_empty_after' );
					}
				}
			} else {

				// Set the Access Denied page text.
				$access_denied = '<h3 class="ddwc access-denied">' . __( 'Access Denied', 'ddwc' ) . '</h3><p>' . __( 'Sorry, but you are not able to view this page.', 'ddwc' ) . '</p>';

				// Return the Access Denied text, filtered.
				return apply_filters( 'ddwc_access_denied', $access_denied );
			}

		} else {
			// Do nothing.
		}
	} else {
		apply_filters( 'ddwc_dashboard_login_form', wp_login_form() );
	}
}
add_shortcode( 'ddwc_dashboard', 'ddwc_dashboard_shortcode' );

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

		// Get all the user roles as an array.
		if ( 0 != $user_id ) {

			$user_roles = $user_meta->roles;

			// Check if the role you're interested in, is present in the array.
			if ( in_array( 'driver', $user_roles, true ) ) {

				if ( isset( $_GET['orderid'] ) && ( '' != $_GET['orderid'] ) ) {
					$driver_id = get_post_meta( $_GET['orderid'], 'ddwc_driver_id', true );
				}

				// Display order info if ?orderid is set.
				if ( isset( $_GET['orderid'] ) && ( '' != $_GET['orderid'] ) && ( $driver_id == $user_id ) ) {

					// Update order status if marked OUT FOR DELIVERY by Driver.
					if ( isset( $_POST['outfordelivery'] ) ) {
						$order = wc_get_order( $_GET['orderid'] );
						$order->update_status( "out-for-delivery" );
						do_action( 'ddwc_email_customer_order_status_out_for_delivery' );
					}

					// Update order status if marked COMPLETED by Driver.
					if ( isset( $_POST['ordercompleted'] ) ) {
						$order = wc_get_order( $_GET['orderid'] );
						$order->update_status( "completed" );
						do_action( 'ddwc_email_admin_order_status_completed' );
					}

					// Get an instance of the WC_Order object
					$order = wc_get_order( $_GET['orderid'] );

					$order_data = $order->get_data(); // The Order data

					//print_r( $order_data );

					$order_id                   = $order_data['id'];
					$order_parent_id            = $order_data['parent_id'];
					$order_status               = $order_data['status'];
					$order_currency             = $order_data['currency'];
					$order_version              = $order_data['version'];
					$order_payment_method       = $order_data['payment_method'];
					$order_payment_method_title = $order_data['payment_method_title'];
					$order_payment_method       = $order_data['payment_method'];
					$order_payment_method       = $order_data['payment_method'];

					$order_date_created   = $order_data['date_created']->date('m-d');
					$order_time_created   = $order_data['date_created']->date('h:i a');

					## CART INFORMATION:

					$order_discount_total = $order_data['discount_total'];
					$order_discount_tax   = $order_data['discount_tax'];
					$order_shipping_total = $order_data['shipping_total'];
					$order_shipping_tax   = $order_data['shipping_tax'];
					$order_cart_tax       = $order_data['cart_tax'];
					$order_total          = $order_data['total'];
					$order_total_tax      = $order_data['total_tax'];
					$order_customer_id    = $order_data['customer_id'];

					## BILLING INFORMATION:

					$order_billing_first_name = $order_data['billing']['first_name'];
					$order_billing_last_name  = $order_data['billing']['last_name'];
					$order_billing_company    = $order_data['billing']['company'];
					$order_billing_address_1  = $order_data['billing']['address_1'];
					$order_billing_address_2  = $order_data['billing']['address_2'];
					$order_billing_city       = $order_data['billing']['city'];
					$order_billing_state      = $order_data['billing']['state'];
					$order_billing_postcode   = $order_data['billing']['postcode'];
					$order_billing_country    = $order_data['billing']['country'];
					$order_billing_email      = $order_data['billing']['email'];
					$order_billing_phone      = $order_data['billing']['phone'];

					## SHIPPING INFORMATION:

					$order_shipping_first_name = $order_data['shipping']['first_name'];
					$order_shipping_last_name  = $order_data['shipping']['last_name'];
					$order_shipping_company    = $order_data['shipping']['company'];
					$order_shipping_address_1  = $order_data['shipping']['address_1'];
					$order_shipping_address_2  = $order_data['shipping']['address_2'];
					$order_shipping_city       = $order_data['shipping']['city'];
					$order_shipping_state      = $order_data['shipping']['state'];
					$order_shipping_postcode   = $order_data['shipping']['postcode'];
					$order_shipping_country    = $order_data['shipping']['country'];

					echo "<div class='ddwc-orders'>";

					// Display order number.
					if ( isset( $order_id ) ) {
						echo "<h3 class='ddwc'>" . __( "Order #", "ddwc" ) . "" . $order_id .  " <span class='" . $order_status . "'>" . wc_get_order_status_name( $order_status ) . "</span></h3>";
					}

					// Display order date.
					if ( isset( $order_date_created ) ) {
						echo "<p><strong>" . __( "Date", "ddwc" ) . ":</strong> " . $order_date_created . " - " . $order_time_created . "</p>";
					}

					// Get payment gateway details.
					$payment_gateway = wc_get_payment_gateway_by_order( $order_id );

					// Display payment method details.
					if ( isset( $payment_gateway ) ) {
						echo "<p><strong>" . __( 'Payment', 'ddwc' ) . ":</strong> " . $payment_gateway->title . "</p>";
					}

					// Display a button to call the customers phone number.
					echo "<p>";
					if ( isset( $order_billing_phone ) ) {
						echo "<a href='tel:" . $order_billing_phone . "' class='button ddwc-button customer'>" . __( "Call Customer", "ddwc" ) . "</a> ";
					}

					// Display a button to call the dispatch number if it's set in the Settings page. 
					if ( false !== get_option( 'ddwc_settings_dispatch_phone_number' ) && '' !== get_option( 'ddwc_settings_dispatch_phone_number' ) ) {
						echo "<a href='tel:" . get_option( 'ddwc_settings_dispatch_phone_number' ) . "' class='button ddwc-button dispatch'>" . __( "Call Dispatch", "ddwc" ) . "</a>";
					}

					echo "</p>";

					echo "<h4>" . __( "Order details", "ddwc" ) . "</h4>";

					echo "<table class='ddwc-dashboard'>";
					echo "<thead><tr><td>" . __( "Product", "ddwc" ) . "</td><td>" . __( "Qty", "ddwc" ) . "</td><td>" . __( "Total", "ddwc" ) . "</td></tr></thead>";
					echo "<tbody>";

					do_action( 'ddwc_driver_dashboard_order_table_tbody_top' );

					// get an instance of the WC_Order object.
					$order_items     = wc_get_order( $_GET['orderid'] );
					$currency_code   = $order_items->get_currency();
					$currency_symbol = get_woocommerce_currency_symbol( $currency_code );

					// print_r ( $order_items );

					if ( ! empty( $order_items ) ) {
						// The loop to get the order items which are WC_Order_Item_Product objects since WC 3+
						foreach( $order_items->get_items() as $item_id=>$item_product ) {
							//Get the product ID
							$product_id = $item_product->get_product_id();
							//Get the WC_Product object
							$product  = $item_product->get_product();
							// Get the product quantity.
							$quantity = $item_product->get_quantity();
							//print_r( $product );

							$sku         = $product->get_sku();
							$name        = $product->get_name();
							$price       = $product->get_price();
							$qtty        = $quantity;
							$qtty_price  = $qtty * $price;
							$total_price = number_format( $qtty_price, 2 );

							echo "<tr><td>" . $name . "</td><td>" . $qtty . "</td><td>" . $currency_symbol . $total_price . "</td></tr>";
						}
					} else {
						// Do nothing.
					}

					echo "<tr class='order-total'><td colspan='2'><strong>" . __( 'Order total', 'ddwc' ) . "</strong></td><td class'total'>" . $currency_symbol . $order_total . "</td></tr>";

					do_action( 'ddwc_driver_dashboard_order_table_tbody_bottom' );

					echo "</tbody>";
					echo "</table>";

					echo "<h4>" . __( "Delivery Address", "ddwc" ) . "</h4>";

					if ( '' == get_option( 'ddwc_settings_google_maps_api_key' ) ) {

						echo "<p>";

						if ( '' !== $order_shipping_first_name ) {
							echo $order_shipping_first_name . " " . $order_shipping_last_name . "<br />";
						} elseif ( '' !== $order_billing_first_name ) {
							echo $order_billing_first_name . " " . $order_billing_last_name . "<br />";
						} else {
							// Do nothing.
						}

						if ( '' !== $order_shipping_address_1 ) {
							echo $order_shipping_address_1 . ' ';
						} elseif ( '' !== $order_billing_address_1 ) {
							echo $order_billing_address_1 . ' ';
						} else {
							// Do nothing.
						}

						if ( '' !== $order_shipping_address_2 ) {
							echo $order_shipping_address_2;
						} elseif ( '' !== $order_billing_address_2 ) {
							echo $order_billing_address_2;
						} else {
							// Do nothing.
						}

						if (  '' !== $order_shipping_city ) {
							echo '<br />' . $order_shipping_city . ', ' . $order_shipping_state . ' ' . $order_shipping_postcode;
						} else {
							echo '<br />' . $order_billing_city . ', ' . $order_billing_state . ' ' . $order_billing_postcode;
						}

						echo "</p>"; // end billing address
					}
					/**
					 * Display a Google Map with the customers address if an API key is added to 
					 * the WooCommerce Settings page.
					 */
					if ( false !== get_option( 'ddwc_settings_google_maps_api_key' ) && '' !== get_option( 'ddwc_settings_google_maps_api_key' ) ) {
						echo '<iframe width="600" height="450" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/place?key=' . get_option( 'ddwc_settings_google_maps_api_key' ) . '
					  &q=' . $order_billing_address_1 . ' ' . $order_billing_address_2 . ' ' . $order_billing_city . ' ' . $order_billing_state . ' ' . $order_billing_postcode . '" allowfullscreen>
				  	</iframe>';
					}

					if ( $order_status == 'driver-assigned' ) {
						echo "<h4>" . __( "Change Status", "ddwc" ) . "</h4>";
						echo '<form method="post"><input type="hidden" name="outfordelivery" value="out-for-delivery" /><input type="submit" value="Out for Delivery" />' . wp_nonce_field( 'ddwc_out_for_delivery_nonce_action', 'ddwc_out_for_delivery_nonce_field' ) . '</form>';
					} elseif ( $order_status == 'out-for-delivery' ) {
						echo "<h4>" . __( "Change Status", "ddwc" ) . "</h4>";
						echo '<form method="post"><input type="hidden" name="ordercompleted" value="completed" /><input type="submit" value="Completed" />' . wp_nonce_field( 'ddwc_order_completed_nonce_action', 'ddwc_order_completed_nonce_field' ) . '</form>';
					} else {
						// Do nothing.
					}
					echo "</div>";

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
						echo "<h3 class='ddwc assigned-orders'>" . __( 'Assigned Orders', 'ddwc' ) . "</h3>";
						echo "<div class='ddwc-display-options'><input type='checkbox' class='ddwc-hide-completed' value='name' /> " . __( "Hide completed orders", "ddwc" ) . "</div>";
						echo "<table class='ddwc-dashboard'>";
						echo "<thead><tr><td>" . __( "ID", "ddwc" ) . "</td><td>" . __( "Date", "ddwc" ) . "</td><td>" . __( "Status", "ddwc" ) . "</td><td>" . __( "Total", "ddwc" ) . "</td></tr></thead>";
						echo "<tbody>";
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

							$order_date_created   = $order_data['date_created']->date('m-d-Y');

							## CART INFORMATION:

							$order_discount_total = $order_data['discount_total'];
							$order_discount_tax   = $order_data['discount_tax'];
							$order_shipping_total = $order_data['shipping_total'];
							$order_shipping_tax   = $order_data['shipping_tax'];
							$order_cart_tax       = $order_data['cart_tax'];
							$order_total          = $order_data['total'];
							$order_total_tax      = $order_data['total_tax'];
							$order_customer_id    = $order_data['customer_id'];

							## BILLING INFORMATION:

							$order_billing_first_name = $order_data['billing']['first_name'];
							$order_billing_last_name  = $order_data['billing']['last_name'];
							$order_billing_company    = $order_data['billing']['company'];
							$order_billing_address_1  = $order_data['billing']['address_1'];
							$order_billing_address_2  = $order_data['billing']['address_2'];
							$order_billing_city       = $order_data['billing']['city'];
							$order_billing_state      = $order_data['billing']['state'];
							$order_billing_postcode   = $order_data['billing']['postcode'];
							$order_billing_country    = $order_data['billing']['country'];
							$order_billing_email      = $order_data['billing']['email'];
							$order_billing_phone      = $order_data['billing']['phone'];

							## SHIPPING INFORMATION:

							$order_shipping_first_name = $order_data['shipping']['first_name'];
							$order_shipping_last_name  = $order_data['shipping']['last_name'];
							$order_shipping_company    = $order_data['shipping']['company'];
							$order_shipping_address_1  = $order_data['shipping']['address_1'];
							$order_shipping_address_2  = $order_data['shipping']['address_2'];
							$order_shipping_city       = $order_data['shipping']['city'];
							$order_shipping_state      = $order_data['shipping']['state'];
							$order_shipping_postcode   = $order_data['shipping']['postcode'];
							$order_shipping_country    = $order_data['shipping']['country'];

							echo "<tr>";

							// print_r( $order_data );

							echo "<td><a href='?orderid=" . $driver_order->ID . "'>" . $driver_order->ID . "</a></td>";
							echo "<td>" . $order_date_created . "</td>";
							echo "<td>" . wc_get_order_status_name( $order_status ) . "</td>";

							if ( isset( $order_total ) ) {
								echo "<td>"  . $currency_symbol . $order_total . "</td>";
							} else {
								echo "<td>-</td>";
							}

							echo "</tr>";
						}
						echo "</tbody>";
						echo "</table>";
					} else {
						echo "<h3 class='ddwc assigned-orders'>" . __( "Assigned Orders", "ddwc" ) . "</h3>";
						echo "<p>" . __( "You do not currently have any assigned orders.", "ddwc" ) . "</p>";
					}
				}
			} else {

				// Set the Access Denied page text.
				$access_denied = "<h3 class='ddwc access-denied'>" . __( "Access Denied", "ddwc" ) . "</h3><p>" . __( "Sorry, but you are not able to view this page.", "ddwc" ) . "</p>";

				// Return the Access Denied text, filtered.
				return apply_filters( 'ddwc_access_denied', $access_denied );
			}

		} else {
			// Do nothing.
		}
	} else {
		wp_login_form();
	}
}
add_shortcode( 'ddwc_dashboard', 'ddwc_dashboard_shortcode' );

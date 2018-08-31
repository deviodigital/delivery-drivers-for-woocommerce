<?php

/**
 * The Delivery Driver Dashboard Shortcode.
 *
 * @link       https://www.wpdispensary.com
 * @since      1.0.0
 *
 * @package    WPD_DDWC
 * @subpackage WPD_DDWC/admin
 */
function wpd_ddwc_dashboard_shortcode() {

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

				// Display order info if ?orderid is set.
				if ( isset( $_GET['orderid'] ) && ( '' != $_GET['orderid'] ) ) {

					// Update order status if marked OUT FOR DELIVERY by Driver.
					if ( isset( $_POST['outfordelivery'] ) ) {
						$order = wc_get_order( $_GET['orderid'] );
						$order->update_status( "out-for-delivery" );
						/**
						 * @todo add wp mail form here to email customer and let them know their order is out for delivery.
						 */
					}

					// Update order status if marked COMPLETED by Driver.
					if ( isset( $_POST['ordercompleted'] ) ) {
						$order = wc_get_order( $_GET['orderid'] );
						$order->update_status( "completed" );
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

					echo "<div class='wpd-ddwc-orders'>";

					if ( isset( $order_id ) ) {
						echo "<h3 class='wpd-ddwc'>Order #" . $order_id .  " <span class='" . $order_status . "'>" . wc_get_order_status_name( $order_status ) . "</span></h3>";
					}

					if ( isset( $order_date_created ) ) {
						echo "<p><strong>Date:</strong> " . $order_date_created . " - " . $order_time_created . "</p>";
					}

					echo "<p>";
					if ( isset( $order_billing_phone ) ) {
						echo "<a href='tel:" . $order_billing_phone . "' class='button wpd-ddwc-button customer'>Call Customer</a> ";
					}
					/**
					 * @todo change this to a phone number in the Settings, or don't display if it's not set.
					 */
					echo "<a href='tel:" . $order_billing_phone . "' class='button wpd-ddwc-button dispatch'>Call Dispatch</a>";
					echo "</p>";

					echo "<h4>Order Items</h4>";

					echo "<table class='wpd-ddwc-dashboard'>";
					echo "<thead><tr><td>ID</td><td>Product</td><td>Qty</td><td>Total</td></tr></thead>";
					echo "<tbody>";

					// get an instance of the WC_Order object
					$order_items = wc_get_order( $_GET['orderid'] );

					$currency_code = $order_items->get_currency();

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

							$sku   = $product->get_sku();
							$name  = $product->get_name();
							$price = $product->get_price();
							$qtty  = $quantity;

							echo "<tr><td>" . $product_id . "</td><td>" . $name . "</td><td>" . $qtty . "</td><td>" . $currency_symbol . ( $qtty * $price ). "</td></tr>";
						}
					} else {
						// echo "DUHHHHHHHHHH";
					}

					echo "</tbody>";
					echo "</table>";

					echo "<h4>Delivery Address</h4>";

					echo "<p>";

					if ( isset( $order_billing_first_name ) ) {
						echo $order_billing_first_name . " " . $order_billing_last_name . "<br />";
					}

					if ( isset( $order_shipping_address_1 ) ) {
						echo $order_shipping_address_1 . ' ';
					} elseif ( isset( $order_billing_address_1 ) ) {
						echo $order_billing_address_1 . ' ';
					} else {
						// Do nothing.
					}

					if ( isset( $order_shipping_address_2 ) ) {
						echo $order_shipping_address_2 . '<br />';
					} elseif ( isset( $order_billing_address_2 ) ) {
						echo $order_billing_address_2 . '<br />';
					} else {
						// Do nothing.
					}

					if ( isset( $order_shipping_city ) ) {
						echo $order_shipping_city . ', ' . $order_shipping_state . ' ' . $order_shipping_postcode;
					} else {
						echo $order_billing_city . ', ' . $order_billing_state . ' ' . $order_billing_postcode;
					}

					echo "</p>"; // end billing address

					/**
					 * @todo Wrap this in a check for the website owner's API KEY in the Settings page
					 */
					echo '<iframe
					width="600"
					height="450"
					frameborder="0" style="border:0"
					src="https://www.google.com/maps/embed/v1/place?key=KEY_GOES_HERE
					  &q=' . $order_billing_address_1 . ' ' . $order_billing_address_2 . ' ' . $order_billing_city . ' ' . $order_billing_state . ' ' . $order_billing_postcode . '" allowfullscreen>
				  	</iframe>';

					if ( $order_status == 'driver-assigned' ) {
						echo "<h4>Change Status</h4>";
						echo '<form method="post"><input type="hidden" name="outfordelivery" value="out-for-delivery" /><input type="submit" value="Out for Delivery" />' . wp_nonce_field( 'wpd_ddwc_out_for_delivery_nonce_action', 'wpd_ddwc_out_for_delivery_nonce_field' ) . '</form>';
					} elseif ( $order_status == 'out-for-delivery' ) {
						echo "<h4>Change Status</h4>";
						echo '<form method="post"><input type="hidden" name="ordercompleted" value="completed" /><input type="submit" value="Completed" />' . wp_nonce_field( 'wpd_ddwc_order_completed_nonce_action', 'wpd_ddwc_order_completed_nonce_field' ) . '</form>';
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
						'meta_key'       => 'wpd_ddwc_driver_id',
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
						echo "<h3 class='wpd-ddwc assigned-orders'>Assigned Orders</h3>";
						echo "<table class='wpd-ddwc-dashboard'>";
						echo "<thead><tr><td>ID</td><td>Date</td><td>Status</td><td>Total</td></tr></thead>";
						echo "<tbody>";
						foreach ( $assigned_orders as $driver_order ) {

							// Get an instance of the WC_Order object
							$order = wc_get_order( $driver_order->ID );

							$order_data = $order->get_data(); // The Order data		

							//print_r( $order_data );

							$currency_code = $order_data['currency'];

							$currency_symbol = get_woocommerce_currency_symbol( $currency_code );

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

							//print_r( $order_data );

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
					}
				}
			} else {
				/**
				 * @todo Check the Settings page to show/hide applications for users who
				 * 	     are not Drivers. And also let the admin customize title/text/button/etc.
				 */
			}

		} else {
			// Do nothing.
		}
	} else {
		wp_login_form();
	}
}
add_shortcode( 'wpd_ddwc_dashboard', 'wpd_ddwc_dashboard_shortcode' );

<?php
/**
 * Delivery driver dashboard shortcode
 * 
 * @package    DDWC
 * @subpackage DDWC/admin
 * @author     Devio Digital <contact@deviodigital.com>
 * @license    GPL-2.0+ http://www.gnu.org/licenses/gpl-2.0.txt
 * @link       https://www.deviodigital.com
 * @since      1.0.0
 */

/**
 * The Delivery Driver Dashboard Shortcode.
 *
 * @since  1.0.0
 * @return string
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

                    // Get the store address.
                    $store_address = ddwc_woocommerce_store_address();

                    // Get an instance of the WC_Order object
                    $order = wc_get_order( $order_id );

                    // Get the order data.
                    $order_data = $order->get_data();

                    // Specific order data.
                    $order_id             = $order_data['id'];
                    $order_status         = $order_data['status'];
                    $order_date_created   = $order_data['date_created']->date( apply_filters( 'ddwc_date_format', get_option( 'date_format' ) ) );
                    $order_time_created   = $order_data['date_created']->date( apply_filters( 'ddwc_time_format', get_option( 'time_format' ) ) );
                    $order_shipping_total = $order_data['shipping_total'];
                    $order_total          = $order_data['total'];
                    $order_customer_note  = $order_data['customer_note'];
                    $order_shipping_addr  = $order_data['shipping']['address_1'];
                    $order_shipping_fname = $order_data['shipping']['first_name'];
                    $order_shipping_lname = $order_data['shipping']['last_name'];
                    $order_billing_fname  = $order_data['billing']['first_name'];
                    $order_billing_lname  = $order_data['billing']['last_name'];
                    $order_billing_phone  = $order_data['billing']['phone'];

                    echo wp_kses( '<div class="ddwc-orders">', wp_kses_allowed_html( 'post' ) );

                    // Display order number.
                    if ( isset( $order_id ) ) {
                        $order_number = '<h3 class="ddwc">' . esc_attr__( 'Order #', 'delivery-drivers-for-woocommerce' ) . apply_filters( 'ddwc_order_number', $order_id ) . ' <span class="' . esc_attr( $order_status ) . '">' . wc_get_order_status_name( $order_status ) . '</span></h3>';
                        echo wp_kses( $order_number, wp_kses_allowed_html( 'post' ) );
                    }

                    // Display a button to call the customers phone number.
                    $contact_info = '<p>';

                        $phone_customer = '';
                        $phone_dispatch = '';

                        // Call Customer button.
                        if ( isset( $order_billing_phone ) ) {
                            $phone_customer = '<a href="' . ddwc_contact_type_link() . esc_html( $order_billing_phone ) . '" class="button ddwc-button customer">' . esc_attr__( 'Call Customer', 'delivery-drivers-for-woocommerce' ) . '</a> ';
                        }

                        // Call Dispatch button.
                        if ( false !== get_option( 'ddwc_settings_dispatch_phone_number' ) && '' !== get_option( 'ddwc_settings_dispatch_phone_number' ) ) {
                            $phone_dispatch = '<a href="' . ddwc_contact_type_link(). get_option( 'ddwc_settings_dispatch_phone_number' ) . '" class="button ddwc-button dispatch">' . esc_attr__( 'Call Dispatch', 'delivery-drivers-for-woocommerce' ) . '</a> ';
                        }

                        // Call buttons.
                        $phone_numbers = apply_filters( 'ddwc_driver_dashboard_phone_numbers', $phone_dispatch . $phone_customer );

                        $contact_info .= $phone_numbers;

                    $contact_info .= '</p>';

                    echo wp_kses( $contact_info, wp_kses_allowed_html( 'post' ) );

                    $address_title = '<h4>' . esc_attr__( 'Delivery Address', 'delivery-drivers-for-woocommerce' ) . '</h4>';

                    echo wp_kses( $address_title, wp_kses_allowed_html( 'post' ) );

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
                        $directions_text = esc_attr__( 'Get Directions', 'delivery-drivers-for-woocommerce' );
                        $plain_address  .= '<p><a target="_blank" href="' . apply_filters( 'ddwc_delivery_address_directions_link', $directions_link, $delivery_address ) . '" class="button">' . apply_filters( 'ddwc_delivery_address_directions_text', $directions_text ) . '</a></p>';

                        // Display the plain text delivery address.
                        $plain_text = apply_filters( 'ddwc_delivery_address_plain_text', $plain_address, $delivery_address );
                        echo wp_kses( $plain_text, wp_kses_allowed_html( 'post' ) );
                    }

                    /**
                     * Display a Google Map with the customers address if an API key
                     * is added to the WooCommerce Settings page.
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
                        if ( null !== get_option( 'ddwc_settings_google_maps_mode' ) ) {
                            $mode = get_option( 'ddwc_settings_google_maps_mode' );
                        } else {
                            $mode = esc_attr__( 'driving', 'delivery-drivers-for-woocommerce' );
                        }

                        // Map mode.
                        $map_mode = '&mode=' . apply_filters( 'ddwc_delivery_address_google_map_mode', $mode );

                        // Create the Google Map.
                        $google_map = '<iframe width="600" height="450" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/directions?origin=' . apply_filters( 'ddwc_google_maps_origin_address', $store_address ) . '&destination=' . apply_filters( 'ddwc_google_maps_delivery_address', $delivery_address ) . '&key=' . get_option( 'ddwc_settings_google_maps_api_key' ) . $map_mode . '" allowfullscreen></iframe>';

                        // Display the Google Map with delivery address.
                        $delivery_map = apply_filters( 'ddwc_delivery_address_google_map', $google_map, $delivery_address, $store_address );
                        echo wp_kses( $delivery_map, ddwc_allowed_tags() );
                    }

                    echo '<h4>' . esc_attr__( 'Order details', 'delivery-drivers-for-woocommerce' ) . '</h4>';

                    do_action( 'ddwc_driver_dashboard_order_details_table_before' );

                    // Get payment gateway details.
                    $payment_gateway = wc_get_payment_gateway_by_order( $order_id );

                    echo wp_kses( '<table class="ddwc-dashboard order-details">', ddwc_allowed_tags() );
                    echo wp_kses( '<tbody>', ddwc_allowed_tags() );

                    do_action( 'ddwc_driver_dashboard_order_details_table_tbody_top' );

                    // Display order date.
                    if ( isset( $order_date_created ) ) {
                        echo wp_kses( '<tr><td><strong>' . esc_attr__( 'Order date', 'delivery-drivers-for-woocommerce' ) . '</strong></td><td>' . esc_html( $order_date_created ) . ' - ' . esc_html( $order_time_created ) . '</td></tr>', ddwc_allowed_tags() );
                    }

                    // Display payment method details.
                    if ( isset( $payment_gateway ) && false !== $payment_gateway ) {
                        $payment_method = '<tr><td><strong>' . esc_attr__( 'Payment method', 'delivery-drivers-for-woocommerce' ) . '</strong></td><td>' . esc_attr( $payment_gateway->title ) . '</td></tr>';
                        echo apply_filters( 'ddwc_driver_dashboard_payment_method', $payment_method );
                    }

                    // Display customer name.
                    if ( '' !== $order_shipping_fname ) {
                        echo wp_kses( '<tr><td><strong>' . esc_attr__( 'Customer name', 'delivery-drivers-for-woocommerce' ) . '</strong></td><td>' . esc_attr( $order_shipping_fname ) . ' ' . esc_attr( $order_shipping_lname ) . '</td></tr>', ddwc_allowed_tags() );
                    } elseif ( '' !== $order_billing_fname ) {
                        echo wp_kses( '<tr><td><strong>' . esc_attr__( 'Customer name', 'delivery-drivers-for-woocommerce' ) . '</strong></td><td>' . esc_attr( $order_billing_fname ) . ' ' . esc_attr( $order_billing_lname ) . '</td></tr>', ddwc_allowed_tags() );
                    } else {
                        // Do nothing.
                    }

                    // Driver rating.
                    $ddwc_driver_rating = get_post_meta( $order_id, 'ddwc_delivery_rating', true );
                    // Display driver rating.
                    if ( ! empty( $ddwc_driver_rating ) ) {
                        // Star icon.
                        $star = '<i class="fas fa-star"></i>';
                        // Delivery rating.
                        echo '<tr><td><strong>' . esc_attr__( 'Delivery rating', 'delivery-drivers-for-woocommerce' ) . '</strong></td><td class="delivery-rating">' . str_repeat( $star, $ddwc_driver_rating ) . '</td></tr>';
                    }

                    // Display customer note.
                    if ( isset( $order_customer_note ) && '' != $order_customer_note ) {
                        echo wp_kses( '<tr><td><strong>' . esc_attr__( 'Customer note', 'delivery-drivers-for-woocommerce' ) . '</strong></td><td>' . esc_html( $order_customer_note ) . '</td></tr>', ddwc_allowed_tags() );
                    }

                    do_action( 'ddwc_driver_dashboard_order_details_table_tbody_bottom' );

                    echo wp_kses( '</tbody>', ddwc_allowed_tags() );
                    echo wp_kses( '</table>', ddwc_allowed_tags() );

                    do_action( 'ddwc_driver_dashboard_order_details_table_after' );

                    do_action( 'ddwc_driver_dashboard_order_table_before' );

                    // Set up total title.
                    $total_title = '<td>' . esc_attr__( 'Total', 'delivery-drivers-for-woocommerce' ) . '</td>';

                    echo wp_kses( '<table class="ddwc-dashboard">', ddwc_allowed_tags() );
                    echo wp_kses( '<thead><tr><td>' . esc_attr__( 'Product', 'delivery-drivers-for-woocommerce' ) . '</td><td>' . esc_attr__( 'Qty', 'delivery-drivers-for-woocommerce' ) . '</td>' . apply_filters( 'ddwc_driver_dashboard_total_title', $total_title ) . '</tr></thead>', ddwc_allowed_tags() );
                    echo wp_kses( '<tbody>', ddwc_allowed_tags() );

                    do_action( 'ddwc_driver_dashboard_order_table_tbody_top' );

                    // get an instance of the WC_Order object.
                    $order_items     = wc_get_order( $order_id );
                    $currency_code   = $order_items->get_currency();
                    $currency_symbol = get_woocommerce_currency_symbol( $currency_code );

                    if ( ! empty( $order_items ) ) {
                        // The loop to get the order items which are WC_Order_Item_Product objects since WC 3+
                        foreach( $order_items->get_items() as $item_id=>$item_product ) {
                            // Get the WC_Product object.
                            $product  = $item_product->get_product();
                            // Get the product quantity.
                            $quantity = $item_product->get_quantity();
                            // Get the product details.
                            $name        = $product->get_name();
                            $price       = $item_product->get_total();
                            $qtty        = $quantity;
                            $qtty_price  = $qtty * $price;
                            $price       = '<td>' . $currency_symbol . number_format( $qtty_price, 2 ) . '</td>';
                            $total_price = apply_filters( 'ddwc_driver_dashboard_order_item_price', $price );
                            $thumb_id    = get_post_thumbnail_id( $product->get_id() ); // Get the attachment ID of the featured image
                            $permalink   = wp_get_attachment_image_url( $thumb_id, 'full' ); // Get the URL of the original image

                            $product_row = '<tr><td><a target="_blank" href="' . esc_url( $permalink ) . '">' . get_the_post_thumbnail( $product->get_id(), array( 40, 40 ), array( 'class' => 'alignleft' ) ) . '</a> ' . $name . '</td><td>' . $qtty . '</td>' . $total_price . '</tr>';
                            echo wp_kses( $product_row, ddwc_allowed_tags() );
                        }
                    }

                    do_action( 'ddwc_driver_dashboard_order_table_tbody_before_delivery' );

                    // Delivery Total.
                    $delivery_total = '<tr class="delivery-charge"><td colspan="2"><strong>' . esc_attr__( 'Delivery', 'delivery-drivers-for-woocommerce' ) . '</strong></td><td class="total">' . $currency_symbol . number_format( (float)$order_shipping_total, 2, '.', ',' ) . '</td></tr>';

                    echo apply_filters( 'ddwc_driver_dashboard_delivery_total', $delivery_total );

                    do_action( 'ddwc_driver_dashboard_order_table_tbody_before_total' );

                    // Order total.
                    $order_total = '<tr class="order-total"><td colspan="2"><strong>' . esc_attr__( 'Order total', 'delivery-drivers-for-woocommerce' ) . '</strong></td><td class="total">' . $currency_symbol . $order_total . '</td></tr>';

                    echo apply_filters( 'ddwc_driver_dashboard_order_total', $order_total );

                    do_action( 'ddwc_driver_dashboard_order_table_tbody_bottom' );

                    echo wp_kses( '</tbody>', ddwc_allowed_tags() );
                    echo wp_kses( '</table>', ddwc_allowed_tags() );

                    do_action( 'ddwc_driver_dashboard_order_table_after' );

                    // Change status forms.
                    apply_filters( 'ddwc_driver_dashboard_change_status_forms', ddwc_driver_dashboard_change_status_forms() );

                    echo wp_kses( '</div>', ddwc_allowed_tags() );

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

                    // Filter the args.
                    $args = apply_filters( 'ddwc_driver_dashboard_assigned_orders_args', $args );

                    /**
                     * Get Orders with Driver ID attached
                     */
                    $assigned_orders = get_posts( $args );

                    /**
                     * If Orders have Driver ID attached
                     */
                    if ( $assigned_orders ) {

                        do_action( 'ddwc_assigned_orders_title_before' );

                        echo '<h3 class="ddwc assigned-orders">' . esc_attr__( 'Assigned Orders', 'delivery-drivers-for-woocommerce' ) . '</h3>';

                        do_action( 'ddwc_assigned_orders_table_before' );

                        $assigned_table = '<table class="ddwc-dashboard">';

                        // Array for assigned orders table thead.
                        $thead = array(
                            esc_attr__( 'ID', 'delivery-drivers-for-woocommerce' ),
                            esc_attr__( 'Date', 'delivery-drivers-for-woocommerce' ),
                            esc_attr__( 'Status', 'delivery-drivers-for-woocommerce' ),
                            apply_filters( 'ddwc_driver_dashboard_assigned_orders_total_title', esc_attr__( 'Total', 'delivery-drivers-for-woocommerce' ) ),
                        );

                        // Filter the thead array.
                        $thead = apply_filters( 'ddwc_driver_dashboard_assigned_orders_order_table_thead', $thead );

                        $assigned_table .= '<thead><tr>';
                        // Loop through $thead.
                        foreach ( $thead as $row ) {
                            // Add td to thead.
                            $assigned_table .= '<td>' . esc_html( $row ) . '</td>';
                        }
                        $assigned_table .= '</tr></thead>';

                        $assigned_table .= '<tbody>';
                        foreach ( $assigned_orders as $driver_order ) {

                            // Get an instance of the WC_Order object.
                            $order = wc_get_order( $driver_order->ID );

                            // Get the order data.
                            $order_data           = $order->get_data();
                            $currency_code        = $order_data['currency'];
                            $currency_symbol      = get_woocommerce_currency_symbol( $currency_code );
                            $order_id             = $order_data['id'];
                            $order_status         = $order_data['status'];
                            $order_date_created   = $order_data['date_created']->date( apply_filters( 'ddwc_date_format', get_option( 'date_format' ) ) );
                            $order_shipping_total = $order_data['shipping_total'];
                            $order_total          = $order_data['total'];

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
                                // Order total.
                                if ( isset( $order_total ) ) {
                                    $order_total = $currency_symbol . $order_total;
                                    $order_total = apply_filters( 'ddwc_driver_dashboard_assigned_orders_total', $order_total, $driver_order->ID );
                                } else {
                                    $order_total = '-';
                                }

                                // Array for assigned orders table tbody.
                                $tbody = array(
                                    '<a href="' . esc_url( apply_filters( 'ddwc_driver_dashboard_assigned_orders_order_details_url', '?orderid=' . $driver_order->ID, $driver_order->ID ) ) . '">' . esc_html( apply_filters( 'ddwc_order_number', $driver_order->ID ) ) . '</a>',
                                    $order_date_created,
                                    wc_get_order_status_name( $order_status ),
                                    $order_total
                                );
        
                                // Array for assigned orders table tbody.
                                $tbody = apply_filters( 'ddwc_driver_dashboard_assigned_orders_order_table_tbody', $tbody, $order_id );

                                $assigned_table .= '<tr>';
                                // Loop through $tbody.
                                foreach ( $tbody as $row ) {
                                    $assigned_table .= '<td>' . $row . '</td>';
                                }
                                $assigned_table .= '<tr>';
                            }
                        }
                        $assigned_table .= '</tbody>';
                        $assigned_table .= '</table>';

                        echo wp_kses( $assigned_table, ddwc_allowed_tags() );

                        do_action( 'ddwc_assigned_orders_table_after' );

                        echo '<h4 class="ddwc assigned-orders">' . esc_attr__( 'Completed Orders', 'delivery-drivers-for-woocommerce' ) . '</h4>';

                        do_action( 'ddwc_completed_orders_table_before' );

                        $total_title = '<td>' . esc_attr__( 'Total', 'delivery-drivers-for-woocommerce' ) . '</td>';

                        echo '<table class="ddwc-dashboard">';
                        echo '<thead><tr><td>' . esc_attr__( 'ID', 'delivery-drivers-for-woocommerce' ) . '</td><td>' . esc_attr__( 'Date', 'delivery-drivers-for-woocommerce' ) . '</td><td>' . esc_attr__( 'Status', 'delivery-drivers-for-woocommerce' ) . '</td>' . apply_filters( 'ddwc_driver_dashboard_completed_orders_total_title', $total_title ) . '</tr></thead>';
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
                            $order_status         = $order_data['status'];
                            $order_date_created   = $order_data['date_created']->date( apply_filters( 'ddwc_date_format', get_option( 'date_format' ) ) );
                            $order_shipping_total = $order_data['shipping_total'];
                            $order_total          = $order_data['total'];

                            if ( 'completed' === $order_status && strtotime( $order_date_created ) > strtotime( '-7 day' ) ) {
                                echo '<tr>';
                                echo '<td><a href="' . apply_filters( 'ddwc_driver_dashboard_completed_orders_order_details_url', '?orderid=' . $driver_order->ID, $driver_order->ID ) . '">' . apply_filters( 'ddwc_order_number', $driver_order->ID ) . '</a></td>';
                                echo '<td>' . esc_html( $order_date_created ) . '</td>';
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
                        $empty  = '<h3 class="ddwc assigned-orders">' . esc_attr__( 'Assigned Orders', 'delivery-drivers-for-woocommerce' ) . '</h3>';
                        $empty .= '<p>' . esc_attr__( 'You do not have any assigned orders.', 'delivery-drivers-for-woocommerce' ) . '</p>';

                        echo apply_filters( 'ddwc_assigned_orders_empty', $empty );

                        do_action( 'ddwc_assigned_orders_empty_after' );
                    }

                    do_action( 'ddwc_driver_dashboard_bottom' );

                }
            } elseif ( ddwc_check_user_roles( array( 'administrator' ) ) ) {

                do_action( 'ddwc_admin_orders_form_before' );

                /**
                 * Args for Orders with Driver ID attached
                 */
                $args = array(
                    'orderby'        => 'date',
                    'post_type'      => 'shop_order',
                    'posts_per_page' => -1,
                    'post_status'    => 'any'
                );
                // Set filter from value.
                $filter_from = esc_attr( date( 'Y-m-d', strtotime( '-7 days' ) ) );
                // Update filter from value if one is set.
                if ( ! empty( filter_input( INPUT_POST, 'filter-from' ) ) ) {
                    $filter_from = filter_input( INPUT_POST, 'filter-from' );
                }
                // Set filter to value.
                $filter_to = esc_attr( date( 'Y-m-d' ) );
                // Update filter to value if one is set.
                if ( ! empty( filter_input( INPUT_POST, 'filter-to' ) ) ) {
                    $filter_to = filter_input( INPUT_POST, 'filter-to' );
                }
                ?>
                <h3><?php esc_attr_e( 'Delivery Orders', 'delivery-drivers-for-woocommerce' ); ?></h3>
                <form class="ddwc-order-filters" method="post" action="<?php filter_input( INPUT_SERVER, 'REQUEST_URI' ); ?>">
                    <div class="form-group">
                        <label><?php esc_attr_e( 'From', 'delivery-drivers-for-woocommerce' ); ?></label>
                        <input type="date" name="filter-from" value="<?php esc_attr( $filter_from ); ?>" />
                    </div>
                    <div class="form-group">
                        <label><?php esc_attr_e( 'To', 'delivery-drivers-for-woocommerce' ); ?></label>
                        <input type="date" name="filter-to" value="<?php esc_attr( $filter_to ); ?>" />
                    </div>
                    <div class="form-group">
                        <label><?php esc_attr_e( 'Driver', 'delivery-drivers-for-woocommerce' ); ?></label>
                        <select name="filter-name">
                            <option value=""></option>
                            <?php
                                $user_query = new WP_User_Query( array( 'role' => 'driver' ) );
                                if ( ! empty( $user_query->get_results() ) ) {
                                    foreach ( $user_query->get_results() as $user ) {
                                        $selected = '';
                                        if ( null !== filter_input( INPUT_POST, 'filter-name' ) && $user->ID == filter_input( INPUT_POST, 'filter-name' ) ) {
                                            $selected = 'selected';
                                        }
                                        $option = '<option ' . esc_html( $selected ) . ' value="' . $user->ID . '">' . $user->display_name . '</option>';
                                        echo wp_kses( $option, ddwc_allowed_tags() );
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="<?php esc_attr_e( 'SUBMIT', 'delivery-drivers-for-woocommerce' ); ?>" />
                    </div>
                </form>
                <?php
                do_action( 'ddwc_admin_orders_form_after' );

                // Filter variables.
                if ( isset( $_SERVER ) && ! empty( $_SERVER ) && 'POST' == filter_input( INPUT_SERVER, 'REQUEST_METHOD' ) ) {
                    // Check if the filter-from field is set.
                    if ( null !== filter_input( INPUT_POST, 'filter-from' ) ) {
                        // Form field - from date.
                        $from_time  = date( 'Y-m-d', strtotime( filter_input( INPUT_POST, 'filter-from' ) ) );
                        $from_year  = date( 'Y', strtotime( $from_time ) );
                        $from_month = date( 'n', strtotime( $from_time ) );
                        $from_day   = date( 'j', strtotime( $from_time ) );
                    }
                    // Check if the filter-to field is set.
                    if ( null !== filter_input( INPUT_POST, 'filter-to' ) ) {
                        // Form field - to date.
                        $to_time  = date( 'Y-m-d', strtotime( filter_input( INPUT_POST, 'filter-to' ) ) );
                        $to_year  = date( 'Y', strtotime( $to_time ) );
                        $to_month = date( 'n', strtotime( $to_time ) );
                        $to_day   = date( 'j', strtotime( $to_time ) );
                    }
                } else {
                    // From time (7 days ago).
                    $from_time  = date( 'Y-m-d', strtotime( '-7 days' ) );
                    $from_year  = date( 'Y', strtotime( $from_time ) );
                    $from_month = date( 'n', strtotime( $from_time ) );
                    $from_day   = date( 'j', strtotime( $from_time ) );
                    // To time (now).
                    $to_time  = date( 'Y-m-d' );
                    $to_year  = date( 'Y', strtotime( $to_time ) );
                    $to_month = date( 'n', strtotime( $to_time ) );
                    $to_day   = date( 'j', strtotime( $to_time ) );
                }

                // Update the args.
                $args['date_query'] = array(
                    array(
                        'after' => array(
                            'year'  => $from_year,
                            'month' => $from_month,
                            'day'   => $from_day,
                        ),
                        'before' => array(
                            'year'  => $to_year,
                            'month' => $to_month,
                            'day'   => $to_day,
                        ),
                        'inclusive' => true,
                    ),
                );

                // Check if the form filter-name is set.
                if ( null !== filter_input( INPUT_POST, 'filter-name' ) ) {
                    // Set the Driver ID key name.
                    $args['meta_key'] = 'ddwc_driver_id';
                    // Set the Driver ID value.
                    $args['meta_value'] = filter_input( INPUT_POST, 'filter-name' );
                }

                /**
                 * Get Delivery Orders
                 */
                $driver_orders = get_posts( $args );

                do_action( 'ddwc_admin_orders_table_before' );

                $html = '<table class="ddwc-dashboard admin-orders">';

                // Array for admin orders table thead.
                $thead = array(
                    esc_attr__( 'ID', 'delivery-drivers-for-woocommerce' ),
                    esc_attr__( 'Date', 'delivery-drivers-for-woocommerce' ),
                    esc_attr__( 'Location', 'delivery-drivers-for-woocommerce' ),
                    esc_attr__( 'Status', 'delivery-drivers-for-woocommerce' ),
                    apply_filters( 'ddwc_driver_dashboard_admin_orders_total_title', esc_attr__( 'Total', 'delivery-drivers-for-woocommerce' ) ),
                );

                // Filter the thead array.
                $thead = apply_filters( 'ddwc_driver_dashboard_admin_orders_order_table_thead', $thead );

                $html .= '<thead><tr>';
                // Loop through $thead.
                foreach ( $thead as $row ) {
                    // Add td to thead.
                    $html .= '<td>' . $row . '</td>';
                }
                $html .= '</tr></thead>';

                $html .= '<tbody>';
                foreach ( $driver_orders as $driver_order ) {

                    // Get an instance of the WC_Order object.
                    $order = wc_get_order( $driver_order->ID );

                    // Get the order data.
                    $order_data           = $order->get_data();
                    $currency_code        = $order_data['currency'];
                    $currency_symbol      = get_woocommerce_currency_symbol( $currency_code );
                    $order_id             = $order_data['id'];
                    $order_status         = $order_data['status'];
                    $order_date_created   = $order_data['date_created']->date( apply_filters( 'ddwc_date_format', get_option( 'date_format' ) ) );
                    $order_shipping_total = $order_data['shipping_total'];
                    $order_total          = $order_data['total'];

                    // Billing information.
                    $order_billing_city  = $order_data['billing']['city'];
                    $order_billing_state = $order_data['billing']['state'];
                    $order_billing_code  = $order_data['billing']['postcode'];

                    // Shipping information.
                    $order_shipping_city  = $order_data['shipping']['city'];
                    $order_shipping_state = $order_data['shipping']['state'];
                    $order_shipping_code  = $order_data['shipping']['postcode'];

                    // Create address to use in the table.
                    $address = $order_billing_city . ' ' . $order_billing_state . ', ' . $order_billing_code;

                    // Set address to shipping (if available).
                    if ( isset( $order_shipping_city ) ) {
                        $address = $order_shipping_city . ' ' . $order_shipping_state . ', ' . $order_shipping_code;
                    }

                    // Order total.
                    if ( isset( $order_total ) ) {
                        $order_total = $currency_symbol . $order_total;
                        $order_total = apply_filters( 'ddwc_driver_dashboard_admin_orders_total', $order_total, $driver_order->ID );
                    } else {
                        $order_total = '-';
                    }

                    // Array for admin orders table tbody.
                    $tbody = array(
                        '<a href="' . esc_url( apply_filters( 'ddwc_driver_dashboard_admin_orders_order_details_url', admin_url( 'post.php?post=' . $driver_order->ID . '&action=edit' ), $driver_order->ID ) ) . '">' . esc_html( apply_filters( 'ddwc_order_number', $driver_order->ID ) ) . '</a>',
                        $order_date_created,
                        apply_filters( 'ddwc_driver_dashboard_admin_orders_orders_table_address', $address ),
                        wc_get_order_status_name( $order_status ),
                        $order_total
                    );

                    // Array for admin orders table tbody.
                    $tbody = apply_filters( 'ddwc_driver_dashboard_admin_orders_order_table_tbody', $tbody, $order_id );

                    // Statuses for driver.
                    $statuses = array(
                        'driver-assigned',
                        'out-for-delivery',
                        'order-returned'
                    );

                    // Filter the statuses.
                    $statuses = apply_filters( 'ddwc_driver_dashboard_orders_table_statuses', $statuses );

                    // Add orders to table if order status matches item in $statuses array.
                    if ( in_array( $order_status, $statuses ) ) {
                        $html .= '<tr>';
                        // Loop through $tbody.
                        foreach ( $tbody as $row ) {
                            $html .= '<td>' . $row . '</td>';
                        }
                        $html .= '<tr>';
                    }
                }
                $html .= '</tbody>';
                $html .= '</table>';

                echo wp_kses( $html, wp_kses_allowed_html( 'post' ) );

                do_action( 'ddwc_admin_orders_table_after' );

                // Add the Delivery Drivers table.
                ddwc_driver_dashboard_admin_drivers_table();

            } else {

                // Set the Access Denied page text.
                $access_denied = '<h3 class="ddwc access-denied">' . esc_attr__( 'Access Denied', 'delivery-drivers-for-woocommerce' ) . '</h3><p>' . esc_attr__( 'Sorry, but you are not able to view this page.', 'delivery-drivers-for-woocommerce' ) . '</p>';

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

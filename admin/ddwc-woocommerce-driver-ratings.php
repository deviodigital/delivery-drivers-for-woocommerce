<?php
/**
 * Custom functions for adding Delivery Driver
 * details to Wommerce Orders
 *
 * @link       https://www.deviodigital.com
 * @since      1.6
 *
 * @package    DDWC
 * @subpackage DDWC/admin
 * @author     Devio Digital <contact@deviodigital.com>
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Add Driver details to customer's order
 *
 * @return string
 * @since 1.6
 */
function ddwc_order_driver_details( $order ) {
    // Get Order ID.
    $order_id = $order->get_id();
    // Get order data.
    $order_data = $order->get_data();
    // Get order status.
    $order_status = $order_data['status'];
    // Get Driver ID.
    $driver_id = get_post_meta( $order_id, 'ddwc_driver_id', true );
    // Get Driver user data.
    $driver_meta = get_userdata( $driver_id );
    // Empty var.
    $driver_pic = '';
    // Driver pic.
    if ( get_user_meta( $driver_id, 'ddwc_driver_picture', true ) ) {
        $driver_pic = get_user_meta( $driver_id, 'ddwc_driver_picture', true );
    }
    // If there's a driver ID attached.
    if ( '-1' !== $driver_id && '' !== $driver_id ) {
        $string  = '<div class="ddwc-driver-details">';
        $string .= '<h2>' . esc_attr__( 'Delivery Driver', 'delivery-drivers-for-woocommerce' ) . '</h2>';
        $string .= '<div class="ddwc-driver-details-img">';
        // Driver picture.
        if ( get_user_meta( $driver_id, 'ddwc_driver_picture', true ) ) {
            $driver_pic = get_user_meta( $driver_id, 'ddwc_driver_picture', true );
            $string    .= '<a href="' . $driver_pic['url'] . '"><img src="' . $driver_pic['url'] . '" alt="' . $driver_meta->user_firstname . ' ' . $driver_meta->user_lastname . '" /></a>';
        } else {
            // Do nothing.
        }
        $string .= '</div>';
        $string .= '<div class="ddwc-driver-details-text">';
        // Driver name.
        $string .= '<h4>' . $driver_meta->user_firstname . ' ' . $driver_meta->user_lastname . '</h4>';
        // Display star rating.
        if ( 'completed' == $order_status ) {
            // Star ratings.
            $string .= esc_attr__( 'Rate Delivery', 'delivery-drivers-for-woocommerce' ) . '<br /><select class="driver-rating" id="rating_' . $order_id . '" data-id="rating_' . $order_id . '"><option value=""></option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select><span class="rating-tooltip">' . apply_filters( 'ddwc_driver_rating_thank_you_text', __( 'Thanks for your rating!', 'delivery-drivers-for-woocommerce' ) ) . '</span>';
        } elseif ( 'driver-assigned' == $order_status || 'out-for-delivery' == $order_status ) {
            // Display driver's phone number.
            if ( 'no' !== get_option( 'ddwc_settings_driver_phone_number' ) ) {
                $driver_number = get_user_meta( $driver_id, 'billing_phone', true );
                // Display driver button.
                if ( $driver_number ) {
                    $string .= '<a href="tel:' . $driver_number . '" class="button ddwc-button customer">' . esc_attr__( 'Call Driver', 'delivery-drivers-for-woocommerce' ) . '</a> ';
                }
            } else {
                // Do nothing.
            }
        } else {
            // Do nothing.
        }
        $string .= '</div>';
        // Display driver details table if order is not completed.
        if ( 'driver-assigned' == $order_status || 'out-for-delivery' == $order_status ) {
            // Driver details table.
            $string .= '<table class="ddwc-driver-details"><tbody><tr>';
            // Vehicle color.
            if ( get_user_meta( $driver_id, 'ddwc_driver_vehicle_color', true ) ) {
                // Color name.
                $color_name = esc_attr__( 'Vehicle Color', 'delivery-drivers-for-woocommerce' );
                if ( '' != get_user_meta( $driver_id, 'ddwc_driver_transportation_type', TRUE ) ) {
                    $color_name = get_user_meta( $driver_id, 'ddwc_driver_transportation_type', TRUE ) . ' Color';
                }
                $string .= '<td>' . $color_name . '<br /><strong>' . get_user_meta( $driver_id, 'ddwc_driver_vehicle_color', true ) . '</strong></td>';
            } else {
                // Do nothing.
            }
            // Vehicle model.
            if ( get_user_meta( $driver_id, 'ddwc_driver_vehicle_model', true ) ) {
                // Model name.
                $model_name = esc_attr__( 'Vehicle Model', 'delivery-drivers-for-woocommerce' );
                if ( '' != get_user_meta( $driver_id, 'ddwc_driver_transportation_type', TRUE ) ) {
                    $model_name = get_user_meta( $driver_id, 'ddwc_driver_transportation_type', TRUE ) . ' Model';
                }
                $string .= '<td>' . $model_name . '<br /><strong>' . get_user_meta( $driver_id, 'ddwc_driver_vehicle_model', true ) . '</strong></td>';
            } else {
                // Do nothing.
            }
            // Driver License plate.
            if ( get_user_meta( $driver_id, 'ddwc_driver_license_plate', true ) ) {
                $string .= '<td>' . esc_attr__( 'License Plate', 'delivery-drivers-for-woocommerce' ) . '<br /><strong>' . get_user_meta( $driver_id, 'ddwc_driver_license_plate', true ) . '</strong></td>';
            } else {
                // Do nothing.
            }
            $string .= '</tr></tbody></table>';
        }
        $string .= '</div>';

        echo $string;

        /**
         * @todo Move JavaScript to public JS file and pass $ddwc_delivery_rating to it
         */

        // Get the ajax rating file.
        $ddwc_delivery_rating = get_post_meta( $order_id, 'ddwc_delivery_rating', TRUE );
    ?>
    <script type="text/javascript">
    $(function() {
        $('.driver-rating').barrating({
            theme: 'fontawesome-stars',
            initialRating: "<?php esc_html_e( $ddwc_delivery_rating ); ?>",
            showSelectedRating: true,
            // onSelect is what triggers the saving of the rating.
            onSelect: function(value, text, event) {
                // Get element id by data-id attribute
                var el = this;
                var el_id = el.$elem.data('id');
                // rating was selected by a user
                if (typeof(event) !== 'undefined') {
                    var split_id = el_id.split("_");
                    var postid = split_id[1]; // postid.
                    // Sending data to the ddwc_driver_rating function.
                    $.post(WPaAjax.ajaxurl, {
                        action: 'ddwc_driver_rating',
                        postid: postid,
                        rating: value
                    }, function(response) {
                        console.log(response);
                    });
                }
                $('span.rating-tooltip').addClass('rated');
                setTimeout(function() {
                    $('span.rating-tooltip').removeClass('rated');
                }, 1600 );
            } // end onSelect
        });
    });
    </script>
    <?php } else {
        // Do nothing.
    }
}

// Display Driver Ratings if WooCommerce setting isn't set to NO.
if ( 'no' !== get_option( 'ddwc_settings_driver_ratings' ) ) {
    add_action( 'woocommerce_order_details_after_order_table', 'ddwc_order_driver_details' );
} else {
    // Do nothing.
}

/**
 * AJAX function to update the delivery driver's rating on an order.
 *
 * @since 1.6
 */
function ddwc_driver_rating_ajax( $order ) {

    $post_id    = filter_input( INPUT_POST, 'postid' );
    $meta_key   = 'ddwc_delivery_rating';
    $meta_value = esc_html( filter_input( INPUT_POST, 'rating' ) );

    // Update delivery rating.
    update_post_meta( $post_id, $meta_key, $meta_value );

    wp_die();
}
add_action( 'wp_ajax_ddwc_driver_rating', 'ddwc_driver_rating_ajax' );
add_action( 'wp_ajax_nopriv_ddwc_driver_rating', 'ddwc_driver_rating_ajax' );

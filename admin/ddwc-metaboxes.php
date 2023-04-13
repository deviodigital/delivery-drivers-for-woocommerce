<?php

/**
 * The Delivery Driver Metaboxes.
 *
 * @package    DDWC
 * @subpackage DDWC/admin
 * @author     Devio Digital <contact@deviodigital.com>
 * @license    GPL-2.0+ http://www.gnu.org/licenses/gpl-2.0.txt
 * @link       https://www.deviodigital.com
 * @since      1.0.0
 */

/**
 * Order Details metabox
 *
 * Adds the order details metabox.
 *
 * @since  1.0
 * @return void
 */
function ddwc_metaboxes() {
    add_meta_box(
        'ddwc_metaboxes',
        esc_attr__( 'Delivery Driver', 'delivery-drivers-for-woocommerce' ),
        'ddwc_build',
        'shop_order',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'ddwc_metaboxes' );

/**
 * Building the metabox
 * 
 * @return string
 */
function ddwc_build() {
    global $post;

    // Noncename needed to verify where the data originated.
    echo '<input type="hidden" name="ddwc_meta_noncename" id="ddwc_meta_noncename" value="' .
    wp_create_nonce( plugin_basename( __FILE__ ) ) . '" />';

    // Get the driver data if its already been entered.
    $ddwc_driver_id = get_post_meta( $post->ID, 'ddwc_driver_id', true );

    // Echo Delivery Driver Metabox Input Field.
    echo '<div class="ddwc-driver-box">';
    wp_dropdown_users( array(
        'show_option_none' => '--',
        'role'             => 'driver',
        'name'             => 'ddwc_driver_id',
        'id'               => 'ddwc_driver_id',
        'selected'         => $ddwc_driver_id,
        'class'            => 'widefat',
        'show'             => 'display_name'
    ) );
    echo '</div>';

    // Get driver rating.
    $ddwc_driver_rating = get_post_meta( $post->ID, 'ddwc_delivery_rating', true );
    // Get driver phone number.
    $ddwc_driver_number = get_user_meta( $ddwc_driver_id, 'billing_phone', true );
    echo '<p>';
    // Display driver button.
    if ( ! empty( $ddwc_driver_number ) &&  '-1' != $ddwc_driver_id ) {
        echo '<a href="tel:' . esc_html( $ddwc_driver_number ) . '" class="button ddwc-button customer">' . esc_attr__( 'Call Driver', 'delivery-drivers-for-woocommerce' ) . '</a> ';
    }
    echo '<a href="/wp-admin/user-edit.php?user_id=' . esc_attr( $ddwc_driver_id ) . '" class="button ddwc-button customer">' . esc_attr__( 'View Profile', 'delivery-drivers-for-woocommerce' ) . '</a>';
    echo '</p>';
    // Display driver rating.
    if ( ! empty( $ddwc_driver_rating ) ) {
        // Star icon.
        $star = '<i class="fas fa-star"></i>';
        // Delivery rating.
        echo '<p>' . esc_attr__( 'Customer rating', 'delivery-drivers-for-woocommerce' ) . ': ' . str_repeat( $star, $ddwc_driver_rating ) . '</p>';
    }
}

/**
 * Save the Metabox Data
 *
 * @param int $post_id
 *
 * @return void
 */
function ddwc_driver_save_order_details( $post_id ) {
    // Verify nonce.
    if ( ! isset( $_POST['ddwc_meta_noncename'] ) || ! wp_verify_nonce( $_POST['ddwc_meta_noncename'], plugin_basename( __FILE__ ) ) ) {
        return;
    }

    // Check if user has permission to edit post.
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Get driver ID.
    $ddwc_driver_id = isset( $_POST['ddwc_driver_id'] ) ? $_POST['ddwc_driver_id'] : '';

    // Get the current driver ID.
    $current_driver_id = get_post_meta( $post_id, 'ddwc_driver_id', true );

    // Save driver ID.
    if ( -1 != $ddwc_driver_id ) {
        update_post_meta( $post_id, 'ddwc_driver_id', $ddwc_driver_id );

        // Get the driver name.
        $driver_name = '';
        if ( ! empty( $ddwc_driver_id ) && '-1' != $ddwc_driver_id ) {
            $driver_name = get_the_author_meta( 'display_name', $ddwc_driver_id );
        }

        // If the driver data is different than the current driver, add a note to the order.
        if ( $ddwc_driver_id != $current_driver_id ) {
            $note  = sprintf( esc_html__( 'Delivery driver changed to %s', 'delivery-drivers-for-woocommerce' ), $driver_name );
            $order = wc_get_order( $post_id );
            $order->add_order_note( $note );

            // If the driver ID goes from none to any driver, update order status to processing.
            if ( empty( $current_driver_id ) && ! empty( $ddwc_driver_id ) ) {
                $order->update_status( 'driver-assigned' );
            }
        }
    } elseif ( -1 == $ddwc_driver_id ) {
        // Only run if current driver ID isn't empty.
        if ( '' != $current_driver_id ) {
            // Delete driver ID and add note that driver was removed.
            delete_post_meta( $post_id, 'ddwc_driver_id' );
            // Create the order note.
            $note  = esc_html__( 'Delivery driver removed', 'delivery-drivers-for-woocommerce' );
            $order = wc_get_order( $post_id );
            // Add the order note.
            $order->add_order_note( $note );
            // Update the order status.
            $order->update_status( 'processing' );
        }
    }
}
add_action( 'save_post', 'ddwc_driver_save_order_details', 10, 1 );

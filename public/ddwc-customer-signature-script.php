<?php
/**
 * Custom functions for adding signature box
 * to order details page
 *
 * @package    DDWC
 * @subpackage DDWC/admin
 * @author     Devio Digital <contact@deviodigital.com>
 * @license    GPL-2.0+ http://www.gnu.org/licenses/gpl-2.0.txt
 * @link       https://www.deviodigital.com
 * @since      3.6.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    wp_die();
}

/**
 * Add Signature Box to Order Page
 * 
 * @since  3.6.0
 * @return void
 */
function add_signature_box_to_order_page( $order ) {
    // Get order data.
    $order_data = $order->get_data();
    // Get the current order.
    $order_status = $order_data['status'];
    // Check if the order status is "out-for-delivery".
    // @TODO add option to choose which status the signature box shows up on.
    if ( $order && $order_status === 'out-for-delivery' ) {
        echo '<div id="signature-box">
            <h3>' . __( 'Signature box', 'delivery-drivers-for-woocommerce' ) . '</h3>
            <p>' . __( 'Please sign below to acknowledge you received the order via delivery', 'delivery-drivers-for-woocommerce' ) . ':</p>
            <canvas id="signature-canvas" width="400" height="200"></canvas>
            <button id="clear-signature">' . __( 'Clear Signature', 'delivery-drivers-for-woocommerce' ) . '</button>
            <button id="save-signature-button" class="btn">' . __( 'Save Signature', 'delivery-drivers-for-woocommerce' ) . '</button>
            <input type="hidden" name="customer_signature" id="customer-signature" />
        </div>';
    }
}
add_action( 'woocommerce_order_details_after_order_table', 'add_signature_box_to_order_page' );

/**
 * Display Signature on Order Admin Page
 * 
 * @param object $order - 
 * 
 * @since  3.6.0
 * @return void
 */
function display_customer_signature_on_admin_page( $order ) {
    $signature = get_post_meta( $order->get_id(), 'customer_signature', true );
    echo '<p><strong>' . __( 'Customer Signature', 'delivery-drivers-for-woocommerce' ) . ':</strong></p>';
    ddwc_display_customer_signature_image( $order->get_id() );
}
add_action( 'woocommerce_admin_order_data_after_billing_address', 'display_customer_signature_on_admin_page' );

/**
 * Save Signature Field on Order
 * 
 * @since  3.6.0
 * @return void
 */
function ddwc_ajax_save_customer_signature() {
    if ( isset( $_POST['action'] ) && $_POST['action'] === 'save_customer_signature' ) {
        $order_id  = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
        $signature = isset( $_POST['customer_signature'] ) ? sanitize_text_field( $_POST['customer_signature'] ) : '';

        if ( $order_id && $signature ) {
            // Convert base64-encoded signature to image and save as attachment
            $attachment_id = ddwc_save_signature_image( $order_id, $signature );

            // Save the signature field value.
            update_post_meta( $order_id, 'customer_signature', $attachment_id );

            // Update order status to completed.
            $order = wc_get_order( $order_id );
            if ( $order ) {
                $order->update_status( 'completed', 'Order marked as completed.', true );
            }

            echo 'Signature saved successfully.';
        } else {
            echo 'Error: Invalid order ID or signature data.';
        }

        exit();
    }
}
add_action( 'wp_ajax_save_customer_signature', 'ddwc_ajax_save_customer_signature' );

/**
 * Save signature image
 * 
 * @since  3.6.0
 * @return void
 */
function ddwc_save_signature_image( $order_id, $signature ) {
    $upload_dir = wp_upload_dir();

    // Create a unique filename for the signature image
    $filename = 'signature_' . $order_id . '_' . md5( $signature ) . '.png';
    $upload_path = $upload_dir['path'] . '/' . $filename;

    // Decode the base64-encoded signature and save as an image file
    $image_data = base64_decode( str_replace( 'data:image/png;base64,', '', $signature ) );
    file_put_contents( $upload_path, $image_data );

    // Prepare attachment data
    $attachment = array(
        'guid'           => $upload_dir['url'] . '/' . $filename,
        'post_mime_type' => 'image/png',
        'post_title'     => $filename,
        'post_content'   => '',
        'post_status'    => 'inherit',
    );

    // Insert the attachment into the media library
    $attachment_id = wp_insert_attachment( $attachment, $upload_path, $order_id );

    // Generate metadata for the attachment and update the database record
    $attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_path );
    wp_update_attachment_metadata( $attachment_id, $attachment_data );

    return $attachment_id;
}

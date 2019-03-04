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
 */

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

    if ( get_user_meta( $driver_id, 'ddwc_driver_picture', true ) ) {
        $driver_pic = get_user_meta( $driver_id, 'ddwc_driver_picture', true );
    } else {
        $driver_pic = '';
    }

    // If there's a driver ID attached.
    if ( '-1' !== $driver_id ) {

        $string  = '<div class="ddwc-driver-details">';
        $string .= '<h2>' . __( 'Delivery Driver', 'ddwc' ) . '</h2>';

        // Driver picture.
        if ( get_user_meta( $driver_id, 'ddwc_driver_picture', true ) ) {
            $driver_pic = get_user_meta( $driver_id, 'ddwc_driver_picture', true );
            $string    .= '<a href="' . $driver_pic['url'] . '"><img src="' . $driver_pic['url'] . '" alt="Robert DeVore" /></a>';
        } else {
            // Do nothing.
        }

        // Driver name.
        $string .= '<h4>' . $driver_meta->user_firstname . ' ' . $driver_meta->user_lastname . '</h4>';

        if ( get_user_meta( $driver_id, 'ddwc_driver_license_plate', true ) ) {
            $string .= '<p>' . __( 'License Plate', 'ddwc' ) . '<br /><strong>' . get_user_meta( $driver_id, 'ddwc_driver_license_plate', true ) . '</strong></p>';
        }

        // Display star rating.
        if ( 'completed' == $order_status ) {
            // Star ratings.
            $string .= __( 'Rate Delivery', 'ddwc' ) . '<br /><select class="driver-rating" id="rating_' . $order_id . '" data-id="rating_' . $order_id . '"><option value=""></option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select>';
        } else {
            // Do nothing.
        }

        $string .= '</div>';

        echo $string;

        // Get the ajax rating file.
        $ddwc_delivery_rating = get_post_meta( $order_id, 'ddwc_delivery_rating', TRUE );
    ?>

    <script type="text/javascript">
    $(function() {
        $('.driver-rating').barrating({
            theme: 'fontawesome-stars',
            initialRating: "<?php echo $ddwc_delivery_rating; ?>",
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

                   // alert(postid + ' ' + value);

                   // Sending data to the ddwc_driver_rating function.
                   $.post(WPaAjax.ajaxurl, {
                        action: 'ddwc_driver_rating',
                        postid: postid,
                        rating: value
                    }, function(response) {
                        console.log(response);
                    });
                }
            } // end onSelect
        });
    });
    </script>

    <?php } else {
        // Do nothing.
    }
}
add_action( 'woocommerce_order_details_after_order_table', 'ddwc_order_driver_details' );

/**
 * AJAX function to update the delivery driver's rating on an order.
 * 
 * @since 1.6
 */
function ddwc_driver_rating_ajax( $order ) {

	$postid    = esc_html( $_POST['postid'] );
	$metakey   = 'ddwc_delivery_rating';
	$metavalue = esc_html( $_POST['rating'] );

	// Update delivery rating.
    update_post_meta( $postid, $metakey, $metavalue );

    wp_die();
}
add_action( 'wp_ajax_ddwc_driver_rating', 'ddwc_driver_rating_ajax' );
add_action( 'wp_ajax_nopriv_ddwc_driver_rating', 'ddwc_driver_rating_ajax' );

/**
 * Save File upload in user profile.
 * 
 * @since 1.6
 */
function ddwc_save_custom_profile_fields( $user_id ) {

    // Get user.
    $user = get_userdata( $user_id );

    /**
     * If the user is a DRIVER, display the driver fields.
     * 
     * @since 1.0
     */
    if ( in_array( 'driver', (array) $user->roles ) ) {

        // Update license plate number.
        if ( isset( $_POST['ddwc_driver_license_plate'] ) ) {
            update_user_meta( $user_id, 'ddwc_driver_license_plate', $_POST['ddwc_driver_license_plate'] );
        }

        // Remove driver picture from user profile.
        if ( isset( $_POST['remove_driver_picture'] ) ) {
            // Update user meta.
            update_user_meta( $user_id, 'ddwc_driver_picture', '' );		
        }

        // If no new files are uploaded, return.
        if ( ! isset( $_FILES ) || empty( $_FILES ) || ! isset( $_FILES['ddwc_driver_picture'] ) )
            return;

        // Include file for wp_handle_upload.
        if ( ! function_exists( 'wp_handle_upload' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }

        // Include file for wp_generate_attachment_metadata.
        if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
        }

        // Handle the upload.
        $_POST['action'] = 'wp_handle_upload';

        // Get doctor recommendation file upload (if any).
        $ddwc_driver_picture = wp_handle_upload( $_FILES['ddwc_driver_picture'], array( 'test_form' => false, 'mimes' => array( 'jpg' => 'image/jpeg', 'gif' => 'image/gif', 'png' => 'image/png', 'jpeg' => 'image/jpeg' ) ) );

        // Take doctor recommendation upload, add to media library.
        if ( isset( $ddwc_driver_picture['file'] ) ) {
            // Update doctor recommendation meta.
            update_user_meta( $user_id, 'ddwc_driver_picture', $ddwc_driver_picture, get_user_meta( $user_id, 'ddwc_driver_picture', true ) );

            $filename   = $ddwc_driver_picture['file'];
            $title      = explode( '.', basename( $filename ) );
            $ext        = array_pop( $title );
            $attachment = array(
                'guid'           => $ddwc_driver_picture['url'], 
                'post_mime_type' => $ddwc_driver_picture['type'],
                'post_title'     => implode( '.', $title ),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );
            $attach_id   = wp_insert_attachment( $attachment, $filename );
            $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );

            wp_update_attachment_metadata( $attach_id, $attach_data );
        }
    }
}
add_action( 'personal_options_update', 'ddwc_save_custom_profile_fields' );
add_action( 'edit_user_profile_update', 'ddwc_save_custom_profile_fields' );
add_action( 'woocommerce_save_account_details', 'ddwc_save_custom_profile_fields' );

/**
 * Add profile options to Edit User screen
 * 
 * @since 1.6
 */
function ddwc_add_profile_options( $profileuser ) {

    // Get driver picture.
    $ddwc_driver_picture = get_user_meta( $profileuser->ID, 'ddwc_driver_picture', true );

    // Get user data.
    $user = get_userdata( $profileuser->ID );

    /**
     * If the user is a DRIVER, display the driver fields.
     * 
     * @since 1.0
     */
    if ( in_array( 'driver', (array) $user->roles ) ) {
    ?>
        <h2><?php _e( 'Driver Verification', 'ddwc' ); ?></h2>

        <table class="form-table">
        <tr>
            <th scope="row"><?php _e( 'Driver Photo', 'ddwc' ); ?></th>
            <td class="ddwc-driver-picture">
                <?php if ( get_user_meta( $profileuser->ID, 'ddwc_driver_picture', true ) ) { ?>
                <div class="ddwc-driver-picture">
                <?php
                    if ( ! isset( $ddwc_driver_picture['error'] ) ) {
                        if ( ! empty( $ddwc_driver_picture ) ) {
                            $ddwc_driver_picture = $ddwc_driver_picture['url'];
                            echo "<a href='" . $ddwc_driver_picture . "' target='_blank'><img src='" . $ddwc_driver_picture . "' width='100' height='100' class='ddwc-driver-picture' /></a><br />";
                        }
                    } else {
                        $ddwc_driver_picture = $ddwc_driver_picture['error'];
                        echo $ddwc_driver_picture. '<br />';
                    }
                ?>
                <button class="ddwc-remove-driver-picture" name="remove_driver_picture"><?php _e( 'x', 'ddwc' ); ?></button>
                </div><!-- /.ddwc-driver-picture -->
                <?php } ?>
                <input type="file" name="ddwc_driver_picture" value="" />
            </td>
        </tr>
        <tr>
            <th scope="row"><?php _e( 'License Plate Number', 'ddwc' ); ?></th>
            <td>
                <input class="regular-text" type="text" name="ddwc_driver_license_plate" value="<?php echo esc_html( get_user_meta( $profileuser->ID, 'ddwc_driver_license_plate', true ) ); ?>" />
            </td>
        </tr>
        </table>

    <?php
    }
}
add_action( 'show_user_profile', 'ddwc_add_profile_options' );
add_action( 'edit_user_profile', 'ddwc_add_profile_options' );

/**
 * Add form upload capabilites to edit user page.
 * 
 * @since 1.6
 */
function ddwc_make_form_accept_uploads() {
	echo ' enctype="multipart/form-data"';
}
add_action( 'user_edit_form_tag', 'ddwc_make_form_accept_uploads' );
add_action( 'woocommerce_edit_account_form_tag', 'ddwc_make_form_accept_uploads' );

/**
 * Add Driver details to WooCommerce My Account page.
 * 
 * @since 1.6
 */
function ddwc_add_to_edit_account_form() {
    // Include file for wp_handle_upload.
    if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }

    // Include file for wp_generate_attachment_metadata.
    if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
    }

    // Get user.
    $user_id = get_current_user_id();
    $user    = get_userdata( $user_id );

    // Save recommendation number.
    if ( isset( $_POST['ddwc_driver_license_plate'] ) ) {
        update_user_meta( $user->ID, 'ddwc_driver_license_plate', $_POST['ddwc_driver_license_plate'] );
    }
    ?>
    <?php
    /**
     * If the user is a DRIVER, display the driver fields.
     * 
     * @since 1.0
     */
    if ( in_array( 'driver', (array) $user->roles ) ) {
    ?>
    <fieldset>
        <legend><?php _e( 'Driver Verification', 'ddwc' ); ?></legend>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="reg_ddwc_driver_picture"><?php _e( 'Driver Picture', 'ddwc' ); ?></label>
            <?php if ( get_user_meta( $user->ID, 'ddwc_driver_picture', true ) ) { ?>
            <div class="ddwc-driver-picture">
            <?php
            $ddwc_driver_picture = get_user_meta( $user->ID, 'ddwc_driver_picture', true );
            if ( ! isset( $ddwc_driver_picture['error'] ) ) {
                if ( ! empty( $ddwc_driver_picture ) ) {
                    $ddwc_driver_picture = $ddwc_driver_picture['url'];
                    echo "<a href='" . $ddwc_driver_picture . "' target='_blank'><img src='" . $ddwc_driver_picture . "' width='100' height='100' class='ddwc-driver-picture' /></a><br />";
                }
            } else {
                $ddwc_driver_picture = $ddwc_driver_picture['error'];
                echo $ddwc_driver_picture. '<br />';
            }
            ?>
            <button class="remove-ddwc-driver-picture" name="remove_driver_picture"><?php _e( 'x', 'ddwc' ); ?></button>
            </div><!-- /.ddwc-driver-picture -->
            <?php } ?>
            <input type="file" name="ddwc_driver_picture" id="reg_ddwc_driver_picture" value="" />
        </p>

        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="reg_ddwc_driver_license_plate"><?php _e( 'License Plate Number', 'ddwc' ); ?></label>
            <input type="text" class="input-text" name="ddwc_driver_license_plate" id="reg_ddwc_driver_license_plate" value="<?php echo get_user_meta( $user->ID, 'ddwc_driver_license_plate', true ); ?>" />
        </p>

    </fieldset>
    <?php
    }
}
add_action( 'woocommerce_edit_account_form', 'ddwc_add_to_edit_account_form' );

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
    // Driver pic.
    if ( get_user_meta( $driver_id, 'ddwc_driver_picture', true ) ) {
        $driver_pic = get_user_meta( $driver_id, 'ddwc_driver_picture', true );
    } else {
        $driver_pic = '';
    }
    // If there's a driver ID attached.
    if ( '-1' !== $driver_id && '' !== $driver_id ) {
        $string  = '<div class="ddwc-driver-details">';
        $string .= '<h2>' . esc_html__( 'Delivery Driver', 'ddwc' ) . '</h2>';
        // Driver picture.
        if ( get_user_meta( $driver_id, 'ddwc_driver_picture', true ) ) {
            $driver_pic = get_user_meta( $driver_id, 'ddwc_driver_picture', true );
            $string    .= '<a href="' . $driver_pic['url'] . '"><img src="' . $driver_pic['url'] . '" alt="' . $driver_meta->user_firstname . ' ' . $driver_meta->user_lastname . '" /></a>';
        } else {
            // Do nothing.
        }
        // Driver name.
        $string .= '<h4>' . $driver_meta->user_firstname . ' ' . $driver_meta->user_lastname . '</h4>';
        // Display star rating.
        if ( 'completed' == $order_status ) {
            // Star ratings.
            $string .= esc_html__( 'Rate Delivery', 'ddwc' ) . '<br /><select class="driver-rating" id="rating_' . $order_id . '" data-id="rating_' . $order_id . '"><option value=""></option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select>';
        } elseif ( 'driver-assigned' == $order_status || 'out-for-delivery' == $order_status ) {
            // Display driver's phone number.
            if ( 'no' !== get_option( 'ddwc_settings_driver_phone_number' ) ) {
                $driver_number = get_user_meta( $driver_id, 'billing_phone', true );
                // Display driver button.
                if ( $driver_number ) {
                    $string .= '<a href="tel:' . $driver_number . '" class="button ddwc-button customer">' . esc_html__( 'Call Driver', 'ddwc' ) . '</a> ';
                }
            } else {
                // Do nothing.
            }
        } else {
            // Do nothing.
        }
        // Driver details table.
        $string .= '<table class="ddwc-driver-details"><tbody><tr>';
        // Vehicle color.
        if ( get_user_meta( $driver_id, 'ddwc_driver_vehicle_color', true ) ) {
            if ( '' != get_user_meta( $driver_id, 'ddwc_driver_transportation_type', TRUE ) ) {
                $color_name = get_user_meta( $driver_id, 'ddwc_driver_transportation_type', TRUE ) . ' Color';
            } else {
                $color_name = esc_html__( 'Vehicle Color', 'ddwc' );
            }
            $string .= '<td>' . $color_name . '<br /><strong>' . get_user_meta( $driver_id, 'ddwc_driver_vehicle_color', true ) . '</strong></td>';
        } else {
            // Do nothing.
        }
        // Vehicle model.
        if ( get_user_meta( $driver_id, 'ddwc_driver_vehicle_model', true ) ) {
            if ( '' != get_user_meta( $driver_id, 'ddwc_driver_transportation_type', TRUE ) ) {
                $model_name = get_user_meta( $driver_id, 'ddwc_driver_transportation_type', TRUE ) . ' Model';
            } else {
                $model_name = esc_html__( 'Vehicle Model', 'ddwc' );
            }
            $string .= '<td>' . $model_name . '<br /><strong>' . get_user_meta( $driver_id, 'ddwc_driver_vehicle_model', true ) . '</strong></td>';
        } else {
            // Do nothing.
        }
        // Driver License plate.
        if ( get_user_meta( $driver_id, 'ddwc_driver_license_plate', true ) ) {
            $string .= '<td>' . esc_html__( 'License Plate', 'ddwc' ) . '<br /><strong>' . get_user_meta( $driver_id, 'ddwc_driver_license_plate', true ) . '</strong></td>';
        }
        $string .= '</tr></tbody></table>';
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

/**
 * Save custom profile fields in user profile.
 * 
 * @since 1.6
 */
function ddwc_save_custom_profile_fields( $user_id ) {
    // Get user.
    $user = get_userdata( $user_id );
    // Get post data.
    $license_plate       = filter_input( INPUT_POST, 'ddwc_driver_license_plate' );
    $transportation_type = filter_input( INPUT_POST, 'ddwc_driver_transportation_type' );
    $vehicle_model       = filter_input( INPUT_POST, 'ddwc_driver_vehicle_model' );
    $vehicle_color       = filter_input( INPUT_POST, 'ddwc_driver_vehicle_color' );
    $driver_availability = filter_input( INPUT_POST, 'ddwc_driver_availability' );
    $remove_picture      = filter_input( INPUT_POST, 'remove_driver_picture' );

    // If the user is a DRIVER, display the driver fields.
    if ( in_array( 'driver', (array) $user->roles ) ) {
        // Update license plate number.
        if ( isset( $license_plate ) ) {
            update_user_meta( $user_id, 'ddwc_driver_license_plate', esc_html( $license_plate ) );
        }
        // Update transportation type.
        if ( isset( $transportation_type ) ) {
            update_user_meta( $user_id, 'ddwc_driver_transportation_type', esc_html( $transportation_type ) );
        }
        // Update vehicle model.
        if ( isset( $vehicle_model ) ) {
            update_user_meta( $user_id, 'ddwc_driver_vehicle_model', esc_html( $vehicle_model ) );
        }
        // Update vehicle color.
        if ( isset( $vehicle_color ) ) {
            update_user_meta( $user_id, 'ddwc_driver_vehicle_color', esc_html( $vehicle_color ) );
        }
        // Update driver availability.
        if ( isset( $driver_availability ) ) {
            update_user_meta( $user_id, 'ddwc_driver_availability', esc_html( $driver_availability ) );
        }
        // Remove driver picture from user profile.
        if ( isset( $remove_picture ) ) {
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

        // Get driver picture file upload (if any).
        $ddwc_driver_picture = wp_handle_upload( $_FILES['ddwc_driver_picture'], array( 'test_form' => false, 'mimes' => array( 'jpg' => 'image/jpeg', 'gif' => 'image/gif', 'png' => 'image/png', 'jpeg' => 'image/jpeg' ) ) );

        // Take driver picture upload, add to media library.
        if ( isset( $ddwc_driver_picture['file'] ) ) {
            // Update driver picture meta.
            update_user_meta( $user_id, 'ddwc_driver_picture', $ddwc_driver_picture, get_user_meta( $user_id, 'ddwc_driver_picture', true ) );

            $filename   = $ddwc_driver_picture['file'];
            $title      = explode( '.', basename( $filename ) );
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
    // If the user is a DRIVER, display the driver fields.
    if ( in_array( 'driver', (array) $user->roles ) ) {
    ?>
        <h2><?php esc_html_e( 'Driver Verification', 'ddwc' ); ?></h2>
        <table class="form-table">
        <tr>
            <th scope="row"><?php esc_html_e( 'Driver Photo', 'ddwc' ); ?></th>
            <td class="ddwc-driver-picture">
                <?php if ( get_user_meta( $profileuser->ID, 'ddwc_driver_picture', true ) ) { ?>
                <div class="ddwc-driver-picture">
                <?php
                    if ( ! isset( $ddwc_driver_picture['error'] ) ) {
                        if ( ! empty( $ddwc_driver_picture ) ) {
                            $ddwc_driver_picture = $ddwc_driver_picture['url'];
                            echo '<a href="' . $ddwc_driver_picture . '" target="_blank"><img src="' . $ddwc_driver_picture . '" width="100" height="100" class="ddwc-driver-picture" /></a><br />';
                        }
                    } else {
                        $ddwc_driver_picture = $ddwc_driver_picture['error'];
                        echo $ddwc_driver_picture. '<br />';
                    }
                ?>
                <button class="ddwc-remove-driver-picture" name="remove_driver_picture"><?php esc_html_e( 'x', 'ddwc' ); ?></button>
                </div><!-- /.ddwc-driver-picture -->
                <?php } ?>
                <input type="file" name="ddwc_driver_picture" value="" />
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e( 'License Plate Number', 'ddwc' ); ?></th>
            <td>
                <input class="regular-text" type="text" name="ddwc_driver_license_plate" value="<?php echo esc_html( get_user_meta( $profileuser->ID, 'ddwc_driver_license_plate', true ) ); ?>" />
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e( 'Transportation Type', 'ddwc' ); ?></th>
            <td>
            <?php
                // Transportation types.
                $transportation_types = apply_filters( 'ddwc_woocommerce_edit_account_transportation_types', array( esc_html__( 'Bicycle', 'ddwc' ), esc_html__( 'Motorcycle', 'ddwc' ), esc_html__( 'Car', 'ddwc' ), esc_html__( 'SUV', 'ddwc' ), esc_html__( 'Truck', 'ddwc' ) ) );
                // Loop through types.
                if ( $transportation_types ) {
                    printf( '<select name="ddwc_driver_transportation_type" id="ddwc_driver_transportation_type" name="ddwc_driver_transportation_type">', get_user_meta( $user->ID, 'ddwc_driver_transportation_type', TRUE ) );
                    echo '<option value="">--</option>';
                    foreach ( $transportation_types as $type ) {
                        if ( $type != get_user_meta( $user->ID, 'ddwc_driver_transportation_type', TRUE ) ) {
                            $imagesizeinfo = '';
                        } else {
                            $imagesizeinfo = 'selected="selected"';
                        }
                        printf( '<option value="%s" ' . esc_html( $imagesizeinfo ) . '>%s</option>', esc_html( $type ), esc_html( $type ) );
                    }
                    print( '</select>' );
                }
            ?>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php
                    if ( '' != get_user_meta( $user->ID, 'ddwc_driver_transportation_type', TRUE ) ) {
                        echo get_user_meta( $user->ID, 'ddwc_driver_transportation_type', TRUE ) . ' Model';
                    } else {
                        esc_html_e( 'Vehicle Model', 'ddwc' );
                    }
                ?>
            </th>
            <td>
                <input class="regular-text" type="text" name="ddwc_driver_vehicle_model" value="<?php echo esc_html( get_user_meta( $profileuser->ID, 'ddwc_driver_vehicle_model', true ) ); ?>" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php
                    if ( '' != get_user_meta( $user->ID, 'ddwc_driver_transportation_type', TRUE ) ) {
                        echo get_user_meta( $user->ID, 'ddwc_driver_transportation_type', TRUE ) . ' Color';
                    } else {
                        esc_html_e( 'Vehicle Color', 'ddwc' );
                    }
                ?>
            </th>
            <td>
                <input class="regular-text" type="text" name="ddwc_driver_vehicle_color" value="<?php echo esc_html( get_user_meta( $profileuser->ID, 'ddwc_driver_vehicle_color', true ) ); ?>" />
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e( 'Availability', 'ddwc' ); ?></th>
            <td>
                <?php
                    if ( get_user_meta( $profileuser->ID, 'ddwc_driver_availability', true ) ) {
                        $checked = 'checked';
                    } else {
                        $checked = '';
                    }
                ?>
                <input class="regular-text" type="checkbox" name="ddwc_driver_availability" <?php esc_attr_e( $checked ); ?> /> <?php esc_html_e( 'Is the driver currently accepting deliveries?', 'ddwc' ); ?>
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
    // Get user data.
    $user_id       = get_current_user_id();
    $user          = get_userdata( $user_id );
    $license_plate = filter_input( INPUT_POST, 'ddwc_driver_license_plate' );
    // Save license plate number.
    if ( isset( $license_plate ) ) {
        update_user_meta( $user->ID, 'ddwc_driver_license_plate', $license_plate );
    }
    ?>
    <?php
    /**
     * If the user is a DRIVER, display the driver fields.
     */
    if ( in_array( 'driver', (array) $user->roles ) ) { ?>
    <fieldset>
        <legend><?php esc_html_e( 'Driver Verification', 'ddwc' ); ?></legend>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="reg_ddwc_driver_picture"><?php esc_html_e( 'Driver Picture', 'ddwc' ); ?></label>
            <?php if ( get_user_meta( $user->ID, 'ddwc_driver_picture', true ) ) { ?>
            <div class="ddwc-driver-picture">
            <?php
            $ddwc_driver_picture = get_user_meta( $user->ID, 'ddwc_driver_picture', true );
            if ( ! isset( $ddwc_driver_picture['error'] ) ) {
                if ( ! empty( $ddwc_driver_picture ) ) {
                    $ddwc_driver_picture = $ddwc_driver_picture['url'];
                    echo '<a href="' . esc_html( $ddwc_driver_picture ) . '" target="_blank"><img src="' . esc_html( $ddwc_driver_picture ) . '" width="100" height="100" class="ddwc-driver-picture" /></a><br />';
                }
            } else {
                $ddwc_driver_picture = $ddwc_driver_picture['error'];
                echo esc_html( $ddwc_driver_picture ) . '<br />';
            }
            ?>
            <button class="remove-ddwc-driver-picture" name="remove_driver_picture"><?php esc_html_e( 'x', 'ddwc' ); ?></button>
            </div><!-- /.ddwc-driver-picture -->
            <?php } ?>
            <input type="file" name="ddwc_driver_picture" id="reg_ddwc_driver_picture" value="" />
        </p>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="reg_ddwc_driver_license_plate"><?php esc_html_e( 'License Plate Number', 'ddwc' ); ?></label>
            <input type="text" class="input-text" name="ddwc_driver_license_plate" id="reg_ddwc_driver_license_plate" value="<?php echo get_user_meta( $user->ID, 'ddwc_driver_license_plate', true ); ?>" />
        </p>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="reg_ddwc_driver_transportation_type"><?php esc_html_e( 'Transportation Type', 'ddwc' ); ?></label>
            <?php
                // Transportation types.
                $transportation_types = apply_filters( 'ddwc_woocommerce_edit_account_transportation_types', array( esc_html__( 'Bicycle', 'ddwc' ), esc_html__( 'Motorcycle', 'ddwc' ), esc_html__( 'Car', 'ddwc' ), esc_html__( 'SUV', 'ddwc' ), esc_html__( 'Truck', 'ddwc' ) ) );

                // Loop through types.
                if ( $transportation_types ) {
                    printf( '<select name="ddwc_driver_transportation_type" id="ddwc_driver_transportation_type" name="ddwc_driver_transportation_type" class="widefat">', get_user_meta( $user->ID, 'ddwc_driver_transportation_type', TRUE ) );
                    echo '<option value="">--</option>';
                    foreach ( $transportation_types as $type ) {
                        if ( $type != get_user_meta( $user->ID, 'ddwc_driver_transportation_type', TRUE ) ) {
                            $imagesizeinfo = '';
                        } else {
                            $imagesizeinfo = 'selected="selected"';
                        }
                        printf( '<option value="%s" ' . esc_html( $imagesizeinfo ) . '>%s</option>', esc_html( $type ), esc_html( $type ) );
                    }
                    print( '</select>' );
                }
            ?>
        </p>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="reg_ddwc_driver_vehicle_model">
                <?php
                    if ( '' != get_user_meta( $user->ID, 'ddwc_driver_transportation_type', TRUE ) ) {
                        echo get_user_meta( $user->ID, 'ddwc_driver_transportation_type', TRUE ) . ' Model';
                    } else {
                        esc_html_e( 'Vehicle Model', 'ddwc' );
                    }
                ?>
            </label>
            <input type="text" class="input-text" name="ddwc_driver_vehicle_model" id="reg_ddwc_driver_vehicle_model" value="<?php echo get_user_meta( $user->ID, 'ddwc_driver_vehicle_model', true ); ?>" />
        </p>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="reg_ddwc_driver_vehicle_color">
            <?php
                if ( '' != get_user_meta( $user->ID, 'ddwc_driver_transportation_type', TRUE ) ) {
                    echo get_user_meta( $user->ID, 'ddwc_driver_transportation_type', TRUE ) . ' Color';
                } else {
                    esc_html_e( 'Vehicle Color', 'ddwc' );
                }
            ?>
            </label>
            <input type="text" class="input-text" name="ddwc_driver_vehicle_color" id="reg_ddwc_driver_vehicle_color" value="<?php echo get_user_meta( $user->ID, 'ddwc_driver_vehicle_color', true ); ?>" />
        </p>
    </fieldset>
    <?php
    }
}
add_action( 'woocommerce_edit_account_form', 'ddwc_add_to_edit_account_form' );

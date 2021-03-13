<?php
/**
 * Custom functions for user profile fields
 *
 * @link       https://www.deviodigital.com
 * @since      3.2
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
        if ( ! isset( $_FILES ) || empty( $_FILES ) || ! isset( $_FILES['ddwc_driver_picture'] ) ) {
            return;
        }

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
        <h2><?php esc_html_e( 'Driver Verification', 'delivery-drivers-for-woocommerce' ); ?></h2>
        <table class="form-table">
        <tr>
            <th scope="row"><?php esc_html_e( 'Driver Photo', 'delivery-drivers-for-woocommerce' ); ?></th>
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
                    <button class="ddwc-remove-driver-picture" name="remove_driver_picture"><?php esc_html_e( 'x', 'delivery-drivers-for-woocommerce' ); ?></button>
                </div><!-- /.ddwc-driver-picture -->
                <?php } ?>
                <input type="file" name="ddwc_driver_picture" value="" />
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e( 'Availability', 'delivery-drivers-for-woocommerce' ); ?></th>
            <td>
                <?php
                    if ( get_user_meta( $profileuser->ID, 'ddwc_driver_availability', true ) ) {
                        $checked = 'checked';
                    } else {
                        $checked = '';
                    }
                ?>
                <input class="regular-text" type="checkbox" name="ddwc_driver_availability" <?php esc_attr_e( $checked ); ?> /> <?php esc_html_e( 'Is the driver currently accepting deliveries?', 'delivery-drivers-for-woocommerce' ); ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e( 'Transportation Type', 'delivery-drivers-for-woocommerce' ); ?></th>
            <td>
            <?php
                // Transportation types.
                $transportation_types = apply_filters( 'ddwc_woocommerce_edit_account_transportation_types', array( esc_attr__( 'Bicycle', 'delivery-drivers-for-woocommerce' ), esc_attr__( 'Motorcycle', 'delivery-drivers-for-woocommerce' ), esc_attr__( 'Car', 'delivery-drivers-for-woocommerce' ), esc_attr__( 'SUV', 'delivery-drivers-for-woocommerce' ), esc_attr__( 'Truck', 'delivery-drivers-for-woocommerce' ) ) );
                // Loop through types.
                if ( $transportation_types ) {
                    printf( '<select name="ddwc_driver_transportation_type" id="ddwc_driver_transportation_type" name="ddwc_driver_transportation_type">', get_user_meta( $user->ID, 'ddwc_driver_transportation_type', TRUE ) );
                    echo '<option value="">--</option>';
                    foreach ( $transportation_types as $type ) {
                        if ( $type != get_user_meta( $user->ID, 'ddwc_driver_transportation_type', TRUE ) ) {
                            $selected = '';
                        } else {
                            $selected = 'selected="selected"';
                        }
                        printf( '<option value="%s" ' . esc_html( $selected ) . '>%s</option>', esc_html( $type ), esc_html( $type ) );
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
                        echo get_user_meta( $user->ID, 'ddwc_driver_transportation_type', TRUE ) . ' ' . __( 'Model', 'delivery-drivers-for-woocommerce' );
                    } else {
                        esc_html_e( 'Vehicle Model', 'delivery-drivers-for-woocommerce' );
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
                        echo get_user_meta( $user->ID, 'ddwc_driver_transportation_type', TRUE ) . ' ' . __( 'Color', 'delivery-drivers-for-woocommerce' );
                    } else {
                        esc_html_e( 'Vehicle Color', 'delivery-drivers-for-woocommerce' );
                    }
                ?>
            </th>
            <td>
                <input class="regular-text" type="text" name="ddwc_driver_vehicle_color" value="<?php echo esc_html( get_user_meta( $profileuser->ID, 'ddwc_driver_vehicle_color', true ) ); ?>" />
            </td>
        </tr>
        <?php if ( 'Bicycle' != get_user_meta( $user->ID, 'ddwc_driver_transportation_type', TRUE ) ) { ?>
        <tr>
            <th scope="row"><?php esc_html_e( 'License Plate Number', 'delivery-drivers-for-woocommerce' ); ?></th>
            <td>
                <input class="regular-text" type="text" name="ddwc_driver_license_plate" value="<?php echo esc_html( get_user_meta( $profileuser->ID, 'ddwc_driver_license_plate', true ) ); ?>" />
            </td>
        </tr>
        <?php } ?>
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
        <legend><?php esc_html_e( 'Driver Verification', 'delivery-drivers-for-woocommerce' ); ?></legend>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="reg_ddwc_driver_picture"><?php esc_html_e( 'Driver Picture', 'delivery-drivers-for-woocommerce' ); ?></label>
            <?php if ( get_user_meta( $user->ID, 'ddwc_driver_picture', true ) ) { ?>
            <div class="ddwc-driver-picture">
                <?php
                // Get driver picture.
                $ddwc_driver_picture = get_user_meta( $user->ID, 'ddwc_driver_picture', true );
                // Display driver picture.
                if ( ! isset( $ddwc_driver_picture['error'] ) ) {
                    if ( ! empty( $ddwc_driver_picture ) ) {
                        $ddwc_driver_picture = $ddwc_driver_picture['url'];
                        echo '<a href="' . esc_html( $ddwc_driver_picture ) . '" target="_blank"><img src="' . esc_html( $ddwc_driver_picture ) . '" width="100" height="100" class="ddwc-driver-picture" /></a><br />';
                    }
                } else {
                    // Get error.
                    $ddwc_driver_picture = $ddwc_driver_picture['error'];
                    // Display error.
                    echo esc_html( $ddwc_driver_picture ) . '<br />';
                }
                ?>
                <button class="remove-ddwc-driver-picture" name="remove_driver_picture"><?php esc_html_e( 'x', 'delivery-drivers-for-woocommerce' ); ?></button>
            </div><!-- /.ddwc-driver-picture -->
            <?php } ?>
            <input type="file" name="ddwc_driver_picture" id="reg_ddwc_driver_picture" value="" />
        </p>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="reg_ddwc_driver_transportation_type"><?php esc_html_e( 'Transportation Type', 'delivery-drivers-for-woocommerce' ); ?></label>
            <?php
                // Transportation types.
                $transportation_types = apply_filters( 'ddwc_woocommerce_edit_account_transportation_types', array( esc_attr__( 'Bicycle', 'delivery-drivers-for-woocommerce' ), esc_attr__( 'Motorcycle', 'delivery-drivers-for-woocommerce' ), esc_attr__( 'Car', 'delivery-drivers-for-woocommerce' ), esc_attr__( 'SUV', 'delivery-drivers-for-woocommerce' ), esc_attr__( 'Truck', 'delivery-drivers-for-woocommerce' ) ) );

                // Loop through types.
                if ( $transportation_types ) {
                    printf( '<select name="ddwc_driver_transportation_type" id="ddwc_driver_transportation_type" name="ddwc_driver_transportation_type" class="widefat">', get_user_meta( $user->ID, 'ddwc_driver_transportation_type', TRUE ) );
                    echo '<option value="">--</option>';
                    // Loop through transportation types.
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
                        echo get_user_meta( $user->ID, 'ddwc_driver_transportation_type', TRUE ) . ' ' . __( 'Model', 'delivery-drivers-for-woocommerce' );
                    } else {
                        esc_html_e( 'Vehicle Model', 'delivery-drivers-for-woocommerce' );
                    }
                ?>
            </label>
            <input type="text" class="input-text" name="ddwc_driver_vehicle_model" id="reg_ddwc_driver_vehicle_model" value="<?php echo get_user_meta( $user->ID, 'ddwc_driver_vehicle_model', true ); ?>" />
        </p>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="reg_ddwc_driver_vehicle_color">
            <?php
                if ( '' != get_user_meta( $user->ID, 'ddwc_driver_transportation_type', TRUE ) ) {
                    echo get_user_meta( $user->ID, 'ddwc_driver_transportation_type', TRUE ) . ' ' . __( 'Color', 'delivery-drivers-for-woocommerce' );
                } else {
                    esc_html_e( 'Vehicle Color', 'delivery-drivers-for-woocommerce' );
                }
            ?>
            </label>
            <input type="text" class="input-text" name="ddwc_driver_vehicle_color" id="reg_ddwc_driver_vehicle_color" value="<?php echo get_user_meta( $user->ID, 'ddwc_driver_vehicle_color', true ); ?>" />
        </p>
        <?php if ( 'Bicycle' != get_user_meta( $user->ID, 'ddwc_driver_transportation_type', TRUE ) ) { ?>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="reg_ddwc_driver_license_plate"><?php esc_html_e( 'License Plate Number', 'delivery-drivers-for-woocommerce' ); ?></label>
            <input type="text" class="input-text" name="ddwc_driver_license_plate" id="reg_ddwc_driver_license_plate" value="<?php echo get_user_meta( $user->ID, 'ddwc_driver_license_plate', true ); ?>" />
        <?php } ?>
        </p>
    </fieldset>
    <?php
    }
}
add_action( 'woocommerce_edit_account_form', 'ddwc_add_to_edit_account_form' );

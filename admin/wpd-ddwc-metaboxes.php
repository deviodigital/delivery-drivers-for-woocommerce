<?php

/**
 * The Delivery Driver Metaboxes.
 *
 * @link       https://www.wpdispensary.com
 * @since      1.0.0
 *
 * @package    WPD_DDWC
 * @subpackage WPD_DDWC/admin
 */

/**
 * Order Details metabox
 *
 * Adds the order details metabox.
 *
 * @since    1.0
 */
function wpd_ddwc_metaboxes() {
	add_meta_box(
		'wpd_ddwc_metaboxes',
		__( 'Delivery Driver', 'wp-dispensary' ),
		'wpd_ddwc_build',
		'shop_order',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'wpd_ddwc_metaboxes' );

/**
 * Building the metabox
 */
function wpd_ddwc_build() {
	global $post;

	/** Noncename needed to verify where the data originated */
	echo '<input type="hidden" name="wpd_ddwc_meta_noncename" id="wpd_ddwc_meta_noncename" value="' .
	wp_create_nonce( plugin_basename( __FILE__ ) ) . '" />';

	/** Get the driver data if its already been entered */
	$wpd_ddwc_driver_id = get_post_meta( $post->ID, 'wpd_ddwc_driver_id', true );
	//echo $wpd_ddwc_driver_id;

	/** Echo Delivery Driver Metabox Input Field */
	echo '<div class="wpd-wddwc-driver-box">';
	wp_dropdown_users( array( 'show_option_none' => __( 'Assign a driver' ), 'role' => 'driver', 'name' => 'wpd_ddwc_driver_id', 'id' => 'wpd_ddwc_driver_id', 'selected' => $wpd_ddwc_driver_id, 'class' => 'widefat', 'show' => 'display_name_with_login' ) );
	echo '</div>';

}

/**
 * Save the Metabox Data
 */
function wpd_ddwc_driver_save_order_details( $post_id, $post ) {

	/**
	 * Verify this came from the our screen and with proper authorization,
	 * because save_post can be triggered at other times
	 */
	if (
		! isset( $_POST['wpd_ddwc_meta_noncename' ] ) ||
		! wp_verify_nonce( $_POST['wpd_ddwc_meta_noncename'], plugin_basename( __FILE__ ) )
	) {
		return $post->ID;
	}

	/** Is the user allowed to edit the post or page? */
	if ( ! current_user_can( 'edit_post', $post->ID ) ) {
		return $post->ID;
	}

	/**
	 * OK, we're authenticated: we need to find and save the data
	 * We'll put it into an array to make it easier to loop though.
	 */
	$wpd_ddwc_driver_order_meta['wpd_ddwc_driver_id'] = $_POST['wpd_ddwc_driver_id'];

	/** Add values of $wpd_ddwc_driver_order_meta as custom fields */

	foreach ( $wpd_ddwc_driver_order_meta as $key => $value ) { /** Cycle through the $thccbd_meta array! */
		if ( 'revision' === $post->post_type ) { /** Don't store custom data twice */
			return;
		}
		$value = implode( ',', (array) $value ); // If $value is an array, make it a CSV (unlikely)
		if ( get_post_meta( $post->ID, $key, false ) ) { // If the custom field already has a value.
			update_post_meta( $post->ID, $key, $value );
		} else { // If the custom field doesn't have a value.
			add_post_meta( $post->ID, $key, $value );
		}
		if ( ! $value ) { /** Delete if blank */
			delete_post_meta( $post->ID, $key );
		}
	}

}

add_action( 'save_post', 'wpd_ddwc_driver_save_order_details', 1, 2 ); // Save the custom fields.

<?php

/**
 * The Delivery Driver Metaboxes.
 *
 * @link       https://www.deviodigital.com
 * @since      1.0.0
 *
 * @package    DDWC
 * @subpackage DDWC/admin
 */

/**
 * Order Details metabox
 *
 * Adds the order details metabox.
 *
 * @since    1.0
 */
function ddwc_metaboxes() {
	add_meta_box(
		'ddwc_metaboxes',
		__( 'Delivery Driver', 'ddwc' ),
		'ddwc_build',
		'shop_order',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'ddwc_metaboxes' );

/**
 * Building the metabox
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
		'show_option_none' => __( 'Assign a driver', 'ddwc' ),
		'role'             => 'driver',
		'name'             => 'ddwc_driver_id',
		'id'               => 'ddwc_driver_id',
		'selected'         => $ddwc_driver_id,
		'class'            => 'widefat',
		'show'             => 'display_name_with_login'
	) );
	echo '</div>';

	// Get driver rating.
	$ddwc_driver_rating = get_post_meta( $post->ID, 'ddwc_delivery_rating', true );
	// Get driver phone number.
	$ddwc_driver_number = get_user_meta( $ddwc_driver_id, 'phone_number', true );
	// Display driver button.
	if ( ! empty( $ddwc_driver_number ) ) {
		echo '<p><a href="tel:' . $ddwc_driver_number . '" class="button ddwc-button customer">' . __( 'Call Driver', 'ddwc' ) . '</a></p>';
	}
	// Display driver rating.
	if ( ! empty( $ddwc_driver_rating ) ) {
		echo '<p>' . __( 'Delivery rating', 'ddwc' ) . ': ' . $ddwc_driver_rating . ' out of 5 stars</p>';
	}
}

/**
 * Save the Metabox Data
 */
function ddwc_driver_save_order_details( $post_id, $post ) {

	/**
	 * Verify this came from the our screen and with proper authorization,
	 * because save_post can be triggered at other times
	 */
	if (
		! isset( $_POST['ddwc_meta_noncename' ] ) ||
		! wp_verify_nonce( $_POST['ddwc_meta_noncename'], plugin_basename( __FILE__ ) )
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
	$ddwc_driver_meta['ddwc_driver_id'] = $_POST['ddwc_driver_id'];

	/** Add values of $ddwc_driver_meta as custom fields */
	foreach ( $ddwc_driver_meta as $key => $value ) {
		if ( 'revision' === $post->post_type ) {
			return;
		}
		$value = implode( ',', (array) $value );
		if ( get_post_meta( $post->ID, $key, false ) ) {
			update_post_meta( $post->ID, $key, $value );
		} else {
			add_post_meta( $post->ID, $key, $value );
		}
		if ( ! $value ) {
			delete_post_meta( $post->ID, $key );
		}
	}
}
add_action( 'save_post', 'ddwc_driver_save_order_details', 1, 2 );

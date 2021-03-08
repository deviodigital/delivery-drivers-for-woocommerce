<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       https://www.deviodigital.com
 * @since      1.0.0
 *
 * @package    DDWC
 * @subpackage DDWC/admin
 * @author     Devio Digital <contact@deviodigital.com>
 */
class Delivery_Drivers_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ddwc-admin.min.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ddwc-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'WPaAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}

}

/**
 * Add Deliver Driver Column to Orders screen
 *
 * @param [type] $columns
 * @since 2.1
 */
function ddwc_show_custom_delivery_driver_column( $columns ) {

	$new_columns = ( is_array( $columns ) ) ? $columns : array();

	// Save shipping address column.
	$shipping_address = $columns[ 'shipping_address' ];

	// Remove shiping address column.
	unset( $new_columns['shipping_address'] );

	// Save order actions column.
	$wc_actions = $columns[ 'wc_actions' ];

	// Remove order actions column.
	unset( $new_columns['wc_actions'] );

	// Add delivery driver column.
	$new_columns['delivery_driver'] = esc_attr__( 'Delivery Driver', 'delivery-drivers-for-woocommerce' );

	// Add shipping address column.
	$new_columns[ 'shipping_address' ] = $shipping_address;

	// Add shipping address column.
	$new_columns[ 'wc_actions' ] = $wc_actions;

	return $new_columns;
}
add_filter( 'manage_edit-shop_order_columns', 'ddwc_show_custom_delivery_driver_column', 21 );

/**
 * Delivery Driver column for Orders screen
 *
 * @param [type] $column
 * @since 2.1
 */
function ddwc_custom_delivery_driver_column( $column ) {
    global $post;

    switch ( $column ) {

      case 'delivery_driver' :
			// Noncename needed to verify where the data originated.
			echo '<input type="hidden" name="ddwc_meta_noncename" id="ddwc_meta_noncename" value="' .
			wp_create_nonce( plugin_basename( __FILE__ ) ) . '" />';

			// Get the driver data if its already been entered.
			$ddwc_driver_id = get_post_meta( $post->ID, 'ddwc_driver_id', true );

			// Get all drivers.
			$driver_args = array(
				'role' => 'driver',
			);
			$drivers = get_users( $driver_args );

			// Create empty array.
			$available_drivers = array();

			// Loop through drivers.
			foreach ( $drivers as $driver ) {
				// Check if driver availability is active.
				if ( get_user_meta( $driver->ID, 'ddwc_driver_availability', true ) ) {
					// Add driver to availabile list.
					$available_drivers[] = $driver->ID;
				}
			}

			// Add active driver to available list.
			$available_drivers[] = $ddwc_driver_id;

			// Echo Delivery Driver Metabox Input Field.
			echo '<div class="ddwc-driver-box">';
			wp_dropdown_users( array(
				'show_option_none' => '--',
				'role'             => 'driver',
				'name'             => $post->ID,
				'id'               => 'ddwc_driver_id',
				'selected'         => $ddwc_driver_id,
				'class'            => 'widefat',
				'show'             => 'display_name',
				'include'          => $available_drivers
			) );
			echo '</div>';

        break;
    }
}
add_action( 'manage_shop_order_posts_custom_column', 'ddwc_custom_delivery_driver_column', 21 );

/**
 * Add "no-link" class to tr's from WooCommerce orders screen
 *
 * @since 2.1
 */
function ddwc_add_no_link_to_woocommerce_orders( $classes ) {
	if ( current_user_can( 'manage_woocommerce' ) ) {
		foreach ( $classes as $class ) {
			if ( 'type-shop_order' == $class ) {
				$classes[] = 'no-link';
			}
		}
	}
	return $classes;
}
add_filter( 'post_class', 'ddwc_add_no_link_to_woocommerce_orders' );

/**
 * AJAX function to update driver ID on WooCommerce Orders page
 *
 * @since 2.1
 */
function ddwc_delivery_driver_settings() {

	$item_id    = filter_input( INPUT_POST, 'item_id' );
	$meta_key   = filter_input( INPUT_POST, 'metakey' );
	$meta_value = filter_input( INPUT_POST, 'metavalue' );

	// Update driver ID for order.
	update_post_meta( $item_id, $meta_key, $meta_value );

	// Get order.
	$order = new WC_Order( $item_id );

	// Update order status.
	if ( -1 == $meta_value ) {
		$order->update_status( 'processing' );
	} else {
		$order->update_status( 'driver-assigned' );
	}

	// WooCommerce product loop $args
	$args = array(
		'post_type'      => 'product',
		'posts_per_page' => 1000,
	);

	// Get all products based on $args.
	$loop = get_posts( $args );

	// Loop through each product.
	foreach ( $loop as $item ) {
		// Update driver ID.
		if ( $meta_value === get_post_meta( $item->ID, $meta_key, true ) ) {
			update_post_meta( $item->ID, $meta_key, $meta_value );
		}
	}

  exit;
}
add_action( 'wp_ajax_ddwc_delivery_driver_settings', 'ddwc_delivery_driver_settings' );
//add_action('wp_ajax_nopriv_ddwc_delivery_driver_settings', 'ddwc_delivery_driver_settings');

/**
 * AJAX function to update driver availability
 *
 * @since 2.3
 */
function ddwc_driver_availability_update() {

	$user_id    = filter_input( INPUT_POST, 'user_id' );
	$meta_value = filter_input( INPUT_POST, 'metavalue' );

	if ( 'on' == $meta_value ) {
		$new_value = 'on';
		$old_value = '';
	} else {
		$new_value = '';
		$old_value = 'on';
	}

	// Update driver availability.
	update_user_meta( $user_id, 'ddwc_driver_availability', $new_value, $old_value );
}
add_action( 'wp_ajax_ddwc_driver_availability_update', 'ddwc_driver_availability_update' );
add_action( 'wp_ajax_nopriv_ddwc_driver_availability_update', 'ddwc_driver_availability_update' );

/**
 * Bulk actions edit
 *
 * Add delivery drivers to the bulk actions for WooCommerce Orders.
 *
 * @since 2.5
 */
function ddwc_driver_bulk_edit( $actions ) {
	// Add order status changes.
	$actions['mark_driver-assigned']  = __( 'Change status to driver assigned', 'delivery-drivers-for-woocommerce' );
	$actions['mark_out-for-delivery'] = __( 'Change status to out for delivery', 'delivery-drivers-for-woocommerce' );
	$actions['mark_order-returned']   = __( 'Change status to order returned', 'delivery-drivers-for-woocommerce' );

	// Get all users with 'driver' user role.
	$user_query = new WP_User_Query( array( 'role' => 'driver' ) );

	// Check to make sure there are drivers added.
	if ( ! empty( $user_query->get_results() ) ) {
		// Loop through 'driver' users.
		foreach ( $user_query->get_results() as $user ) {
			// Add option to set user as the 'driver'.
			$actions['driver_id_' . $user->ID] = sprintf( esc_html__( 'Set %1$s as driver', 'delivery-drivers-for-woocommerce' ), esc_html( $user->display_name ) );
		}
	}

	return $actions;
}
add_filter( 'bulk_actions-edit-shop_order', 'ddwc_driver_bulk_edit', 20, 1 );

/**
 * Handle bulk actions
 *
 * Processes the selected options from the bulk actions list in the
 * WooCommerce Orders screen.
 *
 * @since  2.5
 * @return string
 */
function ddwc_driver_edit_handle_bulk_action( $redirect_to, $action, $post_ids ) {
    if ( $action === $_GET['action'] ) {
		// Processed IDs.
		$processed_ids = array();

		// Loop through selected orders.
		foreach ( $post_ids as $post_id ) {

			// Only run code if the bulk action is changing to Driver Assigned.
			if ( isset( $_REQUEST['mark_driver-assigned'] ) && 1 == $_REQUEST['changed'] ) {
				// Get order.
				$order = new WC_Order( $post_id );
				// Order note.
				$order_note = __( 'That\'s what happened by bulk edit:', 'delivery-drivers-for-woocommerce' );
				// Update order status.
				$order->update_status( 'driver-assigned', $order_note, true );
				// Save order.
				$order->save();
				// Action hook.
				do_action( 'ddwc_mark_driver_assigned', $post_id );
			}

			// Only run code if the bulk action is changing to Out for Delivery.
			if ( isset( $_REQUEST['mark_out-for-delivery'] ) && 1 == $_REQUEST['changed'] ) {
				// Get order.
				$order = new WC_Order( $post_id );
				// Order note.
				$order_note = __( 'That\'s what happened by bulk edit:', 'delivery-drivers-for-woocommerce' );
				// Update order status.
				$order->update_status( 'out-for-delivery', $order_note, true );
				// Save order.
				$order->save();
				// Action hook.
				do_action( 'ddwc_mark_out_for_delivery', $post_id );
			}

			// Only run code if the bulk action is changing to Order Returned.
			if ( isset( $_REQUEST['mark_order-returned'] ) && 1 == $_REQUEST['changed'] ) {
				// Get order.
				$order = new WC_Order( $post_id );
				// Order note.
				$order_note = __( 'That\'s what happened by bulk edit:', 'delivery-drivers-for-woocommerce' );
				// Update order status.
				$order->update_status( 'order-returned', $order_note, true );
				// Save order.
				$order->save();
				// Action hook.
				do_action( 'ddwc_mark_order_returned', $post_id );
			}

			// Only run code if bulk action is assigning orders to a driver.
			if ( strpos( $_GET['action'], 'driver_id_' ) !== false ) {
				// Get only the ID number from action string.
				$driver_id = str_replace( 'driver_id_', '', $_GET['action'] );

				// Get current Assigned Driver.
				$current_driver = get_post_meta( $post_id, 'ddwc_driver_id', true );

				// Update Assigned Driver.
				update_post_meta( $post_id, 'ddwc_driver_id', $driver_id );

				// Get Order instance.
				$order = new WC_Order( $post_id );

				// Get current status.
				$current_status = $order->get_status();

				// Statuses to skip.
				$skip_statuses = array(
					'on-hold',
					'driver-assigned',
					'order-returned',
					'completed',
				);
				$skip_statuses = apply_filters( 'ddwc_orders_bulk_action_update_driver_skip_statuses', $skip_statuses );

				// Update order status.
				if ( ! in_array( $current_status, $skip_statuses ) ) {
					$order->update_status( 'driver-assigned' );
				}

				// Add Post ID to array.
				$processed_ids[] = $post_id;

			}

			// Redirect.
			$redirect_to = add_query_arg( array(
				'processed_count' => count( $processed_ids ),
				'processed_ids'   => implode( ',', $processed_ids ),
			), $redirect_to );

		}
	}
	return $redirect_to;
}
add_filter( 'handle_bulk_actions-edit-shop_order', 'ddwc_driver_edit_handle_bulk_action', 10, 3 );

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
 * @author     Devio Digital <deviodigital@gmail.com>
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

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Delivery_Drivers_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Delivery_Drivers_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

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
    $new_columns['delivery_driver'] = __( 'Delivery Driver', 'ddwc' );

	// Add shipping address column.
	$new_columns[ 'shipping_address' ] = $shipping_address;

	// Add shipping address column.
//	$new_columns[ 'wc_actions' ] = $wc_actions;

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

    global $post, $woocommerce, $the_order;

    switch ( $column ) {

        case 'delivery_driver' :
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
				'name'             => $post->ID,
				'id'               => 'ddwc_driver_id',
				'selected'         => $ddwc_driver_id,
				'class'            => 'widefat',
				'show'             => 'display_name_with_login'
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
	if ( current_user_can( 'manage_woocommerce' ) ) { //make sure we are shop managers 
        foreach ( $classes as $class ) {
	        if ( $class == 'type-shop_order' ) {
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

	$item_id     = $_POST['item_id'];
	$metakey     = $_POST['metakey'];
	$metavalue   = $_POST['metavalue'];

	// Update driver ID for order.
	update_post_meta( $item_id, $metakey, $metavalue );

	// Get order.
	$order = new WC_Order( $item_id );

	// Update order status.
	if ( -1 == $metavalue ) {
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

		// Update inventory.
		if ( $metavalue === get_post_meta( $item->ID, $metakey, true ) ) {
			update_post_meta( $item->ID, $metakey, $metavalue );
		}

	}

    exit;
}
add_action( 'wp_ajax_ddwc_delivery_driver_settings', 'ddwc_delivery_driver_settings' );
//add_action('wp_ajax_nopriv_ddwc_delivery_driver_settings', 'ddwc_delivery_driver_settings');

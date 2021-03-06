<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @link       https://www.deviodigital.com
 * @since      1.0.0
 *
 * @package    DDWC
 * @subpackage DDWC/public
 * @author     Devio Digital <contact@deviodigital.com>
 */
class Delivery_Drivers_Public {

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
	 * @param    string    $plugin_name    The name of the plugin.
	 * @param    string    $version        The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		// Stylesheet.
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ddwc-public.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '-fontawesome', plugin_dir_url( __FILE__ ) . 'css/fontawesome/all.min.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		// Only load scripts on customer order pages.
		if ( is_wc_endpoint_url( 'view-order' ) ) {
			wp_enqueue_script( $this->plugin_name . '-star-rating', 'https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ddwc-public.js', array( 'jquery' ), $this->version, false );
		} elseif ( is_wc_endpoint_url( 'driver-dashboard' ) ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ddwc-public.js', array( 'jquery' ), $this->version, false );
		}
		// Localize the ddwc-public.js file.
		wp_localize_script( $this->plugin_name, 'WPaAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}

}

/**
 * Driver availability option on Driver Dashboard
 *
 * @since 2.3
 */
function ddwc_driver_dashboard_driver_availability() {
	// Checked.
	$checked = '';
	// Driver availability.
	if ( 'on' === get_user_meta( get_current_user_id(), 'ddwc_driver_availability', true ) ) {
		$checked = 'checked';
	}

	echo '<div class="ddwc-availability">
        <h4>' . esc_attr__( 'Accepting deliveries', 'delivery-drivers-for-woocommerce' ) . '</h4>
        <label class="switch">
          <input id="' . get_current_user_id() . '" type="checkbox" ' . $checked . ' />
          <span class="slider round"></span>
        </label>
	</div>';
}
add_action( 'ddwc_driver_dashboard_top', 'ddwc_driver_dashboard_driver_availability' );

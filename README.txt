=== Delivery Drivers for WooCommerce ===
Contributors: deviodigital
Donate link: https://www.deviodigital.com
Tags: delivery, ecommerce, woocommerce, courier, delivery-drivers, marijuana, dispensary, cannabis, weed
Requires at least: 3.0.1
Tested up to: 5.0.3
Stable tag: 1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Streamline your mobile workforce and increase your bottom line.

== Description ==

## Manage your mobile workforce

[Delivery Drivers for WooCommerce](https://robertdevore.com/delivery-drivers-for-woocommerce/) offers better driver management for all delivery services who use WooCommerce.

Your business benefits from this plugin by giving your drivers the ability to easily connect with both the customer of the order and your dispatch center.

Time saved is money earned.

### Get more done

The Delivery Drivers for WooCommerce plugin helps your business by reducing paperwork, communications and other manual tasks that eat up your time.

### Manage your drivers

This plugin gives you the ability to add new users with the designated `Driver` role to your WordPress site.

Once the driver has been added to your userlist, you can edit orders and assign a specific driver to the order.

### Driver capabilites

Give your drivers the ability to view their assigned orders, mark an order as `Out for Delivery` and then `Completed` after the order has been delivered.

With these capabilities passed along to your drivers, you now have additional time free to manage other areas of your business.

## For any mobile workforce

Below are a few examples of delivery services that can benefit from the **Delivery Drivers for WooCommerce** plugin.

* Cannabis
* Flowers
* Health & Beauty
* Mobile Massage
* Alcohol Delivery
* Restaurant
* Grocery Stores
* Healthcare
* Cleaning
* Laundry
* ... and more!

## Pro Features

[Delivery Drivers for WooCommerce Pro](https://deviodigital.com/product/delivery-drivers-for-woocommerce-pro/) includes the following additional features:

* Auto-assign drivers when an order is submitted
* Accept driver applications from the Driver Dashboard
* Email driver when they've been assigned a new order
* Email customer when the driver marks an order as "Out for Delivery"
* Email administrator when the driver marks an order as "Completed"

== Installation ==

1. In your dashboard, go to `Plugins -> Add New`
1. Search for `Delivery Drivers` and Install this plugin
1. Go to `Settings -> Permalinks` and re-save the page
1. Pat yourself on the back for a job well done :)

== Screenshots ==

1. Delivery Drivers WooCommerce Settings page
2. Orders page, showing two new statuses (Driver Assigned & Out for Delivery)
3. Driver dashboard, displaying all assigned orders (theme in use: [CannaBiz](https://www.wpdispensary.com/downloads/cannabiz/))
4. Order details display (theme in use: [CannaBiz](https://www.wpdispensary.com/downloads/cannabiz/))

== Changelog ==

= 1.3 =
* Added payment gateway info to order details in `admin/ddwc-dashboard-shortcode.php`
* Added year to the order date displayed with order details in `admin/ddwc-dashboard-shortcode.php`
* Bugfix shipping/billing address display in order details in `admin/ddwc-dashboard-shortcode.php`
* Updated Order Details title bottom margin in `public/css/ddwc-public.css`
* Updated CSS to include new class name for delivery charge in `public/css/ddwc-public.css`
* Updated order details display style and content in `admin/ddwc-dashboard-shortcode.php`
* Updated `.pot` file with new translation strings in `languages/ddwc.pot`
* Various code cleanup and doc updates throughout multiple files

= 1.2 =
* Bug fix for driver-dashboard query vars in `admin/ddwc-woocommerce-account-tab.php`
* Updated `the_title` to say **Driver Dashboard** for driver-dashboard page in `admin/ddwc-woocommerce-account-tab.php`
* Updated code to display customer name in driver dadhboard in `admin/ddwc-dashboard-shortcode.php`
* Updated `.pot` file with new translation strings in `languages/ddwc.pot`
* Various code updates and general code cleanup throughout multiple files

= 1.1 =
* Added checkbox for drivers to hide completed orders from assigned orders table in `admin/ddwc-dashboard-shortcode.php`
* Added checkbox for drivers to hide completed orders from assigned orders table in `public/js/ddwc-public.js`
* Added checkbox for drivers to hide completed orders from assigned orders table in `public/css/ddwc-public.css`
* Added redirect on login for delivery drivers to the Driver Dashboard in `admin/ddwc-woocommerce-settings.php`
* Added `ddwc_driver_dashboard_order_table_tbody_top` and `ddwc_driver_dashboard_order_table_tbody_bottom` action hooks in order details table in `admin/ddwc-dashboard-shortcode.php`
* Hide "Go Pro" link if Pro plugin is active in `delivery-drivers-for-woocommerce.php`
* Updated individual order details table data in `admin/ddwc-dashboard-shortcode.php`
* Updated `.pot` file with new translation strings in `languages/ddwc.pot`
* Various code updates and general code cleanup throughout multiple files

= 1.0.4 =
* General code clean up in `admin/ddwc-woocommerce-orders.php` and `admin/ddwc-woocommerce-settings.php`
* Updated text with new translation strings in `admin/ddwc-dashboard-shortcode.php`
* Updated text with new translation strings in `admin/ddwc-woocommerce-orders.php`
* Updated text with new translation strings in `admin/ddwc-woocommerce-settings.php`
* Updated `.pot` file with new translation strings in `languages/ddwc.pot`

= 1.0.3 =
* Added `.pot` file for localization in `languages/ddwc.pot`

= 1.0.2 =
* Added conditional check for delivery address display in `admin/ddwc-dashboard-shortcode.php`

= 1.0.1 =
* Added two new action hooks in `admin/ddwc-dashboard-shortcode.php`

= 1.0 =
* Initial release

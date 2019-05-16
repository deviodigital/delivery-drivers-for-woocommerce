# Changelog

### 1.8.1
* Added conditional check for WooCommerce view-order endpoint when loading javascript files on front-end of website in `public/class-ddwc-public.php`
* Updated script loading to remove unnecessary admin.js file in `admin/class-ddwc-admin.php`
* Updated text strings for localization in `admin/ddwc-dashboard-shortcode.php`
* Updated `.pot` file with new translation strings in `languages/ddwc.pot`
* General code cleanup throuhgout multiple files

### 1.8
* Added display driver phone number setting in `admin/ddwc-woocommerce-settings.php`
* Added driver phone number to customer order details in `admin/ddwc-woocommerce-driver-ratings.php`
* Added (optional) text to out for delivery messsage text in `admin/ddwc-dashboard-shortcode.php`
* Updated delivery fee to use the proper decimal placement in `admin/ddwc-dashboard-shortcode.php`
* Updated CSS to include styles for SMS Updates title text on checkout in `public/css/ddwc-public.css`
* Updated `.pot` file with new translation strings in `languages/ddwc.pot`
* General code cleanup throuhgout multiple files

### 1.7
* Added ability for driver to add a note during the "out for delivery" status change in `admin/ddwc-dashboard-shortcode.php`
* Added Company name to customer address display in `admin/ddwc-dashboard-shortcode.php`
* Added CSS for driver's out for delivery message in `public/css/ddwc-public.css`
* Updated `.pot` file with new translation strings in `languages/ddwc.pot`
* General code cleanup throughout multiple files

### 1.6
* Added Delivery Driver details and star ratings on order details page in `includes/class-ddwc.php`
* Added Delivery Driver details and star ratings on order details page in `admin/ddwc-woocommerce-driver-ratings.php`
* Added Delivery Driver star ratings CSS in `admin/css/ddwc-public.css`
* Added Driver rating to Delivery Drivers metabox in `admin/ddwc-metaboxes.php`
* Added Delivery Driver star ratings JavaScript in `public/class-ddwc-public.php`
* Added Delivery Driver star ratings JavaScript in `public/js/ddwc-public.php`
* Added Delivery Driver star ratings CSS in `public/css/ddwc-public.css`
* Added Driver Ratings option to the DDWC WooCommerce Settings page in `admin/ddwc-woocommerce-settings.php`
* Added 2 new action hooks in `admin/ddwc-dashboard-shortcode.php`
* Added new filter for driver login redirect in `admin/ddwc-woocommerce-settings.php`
* Bugfix removed 'no completed orders' text in `admin/ddwc-dashboard-shortcode.php`
* WordPress Coding Standards updates in `admin/ddwc-dashboard-shortcode.php`
* Updated text strings for localization in `admin/ddwc-woocommerce-orders.php`
* Updated `.pot` file with new translation strings in `languages/ddwc.pot`
* General code cleanup throughout multiple files

### 1.5
* Added text output if there are no completed orders in `admin/ddwc-dashboard-shortcode.php`
* Added `ddwc_assigned_orders_empty` filter in `admin/ddwc-dashboard-shortcode.php`
* Added `ddwc_assigned_orders_empty_after` action hook in empty orders output in `admin/ddwc-dashboard-shortcode.php`
* General code cleanup throughout multiple files

### 1.4
* Added 2 new action hooks for the Assigned Orders table in `admin/ddwc-dashboard-shortcode.php`
* Added Completed Orders table to Driver Dashboard in `admin/ddwc-dashboard-shortcode.php`
* Added 2 new action hooks for the Completed Orders table in `admin/ddwc-dashboard-shortcode.php`
* Updated My Account tab text to Driver Dashboard in `admin/ddwc-woocommerce-account-tab.php`
* Updated billing name in the order details view with table codes in `admin/ddwc-dashboard-shortcode.php`
* Updated Assigned Orders table display options and details in `admin/ddwc-dashboard-shortcode.php`
* Updated `.pot` file with new translation strings in `languages/ddwc.pot`

### 1.3
* Added payment gateway info to order details in `admin/ddwc-dashboard-shortcode.php`
* Added year to the order date displayed with order details in `admin/ddwc-dashboard-shortcode.php`
* Bugfix shipping/billing address display in order details in `admin/ddwc-dashboard-shortcode.php`
* Updated Order Details title bottom margin in `public/css/ddwc-public.css`
* Updated CSS to include new class name for delivery charge in `public/css/ddwc-public.css`
* Updated order details display style and content in `admin/ddwc-dashboard-shortcode.php`
* Updated `.pot` file with new translation strings in `languages/ddwc.pot`
* Various code cleanup and doc updates throughout multiple files

### 1.2
* Bug fix for driver-dashboard query vars in `admin/ddwc-woocommerce-account-tab.php`
* Updated `the_title` to say **Driver Dashboard** for driver-dashboard page in `admin/ddwc-woocommerce-account-tab.php`
* Updated code to display customer name in driver dadhboard in `admin/ddwc-dashboard-shortcode.php`
* Updated `.pot` file with new translation strings in `languages/ddwc.pot`
* Various code updates and general code cleanup throughout multiple files

### 1.1
* Added checkbox for drivers to hide completed orders from assigned orders table in `admin/ddwc-dashboard-shortcode.php`
* Added checkbox for drivers to hide completed orders from assigned orders table in `public/js/ddwc-public.js`
* Added checkbox for drivers to hide completed orders from assigned orders table in `public/css/ddwc-public.css`
* Added redirect on login for delivery drivers to the Driver Dashboard in `admin/ddwc-woocommerce-settings.php`
* Added `ddwc_driver_dashboard_order_table_tbody_top` and `ddwc_driver_dashboard_order_table_tbody_bottom` action hooks in order details table in `admin/ddwc-dashboard-shortcode.php`
* Hide "Go Pro" link if Pro plugin is active in `delivery-drivers-for-woocommerce.php`
* Updated individual order details table data in `admin/ddwc-dashboard-shortcode.php`
* Updated `.pot` file with new translation strings in `languages/ddwc.pot`
* Various code updates and general code cleanup throughout multiple files

### 1.0.4
* General code clean up in `admin/ddwc-woocommerce-orders.php` and `admin/ddwc-woocommerce-settings.php`
* Updated text with new translation strings in `admin/ddwc-dashboard-shortcode.php`
* Updated text with new translation strings in `admin/ddwc-woocommerce-orders.php`
* Updated text with new translation strings in `admin/ddwc-woocommerce-settings.php`
* Updated `.pot` file with new translation strings in `languages/ddwc.pot`

### 1.0.3
* Added `.pot` file for localization in `languages/ddwc.pot`

### 1.0.2
* Added conditional check for delivery address display in `admin/ddwc-dashboard-shortcode.php`

### 1.0.1
* Added two new action hooks in `admin/ddwc-dashboard-shortcode.php`

### 1.0.0
* Initial release
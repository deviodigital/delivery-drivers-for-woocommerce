# Changelog

## 2.9
*   Added `ddwc_date_format` & `ddwc_time_format` filters in `admin/ddwc-dashboard-shortcode.php`
*   Added `ddwc_driver_dashboard_assigned_orders_statuses` filter in `admin/ddwc-dashboard-shortcode.php`
*   Added `ddwc_driver_dashboard_change_statuses_redirect_url` filter in `admin/ddwc-functions.php`
*   Added documentation link to settings page in `admin/ddwc-woocommerce-settings.php`
*   Added check for DDWC Pro with admin notice if version number is older than currently available in `delivery-drivers-for-woocommerce.php`
*   Bugfix updates redirect errors on driver status change in ` admin/ddwc-functions.php`
*   Bugfix to hide the 'Call Driver' button in Edit Order metabox if no driver is selected in `admin/ddwc-metaboxes.php`
*   Updated date/time formats to use default WordPress settings in `admin/ddwc-dashboard-shortcode.php`
*   Updated dropdown of available drivers on Orders screen in `admin/class-ddwc-admin.php`
*   Updated delivery drivers metabox dropdown to remove "Assign a driver" text as default in `admin/ddwc-metaboxes.php`
*   Updated text strings for localization in `languages/ddwc.pot`
*   General code cleanup throughout multiple files in the plugin

## 2.8
*   Added `ddwc_driver_dashboard_phone_numbers` filter in `admin/ddwc-dashboard-shortcode.php`
*   Added `ddwc_delivery_address_google_map_mode` filter in `admin/ddwc-dashboard-shortcode.php`
*   Added `Google Maps Mode` option to the WooCommerce DDWC Settings page in `admin/ddwc-woocommerce-settings.php`
*   Updated text strings for localization in `languages/ddwc.pot`
*   General code cleanup throughout multiple files in the plugin

## 2.7.1
*   General code cleanup throughout multiple files in the plugin

## 2.7
*   Added 3 filters for change status redirect URL's in `admin/ddwc-functions.php`
*   Added `ddwc_delivery_address_google_map_geocode` filter in `admin/ddwc-functions.php`
*   Added `ddwc_delivery_address_google_map_geocode` function before Google Map in `admin/ddwc-dashboard-shortcode.php`
*   Added `ddwc_my_account_endpoint_title_driver_dashboard` filter in `admin/ddwc-woocommerce-account-tab.php`
*   Added `ddwc_driver_dashboard_completed_orders_before_tbody` action hook in `admin/ddwc-dashboard-shortcode.php`
*   Added Google Maps Geocode WooCommerce setting in `admin/ddwc-woocommerce-settings.php`
*   Updated text strings for localization in `languages/ddwc.pot`
*   General code cleanup throughout multiple files in the plugin

## 2.6
*   Added driver dashboard for admins, displaying delivery drivers data in a table in `admin/ddwc-dashboard-shortcode.php`
*   Added 2 action hooks for the admin driver dashboard delivery drivers table in `admin/ddwc-dashboard-shortcode.php`
*   Added 3 filters for the admin driver dashboard delivery drivers table in `admin/ddwc-dashboard-shortcode.php`
*   Updated CSS for admin's driver dashboard delivery drivers table in `public/css/ddwc-public.css`
*   Updated text strings for localization in `languages/ddwc.pot`
*   General code cleanup throughout multiple files in the plugin

## 2.5.1
*   Bugfix that made the Bulk Edit options empty if no users with `driver` role found in `admin/class-ddwc-admin.php`
*   General code cleanup throughout multiple files in the plugin

## 2.5
*   Added delivery drivers to the Bulk Edit options in the Edit Orders screen in `admin/class-ddwc-admin.php`
*   Added a new order status "Order Returned" in `admin/ddwc-woocommerce-orders.php`
*   Added "Call Driver" button to Edit Order screen driver metabox in `admin/ddwc-metaboxes.php`
*   Bugfix for driver's picture image alt, removing hardcoded text and using driver name in `admin/ddwc-woocommerce-driver-ratings.php`
*   Updated the order status functions to include an "Order Returned" button when out for delivery in `admin/ddwc-functions.php`
*   Updated assigned orders table to show orders with "Order Returned" status in `admin/ddwc-dashboard-shortcode.php`
*   Updated removed login name from driver select in metabox in `admin/ddwc-metaboxes.php`
*   Updated CSS for driver status change buttons in `public/css/ddwc-public.css`
*   Updated text strings for localization in `languages/ddwc.pot`
*   General code cleanup throughout multiple files in the plugin

## 2.4.2
*   Bugfix removed formatted address filter from everywhere except driver dashboard in `admin/ddwc-woocommerce-settings.php`
*   Updated formatted address filter on driver dashboard in `admin/ddwc-dashboard-shortcode.php`
*   Updated driver name in Orders screen select boxes to only use display name (removed login name) in `admin/class-ddwc-admin.php`
*   Updated text strings for localization in `languages/ddwc.pot`

## 2.4.1
*   Added `ddwc_driver_dashboard_assigned_orders_order_details_url` filter in `admin/ddwc-dashboard-shortcode.php`
*   Bugfix re-added a missing variable for billing phone number in `admin/ddwc-dashboard-shortcode.php`
*   Bugfix re-added a missing variable for billing address in `admin/ddwc-dashboard-shortcode.php`
*   Updated text strings for localization in `languages/ddwc.pot`

## 2.4
*   Added `$order_status` to `ddwc_driver_dashboard_change_status` filter in `admin/ddwc-functions.php`
*   Added `ddwc_driver_dashboard_store_address` filter in `admin/ddwc-dashboard-shortcode.php`
*   Added filter to remove customer name from formatted addresses in `admin/ddwc-woocommerce-settings.php`
*   Added 6 action hooks to the driver dashboard in `admin/ddwc-dashboard-shortcode.php`
*   Bugfix removed `exit` lines from status change helper functions in `admin/ddwc-functions.php`
*   Bugfix updated store state/country based on if the raw country code has `:` or not in `admin/ddwc-dashboard-shortcode.php`
*   Updated `$delivery_address` to use WC formatted addresses in `admin/ddwc-dashboard-shortcode.php`
*   Updated `the_title` filter to no longer pass `$id` in `admin/ddwc-woocommerce-account-tab.php`
*   Updated `ddwc-dashboard` table font-size in `public/css/ddwc-public.css`
*   Updated driver availability to use the `ddwc_driver_dashboard_top` action hook in `admin/ddwc-dashboard-shortcode.php`
*   Updated text strings for localization in `languages/ddwc.pot`
*   General code cleanup throughout multiple files

## 2.3
*   Added `$store_address` variable to `ddwc_delivery_address_google_map` filter in `admin/ddwc-dashboard-shortcode.php`
*   Added `ddwc_assigned_orders_title_before` action hook in `admin/ddwc-dashboard-shortcode.php`
*   Added driver availability option to driver dashboard in multiple files throughout the plugin ([commit](https://github.com/deviodigital/delivery-drivers-for-woocommerce/commit/b53400d5d4303ac0f36ef87567885bdd9bd6de19))
*   Added Transportation `Type`, `Model` and `Color` fields to driver details in `admin/ddwc-woocommerce-driver-ratings.php`
*   Added styles to driver details table on customer order page in `public/css/ddwc-public.css`
*   Updated variable names with underscores in `admin/class-ddwc-admin.php`
*   Updated text strings for localization in `languages/ddwc.pot`

## 2.2
*   Added `ddwc_my_account_menu_item_driver_dashboard` filter in `admin/ddwc-woocommerce-account-tab.php`
*   Added `ddwc_assigned_orders_empty_before` action hook in `admin/ddwc-dashboard-shortcode.php`
*   Added `ddwc_dashboard_login_form` filter in `admin/ddwc-dashboard-shortcode.php`
*   Added `ddwc_my_account_check_user_role_array` filter in `admin/ddwc-woocommerce-account-tab.php`
*   Added `ddwc_check_user_roles` helper function in `admin/ddwc-functions.php`
*   Added `Go Pro` link to DDWC Settings page in `admin/ddwc-woocommerce-settings.php`
*   Bugfix login redirect issues on checkout page in `admin/ddwc-woocommerce-settings.php`
*   Updated user role check to use the new `ddwc_check_user_roles` function in `admin/ddwc-woocommerce-account-tab.php`
*   Updated `.pot` file with new translation strings in `languages/ddwc.pot`
*   General code cleanup throughout multiple files

## 2.1
*   Added `Delivery Driver` column on WooCommerce `Edit Orders` screen in `admin/class-ddwc-admin.php`
*   Added `Delivery Driver` column jQuery codes in `admin/js/ddwc-admin.js`
*   Added `ddwc_order_number` filter in `admin/ddwc-dashboard-shortcode.php`
*   Added `ddwc_delivery_address_directions_text` filter in `admin/ddwc-dashboard-shortcode.php`
*   Added `ddwc_google_maps_origin_address` filter in `admin/ddwc-dashboard-shortcode.php`
*   Added 3 action hooks in the completed orders table in `admin/ddwc-dashboard-shortcode.php`
*   Updated priority of the ddwc_endpoint_content action in `admin/ddwc-woocommerce-account-tab.php`
*   Updated filters to pass the order ID number in `admin/ddwc-dashboard-shortcode.php`
*   Updated check if payment gateway is not `false` in `admin/ddwc-dashboard-shortcode.php`
*   Updated Google Maps to use Directions API in `admin/ddwc-dashboard-shortcode.php`
*   Updated content display for the driver's order details page in `admin/ddwc-dashboard-shortcode.php`
*   Updated `.pot` file with new translation strings in `languages/ddwc.pot`
*   General code cleanup throughout multiple files

## 2.0
*   Added 5 new filters to the driver dashboard shortcode in `admin/ddwc-woocommerce-shortcode.php`
*   Added functions file with two helper functions in `admin/ddwc-functions.php`
*   Updated function name prefix for user login redirect in `admin/ddwc-woocommerce-settings.php`
*   Updated `.pot` file with new translation strings in `languages/ddwc.pot`
*   General code cleanup throughout multiple files

## 1.9.1
*   Bugfix for shipping address check that gets used with Google Maps in `admin/ddwc-dashboard-shortcode.php`
*   Updated delivery address display to display billing address if no shipping address is active in `admin/ddwc-dashboard-shortcode.php`

## 1.9
*   Added 9 new filters for the driver dashboard in `admin/ddwc-dashboard-shortcode.php`
*   Updated default address display to use WooCommerce function in `admin/ddwc-dashboard-shortcode.php`
*   Updated address used with Google Maps to include country in `admin/ddwc-dashboard-shortcode.php`
*   General code cleanup throughout multiple files

## 1.8.1
*   Added conditional check for WooCommerce view-order endpoint when loading javascript files on front-end of website in `public/class-ddwc-public.php`
*   Updated script loading to remove unnecessary admin.js file in `admin/class-ddwc-admin.php`
*   Updated text strings for localization in `admin/ddwc-dashboard-shortcode.php`
*   Updated `.pot` file with new translation strings in `languages/ddwc.pot`
*   General code cleanup throuhgout multiple files

## 1.8
*   Added display driver phone number setting in `admin/ddwc-woocommerce-settings.php`
*   Added driver phone number to customer order details in `admin/ddwc-woocommerce-driver-ratings.php`
*   Added (optional) text to out for delivery messsage text in `admin/ddwc-dashboard-shortcode.php`
*   Updated delivery fee to use the proper decimal placement in `admin/ddwc-dashboard-shortcode.php`
*   Updated CSS to include styles for SMS Updates title text on checkout in `public/css/ddwc-public.css`
*   Updated `.pot` file with new translation strings in `languages/ddwc.pot`
*   General code cleanup throuhgout multiple files

## 1.7
*   Added ability for driver to add a note during the "out for delivery" status change in `admin/ddwc-dashboard-shortcode.php`
*   Added Company name to customer address display in `admin/ddwc-dashboard-shortcode.php`
*   Added CSS for driver's out for delivery message in `public/css/ddwc-public.css`
*   Updated `.pot` file with new translation strings in `languages/ddwc.pot`
*   General code cleanup throughout multiple files

## 1.6
*   Added Delivery Driver details and star ratings on order details page in `includes/class-ddwc.php`
*   Added Delivery Driver details and star ratings on order details page in `admin/ddwc-woocommerce-driver-ratings.php`
*   Added Delivery Driver star ratings CSS in `admin/css/ddwc-public.css`
*   Added Driver rating to Delivery Drivers metabox in `admin/ddwc-metaboxes.php`
*   Added Delivery Driver star ratings JavaScript in `public/class-ddwc-public.php`
*   Added Delivery Driver star ratings JavaScript in `public/js/ddwc-public.php`
*   Added Delivery Driver star ratings CSS in `public/css/ddwc-public.css`
*   Added Driver Ratings option to the DDWC WooCommerce Settings page in `admin/ddwc-woocommerce-settings.php`
*   Added 2 new action hooks in `admin/ddwc-dashboard-shortcode.php`
*   Added new filter for driver login redirect in `admin/ddwc-woocommerce-settings.php`
*   Bugfix removed 'no completed orders' text in `admin/ddwc-dashboard-shortcode.php`
*   WordPress Coding Standards updates in `admin/ddwc-dashboard-shortcode.php`
*   Updated text strings for localization in `admin/ddwc-woocommerce-orders.php`
*   Updated `.pot` file with new translation strings in `languages/ddwc.pot`
*   General code cleanup throughout multiple files

## 1.5
*   Added text output if there are no completed orders in `admin/ddwc-dashboard-shortcode.php`
*   Added `ddwc_assigned_orders_empty` filter in `admin/ddwc-dashboard-shortcode.php`
*   Added `ddwc_assigned_orders_empty_after` action hook in empty orders output in `admin/ddwc-dashboard-shortcode.php`
*   General code cleanup throughout multiple files

## 1.4
*   Added 2 new action hooks for the Assigned Orders table in `admin/ddwc-dashboard-shortcode.php`
*   Added Completed Orders table to Driver Dashboard in `admin/ddwc-dashboard-shortcode.php`
*   Added 2 new action hooks for the Completed Orders table in `admin/ddwc-dashboard-shortcode.php`
*   Updated My Account tab text to Driver Dashboard in `admin/ddwc-woocommerce-account-tab.php`
*   Updated billing name in the order details view with table codes in `admin/ddwc-dashboard-shortcode.php`
*   Updated Assigned Orders table display options and details in `admin/ddwc-dashboard-shortcode.php`
*   Updated `.pot` file with new translation strings in `languages/ddwc.pot`

## 1.3
*   Added payment gateway info to order details in `admin/ddwc-dashboard-shortcode.php`
*   Added year to the order date displayed with order details in `admin/ddwc-dashboard-shortcode.php`
*   Bugfix shipping/billing address display in order details in `admin/ddwc-dashboard-shortcode.php`
*   Updated Order Details title bottom margin in `public/css/ddwc-public.css`
*   Updated CSS to include new class name for delivery charge in `public/css/ddwc-public.css`
*   Updated order details display style and content in `admin/ddwc-dashboard-shortcode.php`
*   Updated `.pot` file with new translation strings in `languages/ddwc.pot`
*   Various code cleanup and doc updates throughout multiple files

## 1.2
*   Bug fix for driver-dashboard query vars in `admin/ddwc-woocommerce-account-tab.php`
*   Updated `the_title` to say **Driver Dashboard** for driver-dashboard page in `admin/ddwc-woocommerce-account-tab.php`
*   Updated code to display customer name in driver dadhboard in `admin/ddwc-dashboard-shortcode.php`
*   Updated `.pot` file with new translation strings in `languages/ddwc.pot`
*   Various code updates and general code cleanup throughout multiple files

## 1.1
*   Added checkbox for drivers to hide completed orders from assigned orders table in `admin/ddwc-dashboard-shortcode.php`
*   Added checkbox for drivers to hide completed orders from assigned orders table in `public/js/ddwc-public.js`
*   Added checkbox for drivers to hide completed orders from assigned orders table in `public/css/ddwc-public.css`
*   Added redirect on login for delivery drivers to the Driver Dashboard in `admin/ddwc-woocommerce-settings.php`
*   Added `ddwc_driver_dashboard_order_table_tbody_top` and `ddwc_driver_dashboard_order_table_tbody_bottom` action hooks in order details table in `admin/ddwc-dashboard-shortcode.php`
*   Hide "Go Pro" link if Pro plugin is active in `delivery-drivers-for-woocommerce.php`
*   Updated individual order details table data in `admin/ddwc-dashboard-shortcode.php`
*   Updated `.pot` file with new translation strings in `languages/ddwc.pot`
*   Various code updates and general code cleanup throughout multiple files

## 1.0.4
*   General code clean up in `admin/ddwc-woocommerce-orders.php` and `admin/ddwc-woocommerce-settings.php`
*   Updated text with new translation strings in `admin/ddwc-dashboard-shortcode.php`
*   Updated text with new translation strings in `admin/ddwc-woocommerce-orders.php`
*   Updated text with new translation strings in `admin/ddwc-woocommerce-settings.php`
*   Updated `.pot` file with new translation strings in `languages/ddwc.pot`

## 1.0.3
*   Added `.pot` file for localization in `languages/ddwc.pot`

## 1.0.2
*   Added conditional check for delivery address display in `admin/ddwc-dashboard-shortcode.php`

## 1.0.1
*   Added two new action hooks in `admin/ddwc-dashboard-shortcode.php`

## 1.0.0
*   Initial release

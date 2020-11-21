jQuery(document).ready(function ($) {
	jQuery("select#ddwc_driver_id").change(function(e) {
		var itemid =  $(this).attr("name");
		var metakey =  $(this).attr("id");
		var metavalue = this.value;
		// Update driver ID metadata.
		$.post(WPaAjax.ajaxurl,{
			action : "ddwc_delivery_driver_settings",
			item_id : itemid,
			metakey : metakey,
			metavalue : metavalue
		});
	});
	// Remove the link click wrapper on WooCommerce Edit Orders screen.
	$("td.delivery_driver.column-delivery_driver a").click(function(){ return false });
});

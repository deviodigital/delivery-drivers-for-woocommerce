jQuery(document).ready(function ($) {
	jQuery("select#ddwc_driver_id").change(function(e) {
		var itemid =  $(this).attr("name");
		var metakey =  $(this).attr("id");
		var metavalue = this.value;
		$.post(WPaAjax.ajaxurl,{
			action : "ddwc_delivery_driver_settings",
			item_id : itemid,
			metakey : metakey,
			metavalue : metavalue
//		},
//		function(data, status) {
//			console.log("Data: " + data + "\nStatus: " + status);
		});
	});
});

// Remove the link click wrapper on WooCommerce Edit Orders screen.
jQuery(document).ready(function ($) {
	$("td.delivery_driver.column-delivery_driver a").click(function(){ return false });
});

var ccm_coreCommerceUseAdvancedCart = false;

ccm_coreCommerceLaunchCart = function(obj, url) {
	if (ccm_coreCommerceUseAdvancedCart) {
		jQuery.fn.dialog.open({
			width: 400,
			height: 300,
			modal: false,
			href: url,
			title: 'Shopping Cart'
		});
	} else {
		window.location.href = obj.href;
	}
}

ccm_coreCommerceRegisterAddToCart = function(id, formaction) {
	if (ccm_coreCommerceUseAdvancedCart) {
		$("#" + id).append('<input type="hidden" name="method" value="JSON" />');
		$("#" + id).ajaxForm({
			beforeSubmit: function(formData, jqForm, options) {
				$("#" + id + " input[type=submit]").attr('disabled', true);
				$("#" + id + " img.ccm-core-commerce-add-to-cart-loader").show();
			},	
			success: function(resp) {
				$("#" + id + " input[type=submit]").attr('disabled', false);
				$("#" + id + " img.ccm-core-commerce-add-to-cart-loader").hide();
				resp = eval('(' + resp + ')');
				ccm_parseJSON(resp, function() {
					jQuery.fn.dialog.open({
						width: 400,
						height: 300,
						modal: false,
						href: formaction, 
						title: 'Shopping Cart'
					});
				});
			}
		});
		return false;
	} else {
		return true;
	}
}

ccm_coreCommerceRegisterCallout = function(id) {
	$("#" + id + " div.ccm-core-commerce-add-to-cart-image img").hover(function(e) {
		var t = $(this).position().top;
		var l = $(this).position().left + $(this).width() + 10;
		var cw = $("#" + id + " div.ccm-core-commerce-add-to-cart-callout").outerWidth();
		var ch = $("#" + id + " div.ccm-core-commerce-add-to-cart-callout").outerHeight();

		if (l + cw > $(window).width() + $(window).scrollLeft()) {
			l = l - $(this).outerWidth() - cw - 20;
		}
		if (t + ch > $(window).height() + $(window).scrollTop()) {
			t = t - ch + $(this).outerHeight();
		}

		var obj = $("#" + id + " div.ccm-core-commerce-add-to-cart-callout");
		if (obj.length > 0) {
			obj.css('top', t);
			obj.css('left', l);
			obj.corner('keep');
			/*var innerObj = obj.find('div.ccm-core-commerce-add-to-cart-callout-inner');
			if(innerObj.length > 0) {
				innerObj.corner('12px');
			}*/
			
			obj.fadeIn(150);
		}
	}, function() {
		var obj = $("#" + id + " div.ccm-core-commerce-add-to-cart-callout");
		obj.fadeOut(150,function() {
/*			var innerObj = obj.find('div.ccm-core-commerce-add-to-cart-callout-inner');
			if(innerObj.length > 0) {
				innerObj.uncorner();
			}
			obj.uncorner();*/
		});
	});
}

ccm_coreCommerceUpdateCart = function(url) {
	if (ccm_coreCommerceUseAdvancedCart) {
		ccm_coreCommerceDeactivateCartControls();
		$("form[name=ccm-core-commerce-cart-form-dialog]").ajaxSubmit({
			success: function(jresp) {
				ccm_coreCommerceActivateCartControls();
				jresp = eval('(' + jresp + ')');
				ccm_parseJSON(jresp, function() {
					$.get(url, function(resp) {
						jQuery.fn.dialog.replaceTop(resp);
					});
				});
			}
		});
		return false;
	} else {
		return true;
	}
}

ccm_coreCommerceRemoveCartItem = function(obj, url) {
	if (ccm_coreCommerceUseAdvancedCart) {	
		ccm_coreCommerceDeactivateCartControls();
		$.get($(obj).attr('href'), {'method': 'JSON'}, function(jresp) {
			jresp = eval('(' + jresp + ')');
			ccm_coreCommerceActivateCartControls();
			ccm_parseJSON(jresp, function() {
				$.get(url, function(resp) {
					jQuery.fn.dialog.replaceTop(resp);
				});
			});
		});
	} else {
		return true;
	}
}

ccm_coreCommerceGoToCheckout = function(url) {
	if (ccm_coreCommerceUseAdvancedCart) {	
		ccm_coreCommerceDeactivateCartControls();
	}
	// if we're leaving the update form, submit the values first
	if($("form[name=ccm-core-commerce-cart-form-dialog]").length) {
		$("form[name=ccm-core-commerce-cart-form-dialog]").ajaxSubmit({async:false});
	}
	window.location.href = url;
}

ccm_coreCommerceDeactivateCartControls = function() {
	if (ccm_coreCommerceUseAdvancedCart) {	
		$("#ccm-core-commerce-cart-update-loader").show();
		$(".ccm-core-commerce-cart-buttons input").attr('disabled', true);
	}
}

ccm_coreCommerceActivateCartControls = function() {
	if (ccm_coreCommerceUseAdvancedCart) {	
		$("#ccm-core-commerce-cart-update-loader").hide();
		$(".ccm-core-commerce-cart-buttons input").attr('disabled', false);
	}
}
$(function() {
	$('input[name=useBillingAddressForShipping], label[for=useBillingAddressForShipping]').click(function() {
		window.location.href = $('input[name=useBillingAddressAction]').val() + ($('input[name=useBillingAddressForShipping]').attr('checked') ? 1 : 0);
	});
	if (jQuery.browser.safari || jQuery.browser.opera || jQuery.browser.mozilla) {
		ccm_coreCommerceUseAdvancedCart = true;
	}
});

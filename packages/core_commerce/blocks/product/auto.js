ccm_coreCommerceProductBlockSelectThumbnailOption = function(obj) {
	if (!obj) {
		obj = $("input[name=displayImage]");
	}
	
	if ($(obj).attr('checked')) {
		$(obj).parent().find('div.ccm-core-commerce-product-block-image-fields').show();
	} else {
		$(obj).parent().find('div.ccm-core-commerce-product-block-image-fields').hide();
	}
}

ccm_coreCommerceProductBlockSelectAddToCart = function(obj) {
	if (!obj) {
		obj = $("input[name=displayAddToCart]");
	}
	
	if ($(obj).attr('checked')) { 
		$("input[name=displayQuantity]").attr('disabled', false);
		$("input[name=addToCartText]").attr('disabled', false);
		$("input[name=addToCartText]").css('color', '#333');
	} else {
		$("input[name=displayQuantity]").attr('checked', false);
		$("input[name=displayQuantity]").attr('disabled', true);
		$("input[name=addToCartText]").attr('disabled', true);
		$("input[name=addToCartText]").css('color', '#aaa');
	}
}


$(function() {
	$("input[name=displayImage]").unbind();
	$("input[name=displayImage]").click(function() {
		ccm_coreCommerceProductBlockSelectThumbnailOption(this);
	});
	
	$('#useOverlaysC').unbind();
	$('#useOverlaysC').click(function() {
		ccm_coreCommerceProductBlockSelectThumbnailOption(this);
	});
	
	$('#useOverlaysL').unbind();
	$('#useOverlaysL').click(function() {
		ccm_coreCommerceProductBlockSelectThumbnailOption(this);
	});
	
	
	$("input[name=displayAddToCart]").unbind();
	$("input[name=displayAddToCart]").click(function() {
		ccm_coreCommerceProductBlockSelectAddToCart(this);
	});

	$("input[name=inheritProductIDFromCurrentPage]").click(function() {
		if ($(this).val() == '1') {
			$('div[class=ccm-core-commerce-product-block-choose-product]').hide();
		} else {
			$('div[class=ccm-core-commerce-product-block-choose-product]').show();
		}
	});
	ccm_coreCommerceProductBlockSelectAddToCart();
	ccm_setupGridStriping('ccm-core-commerce-product-attribute-grid');
	ccm_coreCommerceProductBlockSelectThumbnailOption();
	ccm_coreCommerceProductBlockSelectThumbnailOption($('#useOverlaysL'));
	ccm_coreCommerceProductBlockSelectThumbnailOption($('#useOverlaysC'));
});
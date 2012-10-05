function doSeparator() {
	if (($('#showCartLink').attr('checked')||$('#showItemQuantity').attr('checked')) && $('#showCheckoutLink').attr('checked')) {
		$('.cc-cart-links-divider').show();
	} else {
		$('.cc-cart-links-divider').hide();
	}
}
$(function() {
	$('#showCartLink').change(function(){$('.cc-cart-link').toggle();doSeparator()});
	$('#cartLinkText').change(function(){$('.cc-cart-text').text($('#cartLinkText').val())});
	$('#showItemQuantity').change(function(){$('.cc-item-quantity').toggle();doSeparator()});
	$('#showCheckoutLink').change(function(){$('.cc-checkout-link').toggle();doSeparator()});
	$('#checkoutLinkText').change(function(){$('.cc-checkout-text').text($('#checkoutLinkText').val())});
});

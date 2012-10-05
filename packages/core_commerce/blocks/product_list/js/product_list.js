$(function() {
	$('.product-list-core-commerce-product-search-add-option').click(
		function () {
			$(this).closest('form').find('.product-list-search-field-base').show();
		}
	);
	$('.product-list-search-field-base select').change(
		function () {
			var val = $(this).val();
			$(this).closest('form').find('#product-list-core-commerce-product-search-field-set'+val).show();
			$(this).closest('form').find('#product-list-core-commerce-product-search-field-set'+val+' .product-list-core-commerce-product-selected-field').val(val);
			$(this).val('');
			$(this).closest('form').find('.product-list-search-field-base').hide();
		}
	);
	$('.product-list-search-remove-option').click(
		function () {
			$(this).closest('.product-list-search-field').find('.product-list-core-commerce-product-selected-field').val('');
			$(this).closest('.product-list-search-field').hide();
		}
	);
	$('.product-list-sort-select').change(
		function () {
			document.location = $(this).val();
		}
	);
	$('.product-list-add-to-cart-form').each(
		function () {
			ccm_coreCommerceRegisterAddToCart(this.id);
			//e.preventDefault();
		}
	);
});
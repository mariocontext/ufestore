<?php  
	defined('C5_EXECUTE') or die(_("Access Denied."));
	class CoreCommerceDiscountHelper {
	
		public function askUserForCouponCode() {
			// tests the system to see if there are any enabled discount types that user coupon codes
			Loader::model('discount/model', 'core_commerce');
			$filters = array(
				'discountIsEnabled' => 1,
				'(discountStart is null or discountStart < NOW())',
				'(discountEnd is null or discountEnd > NOW())',
				'(discountCode is not null and discountCode <> "")'
			);
			$promos = CoreCommerceDiscount::getTotal($filters);
			return $promos > 0;
		}
	
	}
?>
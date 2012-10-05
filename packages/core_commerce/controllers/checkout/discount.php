<?php   

Loader::controller('/checkout');

class CheckoutDiscountController extends CheckoutController {
	
	public $helpers = array('form');
	
	public function submit() {
		parent::submit();
		if ($this->post('discount_code') != '') {
			$discount = CoreCommerceDiscount::getByCode($this->post('discount_code'));
			if (!is_object($discount)) {
				$this->error->add('Invalid coupon code.');
			} else {
				// we validate the current code
				$r = $discount->validate();
				if ($r instanceof ValidationErrorHelper) {
					$this->error->add($r);	
				} else {
					$cart = CoreCommerceCart::get();
					Loader::model('order/current', 'core_commerce');
					$o = CoreCommerceCurrentOrder::get();
					$o->setAttribute('discount_code', $this->post('discount_code'));
				}
			}
		} else {
			$o = CoreCommerceCurrentOrder::get();
			$o->setAttribute('discount_code', '');
		}
		
		
		if (!$this->error->has()) {
			$this->redirect($this->getNextCheckoutStep()->getRedirectURL());
		}
	}
	
	public function view() {
		$o = CoreCommerceCurrentOrder::get();
		$this->set('discount_code', $o->getAttribute('discount_code'));
	}
	
}
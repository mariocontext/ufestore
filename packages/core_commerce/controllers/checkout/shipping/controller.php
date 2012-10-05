<?php   

Loader::controller('/checkout');

class CheckoutShippingController extends CheckoutController {
	
	public function view() {
		$this->redirect("/checkout/shipping/address");
	}
	
}
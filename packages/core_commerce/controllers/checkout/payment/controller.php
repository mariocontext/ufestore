<?php   

Loader::controller('/checkout');

class CheckoutPaymentController extends CheckoutController {
	
	public function view() {
		$this->redirect("/checkout/payment/methods");
	}
	
}
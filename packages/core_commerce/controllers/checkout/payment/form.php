<?php   

Loader::controller('/checkout');
Loader::model('attribute/categories/core_commerce_order','core_commerce');
class CheckoutPaymentFormController extends CheckoutController {
	
	public function view() {
		$method = $this->get('order')->getOrderPaymentMethod();
		if (!$method) {
			$this->redirect('/checkout');
			return;
		}

		$this->set('method', $method);
		$method->getController()->form();
	}

	
	public function submit() {
		$this->redirect($this->getNextCheckoutStep()->getRedirectURL());
	}
	
}

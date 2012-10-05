<?php   

Loader::controller('/checkout');
Loader::model('attribute/categories/core_commerce_order','core_commerce');
class CheckoutPaymentMethodController extends CheckoutController {
	
	public function on_start() {
		parent::on_start();
		$methods = $this->get('order')->getAvailablePaymentMethods();
		$this->set('methods', $methods);
	
	}
	
	public function view() {
		$methods = $this->get('methods');
		if (count($methods) == 1) {
			// select one and go
			$this->get('order')->setPaymentMethod($methods[0]);
			$sh = Loader::helper('checkout/step', 'core_commerce');
			if ($_GET['previous'] == 1) { 
				$this->redirect($sh->getPreviousCheckoutStepURL());
			} else { 
				$this->redirect($sh->getNextCheckoutStepURL());
			}
		}
	}
	
	public function submit() {
		parent::submit();
		if (!$this->post('paymentMethodID')) {
			$this->error->add(t('You must specify a payment method.'));
		}
		
		if (!$this->error->has()) {
			Loader::model('payment/method', 'core_commerce');
			$method = CoreCommercePaymentMethod::getByID($this->post('paymentMethodID'));
			if ($method) {
				$this->get('order')->setPaymentMethod($method);
				$this->redirect($this->getNextCheckoutStep()->getRedirectURL());
			}
		}
		
	}
	
}
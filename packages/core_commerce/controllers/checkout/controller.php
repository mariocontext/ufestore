<?php   
class CheckoutController extends Controller {
	
	protected $cPath;
	protected $error;
	public $testStep = true;
	
	public function refreshShippingCost() {
		$order = CoreCommerceCart::get();
		$method = $order->getOrderShippingMethod();
		if (is_object($method)) {
			if ($method->getPrice() != $order->getAttribute('total_shipping')) {
				$v = $order->getAttribute('total_shipping')->getLineItemTotal();
				if (round($v, 2) != round($method->getPrice(), 2)) {
					// something in the order has changed. We update
					$order->setShippingMethod($method);
				}
			}
		}
	}
	
	public function view() { }
	
	/** 
	 * Note - all checkout step related functionality moved into checkout step helper
	 * This is just here for backward compatibility
	 */

	protected function setupSteps() {
		$this->checkoutHelper->setupSteps();
	}
	
	public function getSteps() {
		return $this->checkoutStepHelper->getSteps();
	}
	
	public function setSteps($steps, $overwrite = true) {
		$this->checkoutHelper->setSteps($steps, $overwrite);
	}
	
	
	public function setPath($path) {
		// nothing
	}
	
	public function testCurrentStep() {
		$this->checkoutStepHelper->testCurrentStep();
	}

	public function getCheckoutStep() {
		return $this->checkoutStepHelper->getCheckoutStep();
	}
	
	public function getPreviousCheckoutStep() {
		return $this->checkoutStepHelper->getPreviousCheckoutStep();
	}

	public function getNextCheckoutStep() {
		return $this->checkoutStepHelper->getNextCheckoutStep();
	}

	public function getCheckoutPreviousStepButton() {
		return $this->checkoutStepHelper->getCheckoutPreviousStepButton();
	}


	public function getCheckoutNextStepButton() {
		return $this->checkoutStepHelper->getCheckoutNextStepButton();
	}
	
	protected function getStepIndexByPath($path) {
		return $this->checkoutStepHelper->getStepIndexByPath($path);
	}

	public function disableStep($path) {
		return $this->checkoutStepHelper->disableStep($path);
	}

	public function enableStep($path) {
		return $this->checkoutStepHelper->enableStep($path);
	}
	
	
	public function removeStep($path) {
		return $this->checkoutStepHelper->removeStep($path);
	}
	
	public function replaceStep($checkoutStep, $path) {
		return $this->checkoutStepHelper->replaceStep($checkoutStep, $path);
	}
	
	public function addStepBefore($checkoutStep, $newPath, $existingPath) {
		return $this->checkoutStepHelper->addStepBefore($checkoutStep, $newPath, $existingPath);
	}
	
	public function addStepAfter($checkoutStep, $newPath, $existingPath = NULL ) {
		return $this->checkoutStepHelper->addStepAfter($checkoutStep, $newPath, $existingPath);
	}
	
	public function submit() {

	}

	public function on_before_render() {
		$this->set('error', $this->error);
	}
	
	// searches the system for all enabled discounts and hooks into their appropriate events
	
	public function on_start() {
		Loader::model('cart', 'core_commerce');
		$order = CoreCommerceCart::get();
		if ($order->getTotalProducts() == 0 && $this->testStep) {
			$this->redirect('/cart');
		}
	
		$this->set('order', $order);
		
		Loader::model('discount/model', 'core_commerce');
		Loader::model('sales/tax/rate', 'core_commerce');
		CoreCommerceDiscount::setupEnabledDiscounts($order);
		CoreCommerceSalesTaxRate::setupEnabledRates($order);
		$u = new User();
		Loader::model('attribute/categories/user');
		$this->addHeaderItem(Loader::helper('html')->css('ccm.core.commerce.cart.css', 'core_commerce'));
		$this->addHeaderItem(Loader::helper('html')->css('ccm.core.commerce.checkout.css', 'core_commerce'));
		$this->addHeaderItem(Loader::helper('html')->javascript('ccm.core.commerce.cart.js', 'core_commerce'));
		$this->error = Loader::helper('validation/error');
		
		// before event trigger so they can be modified
		$this->checkoutStepHelper = Loader::helper('checkout/step', 'core_commerce');
		$this->refreshShippingCost();
		
		$ret = Events::fire('core_commerce_on_checkout_start', $this);
		if($this->testStep) {
			$this->testCurrentStep();
		}
		if (is_object($this->getCheckoutStep())) {
			$this->set('action', $this->getCheckoutStep()->getSubmitURL());
		}
		
	}	
}

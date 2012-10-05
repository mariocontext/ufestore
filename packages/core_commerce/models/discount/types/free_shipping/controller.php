<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::library('discount/controller', 'core_commerce');
class CoreCommerceFreeShippingDiscountTypeController extends CoreCommerceDiscountController {

	public function type_form() {
		$db = Loader::db();
		Loader::model('shipping/method', 'core_commerce');
		if (is_object($this->discount)) {
			$r = $db->GetRow('select minimumPurchase, shippingMethod from CoreCommerceDiscountTypeFreeShipping where discountID = ?', array($this->discount->getDiscountID()));
			$this->set('minimumPurchase', Loader::helper('number')->flexround($r['minimumPurchase']));
			$this->set('shippingMethod', $r['shippingMethod']);
		}
		$methods = CoreCommerceShippingMethod::getAll();
		$msel = array();
		foreach($methods as $m) { 
			$msel[$m->getID()] = $m->getName();
		}
		
		if(!is_array($msel) || count($msel) == 0) {
			$error = Loader::helper('validation/error');
			$error->add(t('You must enable at least one shipping method.'));
			$this->set('error',$error);	
		}
		
		$this->set('methods', $msel);
	}
	
	public function replaceShippingOptions($order, $methods, $discount) {
		$methodsTmp = array();
		$db = Loader::db();
		$r = $db->GetRow('select minimumPurchase, shippingMethod from CoreCommerceDiscountTypeFreeShipping where discountID = ?', array($discount->getDiscount()->getDiscountID()));
		if ($order->getBaseOrderTotal() > $r['minimumPurchase']) {
			foreach($methods as $m) {
				if ($m->getID() == $r['shippingMethod']) {
					$m->setPrice(0);
				}
				$methodsTmp[] = $m;
			}
			return $methodsTmp;
		}
	}
	
	protected function validateDiscountToOrder($methodID = false) {
		$o = CoreCommerceCurrentOrder::get();
		$d = $this->getDiscount();
		$db = Loader::db();
		$r = $db->GetRow('select minimumPurchase, shippingMethod from CoreCommerceDiscountTypeFreeShipping where discountID = ?', array($d->getDiscountID()));
		if ($o->getBaseOrderTotal() > $r['minimumPurchase']) {
			if ($methodID) {
				 // we also check whether the passed method == shippingMethod
				 return $methodID == $r['shippingMethod'];
			} else {
				return true;
			}
		}
		return false;
	}
	
	// run by checkout for all discount types. This discount type determines which method to hook into
	public function applyDiscount() {
		Events::extend('core_commerce_on_get_shipping_methods', get_class($this), 'replaceShippingOptions', __FILE__, array($this));
		$o = CoreCommerceCurrentOrder::get();
		if ($this->validateDiscountToOrder($o->getOrderShippingMethodID())) {
			$method = $o->getOrderShippingMethod();
			if (is_object($method)) {
				$o->setAttribute('total_shipping', array('label' => $method->getName(), 'type' => '+', 'value' => $method->getPrice()));
			}
		}
	}

	public function deleteDiscount() {
		// remove from order
		$o = CoreCommerceCurrentOrder::get();
		if ($this->validateDiscountToOrder($o->getOrderShippingMethodID())) {
			// check to see if the shipping is free before we clear it out
			$a = $o->getAttribute('total_shipping');
			if (is_object($a)) {
				if ($a->getLineItemTotal() == 0) {
					$o->clearShippingMethod();
				}
			}
		}
	}

	public function validateDiscount() {
		$e = parent::validateDiscount();
		
		if ($this->post('minimumPurchase') === '') {
			$e->add(t('You must specify a minimum subtotal, even if it is zero.'));
		}
		return $e;
	}
	
	public function validate() {
		$db = Loader::db();
		$r = $db->GetOne('select minimumPurchase from CoreCommerceDiscountTypeFreeShipping where discountID = ?', array($this->discount->getDiscountID()));
		$ec = CoreCommerceCart::get();
		$total = $ec->getBaseOrderTotal();
		if ($total < $r) {
			$ve = Loader::helper('validation/error');
			Loader::library('price','core_commerce');
			$ve->add(t('In order to use this code, your cart must contain items totalling %s or more.', CoreCommercePrice::format($r)));
			return $ve;
		}		
	}
	
	public function save() {
		$db = Loader::db();
		$db->Replace('CoreCommerceDiscountTypeFreeShipping', array('minimumPurchase' => $this->post('minimumPurchase'), 'shippingMethod' => $this->post('shippingMethod'), 'discountID' => $this->discount->getDiscountID()), array('discountID'), true);
	}
	
	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from CoreCommerceDiscountTypeFreeShipping where discountID = ?', array($this->discount->getDiscountID()));
	}
	
}
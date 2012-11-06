<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::library('discount/controller', 'core_commerce');
class CoreCommerceBasicDiscountTypeController extends CoreCommerceDiscountController {

	public function type_form() {
		$db = Loader::db();
		if (is_object($this->discount)) {
			$r = $db->GetRow('select mode, amount from CoreCommerceDiscountTypeBasic where discountID = ?', array($this->discount->getDiscountID()));
			$this->set('mode', $r['mode']);
			$this->set('amount', Loader::helper('number')->flexround($r['amount']));
		}
	}
	
	// called when created
	public function validateDiscount() {
		$e = parent::validateDiscount();
		if ($this->post('amount') === '') {
			$e->add(t('You must specify a discount amount.'));
		}
		return $e;
	}
	
	public function deleteDiscount() {
		// remove from order
		$o = CoreCommerceCurrentOrder::get();
		$o->clearAttribute('discount_basic_adjustment');
	}
	
	public function applyDiscount() {
		Loader::model('cart', 'core_commerce');
		$o = CoreCommerceCurrentOrder::get();
		$db = Loader::db();
		$r = $db->GetRow('select mode, amount from CoreCommerceDiscountTypeBasic where discountID = ?', array($this->discount->getDiscountID()));
		switch($r['mode']) {
			case 'fixed':
				$amt = $r['amount'];
				break;				
			case 'percent':
				$amt = round(($r['amount'] / 100) * $o->getBaseOrderTotal(), 2);
				break;				
		}
		if ($amt > $o->getBaseOrderTotal()) {
			$amt = $o->getBaseOrderTotal();
		}
		$o->setAttribute('discount_basic_adjustment', array('label' => $this->discount->getDiscountName(), 'type' => '-', 'value' => $amt));
	}
	
	public function save() {
		$db = Loader::db();
		$db->Replace('CoreCommerceDiscountTypeBasic', array('amount' => $this->post('amount'), 'mode' => $this->post('mode'), 'discountID' => $this->discount->getDiscountID()), array('discountID'), true);
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from CoreCommerceDiscountTypeBasic where discountID = ?', array($this->discount->getDiscountID()));
	}
	
	
}
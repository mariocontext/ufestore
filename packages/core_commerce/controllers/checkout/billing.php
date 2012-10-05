<?php   

Loader::controller('/checkout');
Loader::model('attribute/categories/core_commerce_order','core_commerce');
class CheckoutBillingController extends CheckoutController {
	
	public function on_start() {
		parent::on_start();
		$akHandles = array('billing_first_name', 'billing_last_name', 'billing_address', 'billing_phone');
		$this->set('akHandles', $akHandles);
	}
	
	public function view() {
		$u = new User();
		$o = CoreCommerceCurrentOrder::get();
		
		if ($u->isRegistered()) {
			$ui = UserInfo::getByID($u->getUserID());
			$attr = array('billing_first_name', 'billing_last_name', 'billing_address', 'billing_phone');
			if (!$o->getOrderEmail()) {
				$o->setOrderEmail($ui->getUserEmail());
			}
			foreach($attr as $atHandle) {
				$uak = UserAttributeKey::getByHandle($atHandle);
				if (is_object($uak)) {
					$uav = $ui->getAttributeValueObject($uak);
					if (is_object($uav)) {
						$oav = $o->getAttributeValueObject(CoreCommerceOrderAttributeKey::getByHandle($atHandle));
						if (!is_object($oav)) {
							$o->setAttribute($atHandle, $uav->getValue());
						}
					}
				}
			}
		}
	}
	
	public function submit() {
		parent::submit();
		$t = Loader::helper('validation/strings');
		if (!$t->email($this->post('oEmail'))) {
			$this->error->add(t('You must specify a valid email address.'));
		}
		
		// pull list of attributes in set that require validation
		$set = AttributeSet::getByHandle('core_commerce_order_billing');
		$validAttributes = array();
		if (is_object($set)) { 
			$keys = $set->getAttributeKeys();
			foreach($keys as $eak) {
				if($eak->isOrderAttributeKeyRequired()) {
					$validAttributes[] = $eak;
				}	
			}
		}
		
		foreach($validAttributes as $eak) {
			if (!$eak->validateAttributeForm()) {
				$this->error->add(t('The field "%s" is required', $eak->getAttributeKeyName()));
			}
		}
		
		if (!$this->error->has()) {
			$o = CoreCommerceCurrentOrder::get();
			$attributes = AttributeSet::getByHandle('core_commerce_order_billing')->getAttributeKeys();
			foreach($attributes as $eak) {
				$eak->saveAttributeForm($o);				
			}
			$o->setOrderEmail($this->post('oEmail'));
			$this->redirect($this->getNextCheckoutStep()->getRedirectURL());
		}
	}
	
}
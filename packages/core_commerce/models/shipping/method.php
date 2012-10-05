<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
class CoreCommerceShippingMethod extends Object {
	
	public function __construct(CoreCommerceShippingType $est, $identifier = false) {
		$this->shippingMethodID = $est->getShippingTypeID();
		$this->shippingType = $est;
		if ($identifier) {
			$this->shippingMethodID .= '_' . $identifier;
		}
	}
	
	public function getAll() {
		Loader::model('shipping/type', 'core_commerce');
		$types = CoreCommerceShippingType::getEnabledList();
		$methods = array();
		foreach($types as $t) {
			$m = $t->getController()->getShippingMethods();
			foreach($m as $key => $_m) {
				$ecm = new CoreCommerceShippingMethod($t, $key);
				$ecm->setName($_m);
				$methods[] = $ecm;	
			}
		}
				
		return $methods;
	}
	
	public function getShippingType() {return $this->shippingType;}
	
	public function setPrice($price) {
		$this->shippingMethodPrice = $price;
	}
	public function setName($name) {
		$this->shippingMethodName = $name;
	}
	
	public function getID() {return $this->shippingMethodID;}
	public function getName() {return $this->shippingMethodName;}
	public function getPrice() {
		return $this->shippingMethodPrice;
	}
	public function getDisplayPrice() {
		Loader::library('price', 'core_commerce');
		return CoreCommercePrice::format($this->shippingMethodPrice);
	}
	
	public function getAvailableMethodByID($id) {
		$o = CoreCommerceCurrentOrder::get();
		$list = $o->getAvailableShippingMethods();
		foreach($list as $m) {
			if ($m->getID() == $id) {
				return $m;
			}
		}
	}
		
}
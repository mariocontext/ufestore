<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::library('shipping/controller', 'core_commerce');
class CoreCommerceFlatShippingTypeController extends CoreCommerceShippingController {

	protected $shippingMethods = array(
		'FLAT' => 'Basic Shipping'
	);

	public function type_form() {
		$pkg = Package::getByHandle('core_commerce');
		$SHIPPING_TYPE_FLAT_BASE = $pkg->config('SHIPPING_TYPE_FLAT_BASE');
		$SHIPPING_TYPE_FLAT_PER_ITEM = $pkg->config('SHIPPING_TYPE_FLAT_PER_ITEM');
		$this->set('SHIPPING_TYPE_FLAT_BASE', $SHIPPING_TYPE_FLAT_BASE);
		$this->set('SHIPPING_TYPE_FLAT_PER_ITEM', $SHIPPING_TYPE_FLAT_PER_ITEM);
	}
	
	public function validate() {
		$e = parent::validate();
		
		if ($this->post('SHIPPING_TYPE_FLAT_BASE') === '') {
			$e->add(t('You must specify a minimum base shipping price, even if it is zero.'));
		}
		if ($this->post('SHIPPING_TYPE_FLAT_BASE') === '') {
			$e->add(t('You must specify a minimum per item shipping price, even if it is zero.'));
		}		
		return $e;
	}
	
	public function save() {
		$pkg = Package::getByHandle('core_commerce');
		$pkg->saveConfig('SHIPPING_TYPE_FLAT_BASE', $this->post('SHIPPING_TYPE_FLAT_BASE'));
		$pkg->saveConfig('SHIPPING_TYPE_FLAT_PER_ITEM', $this->post('SHIPPING_TYPE_FLAT_PER_ITEM'));
	}
	
	public function getAvailableShippingMethods($currentOrder) {
		$pkg = Package::getByHandle('core_commerce');
		$shipping = $pkg->config('SHIPPING_TYPE_FLAT_BASE');
		$perItem = $pkg->config('SHIPPING_TYPE_FLAT_PER_ITEM');
		foreach($currentOrder->getProducts() as $pr) {
			if ($pr->productRequiresShipping()) {
				$thisPerItem = $perItem;
				if ($pr->getProductShippingModifier() != '') {
					$thisPerItem += $pr->getProductShippingModifier();
				}
				$shipping += ($thisPerItem * $pr->getQuantity());
			}
		}

		$ecm = new CoreCommerceShippingMethod($this->getShippingType(), 'FLAT');
		$ecm->setPrice($shipping);
		$ecm->setName(t('Basic Shipping'));
		return $ecm;
	}
	
}
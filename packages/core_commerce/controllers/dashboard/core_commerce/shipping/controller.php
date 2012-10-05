<?php   

Loader::model('shipping/type', 'core_commerce');
class DashboardCoreCommerceShippingController extends Controller {

	public function view() {		
		$this->set('types', CoreCommerceShippingType::getList());
	}
	
	public function edit_type($typeID) {
		$est = CoreCommerceShippingType::getByID($typeID);
		$this->set("type", $est);
	}
	
	public function on_start() {
		$this->set('ih', Loader::helper('concrete/interface'));
		$this->set('form', Loader::helper('form'));
	}
	
	public function add_shipping_type() {
		$pat = CoreCommercePendingShippingType::getByHandle($this->post('shippingTypeHandle'));
		if (is_object($pat)) {
			$pat->install();
		}
		$this->redirect('dashboard/core_commerce/shipping', 'shipping_type_added');
	}
	
	public function save() {
		$shippingTypeID = $this->post('shippingTypeID');
		$type = CoreCommerceShippingType::getByID($shippingTypeID);
		
		$cnt = $type->getController();
		$e = $cnt->validate();
		if ($e->has()) {
			$this->set('error', $e);
			$this->edit_type($shippingTypeID);
		} else {
			$type->update($this->post());
			$this->redirect('/dashboard/core_commerce/shipping/', 'shipping_type_updated');
		}		
	}
	
	public function shipping_type_updated() {
		$this->set('message', t('Shipping Type Saved'));
		$this->view();
	}

	public function shipping_type_added() {
		$this->set('message', t('Shipping Type Installed'));
		$this->view();
	}

}
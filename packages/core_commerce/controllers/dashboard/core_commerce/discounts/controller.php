<?php   

Loader::model('discount/type', 'core_commerce');
Loader::model('discount/model', 'core_commerce');
class DashboardCoreCommerceDiscountsController extends Controller {

	public $helpers = array('date');
	
	public function manage_discount_types() {		
	}
	
	public function on_start() {
		$this->set('ih', Loader::helper('concrete/interface'));
		$this->set('form', Loader::helper('form'));
		$types = CoreCommerceDiscountType::getList();
		$seltypes = array();
		foreach($types as $dt) {
			$seltypes[$dt->getDiscountTypeID()] = $dt->getDiscountTypeName();
		}
		$this->set('types', $types);
		$this->set('seltypes', $seltypes);
		$this->set('discounts', CoreCommerceDiscount::getList());
		$this->addHeaderItem(Loader::helper('html')->css('ccm.core.commerce.dashboard.css', 'core_commerce'));
	}

	public function add() {
		$this->select_type();
		$type = $this->get('type');
		$cnt = $type->getController();
		$e = $cnt->validateDiscount();
		if ($e->has()) {
			$this->set('error', $e);
		} else {
			$dt = Loader::helper('form/date_time');
			$dh = Loader::helper('date');
			
			$args = array(
				'discountHandle' => $this->post('discountHandle'),
				'discountName' => $this->post('discountName'),
				'discountIsEnabled' => $this->post('discountIsEnabled'),
				'discountStart' => $dh->getSystemDateTime($dt->translate('discountStart')),
				'discountEnd' => $dh->getSystemDateTime($dt->translate('discountEnd')),
				'discountCode' => $this->post('discountCode')
			);
			$ed = CoreCommerceDiscount::add($type, $args);
			$this->redirect('/dashboard/core_commerce/discounts/', 'discount_created');
		}
	}

	public function delete($discountID, $token = null) {
		try {
			$discount = CoreCommerceDiscount::getByID($discountID); 
				
			if(!($discount instanceof CoreCommerceDiscount)) {
				throw new Exception(t('Invalid discountID ID.'));
			}
	
			$valt = Loader::helper('validation/token');
			if (!$valt->validate('delete_discount', $token)) {
				throw new Exception($valt->getErrorMessage());
			}
			
			$discount->delete();
			$this->redirect("/dashboard/core_commerce/discounts/", 'discount_deleted');
		} catch (Exception $e) {
			$this->set('error', $e);
		}
	}
	
	
	public function edit($discountID = 0) {
		if ($this->post('discountID')) {
			$discountID = $this->post('discountID');
		}
		$discount = CoreCommerceDiscount::getByID($discountID);
		$type = $discount->getDiscountType();
		$this->set('discount', $discount);
		$this->set('type', $type);
		
		if ($this->isPost()) {
			$dt = Loader::helper('form/date_time');
			$cnt = $type->getController();
			$e = $cnt->validateDiscount();
			if ($e->has()) {
				$this->set('error', $e);
			} else {
				$dt = Loader::helper('form/date_time');
				$dh = Loader::helper('date');
				$args = array(
					'discountHandle' => $this->post('discountHandle'),
					'discountName' => $this->post('discountName'),
					'discountIsEnabled' => $this->post('discountIsEnabled'),
					'discountStart' => $dh->getSystemDateTime($dt->translate('discountStart')),
					'discountEnd' => $dh->getSystemDateTime($dt->translate('discountEnd')),
					'discountCode' => $this->post('discountCode')
				);
				$discount->update($args);
				$this->redirect('/dashboard/core_commerce/discounts/', 'discount_updated');
			}
		}
	}

	public function select_type() {
		$discountTypeID = $this->request('discountTypeID');
		$dt = CoreCommerceDiscountType::getByID($discountTypeID);
		$this->set('type', $dt);
	}	
	
	
	public function add_discount_type() {
		$pat = CoreCommercePendingDiscountType::getByHandle($this->post('discountTypeHandle'));
		if (is_object($pat)) {
			$pat->install();
		}
		$this->redirect('dashboard/core_commerce/discounts', 'discount_type_added');
	}
	
	public function discount_created() {
		$this->set('message', t('Discount Created'));
	}
	public function discount_updated() {
		$this->set('message', t('Discount Updated'));
	}
	public function discount_deleted() {
		$this->set('message', t('Discount Deleted'));
	}
	public function discount_type_updated() {
		$this->set('message', t('Discount Type Saved'));
	}

	public function discount_type_added() {
		$this->set('message', t('Discount Type Installed'));
		$this->manage_discount_types();
	}

}
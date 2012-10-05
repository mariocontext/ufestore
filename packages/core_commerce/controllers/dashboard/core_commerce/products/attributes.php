<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::model('attribute/categories/core_commerce_product', 'core_commerce');
class DashboardCoreCommerceProductsAttributesController extends Controller {
	
	public $helpers = array('form');
	
	public function __construct() {
		parent::__construct();
		$otypes = AttributeType::getList('core_commerce_product');
		$types = array();
		foreach($otypes as $at) {
			$types[$at->getAttributeTypeID()] = $at->getAttributeTypeName();
		}
		$subnav = array(
			array(View::url('/dashboard/core_commerce/products'), t('Products')),
			array(View::url('/dashboard/core_commerce/orders'), t('Orders')),
			array(View::url('/dashboard/core_commerce/discounts'), t('Discounts')),
			array(View::url('/dashboard/core_commerce/shipping'), t('Shipping')),
			array(View::url('/dashboard/core_commerce/payment'), t('Payment')),
			array(View::url('/dashboard/core_commerce/settings'), t('Settings'), true),
		);
		
		$this->set('subnav', $subnav);
		$this->set('types', $types);
	}
	
	public function on_start() {
		$this->set('disableThirdLevelNav', true);
		$this->set('category', AttributeKeyCategory::getByHandle('core_commerce_product'));
	}
		
	public function delete($akID, $token = null){
		try {
			$ak = CoreCommerceProductAttributeKey::getByID($akID); 
				
			if(!($ak instanceof CoreCommerceProductAttributeKey)) {
				throw new Exception(t('Invalid attribute ID.'));
			}
	
			$valt = Loader::helper('validation/token');
			if (!$valt->validate('delete_attribute', $token)) {
				throw new Exception($valt->getErrorMessage());
			}
			
			$ak->delete();
			
			$this->redirect("/dashboard/core_commerce/products/attributes", 'attribute_deleted');
		} catch (Exception $e) {
			$this->set('error', $e);
		}
	}

	public function attribute_deleted() {
		$this->set('message', t('Attribute Deleted.'));
	}
	
	public function attribute_created() {
		$this->set('message', t('Attribute Created.'));
	}

	public function attribute_updated() {
		$this->set('message', t('Attribute Updated.'));
	}
	

	
	public function select_type() {
		$atID = $this->request('atID');
		$at = AttributeType::getByID($atID);
		$this->set('type', $at);
	}
	
	public function add() {
		$this->select_type();
		$type = $this->get('type');
		$cnt = $type->getController();
		$e = $cnt->validateKey($this->post());
		if ($e->has()) {
			$this->set('error', $e);
		} else {
			$type = AttributeType::getByID($this->post('atID'));
			$args = array(
				'akHandle' => $this->post('akHandle'),
				'akName' => $this->post('akName'),
				'akIsSearchable' => $this->post('akIsSearchable'),
				'akIsSearchableIndexed' => $this->post('akIsSearchableIndexed'),
				'akIsAutoCreated' => 0,
				'akIsEditable' => 1
			);
			$ak = CoreCommerceProductAttributeKey::add($type, $this->post());
			$this->redirect('/dashboard/core_commerce/products/attributes/', 'attribute_created');
		}
	}
	
	public function edit($akID = 0) {
		if ($this->post('akID')) {
			$akID = $this->post('akID');
		}
		$key = CoreCommerceProductAttributeKey::getByID($akID);
		$type = $key->getAttributeType();
		$this->set('key', $key);
		$this->set('type', $type);
		
		if ($this->isPost()) {
			$cnt = $type->getController();
			$cnt->setAttributeKey($key);
			$e = $cnt->validateKey($this->post());
			if ($e->has()) {
				$this->set('error', $e);
			} else {
				$type = AttributeType::getByID($this->post('atID'));
				$args = array(
					'akHandle' => $this->post('akHandle'),
					'akName' => $this->post('akName'),
					'akIsSearchable' => $this->post('akIsSearchable'),
					'akIsSearchableIndexed' => $this->post('akIsSearchableIndexed'),
					'akIsAutoCreated' => 0,
					'akIsEditable' => 1
				);
				$key->update($this->post());
				$this->redirect('/dashboard/core_commerce/products/attributes', 'attribute_updated');
			}
		}
	}
	
}
<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::model('attribute/categories/core_commerce_product_option', 'core_commerce');
Loader::model('product/model', 'core_commerce');

class DashboardCoreCommerceProductsOptionsController extends Controller {
	
	public $helpers = array('form');
	

	public function on_start() {
		$this->set('disableThirdLevelNav', true);
		$otypes = AttributeType::getList('core_commerce_product_option');
		$types = array();
		foreach($otypes as $at) {
			$types[$at->getAttributeTypeID()] = $at->getAttributeTypeName();
		}
		$this->set('types', $types);
		$this->set('category', AttributeKeyCategory::getByHandle('core_commerce_product_option'));
	}
		
	public function delete($akID, $token = null){
		try {
			$ak = CoreCommerceProductOptionAttributeKey::getByID($akID); 
				
			if(!($ak instanceof CoreCommerceProductOptionAttributeKey)) {
				throw new Exception(t('Invalid attribute ID.'));
			}
			
			$productID = $ak->getProductID();
			
			$valt = Loader::helper('validation/token');
			if (!$valt->validate('delete_attribute', $token)) {
				throw new Exception($valt->getErrorMessage());
			}
			
			$ak->delete();
			
			$this->redirect('/dashboard/core_commerce/products/search', 'view_detail', $productID, 'option_deleted');
		} catch (Exception $e) {
			$this->set('error', $e);
		}
	}
	
	public function view($productID = 0) {
		if ($productID == 0) {
			$this->redirect('/dashboard/core_commerce/products/search');
		}
		$db = Loader::db();
		$this->set('product', CoreCommerceProduct::getByID($productID));
	}

	public function select_type() {
		$atID = $this->request('atID');
		$this->set('product', CoreCommerceProduct::getByID($this->request('productID')));
		$at = AttributeType::getByID($atID);
		$this->set('type', $at);
	}
	
	public function add() {
		$this->select_type();
		$type = $this->get('type');
		$cnt = $type->getController();
		$product = CoreCommerceProduct::getByID($this->post('productID'));
		$args = $this->post();
		if ($args['akHandle'] != '') {
			$args['akHandle'] = $product->getProductID() . '_' . $this->post('akHandle');
		}
		
		$e = $cnt->validateKey($args);
		
		if ($e->has()) {
			$this->set('error', $e);
		} else {
			$type = AttributeType::getByID($this->post('atID'));
			$ak = CoreCommerceProductOptionAttributeKey::add($type, $product, $args);
			$this->redirect('/dashboard/core_commerce/products/search', 'view_detail', $product->getProductID(), 'option_created');
		}
	}
	
	public function edit($akID = 0) {
		if ($this->post('akID')) {
			$akID = $this->post('akID');
		}
		$key = CoreCommerceProductOptionAttributeKey::getByID($akID);
		$type = $key->getAttributeType();
		$this->set('key', $key);
		$this->set('type', $type);
		
		if ($this->isPost()) {
			$cnt = $type->getController();
			$cnt->setAttributeKey($key);
			$args = $this->post();
			if ($args['akHandle'] != '') {
				$args['akHandle'] = $key->getProductID() . '_' . $this->post('akHandle');
			}

			$e = $cnt->validateKey($args);
			if ($e->has()) {
				$this->set('error', $e);
			} else {
				$type = AttributeType::getByID($this->post('atID'));
				$key->update($args);
				$this->redirect('/dashboard/core_commerce/products/search', 'view_detail', $key->getProductID(), 'option_updated');
			}
		}
	}
	
}
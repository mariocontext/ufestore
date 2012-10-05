<?php   

Loader::model('product/model', 'core_commerce');

class DashboardCoreCommerceProductsAddController extends Controller {

	public function view() {

	}
	
	public function on_start() {
		$this->set('disableThirdLevelNav', true);
	}
	
	public function submit() {
		Loader::model('collection_types');
		$val = Loader::helper('validation/form');
		$vat = Loader::helper('validation/token');
		$val->setData($this->post());
		$val->addRequired("prName", t("Product name required."));
		$val->test();

		$error = $val->getError();
	
		if (!$vat->validate('create_product')) {
			$error->add($vat->getErrorMessage());
		}
		
		$productDetailType = null;
		if ($this->post('parentCID')) {
			$productDetailType = CollectionType::getByHandle('product_detail');
			if (!$productDetailType) {
				$error->add('Unable to create product detail page.  The product detail page type is not defined.');
			}
			$parent = Page::getByID($this->post('parentCID'));
			if (!parent) {
				$error->add('Unable to create product detail page.  The parent page is not valid.');
			}
		}
	
		if ($error->has()) {
			$this->set('error', $error);
		} else {

			$data = $this->post();
			$data['parentCID'] = $this->post('parentCID');
			$product = CoreCommerceProduct::add($data);
			
			$product->setPurchaseGroups($this->post('gID'));
			
			Loader::model("attribute/categories/core_commerce_product", 'core_commerce');
			$aks = CoreCommerceProductAttributeKey::getList();
			foreach($aks as $uak) {
				$uak->saveAttributeForm($product);				
			}
			
			$this->redirect('/dashboard/core_commerce/products/search/', 'view_detail', $product->getProductID(), 'product_created');
		}	
	}
	

}

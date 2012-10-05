<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::model('product/model', 'core_commerce');
class DashboardCoreCommerceProductsImagesController extends Controller {
	
	public $helpers = array('form');
	

	public function on_start() {
		$this->set('disableThirdLevelNav', true);
		$this->addHeaderItem(Loader::helper('html')->css('ccm.core.commerce.dashboard.css', 'core_commerce'));
	}
		
	public function view($productID = 0) {
		if ($productID == 0) {
			$this->redirect('/dashboard/core_commerce/products/search');
		}
		$db = Loader::db();
		$this->set('product', CoreCommerceProduct::getByID($productID));
	}
	
	public function save() {
		$pr = CoreCommerceProduct::getByID($this->post('productID'));
		$pr->updateCoreProductImages($this->post());
		$data = $this->post('additionalProductImageFID');
		if (!is_array($data)) {
			$data = array();
		}
		$pr->setAdditionalProductImages($data);
		
		$this->redirect('/dashboard/core_commerce/products/search', 'view_detail', $pr->getProductID(), 'images_updated');
	}
	
}
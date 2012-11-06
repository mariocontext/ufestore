<?php  defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::controller('/checkout');
Loader::model('attribute/categories/core_commerce_order','core_commerce');
Loader::model('downloadable_product','core_commerce_downloadable_file');
class CheckoutSuccessDownloadController extends CheckoutController {

	public function on_start() {
		Loader::model('order/current', 'core_commerce');
		Loader::model('order/previous', 'core_commerce');
		$e = Loader::helper('validation/error');
		
		$files = array();
		
		$currentOrder = CoreCommerceCurrentOrder::get();
		$previousOrder = @CoreCommercePreviousOrder::get();
		
		$orders = array($currentOrder, $previousOrder);
		foreach($orders as $o) {
			if (is_object($o) && $o->getOrderStatus() > CoreCommerceOrder::STATUS_NEW) {
				$downloads = DownloadableProduct::getDownloadableProductsFromOrder($o);
				if(is_array($downloads) && count($downloads)) {
					foreach($downloads as $d) {
						$files[] = $d;
					}
				}			
			}
		}
		
		if (count($files) == 0) {
			$e->add(t('You do not have access to download any files.'));
		}
		
		$this->set('files', $files);
		$this->set('errorObj', $e);		
		
	}
}
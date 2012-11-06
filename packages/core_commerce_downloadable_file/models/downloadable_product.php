<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

class DownloadableProduct extends Object {

	/**
	 * @return boolean
	 */
	public static function hasPurchased() {
		return true;
	}

	/**
	 * @return boolean
	*/
	public static function hasExpired($datetime) {
		$dh = Loader::helper('date');
		$now = $dh->getSystemDateTime();
		if(strtotime($datetime) > strtotime($now)) {
			return false;
		} else { 
			return true;
		}
	}
	
	public static function getFileByHash($hash) {
		$db = Loader::db();
		$info = $db->getRow("SELECT * FROM CoreCommerceOrderDownloadableFiles WHERE orderProductFileKey = ?",array($hash));
		if($info['limitDownloadTime'] && self::hasExpired($info['timeDownloadEnds'])) {
			return false;
		}
		return $info['fID'];
	} 
	
	public static function recordPurchase($orderID, $productID, $fID, $expire, $expireTime) {
		$db = Loader::db();
		$h = Loader::helper('validation/identifier');
		$key = $h->getString();	
		$vals = array($orderID, $productID, $fID, $key, $expire, $expireTime);
		$db->query("INSERT INTO CoreCommerceOrderDownloadableFiles (orderID, productID, fID, orderProductFileKey, limitDownloadTime, timeDownloadEnds) VALUES (?,?,?,?,?,?)",$vals);
		return $key;
	}

	public static function getDownloadableProductsFromOrder($order) {
		Loader::model('attribute/categories/core_commerce_product', 'core_commerce');
		$uh = Loader::helper('concrete/urls');
		
		// loop through orderproducts, check products for downloadable attributes
		$downloads = array();
		$orderProducts  = $order->getProducts();
		if(is_array($orderProducts) && count($orderProducts)) {
			foreach($orderProducts as $p) {
				$product = $p->getProductObject();
				$attribs = CoreCommerceProductAttributeKey::getList($product); // loop throught the attribs looking for ones that return a downloadable fID
				if(is_array($attribs) && count($attribs)) {
					foreach($attribs as $ak) {
						$at = $ak->getAttributeType();
						if($at->getAttributeTypeHandle() == 'downloadable_product_file') {
							
							$fID = $product->getAttribute($ak,'fid');
							$expire = $product->getAttribute($ak,'expire');
							if($expire !== false) {
								$setExpire = 1;
							} else {
								$setExpire = 0;
							}
							
							if($fID) {
								$hash = self::recordPurchase($order->getOrderID(), $product->getProductID(), $fID, $setExpire, $expire);
								if($hash) {
									$downloads[] = array(
										'product' => $product,
										'name'=>$product->getProductName() . ' - ' . $ak->getAttributeKeyName(),
										'url' =>  BASE_URL.DIR_REL.$uh->getToolsUrl('download','core_commerce_downloadable_file').'?hash='.$hash
										);
								} 
							}
						}
					}
				}
			}
		}
		
		
		return $downloads;
	}
	
	
	public static function onPurchaseComplete($order, $userInfo) { 
		$downloads = self::getDownloadableProductsFromOrder($order);
		
		if(count($downloads)) { // send mail
			$mh = Loader::helper('mail');
			$mh->addParameter('orderID', $order->getOrderID());
			$mh->addParameter('downloads', $downloads);
			$mh->to($order->getOrderEmail());
			$mh->load('downloads_available','core_commerce_downloadable_file');
			@$mh->sendMail();
		}
	}
	

	public function checkoutSetup($checkoutController) {
		if(method_exists($checkoutController, 'replaceStep')) { // make it work with older version of core_commerce
			$downloads = self::getDownloadableProductsFromOrder($checkoutController->get('order'));
			if(count($downloads)) {
				$res = $checkoutController->replaceStep(new CoreCommerceCheckoutStep('/checkout/success_download'), '/checkout/finish');
			}			
		}
	}
}

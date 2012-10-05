<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('order/model', 'core_commerce');
/**
 * Most Recent Completed Order For the Current Session
 *
 */
class CoreCommercePreviousOrder extends CoreCommerceOrder {

	static $orderID = 0;

	public static function set($orderID) {
		$_SESSION['coreCommercePreviousOrderID'] = $orderID;
	}

	public static function get() {
		if (self::$orderID == 0) {
			self::$orderID = CoreCommercePreviousOrder::getFromSession();
		}
		if (self::$orderID > 0) {
			$order = new CoreCommercePreviousOrder();
			$order->load(self::$orderID);
		}	
		return $order;
	}
	
	private function getFromSession() {
		if ($_SESSION['coreCommercePreviousOrderID'] != '') {
			if (!CoreCommercePreviousOrder::isValidOrderID($_SESSION['coreCommercePreviousOrderID'])) {
				$_SESSION['coreCommerceCurrentOrderID'] = '';
			}
		}
		return $_SESSION['coreCommercePreviousOrderID'];
	} 
}
<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('order/model', 'core_commerce');
class CoreCommerceCurrentOrder extends CoreCommerceOrder {

	static $orderID = 0;

	public static function get() {
		if (self::$orderID == 0) {
			self::$orderID = CoreCommerceCurrentOrder::getFromSession();
		}
		$order = new CoreCommerceCurrentOrder();
		$order->load(self::$orderID);

		// reset the cart and set the previous order
		if ($order->getStatus() > 0) { 
			Loader::model('order/previous','core_commerce');
			CoreCommercePreviousOrder::set($order->getOrderID());
			CoreCommerceCurrentOrder::clear();
			$order = CoreCommerceCurrentOrder::get();
		}

		// check for logged in user
		$u = new User();
		if ($u->isRegistered() && $u->getUserID() != $order->getOrderUserID()) {
			$order->setOrderUserID($u->getUserID());
		}
		return $order;
	}
	
	// PHP !@#!@#!@#
	
	private function getFromSession() {
		if ($_SESSION['coreCommerceCurrentOrderID'] != '') {
			if (!CoreCommerceCurrentOrder::isValidOrderID($_SESSION['coreCommerceCurrentOrderID'])) {
				$_SESSION['coreCommerceCurrentOrderID'] = '';
			}
		}
		if (empty($_SESSION['coreCommerceCurrentOrderID'])) {
			$o = CoreCommerceOrder::add();
			$_SESSION['coreCommerceCurrentOrderID'] = $o->getOrderID();
		}
		return $_SESSION['coreCommerceCurrentOrderID'];
	}
	
	public function getCart() {
		$cart = CoreCommerceCart::get();
		return $cart;
	}
	
	public function clear() {
		self::$orderID = 0;
		unset($_SESSION['coreCommerceCurrentOrderID']);
	}
	
	public function getAvailablePaymentMethods() {
		Loader::model("payment/method", "core_commerce");
		$methods = CoreCommercePaymentMethod::getEnabledList();
		$m2 = Events::fire('core_commerce_on_get_payment_methods', $this, $methods);
		if ($m2 != false) {
			return $m2;
		}
		return $methods;
	}

	public function getAvailableShippingMethods() {
		// responsible for passing products to all enabled shipping methods
		$methods = array();
		Loader::model("shipping/type", "core_commerce");
		Loader::model("shipping/method", "core_commerce");
		$types = CoreCommerceShippingType::getEnabledList();
		foreach($types as $t) {
			if ($t->canShipToShippingAddress($this)) {
				$m = $t->getController()->getAvailableShippingMethods($this);
				if ($m != false) {
					if (is_array($m)) {
						foreach($m as $_m) {
							$methods[] = $_m;	
						}
					} else {
						$methods[] = $m;
					}			
				}
			}
		}
		
		$m2 = Events::fire('core_commerce_on_get_shipping_methods', $this, $methods);
		if ($m2 != false) {
			return $m2;
		}
		return $methods;
	}

}

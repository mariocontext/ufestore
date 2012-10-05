<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
class CoreCommercePaymentController extends Controller {
	
	protected $paymentMethod;
	
	public function setPaymentMethod($m) { $this->paymentMethod = $m;}
	public function getPaymentMethod() {return $this->paymentMethod;}
	
	public function setupAndRun($method) {
		$args = func_get_args();
		$args = array_slice($args, 1);
		if ($method) {
			$this->task = $method;
		}
		if (method_exists($this, 'on_start')) {
			call_user_func_array(array($this, 'on_start'), array($method));
		}
		if ($method) {
			$this->runTask($method, $args);
		}
		
		if (method_exists($this, 'on_before_render')) {
			call_user_func_array(array($this, 'on_before_render'), array($method));
		}
	}
	
	public function action($action) {
		$uh = Loader::helper('urls', 'core_commerce');
		$a = func_get_args();
		$args = '';
		for ($i = 1; $i < count($a); $i++) {
			$args .= '&args[]=' . $a[$i];
		}
		$action = $uh->getToolsURL('payment_method_action', true) . '?paymentMethodID=' . $this->paymentMethod->getPaymentMethodID() . '&action=' . $action . $args;
		return $action;
	}
	
	public function validate() {
		$valt = Loader::helper('validation/token');
		$error = Loader::helper('validation/error');
		
		if (!$valt->validate('update_payment_method')) {
			$error->add($valt->getErrorMessage());
		}

		return $error;		
	}
	
	public function save() {}
	public function form() {}
	
	protected function finishOrder($order, $method, $data=null) {
		if ($order->getOrderUserID() > 0) {
			$ui = UserInfo::getByID($order->getOrderUserID());
			$u = User::getByUserID($order->getOrderUserID());
			if ($ui) {
				$groups = array();
				$products = $order->getProducts();
				foreach ($products as $product) {
					$groups = array_merge($groups, $product->getProductPurchaseGroupIDArray());
				}
				foreach ($groups as $gID) {
					$group = Group::getByID($gID);
					$u->enterGroup($group);
				}
				
				$u->refreshUserGroups();
			}
		}

		$pkg = Package::getByHandle('core_commerce');
		if ($pkg->config('ENABLE_ORDER_NOTIFICATION_EMAILS')) {
			$this->sendOrderEmail($order, $method, $data=null);
		}
		
		// now we reduce quantity if that option is enabled
		if ($pkg->config('MANAGE_INVENTORY') == 1 && $pkg->config('MANAGE_INVENTORY_TRIGGER') == 'FINISHED') {
			$products = $order->getProducts();
			foreach ($products as $product) {
				$baseProduct = $product->getProductObject();
				$baseProduct->decreaseProductQuantity($product->getQuantity());
			}
		}

		$this->sendReceiptEmail($order, $data=null);
		
		Events::fire('core_commerce_on_checkout_finish_order',$order,$ui);
	}

	protected function sendOrderEmail($order, $method, $data=null) {
		$pkg = Package::getByHandle('core_commerce');
		$emails = explode(',', $pkg->config('ENABLE_ORDER_NOTIFICATION_EMAIL_ADDRESSES'));
       	$mh = Loader::helper('mail');

		$fromE = $pkg->config('EMAIL_NOTIFICATION_EMAIL');
       	$fromN = $pkg->config('EMAIL_NOTIFICATION_NAME');
       	if ($fromE != '') {
       		$mh->from($fromE, $fromN);
       	}
       	
		foreach ($emails as $email) {
       		$mh->to($email);
		}

		$this->setEmailData($mh, $order, $data);
		$mh->addParameter('paymentMethod', $method);

		$mh->load('order', 'core_commerce');
       	$mh->setSubject(SITE . t(" - Order"));

       	try {
           	$mh->sendMail();
       	} catch (Exception $e) {
           	Log::addEntry('Error sending order email for order #'.$order->getOrderID().': '.$e->getMessage());
       	}
	}

	protected function sendReceiptEmail($order, $data=null) {
       	$mh = Loader::helper('mail');
		$pkg = Package::getByHandle('core_commerce');
		
		$fromE = $pkg->config('EMAIL_RECEIPT_EMAIL');
       	$fromN = $pkg->config('EMAIL_RECEIPT_NAME');
       	if ($fromE != '') {
       		$mh->from($fromE, $fromN);
       	}
       	
       	$mh->to($order->getOrderEmail());

		$this->setEmailData($mh, $order, $data);
		
		$blurb = $pkg->config('RECEIPT_EMAIL_BLURB');
		if (empty($blurb)) {
			$blurb = t('Thank you for your purchase!');
		}
		$mh->addParameter('blurb', $blurb);

		$mh->load('receipt', 'core_commerce');
       	$mh->setSubject(SITE . t(" - Receipt"));

       	try {
           	$mh->sendMail();
       	} catch (Exception $e) {
           	Log::addEntry('Error sending order email for order #'.$order->getOrderID().': '.$e->getMessage());
       	}
	}

	protected function setEmailData($mh, $order, $data=null) {
		$mh->addParameter('orderID', $order->getOrderID());
		$mh->addParameter('totalAmount', $order->getOrderDisplayTotal());

       	$items = $order->getProducts();
       	$i = 0;
		$products = array();
       	foreach ($items as $item) {
			$products[$i]['name'] = $item->getProductName();
			$products[$i]['attributes'] = array();
           	$attribs = $item->getProductConfigurableAttributes();
			$j = 0;
           	foreach($attribs as $ak) {
               	$products[$i]['attributes'][$j++] = $item->getAttribute($ak);
           	}
			$products[$i]['quantity'] = $item->getQuantity();
			$products[$i]['price'] = $item->getProductCartDisplayPrice();
			$i++;
		}
		$mh->addParameter('products', $products);

       	$i = 0;
       	$items = $order->getOrderLineItems();
		$adjustments = array();
       	foreach ($items as $item) {
			$adjustments[$i]['name'] = $item->getLineItemName();
			$adjustments[$i]['type'] = $item->getLineItemType();
			$adjustments[$i]['total'] = $item->getLineItemDisplayTotal();
           	$i++;
       	}
		$mh->addParameter('adjustments', $adjustments);

       	$billing['first_name'] = $order->getAttribute('billing_first_name');
       	$billing['last_name'] = $order->getAttribute('billing_last_name');
       	$billing['email'] = $order->getOrderEmail();
       	$billing['address1'] = $order->getAttribute('billing_address')->getAddress1();
       	$billing['address2'] = $order->getAttribute('billing_address')->getAddress2();
       	$billing['city'] = $order->getAttribute('billing_address')->getCity();
       	$billing['state'] = $order->getAttribute('billing_address')->getStateProvince();
       	$billing['zip'] = $order->getAttribute('billing_address')->getPostalCode();
       	$billing['country'] = $order->getAttribute('billing_address')->getCountry();
       	$billing['phone'] = $order->getAttribute('billing_phone');
		$mh->addParameter('billing', $billing);

		if ($order->getAttribute('shipping_address')) {
       		$shipping['first_name'] = $order->getAttribute('shipping_first_name');
       		$shipping['last_name'] = $order->getAttribute('shipping_last_name');
       		$shipping['email'] = $order->getOrderEmail();
       		$shipping['address1'] = $order->getAttribute('shipping_address')->getAddress1();
       		$shipping['address2'] = $order->getAttribute('shipping_address')->getAddress2();
       		$shipping['city'] = $order->getAttribute('shipping_address')->getCity();
       		$shipping['state'] = $order->getAttribute('shipping_address')->getStateProvince();
       		$shipping['zip'] = $order->getAttribute('shipping_address')->getPostalCode();
       		$shipping['country'] = $order->getAttribute('shipping_address')->getCountry();
       		$shipping['phone'] = $order->getAttribute('shipping_phone');
			$mh->addParameter('shipping', $shipping);
		}

		$bill_attr = AttributeSet::getByHandle('core_commerce_order_billing');
		if ($bill_attr > 0) {
			$akHandles = array('billing_first_name', 'billing_last_name', 'billing_address', 'billing_phone');
        	$keys = $bill_attr->getAttributeKeys();
			$billing_attrs = array();
        	foreach($keys as $ak) {
				if (!in_array($ak->getAttributeKeyHandle(), $akHandles)) {
					$billing_attrs[$ak->getAttributeKeyName()] = $order->getAttribute($ak);
				}
        	}
			$mh->addParameter('billing_attrs', $billing_attrs);
		}

		$ship_attr = AttributeSet::getByHandle('core_commerce_order_shipping');
		if ($ship_attr > 0) {
			$akHandles = array('shipping_first_name', 'shipping_last_name', 'shipping_address', 'shipping_phone');
        	$keys = $ship_attr->getAttributeKeys();
			$shipping_attrs = array();
        	foreach($keys as $ak) {
				if (!in_array($ak->getAttributeKeyHandle(), $akHandles)) {
					$shipping_attrs[$ak->getAttributeKeyName()] = $order->getAttribute($ak);
				}
        	}
			$mh->addParameter('shipping_attrs', $shipping_attrs);
		}

	}

}

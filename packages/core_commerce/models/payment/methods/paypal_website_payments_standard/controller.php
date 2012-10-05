<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::library('payment/controller', 'core_commerce');
class CoreCommercePaypalWebsitePaymentsStandardPaymentMethodController extends CoreCommercePaymentController {

	public function method_form() {
		$pkg = Package::getByHandle('core_commerce');
		$this->set('PAYMENT_METHOD_PAYPAL_STANDARD_EMAIL', $pkg->config('PAYMENT_METHOD_PAYPAL_STANDARD_EMAIL'));
		$this->set('PAYMENT_METHOD_PAYPAL_STANDARD_TEST_MODE', $pkg->config('PAYMENT_METHOD_PAYPAL_STANDARD_TEST_MODE'));
		$this->set('PAYMENT_METHOD_PAYPAL_STANDARD_TRANSACTION_TYPE', $pkg->config('PAYMENT_METHOD_PAYPAL_STANDARD_TRANSACTION_TYPE'));
		$this->set('PAYMENT_METHOD_PAYPAL_STANDARD_CURRENCY_CODE', 
			(strlen($pkg->config('PAYMENT_METHOD_PAYPAL_STANDARD_CURRENCY_CODE'))?$pkg->config('PAYMENT_METHOD_PAYPAL_STANDARD_CURRENCY_CODE'):'USD')
			);
		$this->set('PAYMENT_METHOD_PAYPAL_STANDARD_PASS_ADDRESS',$pkg->config('PAYMENT_METHOD_PAYPAL_STANDARD_PASS_ADDRESS'));
		
		$paypal_currency_codes = array(
			'AUD'=>t('Australian Dollar'),
			'CAD'=>t('Canadian Dollar'),
			'CZK'=>t('Czech Koruna'),
			'DKK'=>t('Danish Krone'),
			'EUR'=>t('Euro'),
			'HKD'=>t('Hong Kong Dollar'),
			'HUF'=>t('Hungarian Forint'),
			'ILS'=>t('Israeli New Sheqel'),
			'JPY'=>t('Japanese Yen'),
			'MXN'=>t('Mexican Peso'),
			'NOK'=>t('Norwegian Krone'),
			'NZD'=>t('New Zealand Dollar'),
			'PLN'=>t('Polish Zloty'),
			'GBP'=>t('Pound Sterling'),
			'SGD'=>t('Singapore Dollar'),
			'SEK'=>t('Swedish Krona'),
			'CHF'=>t('Swiss Franc'),
			'USD'=>t('U.S. Dollar')
		);
		asort($paypal_currency_codes);
		$this->set('paypal_currency_codes',$paypal_currency_codes);	
	}
	
	public function validate() {
		$e = parent::validate();
		$ve = Loader::helper('validation/strings');
		
		if ($this->post('PAYMENT_METHOD_PAYPAL_STANDARD_EMAIL') == '') {
			$e->add(t('You must specify your Paypal ID, which is an email address.'));
		}

		return $e;
	}
	
	public function action_notify_complete() {
		$success = false;
        Loader::model('order/model', 'core_commerce');
		$pkg = Package::getByHandle('core_commerce');

		if ($this->validateIPN()) {
			$eh = Loader::helper('encryption');
			$orderID = $eh->decrypt($_REQUEST['invoice']);
			$o = CoreCommerceOrder::getByID($orderID);
			if ($o) {
				// deal with float comparison problems
				$order_total = number_format($o->getOrderTotal(),2,'.','');
				$paid_total = number_format($_REQUEST['mc_gross'],2,'.','');
				
				if ($paid_total >= $order_total) {
					if ($_REQUEST['payment_status'] == 'Pending') {
						$o->setStatus(CoreCommerceOrder::STATUS_PENDING);
						parent::finishOrder($o, 'Paypal - Website Payments Standard');
					} else if ($_REQUEST['payment_status'] == 'Completed') {
						$o->setStatus(CoreCommerceOrder::STATUS_AUTHORIZED);				
						parent::finishOrder($o, 'Paypal - Website Payments Standard');
					} else {
						Log::addEntry('Unable to set status. Status received: ' . $_REQUEST['payment_status']);
					}			
				} else {
					Log::addEntry('Invalid payment for order# '.$o->getOrderID() . " Requested ". $pkg->config('CURRENCY_SYMBOL').$order_total.', got '.$pkg->config('CURRENCY_SYMBOL').$paid_total );
					Log::addEntry('Invalid payment debug info for order# '.$o->getOrderID().'\n'.var_export($_REQUEST,true) . var_export($o,true));
				}
				
			} else {
				Log::addEntry('Received order notification with unknown order: '.$orderID);
			}
		}
	}
	
	public function form() {
		$pkg = Package::getByHandle('core_commerce');
		if ($pkg->config('PAYMENT_METHOD_PAYPAL_STANDARD_TEST_MODE') == 'test') { 
			$this->set('action', 'https://www.sandbox.paypal.com/cgi-bin/webscr');
		} else {
			$this->set('action', 'https://www.paypal.com/cgi-bin/webscr');
		}
		$o = CoreCommerceCurrentOrder::get();
		$this->set('item_name', SITE);

		// paypal fields
		$fields['cmd'] = '_xclick';
		$fields['address_override'] = 0;
		$fields['rm'] = 2;
		$fields['no_note'] = 1;
		
		// address information
		$fields['item_name'] = t('Purchase from %s', SITE)." - ".t('Order #').$o->getOrderID();
		
		$shipping_address = $o->getAttribute('shipping_address');
		if(($pkg->config('PAYMENT_METHOD_PAYPAL_STANDARD_PASS_ADDRESS') =='shipping') && is_object($shipping_address)) {
			$fields['first_name'] = $o->getAttribute('shipping_first_name');
			$fields['last_name'] = $o->getAttribute('shipping_last_name');
			$fields['address1'] = $shipping_address->getAddress1();
			$fields['address2'] = $shipping_address->getAddress2();
			$fields['city'] = $shipping_address->getCity();
			$fields['state'] = $shipping_address->getStateProvince();
			$fields['zip'] = $shipping_address->getPostalCode();
			$fields['country'] = $shipping_address->getCountry();
			$fields['night_phone_a'] = $o->getAttribute('shipping_phone');
		} else {
			$fields['first_name'] = $o->getAttribute('billing_first_name');
			$fields['last_name'] = $o->getAttribute('billing_last_name');
			$fields['address1'] = $o->getAttribute('billing_address')->getAddress1();
			$fields['address2'] = $o->getAttribute('billing_address')->getAddress2();
			$fields['city'] = $o->getAttribute('billing_address')->getCity();
			$fields['state'] = $o->getAttribute('billing_address')->getStateProvince();
			$fields['zip'] = $o->getAttribute('billing_address')->getPostalCode();
			$fields['country'] = $o->getAttribute('billing_address')->getCountry();
			$fields['night_phone_a'] = $o->getAttribute('billing_phone');
		}
		
		$fields['amount'] = $o->getOrderTotal();
		
		$fields['currency_code'] = $pkg->config('PAYMENT_METHOD_PAYPAL_STANDARD_CURRENCY_CODE');
		
		// email
		$u = new User();
		$ui = UserInfo::getByID($u->getUserID());
		$fields['business'] = $pkg->config('PAYMENT_METHOD_PAYPAL_STANDARD_EMAIL');
		$fields['email'] = $ui ? $ui->getUserEmail() : '';
		$fields['paymentaction'] = $pkg->config('PAYMENT_METHOD_PAYPAL_STANDARD_TRANSACTION_TYPE');
		
		$fields['address_override'] = 1;
		$fields['no_shipping'] = 1;
		
 		//callback
		$fields['notify_url'] = $this->action('notify_complete');
	
		$ch = Loader::helper('checkout/step', 'core_commerce');
		$ns = $ch->getNextCheckoutStep();
		$ps = $ch->getCheckoutStep();
		$returnURL = $ns->getURL();
		$fields['return'] = $returnURL;
		$fields['cancel_return'] = $ps->getURL();
		$eh = Loader::helper('encryption');
		$fields['invoice'] = $eh->encrypt($o->getOrderID());
		$this->set('fields', $fields);
	}
		
	public function save() {
		$pkg = Package::getByHandle('core_commerce');
		$pkg->saveConfig('PAYMENT_METHOD_PAYPAL_STANDARD_EMAIL', $this->post('PAYMENT_METHOD_PAYPAL_STANDARD_EMAIL'));
		$pkg->saveConfig('PAYMENT_METHOD_PAYPAL_STANDARD_TEST_MODE', $this->post('PAYMENT_METHOD_PAYPAL_STANDARD_TEST_MODE'));
		$pkg->saveConfig('PAYMENT_METHOD_PAYPAL_STANDARD_TRANSACTION_TYPE', $this->post('PAYMENT_METHOD_PAYPAL_STANDARD_TRANSACTION_TYPE'));
		$pkg->saveConfig('PAYMENT_METHOD_PAYPAL_STANDARD_CURRENCY_CODE', $this->post('PAYMENT_METHOD_PAYPAL_STANDARD_CURRENCY_CODE'));
		$pkg->saveConfig('PAYMENT_METHOD_PAYPAL_STANDARD_PASS_ADDRESS', $this->post('PAYMENT_METHOD_PAYPAL_STANDARD_PASS_ADDRESS'));
	}
	

	private function validateIPN() {
		$req = 'cmd=_notify-validate';
		foreach ($_POST as $key => $value) {
			$value = urlencode(stripslashes($value));
			$req .= "&$key=$value";
		}

		$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

		$pkg = Package::getByHandle('core_commerce');
		if ($pkg->config('PAYMENT_METHOD_PAYPAL_STANDARD_TEST_MODE') == 'test') { 
			$host = 'www.sandbox.paypal.com';
		} else {
			$host = 'www.paypal.com';
		}

		$fp = @fsockopen('ssl://'.$host, 443, $errnum, $errstr, 30);
		if (!$fp) {
			Log::addEntry('Error opening socket for IPN connection: '.$errstr.'('.$errnum.')');
		} else {
			fputs ($fp, $header . $req); 
			while(!feof($fp)) {
				$info[] = @fgets($fp, 1024);
			}
			fclose($fp);
			$info = implode(',', $info);
			if (eregi('VERIFIED', $info)) {
				return true;
			} else {
				Log::addEntry('Received an unverified IPN response: '.var_export($info,true));
				return false;
			}
		}
	}
	
}

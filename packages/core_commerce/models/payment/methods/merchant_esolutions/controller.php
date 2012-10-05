<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::library('payment/controller', 'core_commerce');


if(!defined('HOSTURI')) {
  define("HOSTURI", "https://cert.merchante-solutions.com/mes-api/tridentApi");
}

require dirname(__FILE__) . "/trident_gateway.inc.php";


class CoreCommerceMerchantEsolutionsPaymentMethodController extends CoreCommercePaymentController {

    public function method_form() {
        $pkg = Package::getByHandle('core_commerce');
        $this->set('PAYMENT_METHOD_MES_MERCHANT_ID',      $pkg->config('PAYMENT_METHOD_MES_MERCHANT_ID'));
        $this->set('PAYMENT_METHOD_MES_MERCHANT_KEY',     $pkg->config('PAYMENT_METHOD_MES_MERCHANT_KEY'));
        $this->set('PAYMENT_METHOD_MES_TRANSACTION_TYPE', $pkg->config('PAYMENT_METHOD_MES_TRANSACTION_TYPE'));
        $this->set('PAYMENT_METHOD_MES_TEST_MODE',        $pkg->config('PAYMENT_METHOD_MES_TEST_MODE'));
        $this->set('PAYMENT_METHOD_MES_CCV',              $pkg->config('PAYMENT_METHOD_MES_CCV'));
        $this->set('PAYMENT_METHOD_MES_EMAIL_RECEIPT',    $pkg->config('PAYMENT_METHOD_MES_EMAIL_RECEIPT'));
        
        if(!defined('PROFILEID')) {
          define("PROFILEID", "94100010140900000001");
        }
        if(!defined('PROFILEKEY')) {
          define("PROFILEKEY", "EUIGmxGThpMlBdpTpDBYeuUMeSRQunSZ");
        }
    }
    
    public function validate() {
        $e = parent::validate();
        $ve = Loader::helper('validation/strings');
        
        if ($this->post('PAYMENT_METHOD_MES_MERCHANT_ID') == '') {
            $e->add(t('You must specify your merchant id.'));
        }
        if ($this->post('PAYMENT_METHOD_MES_MERCHANT_KEY') == '') {
            $e->add(t('You must specify your merchant key.'));
        }

        return $e;
    }
    
    public function form() {
        $this->set('action', $this->action('submit'));

		$fields = array();
        $this->set('fields', $fields);
        return;
    }
        
    public function action_submit() {
        $u = new User();

        $pkg = Package::getByHandle('core_commerce');
        $fields = $_POST;
		    $fields['TransactionType'] = 'CreditCardCharge';
		    
		    /*        
		    $fields['BillingAddress']      = '';
		    $fields['BillingZipCode']      = '';
		    $fields['BillingCity']         = '';
		    $fields['BillingState']        = '';
		    $fields['CVV2']                = '';
	      
	      $fields['ShippingAddress1']     = '';
	      $fields['ShippingCity']         = '';
        $fields['ShippingState']        = '';
        $fields['ShippingZipCode']      = '';
        $fields['ShippingCountry']      = '';
        */
        
        if ($pkg->config('PAYMENT_METHOD_MES_TEST_MODE') == 'test-account' || $pkg->config('PAYMENT_METHOD_MES_TEST_MODE') == 'test-mode') 
        {
            $url = "https://cert.merchante-solutions.com/mes-api/tridentApi";
        
		        $fields['MerchantID']          = $pkg->config('PAYMENT_METHOD_MES_MERCHANT_ID');
		        $fields['MerchantKey']         = $pkg->config('PAYMENT_METHOD_MES_MERCHANT_KEY');
		        $fields['CardNumber']          = $_POST['x_card_num'];
		        $fields['ExpirationDateMMYY']  = $_POST['x_exp_date'];
		        $fields['CVV2']                = $_POST['x_card_code'];

		        /*
		        $fields['MerchantID']          = '10012';
    		    $fields['MerchantKey']         = 'c22a63ee-2e7a-4ace-96ac-0958dc8d953f';
    		    $fields['CardNumber']          = '4111111111111111';
    		    $fields['ExpirationDateMMYY']  = '0115';
    		    */
        } else {
          /* Production Live */
            $url = 'https://api.merchante-solutions.com/mes-api/tridentApi';
            
		        $fields['MerchantID']          = $pkg->config('PAYMENT_METHOD_MES_MERCHANT_ID');
		        $fields['MerchantKey']         = $pkg->config('PAYMENT_METHOD_MES_MERCHANT_KEY');
		        $fields['CardNumber']          = $_POST['x_card_num'];
		        $fields['ExpirationDateMMYY']  = $_POST['x_exp_date'];
		        $fields['CVV2']                = $_POST['x_card_code'];
        }
        
        unset($fields['x_card_num']);
        unset($fields['x_exp_date']);
        unset($fields['x_card_code']);
        unset($fields['submit_next']);
        
        Loader::model('order/current', 'core_commerce');
        $o = CoreCommerceCurrentOrder::get();
        $eh = Loader::helper('encryption');  
        $ui = UserInfo::getByID($u->getUserID());
        
        if ($ui && $ui->getUserEmail()) {
          // $ui->getUserEmail();
        }

          $fields['ReferenceNumber']   = $o->getOrderID();
          $fields['TransactionAmount'] = number_format($o->getOrderTotal(), 2);
        
          $fields['BillingNameFirst'] = $o->getAttribute('billing_first_name');
          $fields['BillingNameLast']  = $o->getAttribute('billing_last_name');
          $fields['BillingFullName']  = $fields['BillingNameFirst'] . " ". $fields['BillingNameLast'];
      
          $addr2 = $o->getAttribute('billing_address')->getAddress2();
          if (!empty($addr2)) {
              $fields['BillingAddress'] = $addr2;
          } else {
              $fields['BillingAddress'] = $o->getAttribute('billing_address')->getAddress1();
          }
      
          $fields['BillingCity']    = $o->getAttribute('billing_address')->getCity();
          $fields['BillingState']   = $o->getAttribute('billing_address')->getStateProvince();
          $fields['BillingZipCode'] = $o->getAttribute('billing_address')->getPostalCode();
        
        //$fields['x_country'] = $o->getAttribute('billing_address')->getCountry();
        //$fields['x_phone'] = $o->getAttribute('billing_phone');

        //$fields['x_ship_to_first_name'] = $o->getAttribute('shipping_first_name');
        //$fields['x_ship_to_last_name'] = $o->getAttribute('shipping_last_name');
        
        
		if ($o->getAttribute('shipping_address')) {
        	$addr2 = $o->getAttribute('shipping_address')->getAddress2();
        	if (!empty($addr2)) {
            	//$fields['x_ship_to_company'] = $o->getAttribute('shipping_address')->getAddress1();
            	$fields['ShippingAddress1'] = $addr2;
        	} else {
            	$fields['ShippingAddress1'] = $o->getAttribute('shipping_address')->getAddress1();
        	}
        	$fields['ShippingCity'] = $o->getAttribute('shipping_address')->getCity();
        	$fields['ShippingState'] = $o->getAttribute('shipping_address')->getStateProvince();
        	$fields['ShippingZipCode'] = $o->getAttribute('shipping_address')->getPostalCode();
        	$fields['ShippingCountry'] = $o->getAttribute('shipping_address')->getCountry();
		}
		
    //$fields['x_ship_to_phone'] = $o->getAttribute('shipping_phone');

		$post_data = "";
		foreach($fields as $key => $value) {
			$post_data .= "$key=" . urlencode($value) . "&";
		}
		$post_data = rtrim($post_data, "& ");
    Log::addEntry("MES Request: ". $post_data);
 
    // Begin Transaction
    
    $tran = new TpgSale( $fields['MerchantID'], $fields['MerchantKey'] );
    $tran->setAvsRequest( $fields['BillingAddress'], $fields['BillingZipCode'] );
    if(strlen($fields['CVV2']) > 1) {
      $tran->setRequestField('cvv2', $fields['CVV2']);
    }
    $tran->setRequestField('invoice_number', $fields['ReferenceNumber']);
    
    $tran->setTransactionData( $fields['CardNumber'], $fields['ExpirationDateMMYY'], $fields['TransactionAmount'] );
    $tran->setHost( $url );
    $tran->execute();
    
    $rsp_data = $tran->ResponseFields;
    
    // End Transaction
    
    //Log::addEntry($rsp_data);
    
		if ($rsp_data['error_code'] == '000' || $rsp_data['error_code'] == 000) {
          $o->setStatus(CoreCommerceOrder::STATUS_AUTHORIZED);
          
          $data = array('Transaction ID'     => $rsp_data['TransactionID'], 
                        'Authorization Code' => $rsp_data['AuthorizationCode']);
          
        	parent::finishOrder($o, 'MES', $data);

			$this->redirect('/checkout/finish');
		} else {
			// Errors that should not be returned to the user (e.g. configuration errors)
			$code_id = $rsp_data['error_code'];
			Log::addEntry('MES Transacation error ('.$code_id.'): '. $this->statusForResponseCode($code_id));
			$err = "This transaction could not be completed at this time.  Please try again later.";
			$this->redirect('/checkout/payment/form?error='.urlencode($err));
		}
	}
	
	public function statusForResponseCode($code) {
    $codes = array(	  
      101 => "Invalid Profile ID or Profile Key. Correct the profile ID and profile key, then resubmit.",
      102 => "Incomplete Request. Provide all required data.",
      103 => "Invoice Number Length Error. Reduce the invoice number length.",
      104 => "Reference Number Length Error. Reduce the reference number length.",
      105 => "AVS Address Length Error. Correct the AVS Address.",
      106 => "AVS Zip Length Error. Correct the AVS Zip.",
      107 => "Merchant Name Length Error. Reduce the merchant name length.",
      108 => "Merchant City Length Error. Reduce the city name length.",
      109 => "Merchant State Length Error. Provide a valid state.",
      110 => "Merchant Zip Length Error. Provide a valid 5 or 9 digit zip."
    );
    $resp = $codes[$code];
    if(!$resp) {
      return "Authorization request failed. xx = Authorization Response Code.";
    }
	  return $resp;
	}
	
	public function to_params($query_string) {
	  $parts = explode("&", $query_string);
	  $params = array();
	  foreach($parts as $part) {
	    $pieces = explode("=", $part);
	    $params[$pieces[0]] = $pieces[1];
	  }
	  return $params;
	}

    public function save() {
        $pkg = Package::getByHandle('core_commerce');
        
        $pkg->saveConfig('PAYMENT_METHOD_MES_MERCHANT_ID', $this->post('PAYMENT_METHOD_MES_MERCHANT_ID'));
        $pkg->saveConfig('PAYMENT_METHOD_MES_MERCHANT_KEY', $this->post('PAYMENT_METHOD_MES_MERCHANT_KEY'));
        $pkg->saveConfig('PAYMENT_METHOD_MES_TRANSACTION_TYPE', $this->post('PAYMENT_METHOD_MES_TRANSACTION_TYPE'));
        $pkg->saveConfig('PAYMENT_METHOD_MES_CCV', $this->post('PAYMENT_METHOD_MES_CCV'));
        $pkg->saveConfig('PAYMENT_METHOD_MES_EMAIL_RECEIPT', $this->post('PAYMENT_METHOD_MES_EMAIL_RECEIPT'));
        $pkg->saveConfig('PAYMENT_METHOD_MES_TEST_MODE', $this->post('PAYMENT_METHOD_MES_TEST_MODE'));
    }
    
}

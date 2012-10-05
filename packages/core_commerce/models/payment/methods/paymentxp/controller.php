<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::library('payment/controller', 'core_commerce');

class CoreCommercePaymentxpPaymentMethodController extends CoreCommercePaymentController {

    public function method_form() {
        $pkg = Package::getByHandle('core_commerce');
        $this->set('PAYMENT_METHOD_PAYMENTXP_MERCHANT_ID',      $pkg->config('PAYMENT_METHOD_PAYMENTXP_MERCHANT_ID'));
        $this->set('PAYMENT_METHOD_PAYMENTXP_MERCHANT_KEY',     $pkg->config('PAYMENT_METHOD_PAYMENTXP_MERCHANT_KEY'));
        $this->set('PAYMENT_METHOD_PAYMENTXP_TRANSACTION_TYPE', $pkg->config('PAYMENT_METHOD_PAYMENTXP_TRANSACTION_TYPE'));
        $this->set('PAYMENT_METHOD_PAYMENTXP_TEST_MODE',        $pkg->config('PAYMENT_METHOD_PAYMENTXP_TEST_MODE'));
        $this->set('PAYMENT_METHOD_PAYMENTXP_CCV',              $pkg->config('PAYMENT_METHOD_PAYMENTXP_CCV'));
        $this->set('PAYMENT_METHOD_PAYMENTXP_EMAIL_RECEIPT',    $pkg->config('PAYMENT_METHOD_PAYMENTXP_EMAIL_RECEIPT'));
    }
    
    public function validate() {
        $e = parent::validate();
        $ve = Loader::helper('validation/strings');
        
        if ($this->post('PAYMENT_METHOD_PAYMENTXP_MERCHANT_ID') == '') {
            $e->add(t('You must specify your merchant id.'));
        }
        if ($this->post('PAYMENT_METHOD_PAYMENTXP_MERCHANT_KEY') == '') {
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
        
        if ($pkg->config('PAYMENT_METHOD_PAYMENTXP_TEST_MODE') == 'test-account' || $pkg->config('PAYMENT_METHOD_PAYMENTXP_TEST_MODE') == 'test-mode') 
        { 
            $url = 'https://webservice.paymentxp.com/wh/webhost.aspx';
        
		        $fields['MerchantID']          = '10012';
    		    $fields['MerchantKey']         = 'c22a63ee-2e7a-4ace-96ac-0958dc8d953f';
    		    $fields['CardNumber']          = '4111111111111111';
    		    $fields['ExpirationDateMMYY']  = '0115';
        } else {
            $url = 'https://webservice.paymentxp.com/wh/webhost.aspx';
            
		        $fields['MerchantID']          = $pkg->config('PAYMENT_METHOD_PAYMENTXP_MERCHANT_ID');
		        $fields['MerchantKey']         = $pkg->config('PAYMENT_METHOD_PAYMENTXP_MERCHANT_KEY');
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
     
		$request = curl_init($url);
		curl_setopt($request, CURLOPT_HEADER, 0); 
    curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($request, CURLOPT_POSTFIELDS, $post_data); //HTTP POST
    curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); 
		
		$data = curl_exec($request);
		curl_close ($request);
    
    $rsp_data = $this->to_params($data);
    
		if ($rsp_data['StatusID'] == '0' || $rsp_data['StatusID'] == 0) {
          $o->setStatus(CoreCommerceOrder::STATUS_AUTHORIZED);
          
          $data = array('Transaction ID'     => $rsp_data['TransactionID'], 
                        'Authorization Code' => $rsp_data['AuthorizationCode']);
          
        	parent::finishOrder($o, 'PaymentXP', $data);

			$this->redirect('/checkout/finish');
		} else {
			Log::addEntry("PaymentXP Request: ". $post_data);
			Log::addEntry("PaymentXP Response: ". $data);
    
			// Errors that should not be returned to the user (e.g. configuration errors)
			$generr = array(0, 5, 7, 8, 9, 16, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28);
			$code_id = $rsp_data['StatusID'];
			Log::addEntry('PaymentXP Transacation error ('.$code_id.'): '. $this->statusForResponseCode($code_id));
			Log::addEntry("PaymentXP Response Message: ". $rsp_data['ResponseMessage']);
			
			$err = "This transaction could not be completed at this time.  Please try again later.  ";
			$err .= $rsp_data['ResponseMessage'];
			
			$this->redirect('/checkout/payment/form?error='.urlencode($err));
		}
	}
	
	public function statusForResponseCode($code) {
	  $codes = array(
	    0  => "APPROVED OR COMPLETED SUCCESSFULLY", 
	    5  => "SYSTEM ERROR", 
	    7  => "INVALID CLERK ID", 
	    8  => "NOT AUTHORIZED", 
	    9  => "INVALID CARD/ACCOUNT NUMBER", 
	    16 => "INVALID TRANSACTION", 
	    19 => "PROCESSOR/PROVIDER DENIAL", 
	    20 => "TIMEOUT", 
	    21 => "AVS ZIPCODE NO MATCH", 
	    22 => "AVS ADDRESS NO MATCH", 
	    23 => "AVS ZIPCODE AND ADDRESS NO MATCH", 
	    24 => "AVS INELIGIBLE TRANSACTION", 
	    25 => "AVS SYSTEM UNAVAILABLE",
	    26 => "CVV NO MATCH", 
	    27 => "CVV NO PROCESSED", 
	    28 => "CVV ISSUER NO REGISTERED");
	  return $codes[$code];
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
        
        $pkg->saveConfig('PAYMENT_METHOD_PAYMENTXP_MERCHANT_ID', $this->post('PAYMENT_METHOD_PAYMENTXP_MERCHANT_ID'));
        $pkg->saveConfig('PAYMENT_METHOD_PAYMENTXP_MERCHANT_KEY', $this->post('PAYMENT_METHOD_PAYMENTXP_MERCHANT_KEY'));
        $pkg->saveConfig('PAYMENT_METHOD_PAYMENTXP_TRANSACTION_TYPE', $this->post('PAYMENT_METHOD_PAYMENTXP_TRANSACTION_TYPE'));
        $pkg->saveConfig('PAYMENT_METHOD_PAYMENTXP_CCV', $this->post('PAYMENT_METHOD_PAYMENTXP_CCV'));
        $pkg->saveConfig('PAYMENT_METHOD_PAYMENTXP_EMAIL_RECEIPT', $this->post('PAYMENT_METHOD_PAYMENTXP_EMAIL_RECEIPT'));
        $pkg->saveConfig('PAYMENT_METHOD_PAYMENTXP_TEST_MODE', $this->post('PAYMENT_METHOD_PAYMENTXP_TEST_MODE'));
    }
    
}

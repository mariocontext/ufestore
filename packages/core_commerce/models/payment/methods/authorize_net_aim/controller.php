<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::library('payment/controller', 'core_commerce');
class CoreCommerceAuthorizeNetAIMPaymentMethodController extends CoreCommercePaymentController {

    public function method_form() {
        $pkg = Package::getByHandle('core_commerce');
        $this->set('PAYMENT_METHOD_AUTHORIZENET_AIM_API_LOGIN', $pkg->config('PAYMENT_METHOD_AUTHORIZENET_AIM_API_LOGIN'));
        $this->set('PAYMENT_METHOD_AUTHORIZENET_AIM_TRANSACTION_KEY', $pkg->config('PAYMENT_METHOD_AUTHORIZENET_AIM_TRANSACTION_KEY'));
        $this->set('PAYMENT_METHOD_AUTHORIZENET_AIM_TRANSACTION_TYPE', $pkg->config('PAYMENT_METHOD_AUTHORIZENET_AIM_TRANSACTION_TYPE'));
        $this->set('PAYMENT_METHOD_AUTHORIZENET_AIM_CCV', $pkg->config('PAYMENT_METHOD_AUTHORIZENET_AIM_CCV'));
        $this->set('PAYMENT_METHOD_AUTHORIZENET_AIM_EMAIL_RECEIPT', $pkg->config('PAYMENT_METHOD_AUTHORIZENET_AIM_EMAIL_RECEIPT'));
        $this->set('PAYMENT_METHOD_AUTHORIZENET_AIM_TEST_MODE', $pkg->config('PAYMENT_METHOD_AUTHORIZENET_AIM_TEST_MODE'));
    }
    
    public function validate() {
        $e = parent::validate();
        $ve = Loader::helper('validation/strings');
        
        if ($this->post('PAYMENT_METHOD_AUTHORIZENET_AIM_API_LOGIN') == '') {
            $e->add(t('You must specify your API Login.'));
        }
        if ($this->post('PAYMENT_METHOD_AUTHORIZENET_AIM_TRANSACTION_KEY') == '') {
            $e->add(t('You must specify your transaction key.'));
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
        if ($pkg->config('PAYMENT_METHOD_AUTHORIZENET_AIM_TEST_MODE') == 'test-account') { 
            $url = 'https://test.authorize.net/gateway/transact.dll';
        } else {
            $url = 'https://secure.authorize.net/gateway/transact.dll';
        }

		$fields = $_POST;
        $fields['x_version'] = '3.1';
        $fields['x_login'] = $pkg->config('PAYMENT_METHOD_AUTHORIZENET_AIM_API_LOGIN');
        $fields['x_tran_key'] = $pkg->config('PAYMENT_METHOD_AUTHORIZENET_AIM_TRANSACTION_KEY');
        $fields['x_type'] = $pkg->config('PAYMENT_METHOD_AUTHORIZENET_AIM_TRANSACTION_TYPE') == 'authorization' ? 'AUTH_ONLY' : 'AUTH_CAPTURE';
        $fields['x_relay_response'] = false;
		$fields['x_delim_data'] = true;
		$fields['x_delim_char'] = ',';
        $fields['x_test_request'] = ($pkg->config('PAYMENT_METHOD_AUTHORIZENET_AIM_API_LOGIN') === 'test-mode' ? 'true' : 'false');

        Loader::model('order/current', 'core_commerce');
        $o = CoreCommerceCurrentOrder::get();
        $fields['x_amount'] = number_format($o->getOrderTotal(), 2);
        $fields['x_description'] = t('Purchase from %s', SITE);
        $fields['x_invoice_num'] = date('ymdGis'.$u->getUserID());

        $eh = Loader::helper('encryption');
        $fields['orderID'] = $o->getOrderID();

        $ui = UserInfo::getByID($u->getUserID());
        if ($ui && $ui->getUserEmail()) {
            $fields['x_email'] = $ui->getUserEmail();
        	if ($pkg->config('PAYMENT_METHOD_AUTHORIZENET_AIM_EMAIL_RECEIPT') == 'true') {
            	$fields['x_email_customer'] = 'true';
			}
        }

        $fields['x_first_name'] = $o->getAttribute('billing_first_name');
        $fields['x_last_name'] = $o->getAttribute('billing_last_name');
        $addr2 = $o->getAttribute('billing_address')->getAddress2();
        if (!empty($addr2)) {
            $fields['x_company'] = $o->getAttribute('billing_address')->getAddress1();
            $fields['x_address'] = $addr2;
        } else {
            $fields['x_address'] = $o->getAttribute('billing_address')->getAddress1();
        }
        $fields['x_city'] = $o->getAttribute('billing_address')->getCity();
        $fields['x_state'] = $o->getAttribute('billing_address')->getStateProvince();
        $fields['x_zip'] = $o->getAttribute('billing_address')->getPostalCode();
        $fields['x_country'] = $o->getAttribute('billing_address')->getCountry();
        $fields['x_phone'] = $o->getAttribute('billing_phone');

        $fields['x_ship_to_first_name'] = $o->getAttribute('shipping_first_name');
        $fields['x_ship_to_last_name'] = $o->getAttribute('shipping_last_name');
		if ($o->getAttribute('shipping_address')) {
        	$addr2 = $o->getAttribute('shipping_address')->getAddress2();
        	if (!empty($addr2)) {
            	$fields['x_ship_to_company'] = $o->getAttribute('shipping_address')->getAddress1();
            	$fields['x_ship_to_address'] = $addr2;
        	} else {
            	$fields['x_ship_to_address'] = $o->getAttribute('shipping_address')->getAddress1();
        	}
        	$fields['x_ship_to_city'] = $o->getAttribute('shipping_address')->getCity();
        	$fields['x_ship_to_state'] = $o->getAttribute('shipping_address')->getStateProvince();
        	$fields['x_ship_to_zip'] = $o->getAttribute('shipping_address')->getPostalCode();
        	$fields['x_ship_to_country'] = $o->getAttribute('shipping_address')->getCountry();
		}
        $fields['x_ship_to_phone'] = $o->getAttribute('shipping_phone');

		$post_data = "";
		foreach($fields as $key => $value) {
			$post_data .= "$key=" . urlencode($value) . "&";
		}
		$post_data = rtrim($post_data, "& ");

		$request = curl_init($url);
		curl_setopt($request, CURLOPT_HEADER, 0);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($request, CURLOPT_POSTFIELDS, $post_data);
		//curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment this line if you get no gateway response.
		$data = curl_exec($request);
		curl_close ($request);

		$rsp_data = explode($fields['x_delim_char'], $data);

		if ($rsp_data[0] == '1') {
            if (strtolower($fields['x_type']) == 'auth_only') {
                $o->setStatus(CoreCommerceOrder::STATUS_PENDING);
            } else if (strtolower($fields['x_type']) == 'auth_capture') {
                $o->setStatus(CoreCommerceOrder::STATUS_AUTHORIZED);
            }

        	$data = array('Invoice'=>$fields['x_invoice_num'], 'Auth Code'=>$rsp_data[4], 'Transaction ID'=>$rsp_data[6]);
        	parent::finishOrder($o, 'Authorize.Net - AIM', $data);

			$this->redirect('/checkout/finish');
		} else {
			// Errors that should not be returned to the user (e.g. configuration errors)
			$generr = array(13, 24, 29, 30, 31, 34, 35, 38, 40, 43, 47, 48, 49, 68, 69, 70, 71, 81, 82, 83);
			if (in_array($rsp_data[2], $generr)) {
				$err = array('orderID' => $o->orderID, 'responseCode' => $rsp_data[1],
				             'responseReasonCode' => $rsp_data[2], 'responseReasonText' => $rsp_data[3]);
				Log::addEntry('Authorize.Net transacation error: '.var_export($err,true));
				$err = "This transaction could not be completed at this time.  Please try again later.";
			} else {
				$err = $rsp_data[3];
			}
			$this->redirect('/checkout/payment/form?error='.urlencode($err));
		}
	}

    public function save() {
        $pkg = Package::getByHandle('core_commerce');
        $pkg->saveConfig('PAYMENT_METHOD_AUTHORIZENET_AIM_API_LOGIN', $this->post('PAYMENT_METHOD_AUTHORIZENET_AIM_API_LOGIN'));
        $pkg->saveConfig('PAYMENT_METHOD_AUTHORIZENET_AIM_TRANSACTION_KEY', $this->post('PAYMENT_METHOD_AUTHORIZENET_AIM_TRANSACTION_KEY'));
        $pkg->saveConfig('PAYMENT_METHOD_AUTHORIZENET_AIM_TRANSACTION_TYPE', $this->post('PAYMENT_METHOD_AUTHORIZENET_AIM_TRANSACTION_TYPE'));
        $pkg->saveConfig('PAYMENT_METHOD_AUTHORIZENET_AIM_CCV', $this->post('PAYMENT_METHOD_AUTHORIZENET_AIM_CCV'));
        $pkg->saveConfig('PAYMENT_METHOD_AUTHORIZENET_AIM_EMAIL_RECEIPT', $this->post('PAYMENT_METHOD_AUTHORIZENET_AIM_EMAIL_RECEIPT'));
        $pkg->saveConfig('PAYMENT_METHOD_AUTHORIZENET_AIM_TEST_MODE', $this->post('PAYMENT_METHOD_AUTHORIZENET_AIM_TEST_MODE'));
    }
    
}

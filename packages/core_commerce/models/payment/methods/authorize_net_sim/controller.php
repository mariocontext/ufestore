<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::library('payment/controller', 'core_commerce');
class CoreCommerceAuthorizeNetSIMPaymentMethodController extends CoreCommercePaymentController {

    public function method_form() {
        $pkg = Package::getByHandle('core_commerce');
        $this->set('PAYMENT_METHOD_AUTHORIZENET_SIM_API_LOGIN', $pkg->config('PAYMENT_METHOD_AUTHORIZENET_SIM_API_LOGIN'));
        $this->set('PAYMENT_METHOD_AUTHORIZENET_SIM_TRANSACTION_KEY', $pkg->config('PAYMENT_METHOD_AUTHORIZENET_SIM_TRANSACTION_KEY'));
        $this->set('PAYMENT_METHOD_AUTHORIZENET_SIM_MD5_SECRET', $pkg->config('PAYMENT_METHOD_AUTHORIZENET_SIM_MD5_SECRET'));
        $this->set('PAYMENT_METHOD_AUTHORIZENET_SIM_TEST_MODE', $pkg->config('PAYMENT_METHOD_AUTHORIZENET_SIM_TEST_MODE'));
        $this->set('PAYMENT_METHOD_AUTHORIZENET_SIM_TRANSACTION_TYPE', $pkg->config('PAYMENT_METHOD_AUTHORIZENET_SIM_TRANSACTION_TYPE'));
        $this->set('PAYMENT_METHOD_AUTHORIZENET_SIM_EMAIL_RECEIPT', $pkg->config('PAYMENT_METHOD_AUTHORIZENET_SIM_EMAIL_RECEIPT'));
    }
    
    public function validate() {
        $e = parent::validate();
        $ve = Loader::helper('validation/strings');
        
        if ($this->post('PAYMENT_METHOD_AUTHORIZENET_SIM_API_LOGIN') == '') {
            $e->add(t('You must specify your API Login.'));
        }
        if ($this->post('PAYMENT_METHOD_AUTHORIZENET_SIM_TRANSACTION_KEY') == '') {
            $e->add(t('You must specify your transaction key.'));
        }
        if (   $this->post('PAYMENT_METHOD_AUTHORIZENET_SIM_TEST_MODE') != 'test-account'
			&& $this->post('PAYMENT_METHOD_AUTHORIZENET_SIM_MD5_SECRET') == '') {
            $e->add(t('You must specify your MD5 secret key.'));
        }

        return $e;
    }
    
    public function action_notify_complete() {
        Loader::model('order/model', 'core_commerce');

		$verified = false;
        do {
        	$pkg = Package::getByHandle('core_commerce');

        	$eh = Loader::helper('encryption');
        	$orderID = $eh->decrypt($_REQUEST['orderID']);
        	$o = CoreCommerceOrder::getByID($orderID);
			if (!$o) {
            	Log::addEntry('Received order notification with unknown order: '.$orderID);
				break;
			}

			// TBD: Don't know what the MD5 secret is for the test server....
			if ($pkg->config('PAYMENT_METHOD_AUTHORIZENET_SIM_TEST_MODE') != 'test-account') {
				$hash = $this->getVerificationHash($_POST['x_trans_id'], $o->getOrderTotal());
				if ($hash != $_POST['x_MD5_Hash']) {
                    Log::addEntry('Unable to verify transaction for order #' . $orderID);
					break;
				}
			}

            if (strtolower($_POST['x_type']) == 'auth_only') {
                $o->setStatus(CoreCommerceOrder::STATUS_PENDING);
            } else if (strtolower($_POST['x_type']) == 'auth_capture') {
                $o->setStatus(CoreCommerceOrder::STATUS_AUTHORIZED);
            }

			$verified = true;
        } while (0);
	
		if ($verified) {
			$message = "Your purchase is now complete. You will now be returned to '".SITE.".";
			$link = BASE_URL . View::url('/checkout/finish');
		} else {
			$message = "An error occured while processing your credit card.  We are unable to complete your order at this time.  Please try again.";
			$link = BASE_URL . View::url('/checkout');
		}

		$data = array('Invoice'=>$_POST['x_invoice_num'], 'Auth Code'=>$_POST['x_auth_code'], 'Transaction ID'=>$_POST['x_trans_id']);
		parent::finishOrder($o, 'Authorize.Net - SIM', $data);

		echo $this->getFinishText(SITE, $message, $link);
    }
    
    public function form() {
        $pkg = Package::getByHandle('core_commerce');
        if ($pkg->config('PAYMENT_METHOD_AUTHORIZENET_SIM_TEST_MODE') == 'test-account') { 
            $this->set('action', 'https://test.authorize.net/gateway/transact.dll');
        } else {
            $this->set('action', 'https://secure.authorize.net/gateway/transact.dll');
        }
        $o = CoreCommerceCurrentOrder::get();
        $u = new User();

        $fields['x_version'] = '3.1';
        $fields['x_login'] = $pkg->config('PAYMENT_METHOD_AUTHORIZENET_SIM_API_LOGIN');
        $fields['x_invoice_num'] = date('ymdGis'.$u->getUserID());
        $fields['x_amount'] = number_format($o->getOrderTotal(), 2);
        $fields['x_description'] = t('Purchase from %s', SITE);

        $fields['x_fp_sequence'] = rand();
        $fields['x_fp_timestamp'] = time();
        $fields['x_fp_hash'] = $this->getFingerPrint($fields['x_fp_sequence'], $fields['x_amount'], $fields['x_fp_timestamp']);
        $fields['x_test_request'] = ($pkg->config('PAYMENT_METHOD_AUTHORIZENET_SIM_API_LOGIN') === 'test-mode' ? 'true' : 'false');
        $fields['x_type'] = $pkg->config('PAYMENT_METHOD_AUTHORIZENET_SIM_TRANSACTION_TYPE') == 'authorization' ? 'AUTH_ONLY' : 'AUTH_CAPTURE';
        $fields['x_show_form'] = 'PAYMENT_FORM';

        $fields['x_relay_response'] = true;
        $fields['x_relay_url'] = $this->action('notify_complete');        

        $eh = Loader::helper('encryption');
        $fields['orderID'] = $eh->encrypt($o->getOrderID());

        $ui = UserInfo::getByID($u->getUserID());
        if ($ui && $ui->getUserEmail()) {
            $fields['x_email'] = $ui->getUserEmail();
			if ($pkg->config('PAYMENT_METHOD_AUTHORIZENET_SIM_EMAIL_RECEIPT') == 'true') {
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

        $this->set('fields', $fields);
        return;
    }
        
    public function save() {
        $pkg = Package::getByHandle('core_commerce');
        $pkg->saveConfig('PAYMENT_METHOD_AUTHORIZENET_SIM_API_LOGIN', $this->post('PAYMENT_METHOD_AUTHORIZENET_SIM_API_LOGIN'));
        $pkg->saveConfig('PAYMENT_METHOD_AUTHORIZENET_SIM_TRANSACTION_KEY', $this->post('PAYMENT_METHOD_AUTHORIZENET_SIM_TRANSACTION_KEY'));
        $pkg->saveConfig('PAYMENT_METHOD_AUTHORIZENET_SIM_MD5_SECRET', $this->post('PAYMENT_METHOD_AUTHORIZENET_SIM_MD5_SECRET'));
        $pkg->saveConfig('PAYMENT_METHOD_AUTHORIZENET_SIM_TEST_MODE', $this->post('PAYMENT_METHOD_AUTHORIZENET_SIM_TEST_MODE'));
        $pkg->saveConfig('PAYMENT_METHOD_AUTHORIZENET_SIM_TRANSACTION_TYPE', $this->post('PAYMENT_METHOD_AUTHORIZENET_SIM_TRANSACTION_TYPE'));
        $pkg->saveConfig('PAYMENT_METHOD_AUTHORIZENET_SIM_EMAIL_RECEIPT', $this->post('PAYMENT_METHOD_AUTHORIZENET_SIM_EMAIL_RECEIPT'));
    }
    

    private function getFingerprint($sequence, $amount, $timestamp) {
        $pkg = Package::getByHandle('core_commerce');
        $login = $pkg->config('PAYMENT_METHOD_AUTHORIZENET_SIM_API_LOGIN');
        $key = $pkg->config('PAYMENT_METHOD_AUTHORIZENET_SIM_TRANSACTION_KEY');
        if (phpversion() >= '5.1.2') {
            $fingerprint = hash_hmac("md5", $login . "^" . $sequence . "^" . $timestamp . "^" . $amount . "^", $key);
        } else {
             $fingerprint = bin2hex(mhash(MHASH_MD5, $login . "^" . $sequence . "^" . $timestamp . "^" . $amount . "^", $key));
        }
        return $fingerprint;
    }

    private function getVerificationHash($transactionID, $amount) {
        $pkg = Package::getByHandle('core_commerce');
        $login = $pkg->config('PAYMENT_METHOD_AUTHORIZENET_SIM_API_LOGIN');
        $key = $pkg->config('PAYMENT_METHOD_AUTHORIZENET_SIM_MD5_SECRET');
		$amount = preg_replace('|\,|', '', number_format($amount, 2));
        $hash = strtoupper(md5($key . $login . $transactionID . $amount));
        return $hash;
	}

	private function getFinishText($title, $message, $link) {
		$txt = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/2000/REC-xhtml1-20000126/DTD/xhtml1-transitional.dtd">
<html>
<head>
<style type="text/css">
body { background: #4D4D4D; }
.ccm-dialog-window div { font: 12px "Helvetica Neue", Arial, Helvetica, sans-serif; }
.ccm-dialog-window a { color: #003C8A; cursor:pointer }
.ccm-dialog-window .ccm-dialog-title-bar-l,.ccm-dialog-window #ccm-dialog-content-wrapper { position:relative }
.ccm-dialog-window { position: relative; color:#333; display:none; text-align:left; width:380px; }
.ccm-dialog-title { float:left; padding:8px 0 5px 0px; margin-bottom:1px; color: #666; position: relative; font-weight: bold !important; font-family: "Lucida Grande", Arial, Helvetica; }
.ccm-dialog-title-bar-l {background: transparent url('.BASE_URL.ASSETS_URL_IMAGES.'/bg_dialog_tl.png) no-repeat scroll 0 0; padding-left: 22px;}
.ccm-dialog-title-bar-r {background: transparent url('.BASE_URL.ASSETS_URL_IMAGES.'/bg_dialog_tr.png) no-repeat scroll 100% 0; padding-right: 22px}
.ccm-dialog-title-bar { background:#eee url('.BASE_URL.ASSETS_URL_IMAGES.'/bg_dialog_t.png) right repeat-x; height:30px; color: #666666; font-weight: bold; font-size: 12px; }
.ccm-dialog-content-b {background: transparent url('.BASE_URL.ASSETS_URL_IMAGES.'/bg_dialog_b.png) repeat-x scroll 0 0; height: 27px}
.ccm-dialog-content-bl {background: transparent url('.BASE_URL.ASSETS_URL_IMAGES.'/bg_dialog_bl.png) no-repeat scroll 0 100%; padding-left: 22px;}
.ccm-dialog-content-br {background: transparent url('.BASE_URL.ASSETS_URL_IMAGES.'/bg_dialog_br.png) no-repeat scroll 100% 100%; padding-right: 22px}
.ccm-dialog-content-l {background: transparent url('.BASE_URL.ASSETS_URL_IMAGES.'/bg_dialog_l.png) repeat-y scroll 0 0; padding-left: 22px;}
.ccm-dialog-content-r {background: transparent url('.BASE_URL.ASSETS_URL_IMAGES.'/bg_dialog_r.png) repeat-y scroll 100% 0; padding-right: 22px}
.ccm-dialog-content { clear:both; padding:8px 8px 0px 0px; overflow:auto; overflow-x: hidden; overflow-y: auto; text-align:left; background-color: #fafafa; line-height:1.4em; font-family: "Lucida Grande", Arial, Helvetica; font-size: 12px; }
.ccm-dialog-content p { padding:5px 0px 5px 0px; }
.ccm-buttons {clear: both; padding-top: 8px}
.ccm-button {float: left; margin-right: 10px}
.ccm-button {display: block; text-decoration: none !important; height: 38px !important; background: transparent url('.BASE_URL.ASSETS_URL_IMAGES.'/button_l.png) no-repeat;}
.ccm-button:hover {background: transparent url('.BASE_URL.ASSETS_URL_IMAGES.'/button_l_active.png) no-repeat scroll;}
.ccm-button span {white-space: nowrap; height: 12px; display: block; float: left; padding: 11px 16px 15px 0px; margin-left: 15px; font-size: 11px; color: #535353; background: transparent url('.BASE_URL.ASSETS_URL_IMAGES.'/button_r.png) repeat-y scroll right top;}
.ccm-button:hover span {background: transparent url('.BASE_URL.ASSETS_URL_IMAGES.'/button_r_active.png) repeat-y scroll right top;}
</style>
</head>
<body>
  <div style="margin: 120px auto; width: 380px; display: block;" class="ccm-dialog-window">
    <div style="cursor: move;" class="ccm-dialog-title-bar-l">
      <div class="ccm-dialog-title-bar-r">
        <div class="ccm-dialog-title-bar">
          <div class="ccm-dialog-title">'.$title.'</div>
        </div>
      </div>
    </div>
    <div class="ccm-dialog-content-l">
      <div class="ccm-dialog-content-r">
        <div style="width: 336px; height: 120px;" class="ccm-dialog-content">
          <p>'.$message.'</p>
          <div class="ccm-buttons">
            <a href="'.$link.'" class="ccm-button"><span>Okay</span></a>
          </div>
        </div>
      </div>
    </div>
    <div class="ccm-dialog-content-bl"><div class="ccm-dialog-content-br"><div class="ccm-dialog-content-b"/></div></div></div>
 </div>
</body>
</html>';
		return $txt;
	}

}

<?php   defined('C5_EXECUTE') or die(_("Access Denied."));
	Loader::model('payment/method', 'core_commerce');
	$pay = CoreCommercePaymentMethod::getByID($_REQUEST['paymentMethodID']);
	if (is_object($pay)) {
		$cnt = $pay->getController();
		if(!is_array($_REQUEST['args'])) {
			$_REQUEST['args'] = array();
		}
		
		call_user_func_array(array($cnt, 'action_' . $_REQUEST['action']), $_REQUEST['args']);
	
	}
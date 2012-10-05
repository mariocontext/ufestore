<?php   

class DashboardCoreCommerceSettingsController extends Controller {

	function save_general() {
		if ($this->isPost()) {
			$pkg = Package::getByHandle('core_commerce');
			$pkg->saveConfig('CURRENCY_SYMBOL', $this->post('CURRENCY_SYMBOL'));
			$pkg->saveConfig('CURRENCY_THOUSANDS_SEPARATOR', $this->post('CURRENCY_THOUSANDS_SEPARATOR'));
			$pkg->saveConfig('CURRENCY_DECIMAL_POINT', $this->post('CURRENCY_DECIMAL_POINT'));
			$pkg->saveConfig('ENABLE_ORDER_NOTIFICATION_EMAILS', $this->post('ENABLE_ORDER_NOTIFICATION_EMAILS')?'1':'0');
			$emails = preg_split("/[\s,]+/", $this->post('ENABLE_ORDER_NOTIFICATION_EMAIL_ADDRESSES'));
			$pkg->saveConfig('ENABLE_ORDER_NOTIFICATION_EMAIL_ADDRESSES', implode(',', $emails));
			$pkg->saveConfig('RECEIPT_EMAIL_BLURB', $this->post('RECEIPT_EMAIL_BLURB'));
	
			$pkg->saveConfig('EMAIL_RECEIPT_EMAIL', $this->post('EMAIL_RECEIPT_EMAIL'));
			$pkg->saveConfig('EMAIL_RECEIPT_NAME', $this->post('EMAIL_RECEIPT_NAME'));
			$pkg->saveConfig('EMAIL_NOTIFICATION_EMAIL', $this->post('EMAIL_NOTIFICATION_EMAIL'));
			$pkg->saveConfig('EMAIL_NOTIFICATION_NAME', $this->post('EMAIL_NOTIFICATION_NAME'));
		}
        
        $this->set('message', t('General Settings Updated.'));
	}

	function save_security() {
        $pkg = Package::getByHandle('core_commerce');
        $pkg->saveConfig('SECURITY_USE_SSL', $this->post('SECURITY_USE_SSL'));
		Config::save('BASE_URL_SSL', trim($this->post('BASE_URL_SSL'), '/'));
	}

	function save_inventory() {
        $pkg = Package::getByHandle('core_commerce');
        $pkg->saveConfig('MANAGE_INVENTORY', $this->post('MANAGE_INVENTORY'));
        $pkg->saveConfig('MANAGE_INVENTORY_TRIGGER', $this->post('MANAGE_INVENTORY_TRIGGER'));
        $this->set("message", t('Inventory settings saved.'));
	}

}

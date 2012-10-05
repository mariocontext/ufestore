<?php 
class DashboardCoreCommerceVsSettingsController extends Controller {
	public $helpers = array("form");
	public function save_api() {
		$pkg = Package::getByHandle('commerce_vs_purchase');
        $pkg->saveConfig('VS_API_KEY', $this->post('VS_API_KEY'));
        $pkg->saveConfig('VS_API_URL', $this->post('VS_API_URL'));  
        $this->set('message',t('Settings have been saved.'));
	}

	
	public function test_api() {
		Loader::model('vs_purchase','commerce_vs_purchase');
		$v = new VsPurchase();
		$test = $v->sendPurchaseInfo('ryan@concrete5.org',array(1),'test','user');
		echo var_dump($test);
		exit;
	}
	
	public function view() {
		$pkg = Package::getByHandle('commerce_vs_purchase');
		$this->set('vs_api_key',$pkg->config('VS_API_KEY'));
		$this->set('vs_api_url',$pkg->config('VS_API_URL'));
	}
}
?>
<?php   defined('C5_EXECUTE') or die(_("Access Denied."));

class CoreCommerceUrlsHelper { 

	public function getToolsURL($tool, $includeBase = false) {
		
		if ($includeBase) {
			$pkg = Package::getByHandle('core_commerce');
			if ($pkg->config('SECURITY_USE_SSL') == 'true') {
				$base_url = Config::get('BASE_URL_SSL');
			} else {
				$base_url = BASE_URL;
			}
		} else {
			$base_url = '';
		}
		$uh = Loader::helper('concrete/urls');
		return $base_url . $uh->getToolsURL($tool, 'core_commerce');
	}
	
}

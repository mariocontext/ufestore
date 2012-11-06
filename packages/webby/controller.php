<?php        

defined('C5_EXECUTE') or die(_("Access Denied."));

class WebbyPackage extends Package {

	protected $pkgHandle = 'webby';
	protected $appVersionRequired = '5.3.0';
	protected $pkgVersion = '1.0';
	
	public function getPackageDescription() {
		return t("Installs Webby theme.");
	}
	
	public function getPackageName() {
		return t("Webby theme");
	}
	
	public function install() {
		$pkg = parent::install();
		
		// install block		
		PageTheme::add('webby', $pkg);		
	}

}
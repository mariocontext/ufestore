<?php     

defined('C5_EXECUTE') or die(_("Access Denied."));

class ThemeUfePackage extends Package {

	protected $pkgHandle = 'theme_ufe';
	protected $appVersionRequired = '5.1';
	protected $pkgVersion = '1.0.2';
	
	public function getPackageDescription() {
		return t("Installs UFE theme");
	}
	
	public function getPackageName() {
		return t("Ufe");
	}
	
	public function install() {
		$pkg = parent::install();
		// install block		
		PageTheme::add('ufe', $pkg);		
	}




}
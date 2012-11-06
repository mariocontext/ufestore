<?php  defined('C5_EXECUTE') or die(_("Access Denied."));

/**
 * @author Ryan Tyler Concrete5.org
 *
 */
class CoreCommerceDownloadableFilePackage extends Package {

	/**
	 * @var string
	 */
	protected $pkgHandle = 'core_commerce_downloadable_file';
	
	/**
	 * @var string
	 */
	protected $appVersionRequired = '5.3.3';

	/**
	 * @var string
	 */
	protected $pkgVersion = '1.0.3';
	
	
	public function getPackageDescription() {
		return t('Allows purchasing of downloadable files.');
	}
	
	public function getPackageName() {
		return t('Downloadable File Purchase');
	}
	
	/**
	 * @return void
	 */
	public function on_start() {
		// register on_order_complete function
		Events::extend('core_commerce_on_checkout_finish_order',
						'DownloadableProduct',
						'onPurchaseComplete',
						'packages/'.$this->pkgHandle.'/models/downloadable_product.php',
						array($ui,$order));
	
		Events::extend('core_commerce_on_checkout_start',
						'DownloadableProduct',
						'checkoutSetup',
						'packages/'.$this->pkgHandle.'/models/downloadable_product.php',
						array($checkoutController));
	}
	
	/* (non-PHPdoc)
	 * @see concrete/models/Package#install()
	 */
	public function install() {
		// verify eCommerce addon is installed
		$installed = Package::getInstalledHandles();
		
		if( !(is_array($installed) && in_array('core_commerce',$installed)) ) {
			throw new Exception(t('This package requires that at least version 1.7.1 of the <a href="http://www.concrete5.org/marketplace/addons/ecommerce/" target="_blank">eCommerce package</a> is installed<br/>'));	
		}
		
		$pkg = Package::getByHandle('core_commerce');
		if (!is_object($pkg) || version_compare($pkg->getPackageVersion(), '1.7.1', '<')) {
			throw new Exception(t('You must upgrade the eCommerce add-on to version 1.7.1 or higher.'));
		}
		
		$pkg = parent::install();
		
		Loader::model('attribute/categories/core_commerce_product', 'core_commerce');
		
		$dpf = AttributeType::getByHandle('downloadable_product_file');
		if($dpf->getAttributeTypeHandle() != 'downloadable_product_file') { // prevent errors on uninstall -> install process
			$dpf = AttributeType::add('downloadable_product_file', t('Downloadable Product File'), $pkg);	
			// get attribute category for product option
			$pock = AttributeKeyCategory::getByHandle('core_commerce_product');
			$pock->associateAttributeKeyType($dpf);
			// add a sample one
			CoreCommerceProductAttributeKey::add('downloadable_product_file', array('akHandle' => 'downloadable_product_file', 'akName' => t('File to Download'), 'akIsSearchable' => false,'akIsSearchableIndexed'=>false));
		}
		
		Loader::model('single_page');
		$p = Page::getByPath('/checkout/success_download');
		if ($p->isError() || (!is_object($p))) {
			SinglePage::add('/checkout/success_download', $pkg);
		}
	}
	
	
	/* (non-PHPdoc)
	 * @see concrete/models/Package#uninstall()
	*/
	public function uninstall() {
		
		return parent::uninstall();
	}
	
}

?>

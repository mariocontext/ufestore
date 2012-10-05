<?php  defined('C5_EXECUTE') or die(_("Access Denied."));

/**
 * @author James OKelly Vertical Streaming
 *
 */
class CommerceVsPurchasePackage extends Package {

	/**
	 * @var string
	 */
	protected $pkgHandle = 'commerce_vs_purchase';
	/**
	 * @var string
	 */
	protected $appVersionRequired = '5.3.3';

	/**
	 * @var string
	 */
	protected $pkgVersion = '1.0';
	
	
	public function getPackageDescription() {
		return t('Authenticates Vertical Streaming Purchases');
	}
	
	public function getPackageName() {
		return t('Vertical Streaming Purchase');
	}
	
	/**
	 * @return void
	 */
	public function on_start() {
		// register on_order_complete function
		Events::extend('core_commerce_on_checkout_finish_order',
						'VsPurchase',
						'onPurchaseComplete',
						'packages/'.$this->pkgHandle.'/models/vs_purchase.php',
						array($ui,$order));
	}
	
	/* (non-PHPdoc)
	 * @see concrete/models/Package#install()
	 */
	public function install() {
		// verify eCommerce addon is installed
		$installed = Package::getInstalledHandles();
		if( !(is_array($installed) && in_array('core_commerce',$installed)) ) {
			throw new Exception(t('This package requires that the <a href="http://www.concrete5.org/marketplace/addons/core_commerce/" target="_blank">eCommerce package</a> is installed<br/>'));	
		}
		
		$pkg = parent::install();
		
		$pkg->saveConfig('VS_API_KEY', '');
        $pkg->saveConfig('VS_API_URL', 'https://distancelearningcenter.com/ext-auth.xml');
     
		Loader::model('single_page');
		Loader::model('collection_types');
		Loader::model('collection_attributes');
		
		// get attribute category for product option
		$pock = AttributeKeyCategory::getByHandle('core_commerce_product_option');
		
		Loader::model('collection_types');
		
		// add product attribute
		Loader::model('product/model', 'core_commerce');
		Loader::model("attribute/categories/core_commerce_product", 'core_commerce');
		//Loader::model('attribute/categories/core_commerce_product_option', 'core_commerce');
	
		$ak_prod_id = CoreCommerceProductAttributeKey::getByHandle('vs_prod_id');	
		if(!is_object($ak_prod_id)) {
			$ak_prod_id = CoreCommerceProductAttributeKey::add('NUMBER', array('akHandle' => 'vs_prod_id', 'akName' => t('Vertical Streaming Product ID'), 'akIsSearchable' => true,'akIsSearchableIndexed'=>false));
		}

		// add order attribute
		Loader::model('order/model', 'core_commerce');
		Loader::model("attribute/categories/core_commerce_order", 'core_commerce');
		$vs_order_response = CoreCommerceOrderAttributeKey::getByHandle('vs_order_response');	
		if(!is_object($vs_order_response)) {
			$vs_order_response = CoreCommerceOrderAttributeKey::add('TEXTAREA', array('akHandle' => 'vs_order_response', 'akName' => t('Vertical Streaming Order Response'), 'akIsSearchable' => true,'akIsSearchableIndexed'=>false));
		}
		
		// add page for configuration
		$dp = SinglePage::add('/dashboard/core_commerce/vs_settings', $pkg);
		$dp->update(array('cName'=>"Vertical Streaming Settings", 'cDescription'=>t('Vertical Streaming Settings')));
	}
	
	
	/* (non-PHPdoc)
	 * @see concrete/models/Package#uninstall()
	 */
	public function uninstall() {
		
		return parent::uninstall();
	}
	
}

?>

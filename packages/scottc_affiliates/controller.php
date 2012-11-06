<?php  defined('C5_EXECUTE') or die(_("Access Denied."));
/**
 * @author Scott Conrad <scott.conrads@gmail.com>
 * @copyright  Copyright (c) 2010-2020 Scott Conrad.
 * @license    Single End User License / Standard Concrete5 Marketplace License Agreement
 * @category eCommerce Affiliate
 */
Loader::model('single_page');
class ScottcAffiliatesPackage extends Package {
	protected $pkgHandle = 'scottc_affiliates';
	protected $appVersionRequired = '5.4.1';
	protected $pkgVersion = '1.0.2';
	public
	function getPackageDescription() {

		return t("Add and Track Affiliates/Earnings - Track Abandoned Carts");
	}
	public
	function getPackageName() {

		return t("eCommerce Affiliate Program");
	}
  function getPackageHandle(){
    return 'scottc_affiliates';
  }
  function setPackageConfigStuff($pkg){
    $co = new Config();
    $co->setPackageObject($pkg);
    $co->save('SCOTTC_COMMERCE_CART_ABANDONED_PER_PAGE', 20);
    $co->save('SCOTTC_AFFILIATE_IGNORE_SUPER_USER', 0);
    $co->save('SCOTTC_AFFILIATES_TIMESTAMP_FORMAT', 'm/d/Y H:i:sa');
    $co->save('SCOTTC_AFFILIATES_GET_VAR_PREFIX', 'qAf');
    $co->save('SCOTTC_AFFILIATE_COOKIE_DAYS',14);
    $co->save('SCOTTC_AFFILIATE_USE_COOKIES',1);
  }
  function getOrDefineStuff(){
    $co = new Config();
    $co->setPackageObject(Package::getByHandle($this->pkgHandle));
    $co->getOrDefine('SCOTTC_COMMERCE_CART_ABANDONED_PER_PAGE', 20);
    $co->getOrDefine('SCOTTC_AFFILIATE_IGNORE_SUPER_USER', 0);
    $co->getOrDefine('SCOTTC_AFFILIATES_TIMESTAMP_FORMAT', 'm/d/Y H:i:sa');
    $co->getOrDefine('SCOTTC_AFFILIATES_GET_VAR_PREFIX', 'qAf');
    $co->getOrDefine('SCOTTC_AFFILIATE_COOKIE_DAYS',14);
    $co->getOrDefine('SCOTTC_AFFILIATE_USE_COOKIES',1);
    $co->getOrDefine('SCOTTC_AFFILIATE_LOG_EARNINGS',1);
    define('SCOTTECOMAFFILATESPACKAGEHANDLE',$this->getPackageHandle());
  }
  function on_start(){
    $this->getOrDefineStuff();
    $u = new User();
    $userID = $u->getUserID();
    $co = new Config();
    $co->setPackageObject($this);
    if($co->get('SCOTTC_AFFILIATE_IGNORE_SUPER_USER') && $userID == USER_SUPER_ID) return;
    //print "runnin";
    $cartEventClassName = 'ScottcAffiliateRelation';
    $cartEventClassPath = 'packages/scottc_affiliates/libraries/affiliate_relation.php';
    define("ENABLE_APPLICATION_EVENTS",true);
    
    //checkout start event
    Events::extend('core_commerce_on_checkout_start', $cartEventClassName, 'eventOnCheckoutStart', $cartEventClassPath);
    
    //shipping address
    Events::extend('core_commerce_on_checkout_shipping_address_submit', $cartEventClassName, 'eventOnCheckoutShippingAddressSubmit', $cartEventClassPath);
    
    //when it gets shipping methods
    Events::extend('core_commerce_on_get_shipping_methods', $cartEventClassName, 'eventOnGetShippingMethods', $cartEventClassPath);
    
    //payment methods event
    Events::extend('core_commerce_on_get_payment_methods', $cartEventClassName, 'eventOnGetPaymentMethods', $cartEventClassPath);
    
    //finish order event(fires after an email is sent?)
    Events::extend('core_commerce_on_checkout_finish_order', $cartEventClassName, 'eventOnCheckoutFinishOrder', $cartEventClassPath);
    
    // When finish page is displayed we should know about it
    Events::extend('core_commerce_on_checkout_finish_page', $cartEventClassName, 'eventOnCheckoutFinishPage', $cartEventClassPath);
    
    if($_GET){
      Loader::library('affiliate_relation',SCOTTECOMAFFILATESPACKAGEHANDLE);
      $har = new ScottcAffiliateRelation($_GET);
    }
  }
  public function upgrade(){
  parent::upgrade();

  }
	public function install() {
    $pkg = Package::getByHandle('core_commerce');
    if(!is_object($pkg) || !$pkg->isPackageInstalled()){ throw new Exception('Concrete5 eCommerce/Core Commerce Must Be Installed');
    return;
    }
    $pkg = parent::install();

    //parent::installDB("db.xml");
    $this->addAffiliateGroup();
    $this->addSinglePages();
    $this->setPackageConfigStuff($pkg);
	}
  function addSinglePages(){
    $affHomeDashboardPage = SinglePage::add('/dashboard/affiliates/',Package::getByHandle($this->getPackageHandle()));
    $affHomeDashboardPage->update(array('cName'=>'eCommerce Affiliates','cDescription'=>'Affiliate/Campaign Management Tools'));
    SinglePage::add('/dashboard/affiliates/detail',Package::getByHandle($this->getPackageHandle()));
    SinglePage::add('/dashboard/affiliates/manage',Package::getByHandle($this->getPackageHandle()));
    //cart abandonment
    $cabHomeDashboardPage = SinglePage::add('/dashboard/cart_abandonment/',Package::getByHandle($this->getPackageHandle()));
    $cabHomeDashboardPage->update(array('cName'=>'eCommerce Cart Activity','cDescription'=>'View Shopping Cart Data'));
    $sp = SinglePage::add('/dashboard/affiliates/settings',Package::getByHandle($this->getPackageHandle()));
    $sp = SinglePage::add('/dashboard/affiliates/help',Package::getByHandle($this->getPackageHandle()));
  }

	function uninstall() {
    Loader::model('groups');
    $g = Group::getByName('Affiliate')->delete();

    $db = Loader::db();
    $droppedDatabases = array("ScottcCommerceAffiliateEarnings","ScottcCommerceAffiliatePayments", "ScottcCommerceAffiliates","ScottcCommerceCartAbandonments");
    foreach($droppedDatabases as $d)  $db->query('DROP TABLE IF EXISTS '.$d);
		parent::uninstall();
	}
  
  function addAffiliateGroup(){
    Loader::model('groups');
    $affiliateGroup = Group::getByName('Affiliates');
    if(!is_object($affiliateGroup)){
      Group::add('Affiliate', 'eCommerce Affiliates');
    }
  }
  /*function addAffiliatePercentage($pkg){
    Loader::model('attribute/type');
    Loader::model('attribute/categories/user');
    $text = AttributeType::getByHandle('text');
    $eaku = AttributeKeyCategory::getByHandle('user');
    UserAttributeKey::add($text, array('akHandle' => 'affiliate_percentage', 'akName' => t('Affiliate Percentage'), 'akIsSearchable' => true), $pkg);
		UserAttributeKey::add($text, array('akHandle' => 'affiliate_tracking_code', 'akName' => t('Affiliate Tracking Code'), 'akIsSearchable' => true), $pkg);
  }*/
}




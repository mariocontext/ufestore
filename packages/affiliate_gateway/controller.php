<?php  defined('C5_EXECUTE') or die(_("Access Denied."));
/**
 * @author Fernando Barajas <fernyb@fernyb.net>
 * @copyright  Copyright (c) 2010-2020 Fernando Barajas.
 * @license    Single End User License / Standard Concrete5 Marketplace License Agreement
 * @category Affiliate Management
 */
Loader::model('single_page');
class AffiliateGatewayPackage extends Package {
	protected $pkgHandle = 'affiliate_gateway';
	protected $appVersionRequired = '5.4.1';
	protected $pkgVersion = '1.0.0';
	public
	function getPackageDescription() {
		return t("Add Affilate Tracking Managment");
	}
	public
	function getPackageName() {
		return t("Affiliate Tracking Management Program");
	}
  function getPackageHandle(){
    return 'affiliate_gateway';
  }
  function setPackageConfigStuff($pkg){
    // $co = new Config();
    // $co->setPackageObject($pkg);
    // $co->save('SCOTTC_COMMERCE_CART_ABANDONED_PER_PAGE', 20);
    // $co->save('SCOTTC_AFFILIATE_IGNORE_SUPER_USER', 0);
    // $co->save('SCOTTC_AFFILIATES_TIMESTAMP_FORMAT', 'm/d/Y H:i:sa');
    // $co->save('SCOTTC_AFFILIATES_GET_VAR_PREFIX', 'qAf');
    // $co->save('SCOTTC_AFFILIATE_COOKIE_DAYS',14);
    // $co->save('SCOTTC_AFFILIATE_USE_COOKIES',1);
  }
  function getOrDefineStuff(){
    // $co = new Config();
    // $co->setPackageObject(Package::getByHandle($this->pkgHandle));
    // $co->getOrDefine('SCOTTC_COMMERCE_CART_ABANDONED_PER_PAGE', 20);
    // $co->getOrDefine('SCOTTC_AFFILIATE_IGNORE_SUPER_USER', 0);
    // $co->getOrDefine('SCOTTC_AFFILIATES_TIMESTAMP_FORMAT', 'm/d/Y H:i:sa');
    // $co->getOrDefine('SCOTTC_AFFILIATES_GET_VAR_PREFIX', 'qAf');
    // $co->getOrDefine('SCOTTC_AFFILIATE_COOKIE_DAYS',14);
    // $co->getOrDefine('SCOTTC_AFFILIATE_USE_COOKIES',1);
    // $co->getOrDefine('SCOTTC_AFFILIATE_LOG_EARNINGS',1);
    // define('SCOTTECOMAFFILATESPACKAGEHANDLE',$this->getPackageHandle());
  }
  
  function on_start(){
    //$u = new User();
    //$userID = $u->getUserID();
    
    //print "runnin";
    //$cartEventClassName = 'ScottcAffiliateRelation';
    //$cartEventClassPath = 'packages/scottc_affiliates/libraries/affiliate_relation.php';
    $eventClassName = 'AffiliateGateway';
    $eventClassPath = 'packages/affiliate_gateway/libraries/affiliate_gateway.php';
    
    define("ENABLE_APPLICATION_EVENTS", true);
  
    Events::extend('on_start', $eventClassName, 'eventOnStart', $eventClassPath, $_GET);
    //Events::extend('on_page_view', 'AffiliateGateway', 'eventOnStart', 'packages/affiliate_gateway/libraries/affiliate_gateway.php');
    
    // if($_GET){
    //       Loader::library('affiliate_relation',SCOTTECOMAFFILATESPACKAGEHANDLE);
    //       $har = new ScottcAffiliateRelation($_GET);
    //     }
  }
    
  // function on_start(){
  //   $this->getOrDefineStuff();
  //   $u = new User();
  //   $userID = $u->getUserID();
  //   $co = new Config();
  //   $co->setPackageObject($this);
  //   if($co->get('SCOTTC_AFFILIATE_IGNORE_SUPER_USER') && $userID == USER_SUPER_ID) return;
  //   //print "runnin";
  //   $cartEventClassName = 'ScottcAffiliateRelation';
  //   $cartEventClassPath = 'packages/scottc_affiliates/libraries/affiliate_relation.php';
  //   define("ENABLE_APPLICATION_EVENTS",true);
  //   
  //   //checkout start event
  //   Events::extend('core_commerce_on_checkout_start', $cartEventClassName, 'eventOnCheckoutStart', $cartEventClassPath);
  //   
  //   //shipping address
  //   Events::extend('core_commerce_on_checkout_shipping_address_submit', $cartEventClassName, 'eventOnCheckoutShippingAddressSubmit', $cartEventClassPath);
  //   
  //   //when it gets shipping methods
  //   Events::extend('core_commerce_on_get_shipping_methods', $cartEventClassName, 'eventOnGetShippingMethods', $cartEventClassPath);
  //   
  //   //payment methods event
  //   Events::extend('core_commerce_on_get_payment_methods', $cartEventClassName, 'eventOnGetPaymentMethods', $cartEventClassPath);
  //   
  //   //finish order event(fires after an email is sent?)
  //   Events::extend('core_commerce_on_checkout_finish_order', $cartEventClassName, 'eventOnCheckoutFinishOrder', $cartEventClassPath);
  //   
  //   // When finish page is displayed we should know about it
  //   Events::extend('core_commerce_on_checkout_finish_page', $cartEventClassName, 'eventOnCheckoutFinishPage', $cartEventClassPath);
  //   
  //   if($_GET){
  //     Loader::library('affiliate_relation',SCOTTECOMAFFILATESPACKAGEHANDLE);
  //     $har = new ScottcAffiliateRelation($_GET);
  //   }
  // }
  
  public function upgrade(){
  parent::upgrade();
  }
  
	function addSidebarPages() {
    $affHomeDashboardPage = SinglePage::add('/dashboard/affiliate_gateway/', Package::getByHandle($this->getPackageHandle()));
    if($affHomeDashboardPage) {
      $affHomeDashboardPage->update(array('cName'=>'Affiliate Gateway','cDescription'=>'Affiliate Gateway Management'));
      $pkg = Package::getByHandle($this->getPackageHandle());
	  
      SinglePage::add('/dashboard/affiliate_gateway/manage',                     $pkg);
      SinglePage::add('/dashboard/affiliate_gateway/advertisers',                $pkg);
      SinglePage::add('/dashboard/affiliate_gateway/advertiser_products',        $pkg);
      SinglePage::add('/dashboard/affiliate_gateway/advertiser_affiliate_codes', $pkg);
      SinglePage::add('/dashboard/affiliate_gateway/affiliates',                 $pkg);
      SinglePage::add('/dashboard/affiliate_gateway/affiliate_codes',            $pkg);

      SinglePage::add('/signup', $pkg);
      SinglePage::add('/signin', $pkg);
      
      $reg = SinglePage::add('/registration', $pkg);
      $reg->setAttribute('exclude_nav', 1);
    }
	}
	
  function addSinglePages(){
    $affHomeDashboardPage = SinglePage::add('/dashboard/affiliates/',Package::getByHandle($this->getPackageHandle()));
    $affHomeDashboardPage->update(array('cName'=>'eCommerce Affiliates','cDescription'=>'Affiliate/Campaign Management Tools'));
    SinglePage::add('/dashboard/affiliates/detail', Package::getByHandle($this->getPackageHandle()));
    
    SinglePage::add('/dashboard/affiliates/manage', Package::getByHandle($this->getPackageHandle()));
    //cart abandonment
    $cabHomeDashboardPage = SinglePage::add('/dashboard/cart_abandonment/',Package::getByHandle($this->getPackageHandle()));
    $cabHomeDashboardPage->update(array('cName'=>'eCommerce Cart Activity','cDescription'=>'View Shopping Cart Data'));
    $sp = SinglePage::add('/dashboard/affiliates/settings',Package::getByHandle($this->getPackageHandle()));
    $sp = SinglePage::add('/dashboard/affiliates/help',Package::getByHandle($this->getPackageHandle()));
  }


	public function install() {
    // $pkg = Package::getByHandle($this->getPackageHandle());
    // if(!is_object($pkg) || !$pkg->isPackageInstalled()){ throw new Exception('Concrete5 eCommerce/Core Commerce Must Be Installed');
    // return;
    // }
    $pkg = parent::install();
    Cache::flush();
    
    $this->addSidebarPages();
    
    $db = Loader::db();
   
    $sql = "CREATE TABLE `AffiliatedGatewayAdvertisers` (";
    $sql .= "`id` int(11) unsigned NOT NULL AUTO_INCREMENT,";
    $sql .= "`name` varchar(255) DEFAULT '',";
    $sql .= "`active` smallint(1) DEFAULT '0',";
    $sql .= "`description` text,";
    $sql .= "`website_url` varchar(500) DEFAULT NULL,";
    $sql .= "PRIMARY KEY (`id`)";
    $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8";
    $db->query($sql);
    
    /** 
    * Table: Affiliate Codes
    * Affiliate Codes will be used to lookup affiliate codes and the corresponding advertiser
    * Once advertiser has been found we look up for the product that is associated with the code
    * Once we find the product we look we redirect to the external url 
    */
    $sql = "CREATE TABLE `AffiliatedGatewayCodes` (";
    $sql .= "`id` int(11) unsigned NOT NULL AUTO_INCREMENT,";
    $sql .= "`affiliated_gateway_advertiser_id` int(11) unsigned DEFAULT NULL,";
    $sql .= "`code` varchar(255) DEFAULT NULL,";
    $sql .= "PRIMARY KEY (`id`)";
    $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8";
    $db->query($sql);
    
    
    $sql = "CREATE TABLE `AffiliatedGatewayProducts` (";
    $sql .= "`id` int(11) unsigned NOT NULL AUTO_INCREMENT,";
    $sql .= "`affiliated_gateway_advertiser_id` int(255) unsigned DEFAULT NULL,";
    $sql .= "`affiliated_gateway_code_id` int(255) unsigned DEFAULT NULL,";
    $sql .= "`name` int(11) DEFAULT NULL,";
    $sql .= "`url` varchar(500) DEFAULT NULL,";
    $sql .= "PRIMARY KEY (`id`)";
    $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8";
    $db->query($sql);


    $sql = "CREATE TABLE `AffiliatedGatewayAffiliates` (";
    $sql .= "`id` int(11) unsigned NOT NULL AUTO_INCREMENT,";
    $sql .= "`email` varchar(500) DEFAULT NULL,";
    $sql .= "`first_name` varchar(300) DEFAULT NULL,";
    $sql .= "`last_name` varchar(300) DEFAULT NULL,";
    $sql .= "`city` varchar(255) DEFAULT NULL,";
    $sql .= "`state` varchar(255) DEFAULT NULL,";
    $sql .= "`zip` int(11) unsigned DEFAULT NULL,";
    $sql .= "`phone` varchar(255) DEFAULT NULL,";
    $sql .= "PRIMARY KEY (`id`)";
    $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8";
    $db->query($sql);

    // install page types
    // $pdt = CollectionType::getByHandle('product_detail');
    // if( !$pdt || !intval($pdt->getCollectionTypeID()) ){
    //     $data['ctHandle'] = 'product_detail';
    //     $data['ctName'] = t('Product Detail');
    //     $pdt = CollectionType::add($data, $pkg);
    // }
    //   
    
    
   /*
    //parent::installDB("db.xml");
    $this->addAffiliateGroup();
    $this->addSinglePages();
    $this->setPackageConfigStuff($pkg);
    */
	}
	
	function uninstall() {
		parent::uninstall();
		Cache::flush();
    
	  /*
    Loader::model('groups');
    $g = Group::getByName('Affiliate')->delete();

    $db = Loader::db();
    $droppedDatabases = array("ScottcCommerceAffiliateEarnings","ScottcCommerceAffiliatePayments", "ScottcCommerceAffiliates","ScottcCommerceCartAbandonments");
    foreach($droppedDatabases as $d)  $db->query('DROP TABLE IF EXISTS '.$d);
    */
    $db = Loader::db();
    $db->query("DROP TABLE IF EXISTS `AffiliatedGatewayAdvertisers`");
    $db->query("DROP TABLE IF EXISTS `AffiliatedGatewayAffiliates`");
    $db->query("DROP TABLE IF EXISTS `AffiliatedGatewayProducts`");
    $db->query("DROP TABLE IF EXISTS `AffiliatedGatewayCodes`");
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




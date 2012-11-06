<?php 
/**
 * @author Scott Conrad <scott.conrads@gmail.com>
 * @copyright  Copyright (c) 2010-2020 Scott Conrad.
 * @license    Single End User License / Standard Concrete5 Marketplace License Agreement
 * @category eCommerce Affiliate
 */
class ScottcAffiliateRelation extends Object{
  static $orderID;
  static $checkoutStepPath;
  static $affiliateCode;
  static $affiliateID;
  static $orderEmail;
  static $cartController;
  static $orderObject;

  function  __construct($_GET) {
    $co = new Config();
    $co->setPackageObject(Package::getByHandle('scottc_affiliates'));
    if($_GET[$co->get("SCOTTC_AFFILIATES_GET_VAR_PREFIX")]) 
    {
      $this->addAffiliateToSession();
    }  
   self::$affiliateCode = self::getAffiliateFromSession();
  }
  
 function addAffiliateToSession(){
   $co = new Config();
   $co->setPackageObject(Package::getByHandle('scottc_affiliates'));
   $affiliateKey = base64_encode($_GET[$co->get("SCOTTC_AFFILIATES_GET_VAR_PREFIX")]);
   $_SESSION['SCOTTC_AFFILIATE'] = $affiliateKey;
   if($co->get("SCOTTC_AFFILIATE_USE_COOKIES")){
     setcookie('SCOTTC_AFFILIATE', $affiliateKey, time()+3600*24* $co->get('SCOTTC_AFFILIATE_COOKIE_DAYS'));
   }
 }
 function getAffiliateFromSession(){
   $co = new Config();
   $co->setPackageObject(Package::getByHandle('scottc_affiliates'));
   if($_SESSION['SCOTTC_AFFILIATE']){
     return base64_decode($_SESSION['SCOTTC_AFFILIATE']);
   }
   if($_COOKIE['SCOTTC_AFFILIATE'] && $co->get("SCOTTC_AFFILIATE_USE_COOKIES")){
     return base64_decode($_COOKIE['SCOTTC_AFFILIATE']);
   }
 }
 
 function addCartData($cart,$eventName){
   self::$affiliateCode = self::getAffiliateFromSession();
  
   //print "event was called";
   
   if($cart instanceof CoreCommerceCurrentOrder){
     self::$orderID = $cart->getOrderID();
     self::$orderEmail = $cart->getOrderEmail();
     self::$orderObject = $cart;
   }
   
   if($cart instanceof CheckoutController) {
   	 self::$cartController = $cart;
     $co = $cart->get('order');
   	
     if(!self::$orderID) {
      self::$orderID = $co->getOrderID();
   	 }
   	 if(!self::$orderEmail) {
   	  self::$orderEmail = $co->getOrderEmail();
   	 }
   	 if(!self::$orderObject) {
   	  self::$orderObject = $co;
   	 }
   }
   
   
   //var_dump($eventName);
   
   //ran on shipping, on billing, address submit payment form
   switch ($eventName) {
     case 'core_commerce_on_get_shipping_methods':
       //
       self::logCheckOutStep();
       break;
     case 'core_commerce_on_get_payment_methods':
       //cart object is instance of CoreCommerceCurrentOrder
       //fired and the object is CoreCommerceCurrentOrder
       self::logCheckOutStep();
       break;

     case 'core_commerce_on_checkout_start':
       //CheckoutBillingController is Passed in as cart in billing submit url?
       self::logCheckOutStep();

       break;
     case 'core_commerce_on_checkout_finish_order':
       self::logCheckOutStep();
      
       if(self::$affiliateCode && $cart instanceof CoreCommerceCurrentOrder) {
         if('SCOTTC_AFFILIATE_LOG_EARNINGS') 
         {
          Loader::library('scottc_commerce_affiliate_calculations',SCOTTECOMAFFILATESPACKAGEHANDLE);
          //if('SCOTTC_COMMERCE_AFFILIATES_ENABLED' == 1)
          ScottcCommerceAffiliateCalculations::calculateEarnings(self::$affiliateCode, $cart);
         }
       }
       break;
     default:
        break;
   }
 }
 
 function logCheckOutStep(){
   self::$affiliateCode = self::getAffiliateFromSession();
   
   //this gets sent a bunch of stuff, but we're only interested in one object
   if(self::$cartController instanceof Controller) {
     $cart = self::$cartController;
   
       if($cart->getCollectionObject() instanceof Collection) { 
         $paths = $cart->getCollectionObject()->getPagePaths();
         if($paths) { 
           self::$checkoutStepPath = $paths[0]['cPath'];
         }
       }
     
   
     /** @var $step CoreCommerceCheckoutStepHelper */
     Loader::library('cart_abandonment', SCOTTECOMAFFILATESPACKAGEHANDLE);
     ScottcCommerceCart::addCartStep(self::$orderID, self::$checkoutStepPath, self::$orderEmail, self::$affiliateCode, self::$orderObject);
   }
 }
 
 function eventOnGetShippingMethods($cart){
   self::addCartData($cart, 'core_commerce_on_get_shipping_methods');
 }
 function eventOnGetPaymentMethods($cart){
   self::addCartData($cart,'core_commerce_on_get_payment_methods');
 }
 function eventOnCheckoutStart($cart) {
   self::addCartData($cart,'core_commerce_on_checkout_start');
 }
 function eventOnCheckoutFinishOrder($cart){
   self::addCartData($cart, 'core_commerce_on_checkout_finish_order');
 }
 function eventOnCheckoutShippingAddressSubmit($obj){
   self::addCartData($obj, 'core_commerce_on_checkout_shipping_address_submit');
 }
 
 function eventOnCheckoutFinishPage($cart) {
   self::$cartController = $cart;
   self::$affiliateCode  = self::getAffiliateFromSession();
   self::$checkoutStepPath = NULL;
   
     $co = $cart->get('order');
   	
     if(!self::$orderID) {
      self::$orderID = $co->getOrderID();
   	 }
   	 if(!self::$orderEmail) {
   	  self::$orderEmail = $co->getOrderEmail();
   	 }
   	 if(!self::$orderObject) {
   	  self::$orderObject = $co;
   	 }
   	 
     /** @var $step CoreCommerceCheckoutStepHelper */
     Loader::library('cart_abandonment', SCOTTECOMAFFILATESPACKAGEHANDLE);
     ScottcCommerceCart::addCartStep(self::$orderID, self::$checkoutStepPath, self::$orderEmail, self::$affiliateCode, self::$orderObject);
 }

}
?>

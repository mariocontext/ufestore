<?php 
/**
 * @author Scott Conrad <scott.conrads@gmail.com>
 * @copyright  Copyright (c) 2010-2020 Scott Conrad.
 * @license    Single End User License / Standard Concrete5 Marketplace License Agreement
 * @category eCommerce Affiliate
 */
Loader::model('scottc_commerce_cart_abandonment',SCOTTECOMAFFILATESPACKAGEHANDLE);
class ScottcCommerceCart {
  
  function addCartStep($orderID, $step, $orderEmail = "", $affiliateCode, /*CoreCommerceCurrentOrder*/ $orderObject) 
  {
    //print "add cart step was called";
    $hcca = new ADODB_Active_Record('ScottcCommerceCartAbandonments');
    $db = Loader::db();
    
    if($affiliateCode){
      $affiliateID = $db->getOne("SELECT affiliateID from ScottcCommerceAffiliates where affiliateCode = ?",array($affiliateCode));
    }
    
    if(!$affiliateID) $affiliateID = 0;
    $hcca->orderID = $orderID;
    $hcca->affiliateID = $affiliateID;
    $hcca->affiliateCode = $affiliateCode;
    $hcca->step = $step;
    $hcca->orderEmail = $orderEmail;
    //$hcca->orderObjectSerialized = serialize($orderObject); //no need
    $hcca->Replace();
  }
  
  function logCheckoutStep(CheckoutController $controller, CoreCommerceCurrentOrder $orderObject){
   self::addCartStep($orderObject->getOrderID(), self::getCheckoutStepFromController($controller), $orderObject->getOrderEmail(),$orderObject);
  }
  function getCheckoutStepFromController(CheckoutController $controller){
    if($controller->getCollectionObject() instanceof Collection){
     $paths = $controller->getCollectionObject()->getPagePaths();
     if($paths) return $paths[0]['cPath'];
     }
  }
  
}
?>

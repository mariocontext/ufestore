<?php 
/**
 * @author Scott Conrad <scott.conrads@gmail.com>
 * @copyright  Copyright (c) 2010-2020 Scott Conrad.
 * @license    Single End User License / Standard Concrete5 Marketplace License Agreement
 * @category eCommerce Affiliate
 */
class ScottcCommerceAffiliateCalculations{
 static protected $earnings;
 
  function calculateEarnings($affiliateCode, CoreCommerceOrder $cco) {
    /*print "calculate earnings inside of scottccommerceaffiliate calculations was called";
    print "affiliateID is ".$affiliateCode;*/
    $db = Loader::db();
    $row = $db->getRow('SELECT * from ScottcCommerceAffiliates where affiliateCode = ?',array($affiliateCode));
    $affiliateID = $row['affiliateID'];
    $type = $row['earningType'];
    $amount = $row['amount'];
    if($amount){
    $orderTotal = $cco->getOrderTotal();
      //print "orderTotal is".$orderTotal;
      //percentage is 1, amount is 2;
    switch($type){
      case 1:
        self::$earnings =  $orderTotal * $amount / 100;
        break;
      case 2:
        self::$earnings = $amount;
        break;
    }



    }//end if on amount
    //if($affiliateID){
      //print "tried to save an affiliate earning";
      Loader::model('scottc_commerce_affiliate_earnings',SCOTTECOMAFFILATESPACKAGEHANDLE);
      $aff = new ScottcCommerceAffiliateEarnings();
      $aff->affiliateID = $affiliateID;
      $aff->affiliateCode = $affiliateCode;
      $aff->amount = self::$earnings;
      $aff->orderID = $cco->getOrderID();
      $aff->orderTotal = $cco->getOrderTotal();
      $aff->Save();
    //}
  }


}
?>

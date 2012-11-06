<?php 
/**
 * @author Scott Conrad <scott.conrads@gmail.com>
 * @copyright  Copyright (c) 2010-2020 Scott Conrad.
 * @license    Single End User License / Standard Concrete5 Marketplace License Agreement
 * @category eCommerce Affiliate
 */
Loader::model('scottc_commerce_affiliate',SCOTTECOMAFFILATESPACKAGEHANDLE);
class ScottcCommerceAffiliateEarnings extends Model{
  var $_table = 'ScottcCommerceAffiliateEarnings'; //sigh
  function getAffiliateEarnings($affiliateID){
    $obj = new ADODB_Active_Record('ScottcCommerceAffiliateEarnings');
    return $obj->find('affiliateID = ? ORDER BY timestamp DESC',array($affiliateID));
  }
  function getTotalEarnings($affiliateID){
    $arr = self::getAffiliateEarnings($affiliateID);
    if($arr) foreach($arr as $a){
     $total += $a->amount;
    }
    return $total;

  }
  function deleteAffiliateEarnings($affiliateID){
    $db = Loader::db();
    $db->execute("DELETE FROM ScottcCommerceAffiliateEarnings where affiliateID = ?",array($affiliateID));
  }
  function getTotalSales($affiliateID){
    $arr = self::getAffiliateEarnings($affiliateID);
    if($arr) foreach($arr as $a){
     $total += $a->orderTotal;
    }
    return $total;

  }
  function delete($id){
    $db = Loader::db();
    $db->execute("DELETE FROM ScottcCommerceAffiliateEarnings where id = ?",array($id));
  }
  function getTotalEarningsByDateRange($dateStart,$dateEnd,$affiliateID){
    $obj = new ADOdb_Active_Record('ScottcCommerceAffiliateEarnings');
    
  }
  function getSalesByAffiliateCode($code){
    $db = Loader::db();
    $sum = $db->getOne("SELECT sum(orderTotal) from ScottcCommerceAffiliateEarnings where affiliateCode = ?",array($code));
    print($sum);
    return $sum;
  }
  function getAllSalesByAffiliateCodes(){
    $db = Loader::db();
    $rows = $db->getAll("SELECT affiliateCode, sum(orderTotal) as total from ScottcCommerceAffiliateEarnings GROUP BY affiliateCode");
    return $rows;
  }




}

?>

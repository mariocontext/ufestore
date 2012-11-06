<?php 
/**
 * @author Scott Conrad <scott.conrads@gmail.com>
 * @copyright  Copyright (c) 2010-2020 Scott Conrad.
 * @license    Single End User License / Standard Concrete5 Marketplace License Agreement
 * @category eCommerce Affiliate
 */
class ScottcCommerceAffiliate extends Model{
  var $_table = "ScottcCommerceAffiliates";

  function getAll(){
    $db = Loader::db();
    $getAll = $db->getActiveRecords('ScottcCommerceAffiliates');
    //$db->setDebug(false);
    return $getAll;
    
  }
  function getEarningTypes(){
    return array(1=>'Percentage of Order',2=>"Amount Per Order");
  }
  function getByID($id){
    $db = Loader::db();

    $row = $db->getRow("SELECT * from ScottcCommerceAffiliates where affiliateID = ?",array($id));
    $obj = new ADOdb_Active_Record('ScottcCommerceAffiliates');
    $obj->Set($row);
    return $obj;
  }
  function delete($id){
    $db = Loader::db();
    $db->query("DELETE FROM ScottcCommerceAffiliates where affiliateID = ?",array($id));
  }
  
  function getByUserID($id){
    $obj = new ADODB_Active_Record("ScottcCommerceAffiliates");
    $aff = $obj->Find("userID = ?",array($id));
    return $aff[0];
  }
}
?>

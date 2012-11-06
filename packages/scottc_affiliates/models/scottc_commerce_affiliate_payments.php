<?php 
/**
 * @author Scott Conrad <scott.conrads@gmail.com>
 * @copyright  Copyright (c) 2010-2020 Scott Conrad.
 * @license    Single End User License / Standard Concrete5 Marketplace License Agreement
 * @category eCommerce Affiliate
 */
class ScottcCommerceAffiliatePayments extends Model{
var $_table = 'ScottcCommerceAffiliatePayments'; //sigh
function getPaymentsByAffiliateID($id){
  $payments = new ADODB_Active_Record('ScottcCommerceAffiliatePayments');
  return $payments->Find('affiliateID = ?',array($id));
}
function getPaymentsMadeToAffiliateAmount($id){
  $db = Loader::db();
  $sum = $db->getOne("SELECT sum(amount) from ScottcCommerceAffiliatePayments where affiliateID = ?",array($id));
  return $sum;
}
function delete($id){
  $db = Loader::db();
  $db->query("DELETE FROM ScottcCommerceAffiliatePayments where id = ?",array($id));
}

}
?>

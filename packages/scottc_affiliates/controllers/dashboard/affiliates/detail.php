<?php 
/**
 * @author Scott Conrad <scott.conrads@gmail.com>
 * @copyright  Copyright (c) 2010-2020 Scott Conrad.
 * @license    Single End User License / Standard Concrete5 Marketplace License Agreement
 * @category eCommerce Affiliate
 */
Loader::model('scottc_commerce_affiliate',SCOTTECOMAFFILATESPACKAGEHANDLE);
Loader::model('scottc_commerce_affiliate_earnings',SCOTTECOMAFFILATESPACKAGEHANDLE);
Loader::model('scottc_commerce_affiliate_payments',SCOTTECOMAFFILATESPACKAGEHANDLE);
Loader::library('price','core_commerce');
class DashboardAffiliatesDetailController extends Controller{
  function on_start(){

  $aff = array();
  $allAffiliates = ScottcCommerceAffiliate::getAll();
  if($allAffiliates) foreach($allAffiliates as $af) $aff[$af->affiliateID] = $af->name;
  $this->set('affiliateKV',$aff);
  $allSales = ScottcCommerceAffiliateEarnings::getAllSalesByAffiliateCodes();
  $this->set("salesByCode",$allSales);
  }
  function financials($id){
   if(!is_numeric($id)){
     $this->set('error',array('Financials are accessed by affiliateID, not a string'));
     return;
   }
   $affiliate = ScottcCommerceAffiliate::getByID($id);
   $earnings = ScottcCommerceAffiliateEarnings::getAffiliateEarnings($id);
   $this->set("affiliate",$affiliate);
   $this->set('affiliateID',$id);
   $this->set('earnings',$earnings);
   $totalEarnings = ScottcCommerceAffiliateEarnings::getTotalEarnings($id);
   $totalSales = ScottcCommerceAffiliateEarnings::getTotalSales($id);
   $totalPayments = ScottcCommerceAffiliatePayments::getPaymentsMadeToAffiliateAmount($id);
   $this->set('totalEarnings',  CoreCommercePrice::format($totalEarnings));
   $this->set('totalSales',  CoreCommercePrice::format($totalSales));
   if($affiliate->affiliateID){
   $this->set('showFinancials',1);
   $this->set('totalPayments',  CoreCommercePrice::format($totalPayments));
   $this->set('accountBalance',CoreCommercePrice::format($totalEarnings - $totalPayments));
   }

  }
  function delete_payment($id){
    ScottcCommerceAffiliatePayments::delete($id);
    $this->set('message','Payment Deleted');
  }
  function delete_financial($id){
    ScottcCommerceAffiliateEarnings::delete($id);
    $this->set('message','Earning Record Deleted');
  }
  function add_payment(){
   $payment = new ScottcCommerceAffiliatePayments();
   $payment->affiliateID = $this->post("id");
   $payment->amount = $this->post('amount');
   if(!is_numeric($this->post('amount'))){
     $this->set('error',array('Payment amount paid must be a numeric value'));
     return;
   }
   $u = new User();
   $payment->userID = $u->getUserID();
   $payment->Save();
   $this->redirect('dashboard/affiliates/detail/financials/'.$this->post('id'));
  }
}

?>
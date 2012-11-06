<?php 
/**
 * @author Scott Conrad <scott.conrads@gmail.com>
 * @copyright  Copyright (c) 2010-2020 Scott Conrad.
 * @license    Single End User License / Standard Concrete5 Marketplace License Agreement
 * @category eCommerce Affiliate
 */
Loader::model('scottc_commerce_affiliate',SCOTTECOMAFFILATESPACKAGEHANDLE);
class DashboardAffiliatesManageController extends Controller{
  function on_start(){
    $aff = new ScottcCommerceAffiliate();
    $this->set('types',$aff->getEarningTypes());
  }
  function save(){
    $val = Loader::helper('validation/form');
    unset($_POST['ccm-submit-button']);
    if(!is_numeric($_POST['amount']) && $POST['amount'] != ""){
      $this->set('error',array('Amount must be a numeric value'));
      return;
    }
    Loader::model('scottc_commerce_affiliate',SCOTTECOMAFFILATESPACKAGEHANDLE);
    $aff = new ScottcCommerceAffiliate();
    if($_POST['affiliateID']) $aff->affiliateID = $_POST['affiliateID'];
    $aff->affiliateCode = $_POST['affiliateCode'];
    $aff->userID = $_POST['userID'];
    $aff->name = $_POST['name'];
    $aff->earningType = $_POST['earningType'];
    $aff->amount = $_POST['amount'];
    if(!$aff->amount) $aff->amount = 0;
    $aff->Replace();
    $this->set('message','Affiliate Added Or Updated');
    $this->view();
  }
  function view(){
    Loader::model('scottc_commerce_affiliate',SCOTTECOMAFFILATESPACKAGEHANDLE);
    $aff = new ScottcCommerceAffiliate();
    $this->set('allAffiliates',$aff->getAll());
  }
  function edit($id){
  $aff = new ScottcCommerceAffiliate();
  $u = $aff->getByID($id);
  $this->set('affiliateID',$id);
    $vars = get_object_vars($u);
    if($vars) foreach($vars as $k => $v) $this->set($k,$v);
  }
  function delete($id){
    $aff = new ScottcCommerceAffiliate();
    $aff->delete($id);
    $this->set('message','Affiliate Deleted');
    $this->view();
  }
}

?>

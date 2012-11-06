<?php 
/**
* @author Scott Conrad <scott.conrads@gmail.com>
* @copyright  Copyright (c) 2010-2020 Scott Conrad.
* @license    Single End User / License Standard Concrete5 Marketplace License Agreement
* @category ScottC Marketplace item
*/
class DashboardAffiliatesSettingsController extends Controller{
public $pn = SCOTTECOMAFFILATESPACKAGEHANDLE;
   function getConfigObject(){
     $c = new Config();
     $c->setPackageObject(Package::getByHandle($this->pn));
     return $c;
   }
   function setConfigOption($k,$v){
     $c = $this->getConfigObject();
     $c->save($k, $v);
   }
   function view(){
    $c = $this->getConfigObject();
    $li = $c->getListByPackage(Package::getByHandle($this->pn));
    if($li) foreach($li as $l) $this->set($l->key,$l->value);
   }
   function save(){
     $val = Loader::helper('validation/form');
     unset($_POST['ccm-submit-button']);
     $val->setData($_POST);
     //
     $val->addInteger('AFFILIATE_COOKIE_DAYS', t('You <b>must</b> specify a whole number for days, you provided: '.$this->post('AFFILIATE_COOKIE_DAYS')), true);
     if(!$val->test()){ $this->set('error',$val->getError()->getList());
     return;
     }

     if($_POST) foreach($_POST as $k => $v) $this->setConfigOption ($k, $v);
     $this->set('message',t('Config Saved'));
     $this->view();
   }
}
?>

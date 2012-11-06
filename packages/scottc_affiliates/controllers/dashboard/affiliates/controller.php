<?php 
/**
 * @author Scott Conrad <scott.conrads@gmail.com>
 * @copyright  Copyright (c) 2010-2020 Scott Conrad.
 * @license    Single End User License / Standard Concrete5 Marketplace License Agreement
 * @category eCommerce Affiliate
 */
class DashboardAffiliatesController extends Controller{
  function on_start(){
   $this->redirect('/dashboard/affiliates/detail');
  }
}

?>

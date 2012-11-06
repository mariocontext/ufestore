<?php 
/**
 * @author Scott Conrad <scott.conrads@gmail.com>
 * @copyright  Copyright (c) 2010-2020 Scott Conrad.
 * @license    Single End User License / Standard Concrete5 Marketplace License Agreement
 * @category eCommerce Affiliate
 */
 Loader::model('scottc_commerce_cart_abandonment',SCOTTECOMAFFILATESPACKAGEHANDLE);
class DashboardCartAbandonmentController extends Controller{
  function on_start(){
  }
  function view(){
  $this->setAbandonedCarts();
  $this->populateOverview();
  }
  function setAbandonedCarts($page = 0){
    //zero index 0-SCOTTC_COMMERCE_CART_ABANDONED_PER_PAGE
    $page = ($page) ? $page - 1 : 0;
    $ca = new ScottcCommerceCartAbandonment();
    $offset = $page * SCOTTC_COMMERCE_CART_ABANDONED_PER_PAGE;
    $getAll = $ca->getAbandonedCarts($offset,SCOTTC_COMMERCE_CART_ABANDONED_PER_PAGE);
   $this->set('abandonedCarts',$getAll);
  }
  function page($pageNumber){
    $this->setAbandonedCarts($pageNumber);
  }
  function populateOverview(){
    $ca = new ScottcCommerceCartAbandonment();
    $this->set('overviewAbandoned',$ca->getAbandonedCartOverview());
  }
}
?>

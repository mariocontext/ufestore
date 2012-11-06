<?php 
/**
 * @author Scott Conrad <scott.conrads@gmail.com>
 * @copyright  Copyright (c) 2010-2020 Scott Conrad.
 * @license    Single End User License / Standard Concrete5 Marketplace License Agreement
 * @category eCommerce Affiliate
 */
class ScottcCommerceCartAbandonment extends Model{
  function getAbandonedCarts($offset = "",$limit = ""){
    $ca = new ScottcCommerceCartAbandonment();
    if($offset || $limit){
      $find = sprintf("step IS NOT NULL ORDER BY updated DESC LIMIT %s, %s",$offset,$limit);
      if(!$offset) $offset = 0;
      if(!$limit) $limit = 20; //?
    }else{
        $find = "step IS NOT NULL ORDER BY updated DESC";
    }
    $getAll = $ca->find($find);
    return $getAll;
  }
  function getAbandonedCartOverview(){
    $db = Loader::db();
    $stepAbandoned = array();
    //could probably do all this in one query, but who cares?
    $steps = $db->execute("SELECT DISTINCT step from ScottcCommerceCartAbandonments where step IS NOT NULL ")->getRows();
    if($steps) foreach($steps as $step){
      $stepAbandoned[$step['step']] = $db->getOne("SELECT COUNT(*)
      from ScottcCommerceCartAbandonments where step = ?",array($step['step']));
    }
    //array flip is acting weird?
    
    return $stepAbandoned;
  }


}

?>

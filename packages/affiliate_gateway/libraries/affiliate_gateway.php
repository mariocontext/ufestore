<?php

class AffiliateGateway extends Object {

  function eventOnStart($obj) {
    // $_GET
    if ($_GET['aff']) {
      $affiliateCode = $_GET['aff'];
      $db = Loader::db();
    
      if($affiliateCode) {
        $affiliatePath = $db->getOne("SELECT affiliatePath from ScottcCommerceAffiliates where affiliateCode = ?", array($affiliateCode));
      }
    }
    
    //$controller = Loader::controller('packages/affiliate_gateway/libraries/affiliate_gateway');
    //$controller->externalRedirect('http://www.yahoo.com/');
  }
} 

?>
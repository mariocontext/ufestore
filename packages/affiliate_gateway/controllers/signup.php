<?php

Loader::model('affiliated_gateway_affiliate', 'affiliate_gateway');

class SignupController extends Controller {
  public function newuser() {
    $vars = $this->post('user');
  
    $errors = AffiliateGatewayAffiliate::signup($vars);  
    var_dump($errors);
    
    //$this->redirect("/");
    die();
  }
}

?>
<?php 
$p = Page::getByID('/dashboard/core_commerce/orders');
$perms = new Permissions($p);
if(!$perms->canRead()){
  exit('Access Denied');
}
Loader::model('order','core_commerce');
$o = CoreCommerceOrder::getByID($_GET['id']);
print_r($o);





/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>

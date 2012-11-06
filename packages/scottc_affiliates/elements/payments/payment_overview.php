<?php 
/**
 * @author Scott Conrad <scott.conrads@gmail.com>
 * @copyright  Copyright (c) 2010-2020 Scott Conrad.
 * @license    Single End User License / Standard Concrete5 Marketplace License Agreement
 * @category eCommerce Affiliate
 */
Loader::model('scottc_commerce_affiliate_payments',SCOTTECOMAFFILATESPACKAGEHANDLE);
Loader::model('scottc_commerce_affiliate',SCOTTECOMAFFILATESPACKAGEHANDLE);
Loader::library('price','core_commerce');
Loader::helper('concrete/interface');
$ih = new ConcreteInterfaceHelper();
$payments = ScottcCommerceAffiliatePayments::getPaymentsByAffiliateID($affiliateID);
if($payments){ ?>
<h2>Payments Made</h2>
<table class="grid-list" cellspacing="1" border="0" <?php  if($style) printf('style="%s"',$style); ?>>
  <thead><tr><td class="header">Payment</td><td class="header">Date Marked As Paid</td><td class="header">By User</td><td class="header">Delete</td></tr></thead>
  <tbody>
    <?php  foreach($payments as $p){ ?>
    <tr>
      <td><?php  echo CoreCommercePrice::format($p->amount); ?></td><td><?php  echo date(SCOTTC_AFFILIATES_TIMESTAMP_FORMAT, strtotime($p->timestamp)); ?></td><td><?php  echo User::getByUserID($p->userID)->getUserName(); ?></td>
      <td><?php echo $ih->button_js("Delete",'delete_payment('.$p->id.')'); ?></td>
    </tr>
    <?php  } ?>
  </tbody>
</table>

  <?php  } ?>



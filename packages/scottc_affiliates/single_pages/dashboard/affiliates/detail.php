<?php  Loader::helper('form'); 
$fh = new FormHelper();
Loader::helper("navigation");
Loader::helper('concrete/interface');
Loader::library('price','core_commerce');
$ih = new ConcreteInterfaceHelper();
$nh = new NavigationHelper();
$currentUrl = $nh->getCollectionURL(Page::getCurrentPage(),true,false);
?>
<script type="text/javascript">
var currentUrl = '<?php  echo $currentUrl; ?>';
var view_details = function(){
  window.location = currentUrl + 'financials/' + $('#affiliateID').val();
}
var delete_payment = function(record){
  confirmed = confirm('Are you sure you want to delete this payment?');
  if(confirmed) window.location = "<?php  echo $this->action('delete_payment'); ?>" + record;
}
var delete_earning = function(record){
  confirmed = confirm('Are you sure you want to delete this affiliate earning?');
  if(confirmed) window.location = "<?php  echo $this->action('delete_financial'); ?>" + record;
}

var savepayment = function(){
   jQuery.fn.dialog.open({
      title: 'Add a Payment',
      element: '#add-payment',
      width: 300,
      modal: false,
      height: 300
   });
}

</script>
<h1><span>Affiliate Tracking Overview</span></h1>
<div class="ccm-dashboard-inner">
  <?php 
  if($salesByCode){ 
    ?>
  <h2>Top Selling Tracked Codes/Affiliates</h2>
  <ol>
    <?php  foreach($salesByCode as $bc) echo "<li><b>".$bc['affiliateCode'].'</b> '.CoreCommercePrice::format($bc['total']).'</li>'; ?>
  </ol>
  <?php   }else{  ?>
  <b>No trackable sales have been reported</b>
  <?php  } ?>
  <h2>View Tracking Codes/Affiliate Revenue</h2>
  <p>Useful for managing how successful adSense campaign is, etc.</p>
  <div>
    <?php  if($affiliateKV){ ?>
    <h2>View Affiliate Earnings</h2>
<?php  echo $fh->select('affiliateID',$affiliateKV, $affiliateID); ?>
    <?php  echo $ih->button_js('View', 'view_details()','left');
    }else{ ?>
    <h2>You haven't created any affiliates</h2<br />
    <?php  echo $ih->button('Create Affiliate', $this->url('/dashboard/affiliates/manage'),'left'); ?>
    <?php 
    }
    ?>
  </div>
  <div style="clear:both;"></div>
  <?php  if($affiliate){ 
    ?>
  <div style="display:none;">
    <form id="add-payment" class="ccm-dashboard-inner" method="post" action="<?php echo $this->action('add_payment'); ?>">
      <h2>Payment For <?php echo $affiliate->name; ?></h2><br />
      <p>Please enter the amount paid out to <?php  echo $affiliate->name; ?></p>
        <input type="text" name="amount" size="15" /><br /><br />
        <input type="hidden" name="id" value="<?php  echo $affiliate->affiliateID; ?>" style="display:none;" />
        <?php  echo $ih->submit('Save Payment','','left'); ?>
    </form>

  </div>
    <?php  } //end if on $affiliate
    ?>

</div>
<?php  if($showFinancials){ ?>
<h1><span><?php  echo $affiliate->name; ?> Earnings</span></h1>
<div class="ccm-dashboard-inner">
  <label for="code">Code:</label><input type="text" readonly="readonly" style="font-size:1.1em;" size="20" value="<?php  echo $affiliate->affiliateCode; ?>" /><br />
  <h2>Financial Details</h2>
  <table class="grid-list" border="0" cellspacing="1" style="width:500px;">
    <thead><tr><td class="header">Total Sales</td><td class="header">Total Earnings</td><td class="header">Payments Made</td><td class="header">Account Balance</td></tr></thead>
    <tbody><tr><td><b><?php  echo $totalSales; ?></b></td><td><b><?php  echo $totalEarnings; ?></b></td><td><b><?php  echo $totalPayments; ?></b></td><td><b><?php  echo $accountBalance; ?></b></td></tbody>
  </table>
  <br />
  <?php echo $ih->button_js('Optional(Record a Payout)', 'savepayment()', 'left'); ?>
  <br /><br /><h2>Orders</h2>
  <?php  if($earnings){ ?>
  <table class="grid-list" border="0" cellspacing="1" style="width:500px;">
    <thead><tr><td class="header">Order</td><td class="header">Earnings</td><td class="header">Order Total</td><td class="header">Date</td><td class="header">Delete</td></tr></thead>
    <tbody>
      <?php  foreach($earnings as $e){  ?>

      <tr>
        <td><a href="<?php  echo $this->url('dashboard/core_commerce/orders/search','detail',$e->orderID); ?>">Order Details</a></td>
        <td><?php  echo CoreCommercePrice::format($e->amount); ?></td>
        <td><?php  echo CoreCommercePrice::format($e->orderTotal); ?></td>
        <td><?php  echo date(SCOTTC_AFFILIATES_TIMESTAMP_FORMAT,strtotime($e->timestamp)); ?></td>
        <td><?php  echo $ih->button_js("Delete", 'delete_earning('.$e->id.')'); ?></td>
      </tr>
      <?php  } ?>
    </tbody>
  </table>
  <br /> <?php  } ?>
  <?php  Loader::packageElement('payments/payment_overview', SCOTTECOMAFFILATESPACKAGEHANDLE ,array('affiliateID' => $affiliate->affiliateID,'style'=>"width:500px;")); ?>
  <?php  } //end of show financials ?>
</div>
 

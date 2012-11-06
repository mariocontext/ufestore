<?php  Loader::helper('time_in_past',SCOTTECOMAFFILATESPACKAGEHANDLE); //
Loader::helper('concrete/urls');
$urlsHelper = new ConcreteUrlsHelper();
$pathToOrderDetails = $urlsHelper->getToolsURL('order_details', SCOTTECOMAFFILATESPACKAGEHANDLE);
?>
<h1><span>Cart Abandonment Details</span></h1>
<div class="ccm-dashboard-inner">
  <h2>View Abandoned Carts</h2>
  <p>This helps determine where users are leaving your checkout process</p>
  <?php  if($abandonedCarts){
    Loader::model('scottc_commerce_affiliate',SCOTTECOMAFFILATESPACKAGEHANDLE);
    //krsort($overviewAbandoned);
    if($overviewAbandoned){
      $flipped = array_flip($overviewAbandoned);
      krsort($flipped);
      $overviewAbandoned = $flipped; //wow really?
      ?>
  <h2>Abandoned Cart Steps</h2>
  <ul>
    <?php  foreach($overviewAbandoned as $k => $v){
      $amount = ($k > 1) ? 'times' : 'time';
      printf('<li><b>%s %s </b>on: %s</li>',$k,$amount,$v);

    }?>
  </ul>
  <?php  } ?>
  <h2>Active or Abandoned Carts</h2>
  <p>Please note, any customers currently going through the checkout process(since they haven't completed it yet) will have their last cart action reflected here.</p>
  <table class="grid-list" cellspacing="1" border="0">
    <thead><tr><td class="header">Order ID (Abandoned) Details</td><td class="header">Order Email</td><td class="header">Checkout Step</td><td class="header">Affiliate ID</td><td class="header">Last Activity Time</td><td class="header">Time Since Last Activity</td></tr></thead>
    <tbody>
    <?php  foreach($abandonedCarts as $activeRecord){
      if($activeRecord->affiliateID){
        $aff = ScottcCommerceAffiliate::getByID($activeRecord->affiliateID);
       $affiliate = ($aff->name) ? $aff->name : 'none';
       //print_r($activeRecord);
      }
      ?>
      <tr>
        <td><a class="modal-window" data-order-id="<?php  echo $activeRecord->orderID; ?>" data-order-email="<?php  echo $activerecord->orderEmail; ?>" data-modal-url="<?php  echo $pathToOrderDetails.'?id='.$activeRecord->orderID; ?>" href="<?php  echo $this->url('/dashboard/core_commerce/orders/search/detail/',$activeRecord->orderID); ?>"><?php  echo $activeRecord->orderID; ?></a></td>
        <td><?php  echo ($activeRecord->orderEmail) ? $activeRecord->orderEmail : 'n/a'; ?></td>
        <td><?php  echo $activeRecord->step; ?></td>
        <td><?php  echo $affiliate; ?></td>
        <td><?php  echo date(SCOTTC_AFFILIATES_TIMESTAMP_FORMAT,strtotime($activeRecord->updated)); ?></td><td><?php  echo TimeInPastHelper::distance_of_time_in_words(strtotime($activeRecord->updated)); ?></td>
      </tr>
    <?php  } ?>
    </tbody>
  </table>
  <?php  }else{
    //no abandoned carts, if we can only all be so lucky :)
    ?>
  <h2>No abandoned carts</h2>

  <?php  } ?>
  <script type="text/javascript">
  $('a.modal-window').bind('click',function(){
    $('#abandoned-order-details-detail').load($(this).attr('href') + ' div.ccm-dashboard-inner','',function(data){     
      $('div#abandoned-order-details').show();
      $('div#abandoned-order-details-detal').html(data);
      jQuery.fn.dialog.open({
      title: 'Abandoned Order:',
      element: 'div#abandoned-order-details-detail',
      width: 500,
      modal: false,
      height: 600
   });

    });
      return false;
    });
  </script>
  <div id="abandoned-order-details" style="display:none; width:600px;">
    <div class="ccm-dashboard-inner" id="abandoned-order-details-detail">
    </div>
  </div>
</div>

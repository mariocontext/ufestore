<?php   if ($this->controller->getTask() == 'edit') { 

$ih = Loader::helper('concrete/interface');
$valt = Loader::helper('validation/token');
?>

<?php   } else if ($this->controller->getTask() == 'detail') { ?>

<?php  
Loader::model('payment/method', 'core_commerce');
Loader::model('order/product', 'core_commerce');

$method = "";
$pm = CoreCommercePaymentMethod::getByID($order->getOrderPaymentMethodID());
if ($pm) {
	$method = $pm->getPaymentMethodName();
}

$products = CoreCommerceOrderProduct::getByOrderID($order->getOrderID());
$adjustments = $order->getOrderLineItems();
$billing = $order->getAttribute('billing_address');
$shipping = $order->getAttribute('shipping_address');
$bill_attr = AttributeSet::getByHandle('core_commerce_order_billing');
$ship_attr = AttributeSet::getByHandle('core_commerce_order_shipping');
$form = Loader::helper('form');

?>

	<h1><span><?php  echo t('Order Detail')?></span></h1>

	<div class="ccm-dashboard-inner">

	<h2><span><?php  echo t('Order Information')?></span></h2>

	<table width="100%">
	<tr>
		<td width="10%"><?php  echo t('Order ID:')?></td>
 		<td width="90%"><?php  echo $order->getOrderID()?></td>
	</tr>
	<tr>
		<td valign="top">Products:</td>
		<td>
			<table width="100%" class="ccm-results-list" cellspacing="0" cellpadding="0">
			<tr>
				<th width="5%">ID</th>
				<th width="20%">Name</th>
				<th width="8%">Price</th>
				<th width="7%">Quantity</th>
				<th width="60%">Options</th>
			</tr>
			<?php   $i = 0; foreach ($products as $op) { ?>
			<tr class="ccm-list-record<?php  echo ($i++%2)?'-alt':''?>">
				<td><?php  echo $op->getProductID()?></td>
				<td><?php  echo $op->getProductName()?></td>
				<td><?php  echo $op->getProductCartDisplayPrice()?></td>
				<td><?php  echo $op->getQuantity()?></td>
				<td>
					<?php   if (is_object($op->product)) { ?>
						<?php   $attribs = $op->getProductConfigurableAttributes() ?>
						<?php   $text = ''; foreach($attribs as $ak) { ?>
							<?php   $text .= $ak->render('label','',true) . ": " . $op->getAttribute($ak, 'display')."," ?>
						<?php   } ?>
						<?php  echo  rtrim($text, ",") ?>
					<?php   } else { ?>
						<?php  echo t('Unknown. This product has been removed.')?>
					<?php   } ?>
				</td>
			</tr>
			<?php   } ?>
			</table>
		</td>
	</tr>
	<?php   if (count($adjustments) > 0) { ?>
	<tr>
		<td valign="top">Adjustments:</td>
		<td>
			<table width="100%" class="ccm-results-list" cellspacing="0" cellpadding="0">
			<tr>
				<th width="25%">Name</th>
				<th width="75%">Amount</th>
			</tr>
			<?php   $i = 0; foreach ($adjustments as $adj) { ?>
			<tr class="ccm-list-record<?php  echo ($i++%2)?'-alt':''?>">
				<td><?php  echo $adj->getLineItemName()?></td>
				<td><?php  echo $adj->getLineItemDisplayTotal()?></td>
			</tr>
			<?php   } ?>
			</table>
		</td>
	</tr>
	<?php   } ?>
	<tr>
		<td>Total:</td>
		<td><?php  echo $order->getOrderDisplayTotal()?></td>
	</tr>
	<tr>
		<td>Payment Method:</td>
		<td><?php  echo $method?></td>
	</tr>
	<?php   if ($order->getOrderUserID() > 0) { ?>
	<tr>
		<td><?php  echo t('User Account:')?></td>
		<?php   $ui = UserInfo::getByID($order->getOrderUserID()); ?>
		<td><a href="<?php  echo $this->url('/dashboard/users/search?uID=' . $ui->getUserID())?>"><?php  echo $ui->getUserName()?></a></td>
	<?php   } ?>
	</table>

	<hr/>

	<h2><span><?php  echo ($shipping||$ship_attr)?t('Billing Information'):t('Billing/Shipping Information')?></span></h2>

	<table width="100%">
	<tr><td width="20%">First Name:</td><td><?php  echo $order->getAttribute('billing_first_name')?></td></tr>
	<tr><td>Last Name:</td><td><?php  echo $order->getAttribute('billing_last_name')?></td></tr>
	<tr><td>Email:</td><td><?php  echo $order->getOrderEmail()?></td></tr>
    <tr><td>Address1:</td><td><?php  echo $billing->getAddress1()?></td></tr>
    <tr><td>Address2:</td><td><?php  echo $billing->getAddress2()?></td></tr>
    <tr><td>City:</td><td><?php  echo $billing->getCity()?></td></tr>
    <tr><td>State/Province:</td><td><?php  echo $billing->getStateProvince()?></td></tr>
    <tr><td>Zip/Postal Code:</td><td><?php  echo $billing->getPostalCode()?></td></tr>
    <tr><td>Country:</td><td><?php  echo $billing->getCountry()?></td></tr>
	<tr><td>Phone:</td><td><?php  echo $order->getAttribute('billing_phone')?></td></tr>
	<?php   if ($bill_attr) {
		$akHandles = array('billing_first_name', 'billing_last_name', 'billing_address', 'billing_phone');
    	$keys = $bill_attr->getAttributeKeys();
    	foreach($keys as $ak) {
			if (!in_array($ak->getAttributeKeyHandle(), $akHandles)) {
            	print '<tr><td>' . $ak->getAttributeKeyName() . ':</td><td>' . $order->getAttribute($ak) . '</td>';
			}
    	}
	} ?>
	</table>

	<?php   if ($shipping || $ship_attr) { ?>

	<hr/>

	<h2><span>Shipping Information</span></h2>

	<table width="100%">
	<?php   if ($shipping) { ?>
	<tr><td width="20%">First Name:</td><td><?php  echo $order->getAttribute('shipping_first_name')?></td></tr>
	<tr><td>Last Name:</td><td><?php  echo $order->getAttribute('shipping_last_name')?></td></tr>
    <tr><td>Address1:</td><td><?php  echo $shipping->getAddress1()?></td></tr>
    <tr><td>Address2:</td><td><?php  echo $shipping->getAddress2()?></td></tr>
    <tr><td>City:</td><td><?php  echo $shipping->getCity()?></td></tr>
    <tr><td>State/Province:</td><td><?php  echo $shipping->getStateProvince()?></td></tr>
    <tr><td>Zip/Postal Code:</td><td><?php  echo $shipping->getPostalCode()?></td></tr>
    <tr><td>Country:</td><td><?php  echo $shipping->getCountry()?></td></tr>
	<tr><td>Phone:</td><td><?php  echo $order->getAttribute('shipping_phone')?></td></tr>
	<?php   } ?>
	<?php   if ($ship_attr) {
		$akHandles = array('shipping_first_name', 'shipping_last_name', 'shipping_address', 'shipping_phone');
    	$keys = $ship_attr->getAttributeKeys();
    	foreach($keys as $ak) {
			if (!in_array($ak->getAttributeKeyHandle(), $akHandles)) {
            	print '<tr><td>' . $ak->getAttributeKeyName() . ':</td><td>' . $order->getAttribute($ak) . '</td>';
			}
    	}
	} ?>

	</table>

	<?php   } ?>
	
	<hr />
	
	<h2><span><?php  echo t('Order Status')?></span></h2>
	
<?php   	$statusHistory = $order->getOrderStatusHistory();  ?>
	<?php   if (count($statusHistory) > 0) { ?>
		<?php  echo t('Currently: ')?><strong><?php  echo $order->getOrderStatusText()?></strong><br/><br/>
	<?php   } ?>
	<table border="0" cellspacing="1" cellpadding="0" class="grid-list">
	<tr>
		<td class="header"><?php  echo t('Status')?></td>
		<td class="header"><?php  echo t('Date Set')?></td>
		<td class="header"><?php  echo t('Set By')?></td>
	</tr>
	<?php   
	if (count($statusHistory) > 0) { ?>
	<?php   foreach($statusHistory as $st) { ?>
		<tr>
			<td><?php  echo $st->getOrderStatusHistoryStatusText()?></td>
			<td><?php  echo $st->getOrderStatusHistoryDateTime()?></td>
			<td><?php  
				if ($st->getOrderStatusHistoryUserID() > 0) { 
					$ui = UserInfo::getByID($st->getOrderStatusHistoryUserID());
					if (is_object($ui)) {
						print '<A href="' . $this->url('/dashboard/users/search?uID=' . $st->getOrderStatusHistoryUserID()) . '">' . $ui->getUserName() . '</a>';
					}
				}
			?></td>
		</tr>
		<?php   } ?>
	<?php   } else { ?>
	<tr>
		<td><?php  echo $order->getOrderStatusText()?></td>
		<td><?php  echo $order->getOrderDateAdded()?></td>
		<td>&nbsp;</td>
	</tr>
	<?php   } ?>	
	</table>
	<Br/>
	
	<form method="post" action="<?php  echo $this->action('update_order_status')?>">
	<h3><?php  echo t('Update Order Status')?></h3>
	<?php   $statuses = $order->getOrderAvailableStatuses(); ?>
	
	<?php  echo $form->hidden("orderID", $order->getOrderID())?>
	<?php  echo $form->select('oStatus', $statuses)?>
	<?php  echo $form->submit('submit', t('Set Status'))?>
	
	</form>
	</table>
	
	</div>

<?php   } else { ?>
	
	<h1><span><?php  echo t('Order Search')?></span></h1>

	<div class="ccm-dashboard-inner">
	
		<table id="ccm-search-form-table" >
			<tr>
				<td valign="top" class="ccm-search-form-advanced-col">
				<?php  
				$searchFields = array(
					'' => '** ' . t('Fields'),
					'date_added' => t('Created Between'),
				);
				
				$uh = Loader::helper('urls', 'core_commerce');
				Loader::model('attribute/categories/core_commerce_order', 'core_commerce');
				$searchFieldAttributes = CoreCommerceOrderAttributeKey::getSearchableList();
				foreach($searchFieldAttributes as $ak) {
					$searchFields[$ak->getAttributeKeyID()] = $ak->getAttributeKeyDisplayHandle();
				}
				
				
				?>
				
				<?php   $form = Loader::helper('form'); ?>
				
					
					<div id="ccm-core-commerce-order-search-field-base-elements" style="display: none">
				
						<span class="ccm-search-option"  search-field="date_added">
						<?php  echo $form->text('date_from', array('style' => 'width: 86px'))?>
						<?php  echo t('to')?>
						<?php  echo $form->text('date_to', array('style' => 'width: 86px'))?>
						</span>
						
						<?php   foreach($searchFieldAttributes as $sfa) { 
							$sfa->render('search'); ?>
						<?php   } ?>
						
					</div>
					
					<form method="get" id="ccm-core-commerce-order-advanced-search" action="<?php  echo $this->url('/dashboard/core_commerce/orders/search')?>">
					<?php  echo $form->hidden('mode', $mode); ?>
					<div id="ccm-core-commerce-order-search-advanced-fields" class="ccm-search-advanced-fields" >
					
						<input type="hidden" name="search" value="1" />
						<div id="ccm-search-box-title">
							<img src="<?php  echo ASSETS_URL_IMAGES?>/throbber_white_16.gif" width="16" height="16" id="ccm-search-loading" />
							<h2><?php  echo t('Search')?></h2>			
						</div>
						
						<div id="ccm-search-advanced-fields-inner">
							<div class="ccm-search-field">
								<table border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td width="100%">
									<?php  echo $form->label('keywords', t('Keywords'))?>
									<?php  echo $form->text('keywords', array('style' => 'width:200px')); ?>
									</td>
								</tr>
								</table>
							</div>
						
							<div class="ccm-search-field">
								<table border="0" cellspacing="0" cellpadding="0" width="100%">
								<tr>
									<td style="white-space: nowrap" align="right"><div style="width: 85px; padding-right:5px"><?php  echo t('Results Per Page')?></div></td>
									<td width="100%">
										<?php  echo $form->select('numResults', array(
											'10' => '10',
											'25' => '25',
											'50' => '50',
											'100' => '100',
											'500' => '500'
										), false, array('style' => 'width:75px'))?>
									</td>
									<td><a href="javascript:void(0)" id="ccm-core-commerce-order-search-add-option"><img src="<?php  echo ASSETS_URL_IMAGES?>/icons/add.png" width="16" height="16" /></a></td>
								</tr>	
								</table>
							</div>

							<div class="ccm-search-field">
								<table border="0" cellspacing="0" cellpadding="0" width="100%">
								<tr>
									<td style="white-space: nowrap" align="right"><div style="width: 85px; padding-right:5px"><?php  echo t('Status')?></div></td>
									<td width="100%">
										<?php   $statuses = array_merge(array('' => t('** All')));
										foreach(CoreCommerceOrder::getOrderAvailableStatuses() as $key => $value) {
											$statuses[$key] = $value;
										}
										?>
										<?php  echo $form->select('oStatus', $statuses, array('style' => 'width:75px'))?>
									</td>
									<td><a href="javascript:void(0)" id="ccm-core-commerce-order-search-add-option"><img src="<?php  echo ASSETS_URL_IMAGES?>/icons/add.png" width="16" height="16" /></a></td>
								</tr>	
								</table>
							</div>
							
							<div id="ccm-search-field-base">				
								<table border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td valign="top" style="padding-right: 4px">
										<?php  echo $form->select('searchField', $searchFields, array('style' => 'width: 85px'));
										?>
										<input type="hidden" value="" class="ccm-core-commerce-order-selected-field" name="selectedSearchField[]" />
										</td>
										<td width="100%" valign="top" class="ccm-selected-field-content">
										<?php  echo t('Select Search Field.')?>
										</td>
										<td valign="top">
										<a href="javascript:void(0)" class="ccm-search-remove-option"><img src="<?php  echo ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a>
										</td>
									</tr>
								</table>
							</div>
							
							<div id="ccm-search-fields-wrapper">			
							</div>
							
							<div id="ccm-search-fields-submit">
								<?php  echo $form->submit('ccm-search-core-commerce-orders', t('Search'))?>
							</div>
						</div>
					
				</div>
				
				</form>
				</td>		
	
				<td valign="top" width="100%">	
					
					<div id="ccm-search-advanced-results-wrapper">
						
						<div id="ccm-search-results">
						
						<div id="ccm-list-wrapper">
							<?php  echo $orderList->displaySummary();?>
							
							<?php  
							$txt = Loader::helper('text');
							$keywords = $_REQUEST['keywords'];
							$uh = Loader::helper('urls', 'core_commerce');
							
							if (count($orders) > 0) { ?>	
								<table border="0" cellspacing="0" cellpadding="0" id="ccm-core-commerce-order-list" class="draggable ccm-results-list">
								<tr>
									<th class="<?php  echo $orderList->getSearchResultsClass('orderID')?>"><a href="<?php  echo $orderList->getSortByURL('orderID', 'asc')?>"><?php  echo t('ID')?></a></th>
									<th class="<?php  echo $orderList->getSearchResultsClass('oDateAdded')?>"><a href="<?php  echo $orderList->getSortByURL('oDateAdded', 'asc')?>"><?php  echo t('Date Added')?></a></th>
									<th class="<?php  echo $orderList->getSearchResultsClass('oStatus')?>"><a href="<?php  echo $orderList->getSortByURL('oStatus', 'asc')?>"><?php  echo t('Status')?></a></th>
									<?php   
									$slist = CoreCommerceOrderAttributeKey::getColumnHeaderList();
									foreach($slist as $ak) { ?>
										<th class="<?php  echo $orderList->getSearchResultsClass($ak)?>"><a href="<?php  echo $orderList->getSortByURL($ak, 'asc')?>"><?php  echo $ak->getAttributeKeyDisplayHandle()?></a></th>
									<?php   } ?>			
									<th class="ccm-search-add-column-header"><a href="<?php  echo $uh->getToolsURL('customize_order_search_columns')?>" id="ccm-search-add-column"><img src="<?php  echo ASSETS_URL_IMAGES?>/icons/add.png" width="16" height="16" /></a></th>
								</tr>
							<?php  
								foreach($orders as $order) { 
									
									if (!isset($striped) || $striped == 'ccm-list-record-alt') {
										$striped = '';
									} else if ($striped == '') { 
										$striped = 'ccm-list-record-alt';
									}
						
									?>
								
									<tr class="ccm-list-record <?php  echo $striped?>">
									<td><a href="<?php  echo $this->controller->getBaseUrl().$this->action('detail', $order->getOrderID())?>"><?php  echo $order->getOrderID()?></a></td>
									<td><?php  echo date(t("m/d/Y - g:i A"), strtotime($order->getOrderDateAdded()))?></td>
									<td><?php  echo $order->getOrderStatusText()?></td>
									<?php   
									$slist = CoreCommerceOrderAttributeKey::getColumnHeaderList();
									foreach($slist as $ak) { ?>
										<td><?php  
										$vo = $order->getAttributeValueObject($ak);
										if (is_object($vo)) {
											print $vo->getValue('display');
										}
										?></td>
									<?php   } ?>
									<td>&nbsp;</td>
									</tr>
									<?php  
								}
						
							?>
							
							</table>
							
							
						
							<?php   } else { ?>
								
								<div id="ccm-list-none"><?php  echo t('No orders found.')?></div>
								
							
							<?php   } 
							$orderList->displayPaging(); ?>
							
						</div>						
						</div>
					
					</div>
				
				</td>	
			</tr>
		</table>		
		
	</div>
	
<?php   } ?>

<script type="text/javascript">
$(function() {
	ccm_coreCommerceSetupOrderSearch();
});
</script>

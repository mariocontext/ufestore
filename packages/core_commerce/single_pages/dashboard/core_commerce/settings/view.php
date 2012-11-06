<?php  
$this->addHeaderItem(Loader::helper('html')->css('ccm.core.commerce.dashboard.css', 'core_commerce'));
$ih = Loader::helper('concrete/interface');
$valt = Loader::helper('validation/token');
$pkg = Package::getByHandle('core_commerce');
$form = Loader::helper('form');
?>

<h1><span><?php  echo t('Attribute Settings')?></span></h1>
<div class="ccm-dashboard-inner">

	<div><a href="<?php  echo $this->url('/dashboard/core_commerce/products/attributes')?>"><?php  echo t('Product Attributes')?></a></div>
	<div><a href="<?php  echo $this->url('/dashboard/core_commerce/orders/attributes')?>"><?php  echo t('Order Attributes')?></a></div>

</div>

<h1><span><?php  echo t('Inventory Settings')?></span></h1>
<div class="ccm-dashboard-inner">
<form method="post" action="<?php  echo $this->action('save_inventory')?>" id="ccm-core-commerce-inventory-settings-form">
<h2><?php  echo t('Automatic Inventory Management')?></h2>
<div>
	<?php  echo $form->radio('MANAGE_INVENTORY', 1, $pkg->config('MANAGE_INVENTORY'))?><?php  echo t('Yes')?>&nbsp;&nbsp;
	<?php  echo $form->radio('MANAGE_INVENTORY', 0, $pkg->config('MANAGE_INVENTORY'))?><?php  echo t('No')?>
</div>

<div id="ccm-core-commerce-manage-inventory-trigger-wrapper">
<br/>
<h3><?php  echo t('Subtract inventory when the following occurs:')?></h3>
<div>
<?php  echo $form->select('MANAGE_INVENTORY_TRIGGER', array(
	'FINISHED' => t('Order clears the payment gateway'),
	'SHIPPED' => t('Order status changed to "shipped"'),
	'COMPLETED' => t('Order status changed to "completed"'),
), $pkg->config('MANAGE_INVENTORY_TRIGGER'))?>
</div>
</div>

<div class="ccm-buttons">
	<a href="javascript:void(0)" onclick="$('#ccm-core-commerce-inventory-settings-form').get(0).submit()" class="ccm-button-right accept"><span><?php  echo t('Save Inventory Settings')?></span></a>
</div>	
<div class="ccm-spacer">&nbsp;</div>

</form>
</div>

<h1><span><?php  echo t('Security Settings')?></span></h1>
<div class="ccm-dashboard-inner">
	<?php   $use_ssl = $pkg->config('SECURITY_USE_SSL') ?>
	<?php   $base_url_ssl = Config::get('BASE_URL_SSL') ?>
	<?php   $base_url_ssl = ($base_url_ssl ? $base_url_ssl : preg_replace('/http:/', 'https:', BASE_URL)) ?>
	<h2><span><?php  echo t('Use SSL for Checkout')?></span></h2>
	<form method="post" action="<?php  echo $this->action('save_security')?>" id="ccm-core-commerce-ssl-settings-form">
	<table>
	<tr>
		<td style="line-height:26px">
			<?php  echo $form->radio('SECURITY_USE_SSL', 'true', $use_ssl == 'true')?><?php  echo t('Yes')?>&nbsp;&nbsp;
			<?php  echo $form->radio('SECURITY_USE_SSL', 'false', $use_ssl != 'true')?><?php  echo t('No')?>&nbsp;&nbsp;
		</td>
		<td>
			<?php   $style = ($use_ssl == 'true' ? '' : 'style="display:none"'); ?>
			<?php   $base_url = preg_replace('/http:/', 'https:', $base_url_ssl) ?>
			<label for="BASE_URL_SSL" class="cc-ssl-base" <?php  echo $style?>>Base URL for SSL pages: </label>
			<input class="ccm-input-text cc-ssl-base" id="BASE_URL_SSL" type="text" name="BASE_URL_SSL" value="<?php  echo $base_url_ssl?>" <?php  echo $style?>/>
		</td>
	</tr>
	</table>
    <div class="ccm-buttons">
	<a href="javascript:void(0)" onclick="$('#ccm-core-commerce-ssl-settings-form').get(0).submit()" class="ccm-button-right accept"><span><?php  echo t('Update Security Settings')?></span></a>
	</div>
    <div class="ccm-spacer">&nbsp;</div>
	</form>
</div>

<h1><span><?php  echo t('General Settings')?></span></h1>
<div class="ccm-dashboard-inner">

	<?php   $pkg = Package::getByHandle('core_commerce'); ?>
	<?php   $currency = $pkg->config('CURRENCY_SYMBOL'); if (empty($currency)) { $currency = '$'; } ?>
	<?php   $emails = str_replace(',', "\n", $pkg->config('ENABLE_ORDER_NOTIFICATION_EMAIL_ADDRESSES')) ?>
	<?php   $blurb = $pkg->config('RECEIPT_EMAIL_BLURB'); if (empty($blurb)) { $blurb = t('Thank you for your purchase!'); } ?>
	<?php   $form = Loader::helper('form'); ?>
	<form method="post" action="<?php  echo $this->action('save_general')?>" id="ccm-core-commerce-general-settings-form">
	<h2><?php  echo t('Currency')?></h2>
	<strong><label for="CURRENCY_SYMBOL"><?php  echo t('Unit')?>: </label></strong>
	<?php  echo $form->text('CURRENCY_SYMBOL', $currency, array('style'=> 'width: 20px'))?>
	&nbsp;&nbsp;
	<?php   Loader::library('price', 'core_commerce'); ?>
	
	<strong><label for="CURRENCY_THOUSANDS_SEPARATOR"><?php  echo t('Thousands Separator')?>: </label></strong>
	<?php  echo $form->text('CURRENCY_THOUSANDS_SEPARATOR', CoreCommercePrice::getThousandsSeparator(), array('style'=> 'width: 20px'))?>
	&nbsp;&nbsp;
	<strong><label for="CURRENCY_DECIMAL_POINT"><?php  echo t('Decimal Symbol/Point')?>: </label></strong>
	<?php  echo $form->text('CURRENCY_DECIMAL_POINT', CoreCommercePrice::getDecimalPoint(), array('style'=> 'width: 20px'))?>

	
	<br/>
	
	<br/>
	

	<h2><?php  echo t('Email')?></h2>

	<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td colspan="2" class="header"><?php  echo t('Receipt Email Settings')?></td>
	</tr>
	<tr>
		<td class="subheader"><?php  echo $form->label('EMAIL_RECEIPT_EMAIL', t('Receipt From Email Address'))?></td>
		<td class="subheader"><?php  echo $form->label('EMAIL_RECEIPT_NAME', t('Receipt From Name'))?></td>
	</tr>
	<tr>
		<td><?php  echo $form->text('EMAIL_RECEIPT_EMAIL', $pkg->config('EMAIL_RECEIPT_EMAIL'), array('style' => 'width: 100%'))?></td>
		<td><?php  echo $form->text('EMAIL_RECEIPT_NAME', $pkg->config('EMAIL_RECEIPT_NAME'), array('style' => 'width: 100%'))?></td>
	</tr>
	<tr>
		<Td colspan="2" class="header"><?php  echo t('Notification Email Settings')?></td>
	</tr>
	<tr>
		<Td colspan="2" class="subheader">
		<?php  echo $form->checkbox('ENABLE_ORDER_NOTIFICATION_EMAILS', 1, $pkg->config('ENABLE_ORDER_NOTIFICATION_EMAILS'))?>
		<?php  echo $form->label('ENABLE_ORDER_NOTIFICATION_EMAILS', t('Send notification emails when an order is placed'))?>
		</td>
	</tr>
	<tr>
		<td width="50%"><?php  echo $form->label('ENABLE_ORDER_NOTIFICATION_EMAIL_ADDRESSES', t('Send To (One Email Per Line)'))?></td>
		<td width="50%"><?php  echo $form->label('RECEIPT_EMAIL_BLURB', t('Text to include with receipt emails'))?></td>
	</tr>
	<tr>
		<td>
		<?php  echo $form->textarea('ENABLE_ORDER_NOTIFICATION_EMAIL_ADDRESSES', $emails, array('style'=>'width:100%; height: 80px'))?>
		</td>
		<td>
		<?php  echo $form->textarea('RECEIPT_EMAIL_BLURB', $blurb, array('style'=>'width:100%; height: 80px'))?>
		</td>
	</tr>
	<tr>
		<td width="50%"><?php  echo t('Notification From Email Address')?></td>
		<td width="50%"><?php  echo t('Notification From Name')?></td>
	</tr>
	<tr>
		<td><?php  echo $form->text('EMAIL_NOTIFICATION_EMAIL', $pkg->config('EMAIL_NOTIFICATION_EMAIL'), array('style' => 'width: 100%'))?></td>
		<td><?php  echo $form->text('EMAIL_NOTIFICATION_NAME', $pkg->config('EMAIL_NOTIFICATION_NAME'), array('style' => 'width: 100%'))?></td>
	</tr>	
	</table>

    <div class="ccm-buttons">
	<a href="javascript:void(0)" onclick="$('#ccm-core-commerce-general-settings-form').get(0).submit()" class="ccm-button-right accept"><span><?php  echo t('Update General Settings')?></span></a>
	</div>
    <div class="ccm-spacer">&nbsp;</div>
	</form>
</div>


<script type="text/javascript">
$(function() {
	$('#SECURITY_USE_SSL1,#SECURITY_USE_SSL2').change(function(){$('.cc-ssl-base').toggle()});

	$("input[name=MANAGE_INVENTORY]").click(function() {
		ccm_updateManageInventorySettings($(this));
	});
	
	ccm_updateManageInventorySettings();
});

ccm_updateManageInventorySettings = function(obj) {
	if (!obj) {
		var obj = $("input[name=MANAGE_INVENTORY][checked=checked]");
	}
	if (obj.attr('value') == 1) {
		$("#MANAGE_INVENTORY_TRIGGER").attr('disabled' , false);
		$("#ccm-core-commerce-manage-inventory-trigger-wrapper").show();
	} else {
		$("#MANAGE_INVENTORY_TRIGGER").attr('disabled' , true);
		$("#ccm-core-commerce-manage-inventory-trigger-wrapper").hide();
	}
}

</script>
</script>

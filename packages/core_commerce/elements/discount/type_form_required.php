<?php   
$form = Loader::helper('form'); 
$ih = Loader::helper("concrete/interface");
$dt = Loader::helper('form/date_time');
$date = Loader::helper('date');

$valt = Loader::helper('validation/token');
$discountHandle = '';
$discountName = '';
$discountIsEnabled = 0;
$discountStart = '';
$discountEnd = '';
$discountCode = '';

if (is_object($discount)) {
	$discountHandle = $discount->getDiscountHandle();
	$discountName = $discount->getDiscountName();
	$discountIsEnabled = $discount->isDiscountEnabled();
	$discountStart = $date->getLocalDateTime($discount->getDiscountStart());
	$discountEnd = $date->getLocalDateTime($discount->getDiscountEnd());
	$discountCode = $discount->getDiscountCode();
	print $form->hidden('discountID', $discount->getDiscountID());
} else if ($this->controller->isPost()) {
	$discountStart = $dt->translate('discountStart');
	$discountEnd = $dt->translate('discountEnd');
}
?>
<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader" width="33%"><?php  echo t('Handle')?> <span class="required">*</span> <span class="explanation"><?php  echo t('(no spaces, for DB)')?></span></td>
	<td class="subheader" width="34%"><?php  echo t('Name')?> <span class="required">*</span> <span class="explanation"><?php  echo t('(displayed in the cart at checkout)')?></span></td>
	<td class="subheader" width="33%"><?php  echo t('Enabled?')?> <span class="required">*</span></td>
</tr>	
<tr>
	<td style="padding-right: 15px" valign="top"><?php  echo $form->text('discountHandle', $discountHandle, array('style' => 'width: 100%'))?></td>
	<td style="padding-right: 15px" valign="top"><?php  echo $form->text('discountName', $discountName, array('style' => 'width: 100%'))?></td>
	<td style="padding-right: 10px" valign="top">
	<?php  echo $form->select('discountIsEnabled', array(
			'1' => t('Enabled'), 
			'0' => t('Disabled')
		), $discountIsEnabled);?>
	</td>
</tr>
<tr>
	<td class="subheader" colspan="2"><?php  echo t('Limited Availability')?> <span class="explanation"><?php  echo t('(leave blank for unlimited)')?></span></td>
	<td class="subheader" width="33%"><?php  echo t('Coupon Code')?> <span class="explanation"><?php  echo t('(entered by shopper)')?></span></td>
</tr>	
<tr>
	<td style="padding-right: 15px" valign="top" colspan="2">
		<?php  echo  $dt->datetime('discountStart', $discountStart, true);?> <span class="explanation"><?php  echo t('(start date)')?></span><br/>
		<?php  echo  $dt->datetime('discountEnd', $discountEnd, true);?> <span class="explanation"><?php  echo t('(end date)')?></span>
	</td>
	<td style="padding-right: 10px" valign="top"><?php  echo $form->text('discountCode', $discountCode, array('style' => 'width: 100%'))?>
	</td>
</tr>
</table>

<?php  echo $form->hidden('discountTypeID', $type->getDiscountTypeID())?>
<?php  echo $valt->output('add_or_update_discount')?>
<?php   $type->render('type_form', $discount); ?>

<?php   if (is_object($discount)) { ?>
	<?php  echo $ih->submit(t('Update Discount'), 'ccm-core-commerce-discount-form')?>
<?php   } else { ?>
	<?php  echo $ih->submit(t('Add Discount'), 'ccm-core-commerce-discount-form')?>
<?php   } ?>

<div class="ccm-spacer">&nbsp;</div>

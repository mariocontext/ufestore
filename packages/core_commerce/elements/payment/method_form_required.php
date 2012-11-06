<?php   
$form = Loader::helper('form'); 
$ih = Loader::helper("concrete/interface");
$valt = Loader::helper('validation/token');
?>

<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader" width="33%"><?php  echo t('Handle')?> <span class="required">*</span></td>
	<td class="subheader" width="34%"><?php  echo t('Name')?> <span class="required">*</span></td>
	<td class="subheader" width="33%"><?php  echo t("Enabled")?></td>
</tr>	
<tr>
	<td style="padding-right: 15px" valign="top"><?php  echo $method->getPaymentMethodHandle()?></td>
	<td style="padding-right: 15px" valign="top"><?php  echo $method->getPaymentMethodName()?></td>
	<td style="padding-right: 10px" valign="top"><?php  
		print $form->select('paymentMethodIsEnabled', array('0' => t('No'), '1' => t('Yes')), $method->isPaymentMethodEnabled());
	?>
	</td>
</tr>
</table>

<?php  echo $form->hidden('paymentMethodID', $method->getPaymentMethodID())?>
<?php  echo $valt->output('update_payment_method')?>

<?php   $method->render('method_form'); ?>

<?php  echo $ih->submit(t('Update Payment Method'), 'ccm-core-commerce-payment-method-form')?>

<div class="ccm-spacer">&nbsp;</div>
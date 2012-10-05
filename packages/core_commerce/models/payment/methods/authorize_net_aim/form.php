<?php   $form = Loader::helper('form'); ?>

<?php   foreach($fields as $key => $value) { ?>

	<input type="hidden" name="<?php  echo $key?>" value="<?php  echo $value?>" />

<?php   } ?>

<div id="ccm-core-commerce-checkout-form-payment" class="ccm-core-commerce-checkout-form">
<h1><?php  echo t('Payment Information')?></h1>

<table border="0" cellspacing="0" cellpadding="0">
<tr>
    <td width="50%">Credit Card Number</td>
    <td width="50%">&nbsp;</td>
</tr>
<tr>
    <td><?php  echo $form->text('x_card_num')?> <span style="font-size:9px">(enter number without spaces or dashes)</span></td>
	<td>&nbsp;</td>
</tr>
<tr>
    <td>Expiration Date</td>
	<td>&nbsp;</td>
</tr>
<tr>
    <td><?php  echo $form->text('x_exp_date')?> <span style="font-size:9px">(mmyy)</span></td>
	<td>&nbsp;</td>
</tr>
<?php  
    $pkg = Package::getByHandle('core_commerce');
    if ($pkg->config('PAYMENT_METHOD_AUTHORIZENET_AIM_CCV') == 'true') {
?>
<tr>
    <td>Card Code (CCV)</td>
	<td>&nbsp;</td>
</tr>
<tr>
    <td><?php  echo $form->text('x_card_code')?> <span style="font-size:9px">(3 or 4 digit number)</span></td>
	<td>&nbsp;</td>
</tr>
<?php   } ?>
</table>

</div>

<?php  echo t("Click 'Next' to submit your payment to Authorize.Net."); ?>

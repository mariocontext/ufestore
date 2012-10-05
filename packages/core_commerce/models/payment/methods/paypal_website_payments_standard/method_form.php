<?php   $form = Loader::helper('form'); ?>

<table class="entry-form" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader" width="25%"><?php  echo t('Paypal Email/Business ID')?> <span class="ccm-required">*</span></td>
	<td class="subheader" width="20%"><?php  echo t('Test Mode')?> <span class="ccm-required">*</span></td>
	<td class="subheader" width="25%"><?php  echo t('Transation Type')?> <span class="ccm-required">*</span></td>
    <td class="subheader" width="30%"><?php  echo t('Address')?> <span class="ccm-required">*</span></td>
    <td class="subheader" width="25%"><?php  echo t('Currency')?></td>
</tr>
<tr>
	<td><?php  echo $form->text('PAYMENT_METHOD_PAYPAL_STANDARD_EMAIL', $PAYMENT_METHOD_PAYPAL_STANDARD_EMAIL)?></td>
	<td>
		<?php  echo $form->radio('PAYMENT_METHOD_PAYPAL_STANDARD_TEST_MODE', 'test', $PAYMENT_METHOD_PAYPAL_STANDARD_TEST_MODE != 'live')?><?php  echo t('Test Mode')?><br/>
		<?php  echo $form->radio('PAYMENT_METHOD_PAYPAL_STANDARD_TEST_MODE', 'live', $PAYMENT_METHOD_PAYPAL_STANDARD_TEST_MODE == 'live')?><?php  echo t('Live')?> 
	</td>
	<td>
		<?php  echo $form->radio('PAYMENT_METHOD_PAYPAL_STANDARD_TRANSACTION_TYPE', 'authorization', $PAYMENT_METHOD_PAYPAL_STANDARD_TRANSACTION_TYPE != 'sale')?><?php  echo t('Authorization')?><br/>
		<?php  echo $form->radio('PAYMENT_METHOD_PAYPAL_STANDARD_TRANSACTION_TYPE', 'sale', $PAYMENT_METHOD_PAYPAL_STANDARD_TRANSACTION_TYPE == 'sale')?><?php  echo t('Sale')?> 
	</td>
	<td>
		<?php  echo $form->radio('PAYMENT_METHOD_PAYPAL_STANDARD_PASS_ADDRESS', 'shipping', $PAYMENT_METHOD_PAYPAL_STANDARD_PASS_ADDRESS == 'shipping')?><?php  echo t('Shipping (if available)')?><br/>
		<?php  echo $form->radio('PAYMENT_METHOD_PAYPAL_STANDARD_PASS_ADDRESS', 'billing', $PAYMENT_METHOD_PAYPAL_STANDARD_PASS_ADDRESS == 'billing')?><?php  echo t('Billing')?>
	</td>
    <td><?php  echo $form->select('PAYMENT_METHOD_PAYPAL_STANDARD_CURRENCY_CODE', $paypal_currency_codes, $PAYMENT_METHOD_PAYPAL_STANDARD_CURRENCY_CODE);?></td>
</tr>
</table>
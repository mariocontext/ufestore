<?php   $form = Loader::helper('form'); ?>

<table class="entry-form" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader" width="50%"><?php  echo t('Merchant E-Solutions API Login ID')?> <span class="ccm-required">*</span></td>
	<td class="subheader" width="50%"><?php  echo t('Merchant E-Solutions Transaction Key')?> <span class="ccm-required">*</span></td>
</tr>
<tr>
	<td><?php  echo $form->text('PAYMENT_METHOD_MES_MERCHANT_ID', $PAYMENT_METHOD_MES_MERCHANT_ID)?></td>
	<td><?php  echo $form->text('PAYMENT_METHOD_MES_MERCHANT_KEY', $PAYMENT_METHOD_MES_MERCHANT_KEY)?></td>
</tr>
<tr>
	<td class="subheader"><?php  echo t('Card Code Verification (CCV)')?> <span class="ccm-required">*</span></td>
	<td class="subheader"><?php  echo t('Send Receipt Email')?> <span class="ccm-required">*</span></td>
</tr>
<tr>
	<td>
		<?php  echo $form->radio('PAYMENT_METHOD_MES_CCV', 'true', $PAYMENT_METHOD_MES_CCV == 'true')?>
		<?php  echo t('Enabled')?>&nbsp;&nbsp;
		<?php  echo $form->radio('PAYMENT_METHOD_MES_CCV', 'false', $PAYMENT_METHOD_MES_CCV != 'true')?>
		<?php  echo t('Disabled')?> 
	</td>
	<td>
		<?php  echo $form->radio('PAYMENT_METHOD_MES_EMAIL_RECEIPT', 'true', $PAYMENT_METHOD_MES_EMAIL_RECEIPT == 'true')?>
		<?php  echo t('Yes')?>&nbsp;&nbsp;
		<?php  echo $form->radio('PAYMENT_METHOD_MES_EMAIL_RECEIPT', 'false', $PAYMENT_METHOD_MES_EMAIL_RECEIPT != 'true')?>
		<?php  echo t('No')?> 
	</td>
</tr>
<tr>
	<td class="subheader"><?php  echo t('Transaction Type')?> <span class="ccm-required">*</span></td>
	<td class="subheader"><?php  echo t('Test Mode')?> <span class="ccm-required">*</span></td>
</tr>
<tr>
	<td>
		<?php  echo $form->radio('PAYMENT_METHOD_MES_TRANSACTION_TYPE', 'authorization', $PAYMENT_METHOD_MES_TRANSACTION_TYPE != 'sale')?>
		<?php  echo t('Authorization')?>&nbsp;&nbsp;
		<?php  echo $form->radio('PAYMENT_METHOD_MES_TRANSACTION_TYPE', 'sale', $PAYMENT_METHOD_MES_TRANSACTION_TYPE == 'sale')?>
		<?php  echo t('Sale')?> 
	</td>
	<td>
		<?php  echo $form->radio('PAYMENT_METHOD_MES_TEST_MODE', 'test-mode', $PAYMENT_METHOD_MES_TEST_MODE == 'test-mode')?>
		<?php  echo t('Live (Test Mode)')?>&nbsp;&nbsp;
		<?php  echo $form->radio('PAYMENT_METHOD_MES_TEST_MODE', 'live', $PAYMENT_METHOD_MES_TEST_MODE == 'live')?>
		<?php  echo t('Live')?> 
	</td>
</tr>
</table>

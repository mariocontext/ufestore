<?php   $form = Loader::helper('form'); ?>

<table class="entry-form" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader" width="33%"><?php  echo t('Authorize.Net API Login ID')?> <span class="ccm-required">*</span></td>
	<td class="subheader" width="34%"><?php  echo t('Authorize.Net Transaction Key')?> <span class="ccm-required">*</span></td>
	<td class="subheader" width="33%"><?php  echo t('Authorize.Net MD5 Secret')?> <span class="ccm-required">*</span></td>
</tr>
<tr>
	<td><?php  echo $form->text('PAYMENT_METHOD_AUTHORIZENET_SIM_API_LOGIN', $PAYMENT_METHOD_AUTHORIZENET_SIM_API_LOGIN)?></td>
	<td><?php  echo $form->text('PAYMENT_METHOD_AUTHORIZENET_SIM_TRANSACTION_KEY', $PAYMENT_METHOD_AUTHORIZENET_SIM_TRANSACTION_KEY)?></td>
	<td><?php  echo $form->text('PAYMENT_METHOD_AUTHORIZENET_SIM_MD5_SECRET', $PAYMENT_METHOD_AUTHORIZENET_SIM_MD5_SECRET, array('maxlength' => 18))?></td>
</tr>
<tr>
	<td class="subheader" width="33%"><?php  echo t('Test Mode')?> <span class="ccm-required">*</span></td>
	<td class="subheader" width="34%"><?php  echo t('Transaction Type')?> <span class="ccm-required">*</span></td>
	<td class="subheader" width="33%"><?php  echo t('Send Receipt Email')?> <span class="ccm-required">*</span></td>
</tr>
<tr>
	<td>
		<?php  echo $form->radio('PAYMENT_METHOD_AUTHORIZENET_SIM_TEST_MODE', 'test-account', $PAYMENT_METHOD_AUTHORIZENET_SIM_TEST_MODE == '' ||
		                                                                             $PAYMENT_METHOD_AUTHORIZENET_SIM_TEST_MODE == 'test-account')?><?php  echo t('Test Account')?>&nbsp;&nbsp;
		<?php  echo $form->radio('PAYMENT_METHOD_AUTHORIZENET_SIM_TEST_MODE', 'test-mode', $PAYMENT_METHOD_AUTHORIZENET_SIM_TEST_MODE == 'test-mode')?><?php  echo t('Live (Test Mode)')?>&nbsp;&nbsp;
		<?php  echo $form->radio('PAYMENT_METHOD_AUTHORIZENET_SIM_TEST_MODE', 'live', $PAYMENT_METHOD_AUTHORIZENET_SIM_TEST_MODE == 'live')?><?php  echo t('Live')?> 
	</td>
	<td>
		<?php  echo $form->radio('PAYMENT_METHOD_AUTHORIZENET_SIM_TRANSACTION_TYPE', 'authorization', $PAYMENT_METHOD_AUTHORIZENET_SIM_TRANSACTION_TYPE != 'sale')?><?php  echo t('Authorization')?>&nbsp;&nbsp;
		<?php  echo $form->radio('PAYMENT_METHOD_AUTHORIZENET_SIM_TRANSACTION_TYPE', 'sale', $PAYMENT_METHOD_AUTHORIZENET_SIM_TRANSACTION_TYPE == 'sale')?><?php  echo t('Sale')?> 
	</td>
	<td>
		<?php  echo $form->radio('PAYMENT_METHOD_AUTHORIZENET_SIM_EMAIL_RECEIPT', 'true', $PAYMENT_METHOD_AUTHORIZENET_SIM_EMAIL_RECEIPT == 'true')?><?php  echo t('Yes')?>&nbsp;&nbsp;
		<?php  echo $form->radio('PAYMENT_METHOD_AUTHORIZENET_SIM_EMAIL_RECEIPT', 'false', $PAYMENT_METHOD_AUTHORIZENET_SIM_EMAIL_RECEIPT != 'true')?><?php  echo t('No')?> 
	</td>
</tr>
</table>

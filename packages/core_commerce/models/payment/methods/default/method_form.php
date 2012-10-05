<?php   $form = Loader::helper('form'); ?>

<table class="entry-form" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader" width="50%"><?php  echo t('Send Receipt Email')?> <span class="ccm-required">*</span></td>
    <td class="subheader" width="50%"><?php  echo t('Transaction Type')?> <span class="ccm-required">*</span></td>
</tr>
<tr>
	<td>
		<?php  echo $form->radio('PAYMENT_METHOD_DEFAULT_EMAIL_RECEIPT', 'true', $PAYMENT_METHOD_DEFAULT_EMAIL_RECEIPT == 'true')?><?php  echo t('Yes')?>&nbsp;&nbsp;
		<?php  echo $form->radio('PAYMENT_METHOD_DEFAULT_EMAIL_RECEIPT', 'false', $PAYMENT_METHOD_DEFAULT_EMAIL_RECEIPT != 'true')?><?php  echo t('No')?> 
	</td>
    <td>
        <?php  echo $form->radio('PAYMENT_METHOD_DEFAULT_TRANSACTION_TYPE', 'authorization', $PAYMENT_METHOD_DEFAULT_TRANSACTION_TYPE == 'authorization')?><?php  echo t('Authorization')?>&nbsp;&nbsp;
        <?php  echo $form->radio('PAYMENT_METHOD_DEFAULT_TRANSACTION_TYPE', 'sale', $PAYMENT_METHOD_DEFAULT_TRANSACTION_TYPE != 'authorization')?><?php  echo t('Sale')?>
    </td>
</tr>
</table>

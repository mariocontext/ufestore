<?php   $form = Loader::helper('form'); ?>
<table class="entry-form" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader"><?php  echo t('Minimum Total Purchase Required')?> <span class="required">*</span></td>
	<td class="subheader"><?php  echo t('Shipping Method')?> <span class="required">*</span></td>
</tr>
<tr>
	<td valign="top">
		<?php  echo $form->text('minimumPurchase', $minimumPurchase)?><br/>
		<span class="explanation">(e.g. 12.95)</span>
	</td>
	<td valign="top">
		<?php   if(is_object($error) && $error->has()) { $error->output(); } else { ?>
			<?php  echo $form->radio('behavior', 'hide',  $behavior != 'replace')?>
			<?php   print $form->select('shippingMethod', $methods, $shippingMethod); ?>
		<?php   } ?>
	</td>
</tr>
</table>

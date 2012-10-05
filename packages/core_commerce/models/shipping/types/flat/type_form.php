<?php   $form = Loader::helper('form'); ?>
<table class="entry-form" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader"><?php  echo t('Starting Rate')?></td>
	<td class="subheader"><?php  echo t('Additional Cost Per Item')?></td>
</tr>
<tr>
	<td><?php  echo $form->text('SHIPPING_TYPE_FLAT_BASE', $SHIPPING_TYPE_FLAT_BASE)?></td>
	<td><?php  echo $form->text('SHIPPING_TYPE_FLAT_PER_ITEM', $SHIPPING_TYPE_FLAT_PER_ITEM)?></td>
</tr>
</table>

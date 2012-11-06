<?php   if (isset($type)) { ?>

<h1><span><?php  echo t('Edit Shipping Type')?></span></h1>
<div class="ccm-dashboard-inner">

<form method="post" action="<?php  echo $this->action('save')?>" id="ccm-core-commerce-shipping-type-form">

<?php   Loader::packageElement("shipping/type_form_required", 'core_commerce', array('type' => $type)); ?>

</form>	

</div>

<?php   } else { ?>

	<h1><span><?php  echo t('Shipping Types')?></span></h1>
	<div class="ccm-dashboard-inner">

	<?php   if (count($types) == 0) { ?>
		<p><?php  echo t('There are no shipping types installed.')?></p>
	<?php   } else { ?>
	
	<table class="grid-list" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="header"><?php  echo t('Handle')?></td>
		<td class="header"><?php  echo t('Name')?></td>
		<td class="header"><?php  echo t('Enabled')?></td>
		<td class="header">&nbsp;</td>
	</tr>
	<?php   foreach($types as $st) { ?>
		<tr>
			<td><?php  echo $st->getShippingTypeHandle()?></td>
			<td><?php  echo $st->getShippingTypeName()?></td>
			<td><?php  echo $st->isShippingTypeEnabled() ? t('Yes') : t('No')?></td>
			<td width="60"><?php  
				print $ih->button(t('Edit'), $this->url('/dashboard/core_commerce/shipping', 'edit_type', $st->getShippingTypeID()), 'left');		
			?>
		</tr>
	<?php   } ?>
	</table>
	<?php   } ?>
	
	</div>
	
	
	<h1><span><?php  echo t('Custom Shipping Types')?></span></h1>
<div class="ccm-dashboard-inner">
<?php   $types = CoreCommercePendingShippingType::getList(); ?>
<?php   if (count($types) == 0) { ?>
	<?php  echo t('There are no available shipping types awaiting installation.')?>
<?php   } else { ?>
	<table border="0" cellspacing="0" cellpadding="0">
	<?php   foreach($types as $at) { ?>
	<tr>
		<td style="padding:  0px 10px 10px 0px"><?php  echo $at->getShippingTypeName()?></td>
		<td style="padding:  0px 10px 10px 0px"><form id="ccm_core_commerce_shipping_type_install_form_<?php  echo $at->getShippingTypeHandle()?>" method="post" action="<?php  echo $this->action('add_shipping_type')?>"><?php  
			print $form->hidden("shippingTypeHandle", $at->getShippingTypeHandle());
			$b1 = $ih->submit(t('Install'), 'ccm_core_commerce_shipping_type_install_form_' . $at->getShippingTypeHandle());
			print $b1;
			?>
			</form></td>
	</tr>
	<?php   } ?>
	</table>
<?php   } ?>
</div>

<?php   } ?>
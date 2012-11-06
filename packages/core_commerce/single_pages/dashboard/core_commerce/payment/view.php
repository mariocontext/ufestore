<?php   if (isset($method)) { ?>

<h1><span><?php  echo t('Edit Payment Method')?></span></h1>
<div class="ccm-dashboard-inner">

<form method="post" action="<?php  echo $this->action('save')?>" id="ccm-core-commerce-payment-method-form">

<?php   Loader::packageElement("payment/method_form_required", 'core_commerce', array('method' => $method)); ?>

</form>	

</div>

<?php   } else { ?>

	<h1><span><?php  echo t('Payment Methods')?></span></h1>
	<div class="ccm-dashboard-inner">

	<?php   if (count($methods) == 0) { ?>
		<p><?php  echo t('There are no payment methods installed.')?></p>
	<?php   } else { ?>
	
	<table class="grid-list" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="header"><?php  echo t('Handle')?></td>
		<td class="header"><?php  echo t('Name')?></td>
		<td class="header"><?php  echo t('Enabled')?></td>
		<td class="header">&nbsp;</td>
	</tr>
	<?php   foreach($methods as $st) { ?>
		<tr>
			<td><?php  echo $st->getPaymentMethodHandle()?></td>
			<td><?php  echo $st->getPaymentMethodName()?></td>
			<td><?php  echo $st->isPaymentMethodEnabled() ? t('Yes') : t('No')?></td>
			<td width="60"><?php  
				print $ih->button(t('Edit'), $this->url('/dashboard/core_commerce/payment', 'edit_method', $st->getPaymentMethodID()), 'left');		
			?>
		</tr>
	<?php   } ?>
	</table>
	<?php   } ?>
	
	</div>
	
	
<h1><span><?php  echo t('Custom Payment Methods')?></span></h1>
<div class="ccm-dashboard-inner">
<?php   $methods = CoreCommercePendingPaymentMethod::getList(); ?>
<?php   if (count($methods) == 0) { ?>
	<?php  echo t('There are no available payment methods awaiting installation.')?>
<?php   } else { ?>
	<table border="0" cellspacing="0" cellpadding="0">
	<?php   foreach($methods as $at) { ?>
	<tr>
		<td style="padding:  0px 10px 10px 0px"><?php  echo $at->getPaymentMethodName()?></td>
		<td style="padding:  0px 10px 10px 0px"><form id="ccm_core_commerce_payment_method_install_form_<?php  echo $at->getPaymentMethodHandle()?>" method="post" action="<?php  echo $this->action('add_payment_method')?>"><?php  
			print $form->hidden("paymentMethodHandle", $at->getPaymentMethodHandle());
			$b1 = $ih->submit(t('Install'), 'ccm_core_commerce_payment_method_install_form_' . $at->getPaymentMethodHandle());
			print $b1;
			?>
			</form></td>
	</tr>
	<?php   } ?>
	</table>
<?php   } ?>
</div>

<h1><span><?php  echo t('Sales Tax Rates')?></span></h1>
<div class="ccm-dashboard-inner">
<?php   $rates = CoreCommerceSalesTaxRate::getList(); ?>
<?php   if (count($rates) == 0) { ?>
	<?php  echo t('There are no available sales tax rates.')?>
<?php   } else { ?>
	<table class="grid-list" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="header"><?php  echo t('Name')?></td>
		<td class="header"><?php  echo t('Enabled')?></td>
		<td class="header">&nbsp;</td>
	</tr>
	<?php   foreach($rates as $rate) { ?>
		<tr>
			<td><?php  echo $rate->getSalesTaxRateName()?></td>
			<td><?php  echo $rate->isSalesTaxRateEnabled() ? t('Yes') : t('No')?></td>
			<td width="60"><?php  
				print $ih->button(t('Edit'), $this->url('/dashboard/core_commerce/payment/tax', 'edit', $rate->getSalesTaxRateID()), 'left');		
			?>
		</tr>
	<?php   } ?>
	</table>
<?php   } ?>

<div class="ccm-spacer">&nbsp;</div>
<br/>

<?php  
	print $ih->button(t('Add Sales Tax Rate'), $this->url('/dashboard/core_commerce/payment/tax', 'add_rate'), 'left');		
?>

<div class="ccm-spacer">&nbsp;</div>

</div>


<?php   } ?>

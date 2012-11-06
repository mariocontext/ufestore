<?php   if (is_object($type) && ($this->controller->getTask() == 'select_type' || $this->controller->getTask() == 'add')) { ?>

<h1><span><?php  echo t('Add Discount')?></span></h1>
<div class="ccm-dashboard-inner">

	<form method="post" action="<?php  echo $this->action('add')?>" id="ccm-core-commerce-discount-form">
	
	<h2><?php  echo t('Discount Type')?></h2>
	<strong><?php  echo $type->getDiscountTypeName()?></strong><br/><br/>
	
	<?php   Loader::packageElement("discount/type_form_required", 'core_commerce', array('type' => $type)); ?>

	</form>	

</div>

<?php   } else if (isset($discount)) { ?>


<h1><span><?php  echo t('Edit Discount')?></span></h1>
<div class="ccm-dashboard-inner">

<h2><?php  echo t('Type')?></h2>

<strong><?php  echo $type->getDiscountTypeName()?></strong>
<br/><br/>


<form method="post" action="<?php  echo $this->action('edit')?>" id="ccm-core-commerce-discount-form">

<?php   Loader::packageElement("discount/type_form_required", 'core_commerce', array('type' => $type, 'discount' => $discount)); ?>

</form>	

</div>

<h1><span><?php  echo t('Delete Discount')?></span></h1>

<div class="ccm-dashboard-inner">
	<div class="ccm-spacer"></div>
	<?php  
	$valt = Loader::helper('validation/token');
	$ih = Loader::helper('concrete/interface');
	$delConfirmJS = t('Are you sure you want to remove this discount?');
	?>
	<script type="text/javascript">
	deleteDiscount = function() {
		if (confirm('<?php  echo $delConfirmJS?>')) { 
			location.href = "<?php  echo $this->url('/dashboard/core_commerce/discounts', 'delete', $discount->getDiscountID(), $valt->generate('delete_discount'))?>";				
		}
	}
	</script>
	<?php   print $ih->button_js(t('Delete Discount'), "deleteDiscount()", 'left');?>

	<div class="ccm-spacer"></div>
</div>




<?php   } else if ($this->controller->getTask() == 'manage_discount_types' || $this->controller->getTask() == 'discount_type_added') { ?>


	<h1><span><?php  echo t('Discount Types')?></span></h1>
	<div class="ccm-dashboard-inner">

	<?php   if (count($types) == 0) { ?>
		<p><?php  echo t('There are no discount types installed.')?></p>
	<?php   } else { ?>
	
	<table class="grid-list" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="header"><?php  echo t('Handle')?></td>
		<td class="header"><?php  echo t('Name')?></td>
	</tr>
	<?php   foreach($types as $st) { ?>
		<tr>
			<td><?php  echo $st->getDiscountTypeHandle()?></td>
			<td><?php  echo $st->getDiscountTypeName()?></td>
		</tr>
	<?php   } ?>
	</table>
	<?php   } ?>
	
	</div>
	
	
	<h1><span><?php  echo t('Custom Discount Types')?></span></h1>
	<div class="ccm-dashboard-inner">
	<?php   $types = CoreCommercePendingDiscountType::getList(); ?>
	<?php   if (count($types) == 0) { ?>
		<?php  echo t('There are no available discount types awaiting installation.')?>
	<?php   } else { ?>
		<table border="0" cellspacing="0" cellpadding="0">
		<?php   foreach($types as $at) { ?>
		<tr>
			<td style="padding:  0px 10px 10px 0px"><?php  echo $at->getDiscountTypeName()?></td>
			<td style="padding:  0px 10px 10px 0px"><form id="ccm_core_commerce_discount_type_install_form_<?php  echo $at->getDiscountTypeHandle()?>" method="post" action="<?php  echo $this->action('add_discount_type')?>"><?php  
				print $form->hidden("discountTypeHandle", $at->getDiscountTypeHandle());
				$b1 = $ih->submit(t('Install'), 'ccm_core_commerce_discount_type_install_form_' . $at->getDiscountTypeHandle());
				print $b1;
				?>
				</form></td>
		</tr>
		<?php   } ?>
		</table>
	<?php   } ?>
	</div>
	
<?php   } else { ?>

<h1><a class="ccm-dashboard-header-option" href="<?php  echo $this->url('/dashboard/core_commerce/discounts/', 'manage_discount_types')?>"><?php  echo t('Manage Discount Types')?></a><span><?php  echo t('Discounts')?></span></h1>
<div class="ccm-dashboard-inner">

<?php   if (count($discounts) == 0) { ?>
	<?php  echo t('No discounts defined.')?>
<?php   } else { ?>

<table border="0" cellspacing="1" cellpadding="0" class="grid-list">
<tr>
	<td class="header"><?php  echo t('Name')?></td>
	<td class="header"><?php  echo t('Type')?></td>
	<td class="header"><?php  echo t('Valid From')?></td>
	<td class="header"><?php  echo t('Valid To')?></td>
	<td class="header"><?php  echo t('Code')?></td>
	<td class="header"><?php  echo t('Enabled?')?></td>
</tr>


	<?php  
	foreach($discounts as $d) { ?>
	<tr id="discountID_<?php  echo $d->getDiscountID()?>">
		<td><a href="<?php  echo $this->url('/dashboard/core_commerce/discounts', 'edit', $d->getDiscountID())?>"><?php  echo $d->getDiscountName()?></a></td>
		<td><?php  echo $d->getDiscountType()->getDiscountTypeName()?></td>
		<td><?php  echo $date->getLocalDateTime($d->getDiscountStart())?></td>
		<td><?php  echo $date->getLocalDateTime($d->getDiscountEnd())?></td>
		<td><?php  echo $d->getDiscountCode()?></td>
		<td><?php  echo  ($d->isDiscountEnabled()) ? t('Yes') : t('No') ?></td>
	</tr>
	
	<?php   } ?>

</table>

<?php   } ?>

</div>

<h1><span><?php  echo t('Add Discount')?></span></h1>
<div class="ccm-dashboard-inner">

<h2><?php  echo t('Choose Discount Type')?></h2>

<form method="get" action="<?php  echo $this->action('select_type')?>" id="ccm-core-commerce-discount-type-form">

<?php  echo $form->select('discountTypeID', $seltypes)?>
<?php  echo $form->submit('submit', t('Go'))?>

</form>

</div>

<?php   } ?>
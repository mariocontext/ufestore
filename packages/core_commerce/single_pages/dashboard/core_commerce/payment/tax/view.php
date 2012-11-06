<?php   
if ($this->controller->getTask() == 'edit') { ?>


<h1><span><?php  echo t('Edit Sales Tax Rate')?></span></h1>
<div class="ccm-dashboard-inner">

<form method="post" action="<?php  echo $this->action('edit')?>" id="ccm-core-commerce-sales-tax-form">

<?php   Loader::packageElement("sales/tax_form_required", 'core_commerce', array('rate' => $rate)); ?>

</form>	

</div>

<h1><span><?php  echo t('Delete Sales Tax Rate')?></span></h1>

<div class="ccm-dashboard-inner">
	<div class="ccm-spacer"></div>
	<?php  
	$valt = Loader::helper('validation/token');
	$ih = Loader::helper('concrete/interface');
	$delConfirmJS = t('Are you sure you want to remove this rate?');
	?>
	<script type="text/javascript">
	deleteRate = function() {
		if (confirm('<?php  echo $delConfirmJS?>')) { 
			location.href = "<?php  echo $this->url('/dashboard/core_commerce/payment/tax', 'delete', $rate->getSalesTaxRateID(), $valt->generate('delete_rate'))?>";				
		}
	}
	</script>
	<?php   print $ih->button_js(t('Delete Rate'), "deleteRate()", 'left');?>

	<div class="ccm-spacer"></div>
</div>

<?php   
} else if ($this->controller->getTask() == 'add_rate') { ?>

<h1><span><?php  echo t('Add Sales Tax Rate')?></span></h1>
<div class="ccm-dashboard-inner">

<form method="post" action="<?php  echo $this->action('add_rate')?>" id="ccm-core-commerce-sales-tax-form">

<?php   Loader::packageElement("sales/tax_form_required", 'core_commerce'); ?>

</form>	

</div>

<?php   } ?>
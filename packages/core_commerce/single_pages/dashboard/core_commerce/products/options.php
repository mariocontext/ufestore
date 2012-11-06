
<?php   if ($this->controller->getTask() != 'select_type' && $this->controller->getTask() != 'add' && $this->controller->getTask() != 'edit') { ?>
	<h1><span><?php  echo t('Customer Choices')?><a href="<?php  echo View::url('/dashboard/core_commerce/products/search', 'view_detail', $product->getProductID())?>" class="ccm-dashboard-header-option"><?php  echo t('View Product')?></a></span></h1>
	<div class="ccm-dashboard-inner">
	<?php  
	$attribs = CoreCommerceProductOptionAttributeKey::getList($product);
	Loader::element('dashboard/attributes_table', array('category' => $category, 'attribs'=> $attribs, 'editURL' => '/dashboard/core_commerce/products/options')); ?>

</div>

<?php   } ?>


<?php   if (isset($key)) { ?>

<h1><span><?php  echo t('Edit Option')?></span></h1>
<div class="ccm-dashboard-inner">

<h2><?php  echo t('Type')?></h2>

<strong><?php  echo $type->getAttributeTypeName()?></strong>
<br/><br/>


<form method="post" action="<?php  echo $this->action('edit')?>" id="ccm-attribute-key-form">

<?php   Loader::element("attribute/type_form_required", array('category' => $category, 'type' => $type, 'key' => $key)); ?>

</form>	

</div>

<h1><span><?php  echo t('Delete Option')?></span></h1>

<div class="ccm-dashboard-inner">
	<div class="ccm-spacer"></div>
	<?php  
	$valt = Loader::helper('validation/token');
	$ih = Loader::helper('concrete/interface');
	$delConfirmJS = t('Are you sure you want to remove this option?');
	?>
	<script type="text/javascript">
	deleteOption = function() {
		if (confirm('<?php  echo $delConfirmJS?>')) { 
			location.href = "<?php  echo $this->url('/dashboard/core_commerce/products/options', 'delete', $key->getAttributeKeyID(), $valt->generate('delete_attribute'))?>";				
		}
	}
	</script>
	<?php   print $ih->button_js(t('Delete Option'), "deleteOption()", 'left');?>

	<div class="ccm-spacer"></div>
</div>

<?php   } else { ?>

<h1><span><?php  echo t('Add Option')?><a class="ccm-dashboard-header-option" href="<?php  echo $this->url('/dashboard/settings/', 'manage_attribute_types')?>"><?php  echo t('Manage Attribute Types')?></a></span></h1>
<div class="ccm-dashboard-inner">

<h2><?php  echo t('Product')?></h2>
<strong><?php  echo $product->getProductName()?></strong>
<Br/><br/>

<h2><?php  echo t('Choose Type')?></h2>

<form method="get" action="<?php  echo $this->action('select_type')?>" id="ccm-attribute-type-form">

<?php  echo $form->select('atID', $types)?>
<?php  echo $form->hidden('productID', $product->getProductID())?>
<?php  echo $form->submit('submit', t('Go'))?>

</form>

<?php   if (isset($type)) { ?>
	<br/>

	<form method="post" action="<?php  echo $this->action('add')?>" id="ccm-attribute-key-form">

	<?php  echo $form->hidden('productID', $product->getProductID())?>

	<?php   Loader::element("attribute/type_form_required", array('category' => $category, 'type' => $type)); ?>

	</form>	
<?php   } ?>

</div>

<?php   } ?>

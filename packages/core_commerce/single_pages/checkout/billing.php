<?php  

$fa = Loader::helper('form/attribute'); 
$form = Loader::helper('form');
$o = CoreCommerceCurrentOrder::get();
$fa->setAttributeObject($o);

?>

<div id="ccm-core-commerce-checkout-cart">

<?php  echo Loader::packageElement('cart_item_list', 'core_commerce', array('edit' => false))?>

</div>

<?php   if (isset($error) && $error->has()) {
	$error->output();
} ?>

<div id="ccm-core-commerce-checkout-form-billing" class="ccm-core-commerce-checkout-form">
<h1><?php  echo t('Billing Information')?></h1>

<form method="post" action="<?php  echo $action?>">

<table border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="25%"><label for="oEmail"><?php  echo t('Email Address')?> <span class="ccm-required">*</span></label><?php  echo $form->text('oEmail', $o->getOrderEmail())?></td>
	<td width="25%">
		<?php  
		$ak = CoreCommerceOrderAttributeKey::getByHandle('billing_first_name');
		echo $fa->display($ak, $ak->isOrderAttributeKeyRequired());
		?>
	</td>
	<td width="25%">
		<?php  
		$ak = CoreCommerceOrderAttributeKey::getByHandle('billing_last_name');
		echo $fa->display($ak, $ak->isOrderAttributeKeyRequired());
		?>
	<td width="25%">
		<?php  
		$ak = CoreCommerceOrderAttributeKey::getByHandle('billing_phone');
		echo $fa->display($ak, $ak->isOrderAttributeKeyRequired());
		?>
		</td>
</tr>
<tr>
	<td colspan="4">
		<?php  
		$ak = CoreCommerceOrderAttributeKey::getByHandle('billing_address');
		echo $fa->display($ak, $ak->isOrderAttributeKeyRequired());
		?>
		</td>
</tr>
<?php  
$set = AttributeSet::getByHandle('core_commerce_order_billing');
if (is_object($set)) { 
	$keys = $set->getAttributeKeys();
	$i = 0;
	foreach($keys as $ak) {
		if ($i % 3 == 0) {
			print '<tr>';
		}
		if (!in_array($ak->getAttributeKeyHandle(), $akHandles)) { ?>	
			<tr>
				<td colspan="4"><?php  echo $fa->display($ak->getAttributeKeyHandle(), $ak->isOrderAttributeKeyRequired())?></td>
			</tr>
		<?php   }
	}
} ?>
</table>

<div class="ccm-core-commerce-cart-buttons">
<?php  echo $this->controller->getCheckoutNextStepButton()?>
<?php  echo $this->controller->getCheckoutPreviousStepButton()?>
</div>

</form>

</div>
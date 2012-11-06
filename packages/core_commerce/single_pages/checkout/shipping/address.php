<?php  

$fa = Loader::helper('form/attribute'); 
$fo = Loader::helper('form');
$o = CoreCommerceCurrentOrder::get();
$fa->setAttributeObject($o);

?>

<div id="ccm-core-commerce-checkout-cart">

<?php  echo Loader::packageElement('cart_item_list', 'core_commerce', array('edit' => false))?>

</div>

<?php   if (isset($error) && $error->has()) {
	$error->output();
} ?>

<div id="ccm-core-commerce-checkout-form-shipping" class="ccm-core-commerce-checkout-form">
<h1><?php  echo t('Shipping Information')?></h1>

<form method="post" action="<?php  echo $action?>">

<table border="0" cellspacing="0" cellpadding="0">
<tr>
	<td colspan="3">
		<?php  echo $fo->checkbox('useBillingAddressForShipping', 1, $useBillingAddressForShipping); ?>
		<?php  echo $fo->label('useBillingAddressForShipping', t('Use Billing Address'))?>
		<?php  echo $fo->hidden('useBillingAddressAction', $this->url('/checkout/shipping/address', 'update_shipping_to_billing'))?>
	</td>
</tr>

<tr>
	<td>
		<?php   
		$ak = CoreCommerceOrderAttributeKey::getByHandle('shipping_first_name');
		echo $fa->display($ak, $ak->isOrderAttributeKeyRequired()); 
		?>
	</td>
	<td>
		<?php   
		$ak = CoreCommerceOrderAttributeKey::getByHandle('shipping_last_name');
		echo $fa->display($ak, $ak->isOrderAttributeKeyRequired()); 
		?>
	<td>
		<?php   
		$ak = CoreCommerceOrderAttributeKey::getByHandle('shipping_phone');
		echo $fa->display($ak, $ak->isOrderAttributeKeyRequired()); 
		?>
	</td>
</tr>
<tr>
	<td colspan="3">
		<?php   
		$ak = CoreCommerceOrderAttributeKey::getByHandle('shipping_address');
		echo $fa->display($ak, $ak->isOrderAttributeKeyRequired()); 
		?>
	</td>
</tr>
<?php  
$set = AttributeSet::getByHandle('core_commerce_order_shipping');
if (is_object($set)) { 
	$keys = $set->getAttributeKeys();
	foreach($keys as $ak) {
		if (!in_array($ak->getAttributeKeyHandle(), $akHandles)) { ?>
	
			<tr>
				<td colspan="3"><?php  echo $fa->display($ak->getAttributeKeyHandle())?></td>
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
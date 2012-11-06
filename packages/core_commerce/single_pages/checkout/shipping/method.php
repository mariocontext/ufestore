<?php  
$fo = Loader::helper('form');
?>

<div id="ccm-core-commerce-checkout-cart">
<?php  echo Loader::packageElement('cart_item_list', 'core_commerce', array('edit' => false))?>
</div>

<?php   if (isset($error) && $error->has()) {
	$error->output();
} ?>

<div id="ccm-core-commerce-checkout-form-shipping-method" class="ccm-core-commerce-checkout-form">

<h1><?php  echo t('Shipping Method')?></h1>

<form method="post" action="<?php  echo $action?>">

<?php   if (count($methods) > 0) { ?>
	<?php   foreach($methods as $sm) { 
		
		$type = $sm->getShippingType();
		if (!isset($typeID) || ($typeID != $type->getShippingTypeID())) { ?>
			<strong><?php  echo $type->getShippingTypeName()?></strong><br/>
		<?php   } ?>
		
		
		<div class="ccm-core-commerce-checkout-form-shipping-method-option">
			<?php  echo $fo->radio('shippingMethodID', $sm->getID(), $order->getOrderShippingMethodID())?>
			<?php  echo $sm->getName()?> <?php   if ($sm->getPrice() > 0) {  print t('(Cost: <strong>%s</strong>)', $sm->getDisplayPrice()); } else { print t('Cost: <strong>Free</strong>'); }  ?>
		</div>
	
		<?php   $typeID = $type->getShippingTypeID(); ?>
		
	<?php   } ?>
<?php   } else { ?>
	<?php  echo t('Shipping is Unavailable.');?>
<?php   } ?>

<div class="ccm-core-commerce-cart-buttons">
<?php  echo $this->controller->getCheckoutNextStepButton()?>
<?php  echo $this->controller->getCheckoutPreviousStepButton()?>
</div>

</form>

</div>
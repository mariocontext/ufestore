<div id="ccm-core-commerce-checkout-cart">

<?php  echo Loader::packageElement('cart_item_list', 'core_commerce', array('edit' => false))?>

<?php  
$fo = Loader::helper('form'); 
if (isset($error) && $error->has()) {
	$error->output();
} ?>

<div id="ccm-core-commerce-checkout-form-payment-method" class="ccm-core-commerce-checkout-form">

<h1><?php  echo t('Payment Method')?></h1>

<form method="post" action="<?php  echo $action?>">

<?php   if (count($methods) > 0) { ?>
	<?php   foreach($methods as $sm) { ?>
		
		<div class="ccm-core-commerce-checkout-form-payment-method-option">
			<?php  echo $fo->radio('paymentMethodID', $sm->getPaymentMethodID(), $sm->getPaymentMethodID() == $order->getOrderPaymentMethodID())?>
			<?php  echo $sm->getPaymentMethodName()?>
		</div>
	
		
	<?php   } ?>
<?php   } else { ?>
	<?php  echo t('Payment is Unavailable.');?>
<?php   } ?>

<div class="ccm-core-commerce-cart-buttons">
<?php  echo $this->controller->getCheckoutNextStepButton()?>
<?php  echo $this->controller->getCheckoutPreviousStepButton()?>
</div>

</form>

</div>
</div>
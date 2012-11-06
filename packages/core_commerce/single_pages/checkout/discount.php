<div id="ccm-core-commerce-checkout-cart">

<?php  echo Loader::packageElement('cart_item_list', 'core_commerce', array('edit' => false))?>

</div>

<?php   if (isset($error) && $error->has()) {
	$error->output();
} ?>

<div id="ccm-core-commerce-checkout-form-discount" class="ccm-core-commerce-checkout-form">
<h1><?php  echo t('Special Offer Code')?></h1>

<form method="post" action="<?php  echo $action?>">
<?php  echo t('To claim a discount, enter a valid coupon code below. If you don\'t have a coupon code, just click "Next" to skip this step.')?>
<?php  echo $form->text('discount_code', $discount_code, array('style' => 'width: 150px'))?>&nbsp;&nbsp;<?php  echo $this->controller->getCheckoutNextStepButton()?>

<div class="ccm-core-commerce-cart-buttons">
<?php  echo $this->controller->getCheckoutPreviousStepButton()?>
</div>

</form>

</div>
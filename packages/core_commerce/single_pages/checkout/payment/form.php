<div id="ccm-core-commerce-checkout-cart">

<?php  echo Loader::packageElement('cart_item_list', 'core_commerce', array('edit' => false))?>

<?php  
$fo = Loader::helper('form'); 
if (isset($error)) {
	if (isset($_REQUEST['error'])) {
		$error->add($_REQUEST['error']);
	}
	if ($error->has()) {
		$error->output();
	}
} ?>

<div id="ccm-core-commerce-checkout-form-payment-method" class="ccm-core-commerce-checkout-form">

<form method="post" action="<?php  echo $action?>">

<?php  echo $method->render("form")?>

<div class="ccm-core-commerce-cart-buttons">
<?php  echo $this->controller->getCheckoutNextStepButton()?>
<?php  echo $this->controller->getCheckoutPreviousStepButton()?>
</div>

</form>


</div>
</div>
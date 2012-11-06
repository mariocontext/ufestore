<?php   if (isset($error) && $error->has()) {
	$error->output();
} ?>

<div id="ccm-core-commerce-checkout-cart">

<?php  echo Loader::packageElement('cart_item_list', 'core_commerce', array('edit' => true, 'ajax' => false))?>

</div>


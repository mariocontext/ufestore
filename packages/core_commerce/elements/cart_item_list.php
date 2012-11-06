<?php  

Loader::model('product/model', 'core_commerce');
Loader::model('cart', 'core_commerce');

$cart = CoreCommerceCart::get();
Loader::library('price', 'core_commerce');
$items = $cart->getProducts(); 
$uh = Loader::helper('urls', 'core_commerce');

if (!isset($edit)) {
	$edit =true;
}

?>
<form method="post" action="<?php  echo View::url('/cart', 'update')?>" <?php   if ($dialog) { ?>onsubmit="return ccm_coreCommerceUpdateCart('<?php  echo $uh->getToolsURL('cart_dialog')?>')"<?php   } ?> name="ccm-core-commerce-cart-form<?php   if ($dialog) { ?>-dialog<?php   } ?>" id="ccm-core-commerce-cart-form<?php   if ($dialog) { ?>-dialog<?php   } ?>">
<?php  
if(is_array($errors) && count($errors)) {
	print '<ul class="ccm-error">';
	foreach($errors as $error){
		print '<li>' . $error . '</li>';
	}
	print '</ul>';
}
?>
<input type="hidden" name="dialog" value="<?php  echo $dialog?>" />

<table border="0" class="ccm-core-commerce-cart" cellspacing="0" cellpadding="0">
<tr>
	<th>&nbsp;</th>
	<th class="ccm-core-commerce-cart-name"><?php  echo t('Name')?></th>
	<th class="ccm-core-commerce-cart-quantity"><?php  echo t('Quantity')?></th>
	<th class="ccm-core-commerce-cart-quantity"><?php  echo t('Price')?></th>
	<th>&nbsp;</th>
</tr>
<tr>

<?php   if (count($items) == 0) { ?>

	<td colspan="4"><?php  echo t('Your cart is empty.')?></td>
	
<?php   } else { ?>


<?php   foreach($items as $it) { ?>
<tr>
	<td class="ccm-core-commerce-cart-thumbnail"><?php  echo $it->outputThumbnail()?></td>
	<td class="ccm-core-commerce-cart-name"><?php  echo $it->getProductName()?>
	<?php   $attribs = $it->getProductConfigurableAttributes()?>
	<?php   if (count($attribs) > 0) { ?>
		<br/>
		<?php  
		foreach($attribs as $ak) { ?>
			<?php  echo $ak->render("label")?>: <?php  echo $it->getAttribute($ak, 'display')?><br/>
		<?php   } 
	} ?>
	</td>
	<?php   if ($edit) { ?>
		<td class="ccm-core-commerce-cart-quantity"><?php  echo $it->getQuantityField()?></td>
	<?php   } else { ?>
		<td class="ccm-core-commerce-cart-quantity"><?php  echo $it->getQuantity()?></td>
	<?php   } ?>
	<td class="ccm-core-commerce-cart-price"><?php  echo $it->getProductCartDisplayPrice()?></td>
	<td class="ccm-core-commerce-cart-remove"><a href="<?php  echo View::url('/cart', 'remove_product', $it->getOrderProductID())?>" <?php   if ($dialog) { ?> onclick="ccm_coreCommerceRemoveCartItem(this, '<?php  echo $uh->getToolsURL('cart_dialog')?>'); return false" <?php   } ?>><img src="<?php  echo DIR_REL?>/packages/core_commerce/images/icons/delete_small.png" width="16" height="16" /></a></td>
</tr>
<?php   } ?>
<tr class="ccm-core-commerce-cart-subtotal">
	<td colspan="3">&nbsp;</td>
	<td class="ccm-core-commerce-cart-price"><?php  echo $cart->getBaseOrderDisplayTotal()?></td>
	<td>&nbsp;</td>
</tr>
<?php   
if (!$edit) { 
	// now we include any of the order's attributes which affect the total ?>
	<?php   $items = $cart->getOrderLineItems(); ?>
	<?php   
	foreach($items as $it) { ?>	
	<tr>
		<td class="ccm-core-commerce-cart-thumbnail">&nbsp;</td>
		<td class="ccm-core-commerce-cart-name"><?php  echo $it->getLineItemName()?></td>
		<td>&nbsp;</td>
		<td class="ccm-core-commerce-cart-price"><?php  echo $it->getLineItemDisplayTotal()?></td>
	</tr>
	
	<?php   }  ?>
	
	<?php   
	

if (count($items) > 0) { ?>

<tr class="ccm-core-commerce-cart-subtotal">
	<td colspan="3">&nbsp;</td>
	<td class="ccm-core-commerce-cart-price"><?php  echo $cart->getOrderDisplayTotal()?></td>
	<td>&nbsp;</td>
</tr>


<?php   }
	
	}
	
}

?>
</table>

<?php   if ($edit) { ?>

<div class="ccm-core-commerce-cart-buttons">
	<?php   if ($dialog == true) { ?>
		<input type="button" style="float: left" onclick="jQuery.fn.dialog.closeTop()" value="<?php  echo t('&lt; Return to Shopping')?>" class="ccm-core-commerce-cart-buttons-checkout" />
	<?php   } else if (isset($_REQUEST['rcID']) && $_REQUEST['rcID'] > 0) { 
		$rc = Page::getByID($_REQUEST['rcID']);
		?>
		<input type="button" style="float: left" onclick="window.location.href='<?php  echo Loader::helper('navigation')->getLinkToCollection($rc)?>'" value="<?php  echo t('&lt; Return to Shopping')?>" />
	<?php   } else { ?>
      <input type="button" style="float: left" onclick="history.go(-1);" value="<?php    echo t('&lt; Return to Shopping')?>" />
   <?php   } ?>
	
   <?php   if (count($items) > 0) { ?>

	<?php   if ($dialog) { ?>
	<input 
		type="button" 
		class="ccm-core-commerce-cart-buttons-checkout" 
		onclick="ccm_coreCommerceGoToCheckout('<?php  echo View::url('/checkout')?>')" 
		value="<?php  echo t('Check Out')?>" />	
	
	<?php   } else { ?>
		<input type="submit" name="checkout_no_dialog" value="<?php  echo t('Check Out')?>" class="ccm-core-commerce-cart-buttons-checkout" />
	<?php   } ?>
	
	<input type="submit" class="ccm-core-commerce-cart-buttons-checkout" value="<?php  echo t('Update Cart')?>" />	
		<img src="<?php  echo ASSETS_URL_IMAGES?>/throbber_white_16.gif" width="16" height="16" id="ccm-core-commerce-cart-update-loader" />
	<?php   } ?>
</div>

<?php   } ?>

<input type="hidden" name="rcID" value="<?php  echo $_REQUEST['rcID']?>" />
<?php   if ($dialog) { ?>
	<input type="hidden" name="method" value="JSON" />
<?php   } ?>
</form>

<div style="clear: both">&nbsp;</div>
<script>
	$(function() {
		$('#ccm-core-commerce-cart-form<?php   if ($dialog) { ?>-dialog<?php   } ?> .ccm-error').hide();
		$('#ccm-core-commerce-cart-form<?php   if ($dialog) { ?>-dialog<?php   } ?> .ccm-error').slideDown(500,
			function () {
				setTimeout("$('#ccm-core-commerce-cart-form<?php   if ($dialog) { ?>-dialog<?php   } ?> .ccm-error').slideUp('slow');",5000);
			}
		);
	});
</script>
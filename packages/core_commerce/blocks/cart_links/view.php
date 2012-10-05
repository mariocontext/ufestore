<?php   defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<?php  
Loader::model('cart', 'core_commerce');
$cart = CoreCommerceCart::get();
$th = Loader::helper('concrete/urls')->getToolsURL('cart_dialog', 'core_commerce');
$c = Page::getCurrentPage();
?>

<div class="cc-cart-links">
<?php   if ($showCartLink) { ?>
	<a href="<?php  echo $this->url('/cart?rcID=' . $c->getCollectionID())?>" onclick="ccm_coreCommerceLaunchCart(this, '<?php  echo $th?>'); return false"><?php  echo $cartLinkText?></a>
<?php   } ?>
<?php   if ($showItemQuantity) { ?>
	(<?php  echo $items?> item<?php  echo ($items != 1?'s':'')?>)
<?php   } ?>
<?php   if (($showCartLink || $showItemQuantity) && $showCheckoutLink  && $cart->getTotalProducts()) { ?>
    | 
<?php   } ?>
<?php   if ($showCheckoutLink && $cart->getTotalProducts() > 0) { ?>
	<a href="<?php  echo View::url('/checkout')?>"><?php  echo $checkoutLinkText?></a>
<?php   } ?>
</div >

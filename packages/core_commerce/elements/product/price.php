<?php   defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<?php   if (($product->getProductPrice() != $product->getProductSpecialPrice()) && $product->getProductSpecialPrice(false) > 0) { ?>
	<?php   if ($displayDiscount) { ?>
		<strike><?php  echo $product->getProductDisplayPrice()?></strike> <strong><?php  echo t("Now %s", $product->getProductSpecialDisplayPrice())?></strong>
	<?php   } else { ?>
		<?php  echo $product->getProductSpecialDisplayPrice()?>
	<?php   } ?>
<?php   } else if ($product->getProductPrice() > 0) { ?>
	<?php  echo $product->getProductDisplayPrice()?>
<?php   } ?>
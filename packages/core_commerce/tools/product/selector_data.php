<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('product/model', 'core_commerce');

$p = CoreCommerceProduct::getByID($_REQUEST['productID']);

?>

<div class="ccm-core-commerce-product-selected" productID="<?php  echo $_REQUEST['productID']?>" ccm-core-commerce-product-manager-field="<?php  echo $_REQUEST['ccm_core_commerce_product_selected_field']?>">
<div class="ccm-core-commerce-product-selected-thumbnail"><?php  echo $p->outputThumbnail()?></div>
<div class="ccm-core-commerce-product-selected-data"><div><strong><?php  echo $p->getProductName()?></strong><br/><?php  echo $p->getProductDisplayPrice()?></div><div></div></div>
<div class="ccm-spacer">&nbsp;</div>
</div>
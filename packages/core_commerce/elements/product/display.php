<?php   defined('C5_EXECUTE') or die(_("Access Denied."));

$uh = Loader::helper('urls', 'core_commerce');
$ih = Loader::helper('image');

$c = Page::getCurrentPage();

$link_before = '';
$link_after = '';
if ($product->getProductCollectionID()>0 && $displayLinkToFullPage && $GLOBALS['c']->getCollectionID() != $product->getProductCollectionID()) {
	$linksTo = Page::getByID($product->getProductCollectionID());
	if ($linksTo->cID>0) {
		$link_before = '<a href="'.$this->url($linksTo->getCollectionPath()).'">';
		$link_after = '</a>';
	}
}

// setup the image objects
if (!is_object($primaryImage)) {
	if($primaryImage != '') {
		$primaryImage = $product->getFileObjectFromImageOption($primaryImage);
	} else {
		$primaryImage = $product->getProductThumbnailImageObject();
	}
}

if(is_string($primaryHoverImage)) {
	$primaryHoverImage = $product->getFileObjectFromImageOption($primaryHoverImage);
}
if(is_string($overlayCalloutImage)) {
	$overlayCalloutImage = $product->getFileObjectFromImageOption($overlayCalloutImage);
} else {
	// legacy support
	$overlayCalloutImage = $product->getProductFullImageObject();
}


if ($displayImage) { 
	$pi = $primaryImage;
	if (is_object($pi)) {
		if($imageMaxWidth<=0) {$imageMaxWidth = 200;} 
		if($imageMaxHeight<=0) {$imageMaxHeight = 200;} 
		$thumb = $ih->getThumbnail($pi, $imageMaxWidth, $imageMaxHeight);
		$img = '<img src="' . $thumb->src . '" width="' . $thumb->width . '" height="' . $thumb->height . '" ';
		if (is_object($primaryHoverImage)) {
			$hthumb = $ih->getThumbnail($primaryHoverImage, $imageMaxWidth, $imageMaxHeight);
			if (is_object($hthumb)) {
				$img .= 'onmouseover="this.src=\'' . $hthumb->src . '\'" onmouseout="this.src=\'' . $thumb->src . '\'"';
			}
		}
		$img .= ' />';
		if (!$useOverlaysL) {
			$img = $link_before.$img.$link_after;
		}
	}
	
	if ($useOverlaysL) {
		$images = $product->getAdditionalProductImages();
		if (is_object($images[0])) { // load up first image for the lightbox
			$fi = $images[0];
			if($overlayLightboxImageMaxWidth<=0) {$overlayLightboxImageMaxWidth = 600; }
			if($overlayLightboxImageMaxHeight<=0) {$overlayLightboxImageMaxHeight = 600; }
			$resized = $ih->getThumbnail($fi, $overlayLightboxImageMaxWidth, $overlayLightboxImageMaxHeight);
			$img = '<a href="' . $resized->src .'" class="ccm-core-commerce-add-to-cart-lightbox-image" title="' . $fi->getTitle() . '">' . $img . '</a>';
		}
	}
}

$form = Loader::helper('form');
if (!$valign) {
	$valign = 'top';
}
if (!$halign) {
	$halign = 'left';
}
?>

<div class="ccm-core-commerce-add-to-cart">
<form method="post" id="ccm-core-commerce-add-to-cart-form-<?php  echo $id?>" action="<?php  echo $this->url('/cart', 'update')?>">
<input type="hidden" name="rcID" value="<?php  echo $c->getCollectionID()?>" />

<table border="0" cellspacing="0" cellpadding="0" width="100%">
<?php   if ($displayImage && $imagePosition == 'T') { ?>
<tr>
	<td valign="<?php  echo $valign?>" align="<?php  echo $halign?>" class="ccm-core-commerce-add-to-cart-thumbnail-top" <?php   if ($imageMaxHeight > 0) { ?>style="height:<?php  echo $imageMaxHeight?>px"<?php   } ?>>
		<div class="ccm-core-commerce-add-to-cart-image"><?php  echo $img?></div>
	</td>
</tr>
<?php   } ?>
<tr>
	<?php   if ($displayImage && $imagePosition == 'L') { ?>
	<td valign="<?php  echo $valign?>" align="<?php  echo $halign?>" class="ccm-core-commerce-add-to-cart-thumbnail-left">
		<div class="ccm-core-commerce-add-to-cart-image" <?php   if ($imageMaxWidth > 0) { ?>style="width:<?php  echo $imageMaxWidth?>px"<?php   } ?>><?php  echo $img?></div>
	</td>
	<?php   } ?>
	<td valign="top" align="<?php  echo $halign?>" width="100%">
		<div>
		<?php   if ($displayNameP) { ?>
			<strong><?php  echo $link_before.$product->getProductName().$link_after?></strong> 
		<?php   } ?>
		<?php   if ($displayNameP && $displayPriceP) { ?>
		- 
		<?php   } ?>
		<?php   if ($displayPriceP) { ?>
			<?php  echo Loader::packageElement('product/price', 'core_commerce', array('product' => $product, 'displayDiscount' => $displayDiscountP)); ?>
		<?php   } ?>
		
		</div>
		<?php   if ($displayDescriptionP) { ?>
		<div>
		<?php  echo $product->getProductDescription()?>
		</div>
		<?php   } ?>
		
		<table border="0" cellspacing="0" cellpadding="0">
		<?php   if ($displayDimensionsP) { ?>
		<tr>
			<td valign="top" style="padding-right: 10px"><?php  echo t('Dimensions')?></td>
			<td valign="top"><?php  echo $product->getProductDimensionLength()?>x<?php  echo $product->getProductDimensionWidth()?>x<?php  echo $product->getProductDimensionHeight()?> <?php  echo $product->getProductDimensionUnits()?></td>
		</tr>
		
		<?php   } 

		if ($displayQuantityInStockP) { ?>
		<tr>
			<td valign="top" style="padding-right: 10px"><?php  echo t('# In Stock')?></td>
			<td valign="top"><?php  echo $product->getProductQuantity()?></td>
		</tr>
		
		<?php   }
		
		foreach($attributesP as $dak) { 
			$av = $product->getAttributeValueObject($dak);
			if (is_object($av)) { ?>
		
		<tr>
			<td valign="top" style="padding-right: 10px"><?php  echo $dak->getAttributeKeyName()?></td>
			<td valign="top"><?php  echo $av->getValue('display')?></td>
		</tr>
		
			<?php   } ?>
		<?php   } ?>
		
		<?php   
		if (!$displayAddToCart) {
			?></table><?php  
		}
		else {
			$attribs = $product->getProductConfigurableAttributes();			
			foreach($attribs as $at) { ?>
			<tr>
				<td valign="top" style="padding-right: 10px"><?php  echo $at->render("label")?><?php   if ($at->isProductOptionAttributeKeyRequired()) { ?> <span class="ccm-required">*</span><?php   } ?></td>
				<td valign="top"><?php  echo $at->render('form');?></td>
			</tr>
			
			<?php   } 
			if ($displayQuantity) { 
			?>
			<tr>
				<td valign="top" style="padding-right: 10px"><?php  echo $form->label('quantity', t('Quantity'))?> <span class="ccm-required">*</span></td>
				<td valign="top">
				<?php   if ($product->productIsPhysicalGood()) { ?>
					<?php  echo $form->text("quantity", 1, array("style" => "width: 20px"));?>
				<?php   } else { ?>
					<?php  echo $form->hidden("quantity", 1);?>
					1
				<?php   } ?>
				</td>
			</tr>
			<?php   } ?>
			</table>
			<div style="padding: 6px 0px 12px 0px">
				<?php   if ($product->isProductEnabled()) { ?>
					<?php  echo $form->submit('submit', $addToCartText); ?>
					<img src="<?php  echo ASSETS_URL_IMAGES?>/throbber_white_16.gif" width="16" height="16" class="ccm-core-commerce-add-to-cart-loader" />

				<?php   } else { ?>
					<strong><?php  echo t('This product is unavailable.')?></strong>
				<?php   } ?>
			</div>
			<?php  echo $form->hidden('productID', $product->getProductID()); ?>
		<?php   } ?>
	</td>
	<?php   if ($displayImage && $imagePosition == 'R') { ?>
	<td valign="<?php  echo $valign?>" class="ccm-core-commerce-add-to-cart-thumbnail-right">
		<div class="ccm-core-commerce-add-to-cart-image"><?php  echo $img?></div>
	</td>
	<?php   } ?>
</tr>
<?php   if ($displayImage && $imagePosition == 'B') { ?>
<tr>
	<td valign="<?php  echo $valign?>" class="ccm-core-commerce-add-to-cart-thumbnail-bottom" <?php   if ($imageMaxHeight > 0) { ?>style="height:<?php  echo $imageMaxHeight?>px"<?php   } ?>>
		<div class="ccm-core-commerce-add-to-cart-image"><?php  echo $img?></div>
	</td>
</tr>
<?php   } ?>
</table>
<?php   if ($useOverlaysC) { ?>
	<div class="ccm-core-commerce-add-to-cart-callout">
		<div class="ccm-core-commerce-add-to-cart-callout-inner">
		<div class="ccm-core-commerce-add-to-cart-callout-image">
			<?php  
			if(is_object($overlayCalloutImage)) {
				$im = Loader::helper('image');
				if($overlayCalloutImageMaxWidth<=0) {
					$overlayCalloutImageMaxWidth = 300;
				}
				if($overlayCalloutImageMaxHeight<=0) {
					$overlayCalloutImageMaxHeight = 300;
				}
				$im->outputThumbnail($overlayCalloutImage, $overlayCalloutImageMaxWidth, $overlayCalloutImageMaxHeight);
			}
			?>
		
		<div>
		<?php   if ($displayNameC) { ?>
			<strong><?php  echo $product->getProductName()?></strong> 
		<?php   } ?>
		<?php   if ($displayNameC && $displayPriceC) { ?>
		- 
		<?php   } ?>
		<?php   if ($displayPriceC) { ?>
			<?php  echo Loader::packageElement('product/price', 'core_commerce', array('product' => $product, 'displayDiscount' => $displayDiscount)); ?>
		<?php   } ?>
		</div>
		
		<?php   if ($displayDescriptionC) { ?>
		<div>
		<?php  echo $product->getProductDescription()?>
		</div>
		<?php   } ?>
		
		<table border="0" cellspacing="0" cellpadding="0">
		<?php   if ($displayDimensionsC) { ?>
		<tr>
			<td valign="top" style="padding-right: 10px"><?php  echo t('Dimensions')?></td>
			<td valign="top"><?php  echo $product->getProductDimensionLength()?>x<?php  echo $product->getProductDimensionWidth()?>x<?php  echo $product->getProductDimensionHeight()?> <?php  echo $product->getProductDimensionUnits()?></td>
		</tr>
		
		<?php   } 

		if ($displayQuantityInStockC) { ?>
		<tr>
			<td valign="top" style="padding-right: 10px"><?php  echo t('# in Stock')?></td>
			<td valign="top"><?php  echo $product->getProductQuantity()?></td>
		</tr>
		
		<?php   } 		
		foreach($attributesC as $dak) { 
			$av = $product->getAttributeValueObject($dak);
			if (is_object($av)) { ?>
		
		<tr>
			<td valign="top" style="padding-right: 10px"><?php  echo $dak->getAttributeKeyName()?></td>
			<td valign="top"><?php  echo $av->getValue('display')?></td>
		</tr>
		
			<?php   } ?>
		<?php   } ?>
		</table>
		</div>
		</div>
	</div>
<?php   } 

if ($useOverlaysL) {
	for ($i = 1; $i < count($images); $i++ ) {
		$f = $images[$i];
		$resized = $ih->getThumbnail($f, $overlayLightboxImageMaxWidth, $overlayLightboxImageMaxHeight);
		?>
		<a style="display: none" href="<?php  echo $resized->src?>" title="<?php  echo $f->getTitle()?>" class="ccm-core-commerce-add-to-cart-lightbox-image">&nbsp;</a>
	<?php   } ?>	
	<div class="ccm-core-commerce-add-to-cart-lightbox-caption">
		<table border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		
		<?php   if ($displayNameL) { ?>
			<tr>
			<td colspan="2">
				<strong><?php  echo $product->getProductName()?></strong> 
			</td>
			</tr>
		<?php   } ?>
		
		<?php   if ($displayPriceL) { ?>
		<tr>	
			<td valign="top" style="padding-right: 10px"><?php  echo t('Price')?></td>
			<td valign="top"><?php  echo Loader::packageElement('product/price', 'core_commerce', array('product' => $product, 'displayDiscount' => $displayDiscount)); ?></td>
		</tr>
		
		<?php   } ?>
		
		<?php   if ($displayDescriptionL) { ?>
		<tr>	
			<td valign="top" style="padding-right: 10px"><?php  echo t('Description')?></td>
			<td><?php  echo $product->getProductDescription()?></td>
		</tr>		
		<?php   
		}
		
		if ($displayDimensionsL) { ?>
		<tr>
			<td valign="top" style="padding-right: 10px"><?php  echo t('Dimensions')?></td>
			<td valign="top"><?php  echo $product->getProductDimensionLength()?>x<?php  echo $product->getProductDimensionWidth()?>x<?php  echo $product->getProductDimensionHeight()?> <?php  echo $product->getProductDimensionUnits()?></td>
		</tr>
		
		<?php   } 

		if ($displayQuantityInStockL) { ?>
		<tr>
			<td valign="top" style="padding-right: 10px"><?php  echo t('# In Stock')?></td>
			<td valign="top"><?php  echo $product->getProductQuantity()?></td>
		</tr>
		
		<?php   } 		
		
		foreach($attributesL as $dak) { 
			$av = $product->getAttributeValueObject($dak);
			if (is_object($av)) { ?>
		
		<tr>
			<td valign="top" style="padding-right: 10px"><?php  echo $dak->getAttributeKeyName()?></td>
			<td valign="top"><?php  echo $av->getValue('display')?></td>
		</tr>
		
			<?php   } ?>
		<?php   } ?>
		</table>
	</div>
	
	<?php  
}

?>
</form>
</div>

<?php   if (!$c->isEditMode()) { ?>
<script type="text/javascript">
	$(function() {
		ccm_coreCommerceRegisterAddToCart('ccm-core-commerce-add-to-cart-form-<?php  echo $id?>', '<?php  echo $uh->getToolsURL('cart_dialog')?>');
		<?php   if ($useOverlaysC) { ?>
			ccm_coreCommerceRegisterCallout('ccm-core-commerce-add-to-cart-form-<?php  echo $id?>');
		<?php   } ?>
		<?php   if ($useOverlaysL) { ?>
			$('#ccm-core-commerce-add-to-cart-form-<?php  echo $id?> .ccm-core-commerce-add-to-cart-lightbox-image').lightBox({
				imageLoading: '<?php   echo ASSETS_URL_IMAGES?>/throbber_white_32.gif',
				imageBtnPrev: '<?php   echo $lightboxURL?>/images/lightbox-btn-prev.gif',	
				imageBtnNext: '<?php   echo $lightboxURL?>/images/lightbox-btn-next.gif',			
				imageBtnClose: '<?php   echo $lightboxURL?>/images/lightbox-btn-close.gif',	
				imageBlank:	'<?php   echo $lightboxURL?>/images/lightbox-blank.gif',   
				imageCaptionAdditional: '#ccm-core-commerce-add-to-cart-form-<?php  echo $id?> .ccm-core-commerce-add-to-cart-lightbox-caption'
			});
		<?php   } ?>
		
	});
</script>
<?php   } ?>
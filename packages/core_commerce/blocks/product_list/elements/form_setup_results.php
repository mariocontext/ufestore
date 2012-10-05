<?php  

$form = Loader::helper('form');

extract($controller->block_args);

$allFields = $controller->getProductFieldsInOrder();
$sortFields = $controller->getProductsSortableFields();

if ($baseSearchPath == "") $baseSearchPath = "THIS";
if (!isset($options['show_search_form'])) $options['show_search_form'] = 1;
if (!isset($options['show_products'])) $options['show_products'] = 1;

if ($addToCartText == "" && $this->bID==0) $addToCartText = t('Add to cart');

?>
<div class="ccm-block-field-group">
<h2><?php  echo t('Where should the product properties be displayed?')?></h2>

<table border="0" cellspacing="0" class="ccm-grid" cellpadding="0" width="400" id="ccm-core-commerce-product-attribute-grid">
<tr>
	<th><?php  echo t('Property')?></th>
	<th><?php  echo t('Page')?></th>
	<th><?php  echo t('Callout')?></th>
	<th><?php  echo t('Lightbox')?></th>
</tr>
<?php   foreach ($allFields as $field => $label) { ?>
<tr>
	<td><?php  echo $label?></td>
	<td class="ccm-grid-cb"><?php  echo $form->checkbox('displayField['.$field.'][]', 'P', $controller->shouldDisplay($field,'P'))?></td>
	<td class="ccm-grid-cb"><?php  echo $form->checkbox('displayField['.$field.'][]', 'C', $controller->shouldDisplay($field,'C'))?></td>
	<td class="ccm-grid-cb"><?php  echo $form->checkbox('displayField['.$field.'][]', 'L', $controller->shouldDisplay($field,'L'))?></td>
</tr>
<?php   } ?>
</table>

</div>

<div class="ccm-block-field-group">
<h2><?php  echo t('Product Links')?></h2>
<table border="0" cellspacing="0" cellpadding="0">
<tr>
	<td valign="top">
	<?php  echo $form->checkbox('displayLinkToFullPage', 1, $displayLinkToFullPage)?> <?php  echo $form->label('displayLinkToFullPage', t('Link product to its default page.'))?><br />
	<?php  echo $form->checkbox('displayAddToCart', 1, $displayAddToCart)?> <?php  echo $form->label('displayAddToCart', t('Include add to cart link.'))?><br />
	<span style="margin-left:15px">&nbsp;</span><?php  echo $form->checkbox('displayQuantity', 1, $displayQuantity)?> <?php  echo $form->label('displayQuantity', t('Allow quantity to be chosen.'))?>
	</td>
	<td><div style="width: 60px">&nbsp;</div></td>
	<td valign="top">
	<?php  echo $form->label('addToCartText', t('Add to Cart Button Text'))?><br/>
	<?php  echo $form->text('addToCartText', $addToCartText)?></div>
	</td>
</tr>
</table>
</div>

<div class="ccm-block-field-group">
	<h2><?php  echo t('Image')?></h2>
	<?php  echo $form->checkbox('displayImage', 1, $displayImage)?> <?php  echo $form->label('displayImage', t('Display product image.'))?>
	<div class="ccm-core-commerce-product-block-image-fields" style="padding-left:22px;">
		<br/>
		<div>
			<?php  echo  $form->select('primaryImage', $controller->getAvailableImageOptions(), $primaryImage); ?>
		</div>
		<br/>
		<table border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td valign="top">
			<?php  echo $form->label('imageMaxWidth', t('Width'))?><br/>
			<?php  echo $form->text('imageMaxWidth', $imageMaxWidth, array('size'=>'6'))?>
			</td>
			<td><div style="width: 10px">&nbsp;</div></td>
			<td valign="top">
				<?php  echo $form->label('imageMaxHeight', t('Height'))?><br/>
				<?php  echo $form->text('imageMaxHeight', $imageMaxHeight, array('size'=>'6'))?>
			</td>
			<td><div style="width: 10px">&nbsp;</div></td>
			<td valign="top">
				<?php  echo t('Image Position')?><br/>
				<?php  echo $form->radio('imagePosition', 'L', $imagePosition)?>
				<?php  echo $form->label('imagePosition1', t('Left'))?>
				&nbsp;&nbsp;
				<?php  echo $form->radio('imagePosition', 'T', $imagePosition)?>
				<?php  echo $form->label('imagePosition2', t('Top'))?>
				&nbsp;&nbsp;
				<?php  echo $form->radio('imagePosition', 'R', $imagePosition)?>
				<?php  echo $form->label('imagePosition3', t('Right'))?>
				&nbsp;&nbsp;
				<?php  echo $form->radio('imagePosition', 'B', $imagePosition)?>
				<?php  echo $form->label('imagePosition4', t('Bottom'))?>
			</td>
		</tr>
		</table>
		<br/>
		<div>
			<?php  echo $form->label('primaryImageHoverFID', t('Alternate image to display on roll-over:'))?>
			<?php  
				$imageOptions = array_merge(array('-'=>t('None')),$controller->getAvailableImageOptions());
				echo $form->select('primaryHoverImage', $imageOptions, $primaryHoverImage); 
			?>
		</div>
	</div>
</div>

<div class="ccm-block-field-group">
	<h2><?php  echo t('Use Overlays')?></h2>
	<div>
		<?php  echo $form->checkbox('useOverlaysC', 'C', $useOverlaysC)?>
		<?php  echo $form->label('useOverlaysC', t('Callout'))?>
		<div class="ccm-core-commerce-product-block-image-fields" style="padding-left:22px;">
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td valign="top">
					<?php  echo $form->label('overlayCalloutImage', t('Image'))?><br/>
					<?php  echo  $form->select("overlayCalloutImage", $imageOptions, $overlayCalloutImage); ?>
				</td>
				<td><div style="width: 10px">&nbsp;</div></td>
				<td valign="top">
					<?php  echo $form->label('overlayCalloutImageMaxWidth', t('Width'))?><br/>
					<?php  echo $form->text('overlayCalloutImageMaxWidth', $overlayCalloutImageMaxWidth, array('size'=>'6'))?>
				</td>
				<td><div style="width: 10px">&nbsp;</div></td>
				<td valign="top">
					<?php  echo $form->label('overlayCalloutImageMaxHeight', t('Height'))?><br/>
					<?php  echo $form->text('overlayCalloutImageMaxHeight', $overlayCalloutImageMaxHeight, array('size'=>'6'))?>
				</td>
			</tr>
			</table>
		</div>
	</div>
	&nbsp;&nbsp;
	<div>
		<?php  echo $form->checkbox('useOverlaysL', 'L', $useOverlaysL)?>
		<?php  echo $form->label('useOverlaysL', t('Lightbox'))?>
		<div class="ccm-core-commerce-product-block-image-fields" style="padding-left:22px;">
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td valign="top">
					<?php  echo $form->label('overlayLightboxImageMaxWidth', t('Image Width'))?><br/>
					<?php  echo $form->text('overlayLightboxImageMaxWidth', $overlayLightboxImageMaxWidth, array('size'=>'6'))?>
				</td>
				<td><div style="width: 10px">&nbsp;</div></td>
				<td valign="top">
					<?php  echo $form->label('overlayLightboxImageMaxHeight', t('Image Height'))?><br/>
					<?php  echo $form->text('overlayLightboxImageMaxHeight', $overlayLightboxImageMaxHeight, array('size'=>'6'))?>
				</td>
			</tr>
			</table>
		</div>
	</div>
</div>
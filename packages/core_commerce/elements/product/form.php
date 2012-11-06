<?php   defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<?php   
$form = Loader::helper('form');

$v = View::getInstance();
$html = Loader::helper('html');
$th = Loader::helper('concrete/urls'); 
$ps = Loader::helper('form/page_selector');
$this->addHeaderItem($html->javascript('tiny_mce/tiny_mce.js'));

if (is_object($product)) { 
	$prName = $product->getProductName();
	$prDescription = $product->getProductDescription();
	$prStatus = $product->getProductStatus();
	$prPrice = $product->getProductPrice();
	$prSpecialPrice = $product->getProductSpecialPrice(false);
	if ($prSpecialPrice == 0) {
		$prSpecialPrice = '';
	}
	$prQuantity = $product->getProductQuantity();
	$prQuantityUnlimited = $product->productHasUnlimitedQuantity();
	$prPhysicalGood = $product->productIsPhysicalGood();
	$prRequiresShipping = $product->productRequiresShipping();
	
	$prWeight = $product->getProductWeight();
	$prWeightUnits = $product->getProductWeightUnits();
	$prDimL = $product->getProductDimensionLength();
	$prDimW = $product->getProductDimensionWidth();
	$prDimH = $product->getProductDimensionHeight();
	$prDimUnits = $product->getProductDimensionUnits();

	$productID = $product->getProductID();
	$prRequiresTax = $product->productRequiresSalesTax();
	$prShippingModifier = $product->getProductShippingModifier();
	$gIDs = $product->getProductPurchaseGroupIDArray();
	$cID = $product->getProductCollectionID();
}

?>
	<div>
	<?php  echo $form->hidden('productID', $productID); ?>
	<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="header" width="100%" colspan="5"><?php  echo t('Base Information')?></td>
	</tr>
	<tr>
		<td class="subheader" width="100%" colspan="5"><?php  echo $form->label('prName', t('Name'))?> <span class="ccm-required">*</span></td>
	</tr>
	<tr>
		<td colspan="5"><?php  echo $form->text('prName', $prName, array('style' => 'width: 100%'))?></td>
	</tr>
	<tr>
		<td class="subheader" width="100%" colspan="5"><?php  echo $form->label('prDescription', t('Description'))?> <span class="ccm-required">*</span></td>
	</tr>
	<tr>
		<td colspan="5">
			<?php   Loader::element('editor_init'); ?>
			<?php   Loader::element('editor_config'); ?>
			<?php   Loader::element('editor_controls', array('mode'=>'full')); ?>
			<?php  echo $form->textarea('prDescription', $prDescription, array('style' => 'width: 100%; height: 150px', 'class' => 'ccm-advanced-editor'))?>
		</td>
	</tr>
	<tr>
		<td class="subheader" width="20%"><?php  echo $form->label('prStatus', t('Status'))?> <span class="ccm-required">*</span></td>
		<td class="subheader" width="20%"><?php  echo $form->label('prPrice', t('Price'))?></td>
		<td class="subheader" width="24%"><?php  echo $form->checkbox('prHasSpecialPrice', 1, $prSpecialPrice != '')?> <?php  echo $form->label('prSpecialPrice', t('Special/Sale Price'))?></td>
		<td class="subheader" width="16%"><?php  echo $form->label('prQuantity', t('Quantity In Stock'))?></td>
		<td class="subheader" width="20%"><?php  echo $form->label('prPhysicalGood', t('Physical Good'))?></td>

	</tr>
	<tr>
		<td><?php  echo $form->select('prStatus', array(
			'1' => t('Enabled'), 
			'0' => t('Disabled')
		), $prStatus);?></td>
		<td><?php  echo $form->text('prPrice', $prPrice, array('style' => 'width: 100px'))?></td>
		<td><?php  echo $form->text('prSpecialPrice', $prSpecialPrice, array('style' => 'width: 100px'))?></td>
		<td style="white-space: nowrap"><?php  echo $form->text('prQuantity', $prQuantity, array('style' => 'width: 50px'))?>
			<?php  echo $form->checkbox('prQuantityUnlimited', 1, $prQuantityUnlimited)?>
			<?php  echo t('Unlimited')?>
		</td>
		<td><?php  echo $form->select('prPhysicalGood', array(
		'1' => t('Yes'),
		'0' => t('No')
	), $prPhysicalGood);?></td>

	</tr>
	<tr>
		<td colspan="5" class="header"><?php  echo t('Shipping Information')?></td>
	</tr>
	<tr>
		<td class="subheader"><?php  echo t('Requires Shipping')?></td>
		<td class="subheader"><?php  echo t('Weight')?></td>
		<td class="subheader"><?php  echo t('Dimensions (LxWxH)')?></td>
		<td class="subheader"><?php  echo $form->label('prShippingModifier', t('Shipping Modifier'))?></td>
		<td class="subheader"><?php  echo $form->label('prRequiresTax', t('Charge Sales Tax'))?></td>
	</tr>
	<tr>
		<td class="ccm-core-commerce-product-requires-shipping"><?php  echo $form->select('prRequiresShipping', array(
		'1' => t('Yes'),
		'0' => t('No')
	), $prRequiresShipping);?></td>
		<td class="ccm-core-commerce-product-weight">
		<?php  echo $form->text('prWeight', $prWeight, array('style' => 'width: 60px'))?>
		<?php  echo $form->select('prWeightUnits', array(
			'lb' => t('lb'),
			'g' => t('g'),
			'kg' => t('kg'),
			'oz' => t('oz'),
		), $prWeightUnits);?>		
		</td>
		<td class="ccm-core-commerce-product-dimensions">
		<?php  echo $form->text('prDimL', $prDimL, array('style' => 'width: 20px'))?>
		<?php  echo $form->text('prDimW', $prDimW, array('style' => 'width: 20px'))?>
		<?php  echo $form->text('prDimH', $prDimH, array('style' => 'width: 20px'))?>
		<?php  echo $form->select('prDimUnits', array(
			'in' => t('in'),
			'mm' => t('mm'),
			'cm' => t('cm')
		), $prDimUnits);?>
		</td>
		<td class="ccm-core-commerce-product-shipping-modifier"><?php  echo $form->text('prShippingModifier', $prShippingModifier, array('style' => 'width: 60px'))?></td>
		<td class="ccm-core-commerce-product-requires-tax"><?php  echo $form->select('prRequiresTax', array(
		'1' => t('Yes'),
		'0' => t('No')
	), $prRequiresTax);?></td>
	</tr>
	<?php  
	Loader::model('attribute/categories/core_commerce_product', 'core_commerce');
	$attribs = CoreCommerceProductAttributeKey::getList();
	
	if (count($attribs) > 0) { ?>
	<tr>
		<td class="header" colspan="5"><?php  echo t('Other Product Attributes')?></td>
	</tr>
	<?php   foreach($attribs as $ak) { 
		if (is_object($product)) {
			$caValue = $product->getAttributeValueObject($ak);
		}
		?>
	<tr>
		<td class="subheader" colspan="5"><?php  echo $ak->getAttributeKeyName()?></td>
	</tr>
	<tr>
		<td width="100%" colspan="5"><?php   $ak->render('form', $caValue, false)?></td>
	</tr>
	<?php   } 
	
	}
	if (is_object($product)) { ?>
		<tr>
			<td class="header" colspan="5"><?php  echo t('Product Page: ')?></td>
		</tr>
		<tr>
			<td colspan="5">
				<?php  echo $ps->selectPage('cID', $cID)?>
			</td>
		</tr>
		<?php  
	}
	Loader::model("search/group");
	$gl = new GroupSearch();
	if ($gl->getTotal() < 1000) { 
		$gl->setItemsPerPage(1000);
	?>
	<tr>
		<td class="header" colspan="5"><?php  echo t('Buying this product will place you in the following groups: ')?></td>
	</tr>
	<tr>
		<td colspan="5">
		<?php   $gArray = $gl->getPage(); ?>
		<?php   if (!isset($gIDs)) {
			$gIDs = $_POST['gID'];
		} ?>
			<?php   foreach ($gArray as $g) { ?>
				<input type="checkbox" name="gID[]" value="<?php  echo $g['gID']?>" style="vertical-align: middle" <?php   
					if (is_array($gIDs)) {
						if (in_array($g['gID'], $gIDs)) {
							echo(' checked ');
						}
					}
				?> /> <?php  echo $g['gName']?><br>
			<?php   } ?>
			
		</td>
	</tr>
	<?php   } ?>
	
	</table>
	</div>

<script type="text/javascript">
ccmCoreCommerceProductCheckSelectors = function(s) {
	if ($('select[name=prPhysicalGood]').val() == '1') {
		if (s && s.attr('name') != 'prRequiresShipping') {
			$("select[name=prRequiresShipping]").val(1);
		}
		$("td.ccm-core-commerce-product-requires-shipping select").attr('disabled', false);
	} else {
		$("select[name=prRequiresShipping]").val(0);
		$("td.ccm-core-commerce-product-requires-shipping select").attr('disabled', true);
	}

	if ($('select[name=prRequiresShipping]').val() == '1') {
		$("td.ccm-core-commerce-product-dimensions input").attr('disabled', false);
		$("td.ccm-core-commerce-product-dimensions select").attr('disabled', false);
		$("td.ccm-core-commerce-product-weight input").attr('disabled', false);
		$("td.ccm-core-commerce-product-weight select").attr('disabled', false);
		$("td.ccm-core-commerce-product-shipping-modifier input").attr('disabled', false);
	} else {
		$("td.ccm-core-commerce-product-dimensions input").attr('disabled', true);
		$("td.ccm-core-commerce-product-dimensions select").attr('disabled', true);
		$("td.ccm-core-commerce-product-weight input").attr('disabled', true);
		$("td.ccm-core-commerce-product-weight select").attr('disabled', true);
		$("td.ccm-core-commerce-product-shipping-modifier input").attr('disabled', true);

	}


	if ($('input[name=prQuantityUnlimited]').attr('checked')) {
		$("input[name=prQuantity]").val("");
		$("input[name=prQuantity]").attr('disabled', true);
	} else {
		$("input[name=prQuantity]").attr('disabled', false);
		if (s && s.attr('name') == 'prQuantityUnlimited') {
			$("input[name=prQuantity]").get(0).focus();
		}
	}

	if ($('input[name=prHasSpecialPrice]').attr('checked')) {
		$("input[name=prSpecialPrice]").attr('disabled', false);
		if (s && s.attr('name') == 'prHasSpecialPrice') {
			$("input[name=prSpecialPrice]").get(0).focus();
		}
	} else {
		$("input[name=prSpecialPrice]").val("");
		$("input[name=prSpecialPrice]").attr('disabled', true);

	}

}

$(function() {
	ccm_activateFileSelectors();
	$("input[name=prQuantityUnlimited]").click(function() {
		ccmCoreCommerceProductCheckSelectors($(this));
	});
	$("select[name=prRequiresShipping]").change(function() {
		ccmCoreCommerceProductCheckSelectors($(this));
	});
	$("input[name=prHasSpecialPrice]").click(function() {
		ccmCoreCommerceProductCheckSelectors($(this));
	});
	$("select[name=prPhysicalGood]").change(function() {
		ccmCoreCommerceProductCheckSelectors($(this));
	});
	
	ccmCoreCommerceProductCheckSelectors();
	$(".page-selector").click( 
		function (e) {
            $.fn.dialog.open({
                href: "<?php  echo $th->getToolsURL('create_page', 'core_commerce')?>",
                title: "<?php  echo t('Create a Product Page?')?>",
                width: 550,
                modal: true,
                onOpen:function(){},
                onClose: function(e){},
                height: 480
            });
           e.preventDefault(); 
		}
	);
	//$(".dialog-launch").dialog();
	
});
</script>
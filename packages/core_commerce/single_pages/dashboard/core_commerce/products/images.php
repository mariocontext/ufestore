<h1><span><?php  echo t('Product Options')?><a href="<?php  echo View::url('/dashboard/core_commerce/products/search', 'view_detail', $product->getProductID())?>" class="ccm-dashboard-header-option">View Product</a></span></h1>
<div class="ccm-dashboard-inner">
<h2><?php  echo $product->getProductName()?></h2>

<?php   
$form = Loader::helper('form');
$ast = Loader::helper('concrete/asset_library');

$prThumbnailImage = $product->getProductThumbnailImageObject();
$prAltThumbnailImageFID = $product->getProductAlternateThumbnailImageObject();
$prFullImage = $product->getProductFullImageObject();

$ih = Loader::helper('concrete/interface');

$ast = Loader::helper('concrete/asset_library');
$images = array();
if ($this->controller->isPost()) {
	foreach($this->post('additionalProductImageFID') as $fID) {
		$images[] = File::getByID($fID);
	}
} else {
	$images = $product->getAdditionalProductImages();
}
?>
	<form method="post" id="ccm-core-commerce-product-images" action="<?php  echo $this->url('/dashboard/core_commerce/products/images', 'save')?>">
	
	<?php  echo $form->hidden('productID', $product->getProductID()); ?>

	<h3><?php  echo t('Standard Images')?></h3>

	<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="subheader" width="33%"><?php  echo $form->label('prThumbnailImageFID', t('Thumbnail Image'))?></td>
		<td class="subheader" width="34%"><?php  echo $form->label('prAltThumbnailImageFID', t('Alternate Thumbnail'))?></td>
		<td class="subheader" width="33%"><?php  echo $form->label('prFullImageFID', t('Full Image'))?></td>
	</tr>
	<tr>
		<td><?php  echo $ast->image('prThumbnailImageFID', 'prThumbnailImageFID', t('Choose Image'), $prThumbnailImage)?></td>
		<td><?php  echo $ast->image('prAltThumbnailImageFID', 'prAltThumbnailImageFID', t('Choose Image'), $prAltThumbnailImageFID)?></td>
		<td><?php  echo $ast->image('prFullImageFID', 'prFullImageFID', t('Choose Image'), $prFullImage)?></td>
	</tr>
	</table>

	<?php  echo $ih->button_js(t('Add Image'), 'ccm_chooseAsset=ccm_chooseAdditionalImage; javascript:ccm_launchFileManager(\'&fType=' . FileType::T_IMAGE . '\')', 'right');?>

	<h2><?php  echo t('Additional Images')?></h2>
	
	
	<div id="ccm-core-commerce-product-additional-images">
	<?php   foreach($images as $f) { ?>
		<div class="ccm-core-commerce-product-additional-image"><input type="hidden" name="additionalProductImageFID[]" value="<?php  echo $f->getFileID()?>" />
			<img src="<?php  echo $f->getThumbnailSRC(1)?>" class="ccm-core-commerce-product-additional-thumbnail" />
			<a href="javascript:void(0)" onclick="ccm_coreCommerceRemoveProductImage(this)" class="ccm-core-commerce-product-additional-remove"><img src="<?php  echo ASSETS_URL_IMAGES?>/icons/delete_small.png" /></a>
			<h3><?php  echo $f->getTitle()?></h3>
			<div class="ccm-spacer">&nbsp;</div>
		</div>
		
	<?php   } ?>
	</div>

	<div class="ccm-spacer">&nbsp;</div><br/>
	
	
	<div class="ccm-buttons">
		<a href="javascript:void(0)" onclick="$('#ccm-core-commerce-product-images').get(0).submit()" class="ccm-button-right accept"><span><?php  echo t('Update Images')?></span></a>
	</div>	

	<div class="ccm-spacer">&nbsp;</div>
	
	</form>
	
</div>

<script type="text/javascript">
var ccm_chooseAsset;
$(function() {
	ccm_activateFileSelectors();
	$("div#ccm-core-commerce-product-additional-images").sortable({
		handle: 'img.ccm-core-commerce-product-additional-thumbnail',
		cursor: 'move',
		opacity: 0.5
	});
});

ccm_coreCommerceRemoveProductImage = function(obj) {
	$(obj).parent().fadeOut(120, function() {
		$(obj).parent().remove();	
	});
}

var ccm_chooseImageTimeout = false;
ccm_chooseAdditionalImage = function(obj) {
	clearTimeout(ccm_chooseImageTimeout);
	html = '<div class="ccm-core-commerce-product-additional-image"><input type="hidden" name="additionalProductImageFID[]" value="' + obj.fID + '" />';
	html += '<img src="' + obj.thumbnailLevel1 + '" class="ccm-core-commerce-product-additional-thumbnail" />';
	html += '<a href="javascript:void(0)" onclick="ccm_coreCommerceRemoveProductImage(this)" class="ccm-core-commerce-product-additional-remove"><img src="<?php  echo ASSETS_URL_IMAGES?>/icons/delete_small.png" /><\/a>';
	html += '<h3>' + obj.title + '<\/h3>';
	html +='<div class="ccm-spacer">&nbsp;<\/div><\/div>';
	$("#ccm-core-commerce-product-additional-images").append(html);
	// this hackery is due to internet explorer. I hate you internet explorer.
	ccm_chooseImageTimeout = setTimeout(function() {
		ccm_chooseAsset = false;
	}, 200);
}
</script>

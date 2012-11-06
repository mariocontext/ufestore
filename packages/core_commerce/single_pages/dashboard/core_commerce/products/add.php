<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));

$txt = Loader::helper('text');
$vals = Loader::helper('validation/strings');
$valt = Loader::helper('validation/token');
$valc = Loader::helper('concrete/validation');
$dtt = Loader::helper('form/date_time');
$form = Loader::helper('form');
$ast = Loader::helper('concrete/asset_library');
$ih = Loader::helper('concrete/interface');
$uh = Loader::helper('urls', 'core_commerce');

?>
	<h1><span><?php  echo t('Create Product')?></span></h1>
	
	<div class="ccm-dashboard-inner"> 
	
	<form method="post" id="ccm-core-commerce-product-add-form" onsubmit="askAboutAPage();return false;" action="<?php  echo $this->url('/dashboard/core_commerce/products/add', 'submit')?>">
	<?php  echo $valt->output('create_product')?>
	
	<?php   Loader::packageElement('product/form', 'core_commerce'); ?>
	
	<div class="ccm-buttons">
		<input type="hidden" name="create" value="1" />
		<input id="cc-parent-page" type="hidden" name="parentCID" value="0" />
		<a href="javascript:void(0)" onclick="askAboutAPage()" class="ccm-button-right accept"><span><?php  echo t('Create Product')?></span></a>
	</div>	

	<div class="ccm-spacer">&nbsp;</div>
	
	</form>
	</div>

	<script type="text/javascript">
		function askAboutAPage() {
            $.fn.dialog.open({
                href: "<?php  echo $uh->getToolsURL('create_page')?>",
                title: "<?php  echo t('Create a Product Page?')?>",
                width: 550,
                modal: true,
                onOpen:function(){},
                onClose: function(){$('#ccm-core-commerce-product-add-form').get(0).submit()},
                height: 480
            });
		}
	</script>

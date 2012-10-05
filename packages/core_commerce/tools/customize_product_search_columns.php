<?php   defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('attribute/categories/core_commerce_product', 'core_commerce');
$uh = Loader::helper('urls', 'core_commerce');
$form = Loader::helper('form');
$c1 = Page::getByPath('/dashboard/core_commerce');
$cp1 = new Permissions($c1);
if (!$cp1->canRead()) { 
	die(_("Access Denied."));
}

$selectedAKIDs = array();
$slist = CoreCommerceProductAttributeKey::getColumnHeaderList();
foreach($slist as $sk) {
	$selectedAKIDs[] = $sk->getAttributeKeyID();
}

if ($_POST['task'] == 'update_columns') {
	Loader::model('attribute/category');
	$sc = AttributeKeyCategory::getByHandle('core_commerce_product');
	$sc->clearAttributeKeyCategoryColumnHeaders();
	
	if (is_array($_POST['akID'])) {
		foreach($_POST['akID'] as $akID) {
			$ak = CoreCommerceProductAttributeKey::getByID($akID);
			$ak->setAttributeKeyColumnHeader(1);
		}
	}
	
	exit;
}

$list = CoreCommerceProductAttributeKey::getList();

?>

<form method="post" id="ccm-core-commerce-product-customize-search-columns-form" action="<?php  echo $uh->getToolsURL('customize_product_search_columns')?>">
<?php  echo $form->hidden('task', 'update_columns')?>

<h1><?php  echo t('Additional Searchable Attributes')?></h1>

<p><?php  echo t('Choose the additional attributes you wish to include as column headers.')?></p>

<?php   foreach($list as $ak) { ?>

	<div><?php  echo $form->checkbox('akID[]', $ak->getAttributeKeyID(), in_array($ak->getAttributeKeyID(), $selectedAKIDs), array('style' => 'vertical-align: middle'))?> <?php  echo $ak->getAttributeKeyDisplayHandle()?></div>
	
<?php   } ?>

<br/><br/>
<?php  
$h = Loader::helper('concrete/interface');
$b1 = $h->button_js(t('Save'), 'ccm_submitCustomizeSearchColumnsForm()', 'left');
print $b1;
?>

</form>

<script type="text/javascript">
ccm_submitCustomizeSearchColumnsForm = function() {
	ccm_deactivateSearchResults();
	$("#ccm-core-commerce-product-customize-search-columns-form").ajaxSubmit(function(resp) {
		jQuery.fn.dialog.closeTop();
		$("#ccm-core-commerce-product-advanced-search").ajaxSubmit(function(resp) {
			ccm_parseAdvancedSearchResponse(resp);
		});
	});
}

$(function() {
	$('#ccm-core-commerce-product-customize-search-columns-form').submit(function() {
		ccm_submitCustomizeSearchColumnsForm();
	});
});


</script>

<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
$c1 = Page::getByPath('/dashboard/core_commerce');
$cp1 = new Permissions($c1);
if (!$cp1->canRead()) { 
	die(_("Access Denied."));
}

$cnt = Loader::controller('/dashboard/core_commerce/products/search');
$productList = $cnt->getRequestedSearchResults();
$products = $productList->getPage();
$pagination = $productList->getPagination();
if (!isset($mode)) {
	$mode = $_REQUEST['mode'];
}
?>

<div id="ccm-search-overlay" >
	
		<table id="ccm-search-form-table" >
			<tr>
				<td valign="top" class="ccm-search-form-advanced-col">
					<?php   Loader::packageElement('product/search', 'core_commerce', array('mode' => $mode)) ; ?>
				</td>		
				<?php   /* <div id="ccm-file-search-advanced-fields-gutter">&nbsp;</div> */ ?>		
				<td valign="top" width="100%">	
					
					<div id="ccm-search-advanced-results-wrapper">
					
						<div id="ccm-search-results">
						
							<?php   Loader::packageElement('product/search_results', 'core_commerce', array('mode' => $mode, 'products' => $products, 'productList' => $productList, 'pagination' => $pagination)); ?>
						
						</div>
					
					</div>
				
				</td>	
			</tr>
		</table>		

</div>

<script type="text/javascript">
$(function() {
	ccm_coreCommerceSetupSearch();
});
</script>
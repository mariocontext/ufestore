<?php   defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 

<div id="ccm-list-wrapper">
<?php  
	if (!$mode) {
		$mode = $_REQUEST['mode'];
	}
	
	?>
	<?php  echo $productList->displaySummary();?>
	
	<?php  
	$txt = Loader::helper('text');
	$keywords = $_REQUEST['keywords'];
	$uh = Loader::helper('urls', 'core_commerce');
	$bu = $uh->getToolsURL('product/search_results');
	
	if (count($products) > 0) { ?>	
		<table border="0" cellspacing="0" cellpadding="0" id="ccm-product-list" class="ccm-results-list">
		<tr>
			<?php   /*
			<th><input id="ccm-core-commerce-product-list-cb-all" type="checkbox" /></td>
			*/ ?>
			<th><?php   /* <select id="ccm-core-commerce-product-list-multiple-operations" disabled>
				<option value="">**</option>
				<?php   if ($mode == 'choose_multiple') { ?>
					<option value="delete"><?php  echo t('Choose')?></option>			
				<?php   } ?>
			</select>*/ ?>
			&nbsp;
			</th>
			<th width="200" class="<?php  echo $productList->getSearchResultsClass('prName')?>"><a href="<?php  echo $productList->getSortByURL('prName', 'asc', $bu)?>"><?php  echo t('Name')?></a></th>
			<th class="<?php  echo $productList->getSearchResultsClass('prPrice')?>"><a href="<?php  echo $productList->getSortByURL('prPrice', 'asc', $bu)?>"><?php  echo t('Price')?></a></th>
			<th class="<?php  echo $productList->getSearchResultsClass('prDateAdded')?>"><a href="<?php  echo $productList->getSortByURL('prDateAdded', 'asc', $bu)?>"><?php  echo t('Date Added')?></a></th>
			<th class="<?php  echo $productList->getSearchResultsClass('prStatus')?>"><a href="<?php  echo $productList->getSortByURL('prStatus', 'asc', $bu)?>"><?php  echo t('Status')?></a></th>
			<?php   
			$slist = CoreCommerceProductAttributeKey::getColumnHeaderList();
			foreach($slist as $ak) { ?>
				<th class="<?php  echo $productList->getSearchResultsClass($ak)?>"><a href="<?php  echo $productList->getSortByURL($ak, 'asc', $bu)?>"><?php  echo $ak->getAttributeKeyDisplayHandle()?></a></th>
			<?php   } ?>			
			<th class="ccm-search-add-column-header"><a href="<?php  echo $uh->getToolsURL('customize_product_search_columns')?>" id="ccm-search-add-column"><img src="<?php  echo ASSETS_URL_IMAGES?>/icons/add.png" width="16" height="16" /></a></th>
		</tr>
	<?php  
		foreach($products as $pr) { 
			
			if ($mode == 'choose_one' || $mode == 'choose_multiple') {
				$action = 'javascript:void(0); ccm_coreCommerceSelectProduct(' . $pr->getProductID() . '); jQuery.fn.dialog.closeTop();';
			} else {
				$action = View::url('/dashboard/core_commerce/products/search', 'view_detail', $pr->getProductID());
			}
			
			if (!isset($striped) || $striped == 'ccm-list-record-alt') {
				$striped = '';
			} else if ($striped == '') { 
				$striped = 'ccm-list-record-alt';
			}

			?>
		
			<tr class="ccm-list-record <?php  echo $striped?>">
			<?php   /* <td class="ccm-core-commerce-product-list-cb" style="vertical-align: middle !important"><input type="checkbox" value="<?php  echo $pr->getProductID()?>" product-name="<?php  echo $pr->getProductName()?>" /></td> */ ?>
			<td><div class="ccm-core-commerce-search-thumbnail"><?php  echo $pr->outputThumbnail()?></div></td>
			<td><a href="<?php  echo $action?>"><?php  echo $txt->highlightSearch($pr->getProductName(), $keywords)?></a></td>
			<td><?php  echo $txt->highlightSearch($pr->getProductDisplayPrice(), $keywords)?></td>
			<td><?php  echo date(t("m/d/Y g:i A"), strtotime($pr->getProductDateAdded()))?></td>
			<td><?php  echo ($pr->getProductStatus() == 1) ? t('Enabled') : t('Disabled')?></td>
			<?php   
			$slist = CoreCommerceProductAttributeKey::getColumnHeaderList();
			foreach($slist as $ak) { ?>
				<td><?php  
				$vo = $pr->getAttributeValueObject($ak);
				if (is_object($vo)) {
					print $vo->getValue('display');
				}
				?></td>
			<?php   } ?>		
			<td>&nbsp;</td>
			</tr>
			<?php  
		}

	?>
	
	</table>
	
	

	<?php   } else { ?>
		
		<div id="ccm-list-none"><?php  echo t('No products found.')?></div>
		
	
	<?php   } 
	$productList->displayPaging($bu); ?>
	
</div>

<script type="text/javascript">
$(function() { 
	ccm_coreCommerceSetupSearch(); 
});
</script>

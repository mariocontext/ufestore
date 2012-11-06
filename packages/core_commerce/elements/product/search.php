<?php   defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<?php  
$searchFields = array(
	'' => '** ' . t('Fields'),
	'date_added' => t('Created Between')
);

$uh = Loader::helper('urls', 'core_commerce');
Loader::model('attribute/categories/core_commerce_product', 'core_commerce');
$searchFieldAttributes = CoreCommerceProductAttributeKey::getSearchableList();
foreach($searchFieldAttributes as $ak) {
	$searchFields[$ak->getAttributeKeyID()] = $ak->getAttributeKeyDisplayHandle();
}


?>

<?php   $form = Loader::helper('form'); ?>

	
	<div id="ccm-core-commerce-product-search-field-base-elements" style="display: none">

		<span class="ccm-search-option"  search-field="date_added">
		<?php  echo $form->text('date_from', array('style' => 'width: 86px'))?>
		<?php  echo t('to')?>
		<?php  echo $form->text('date_to', array('style' => 'width: 86px'))?>
		</span>
		
		<?php   foreach($searchFieldAttributes as $sfa) { 
			$sfa->render('search'); ?>
		<?php   } ?>
		
	</div>
	
	<form method="get" id="ccm-core-commerce-product-advanced-search" action="<?php  echo $uh->getToolsURL('product/search_results')?>">
	<?php  echo $form->hidden('mode', $mode); ?>
	<div id="ccm-core-commerce-product-search-advanced-fields" class="ccm-search-advanced-fields" >
	
		<input type="hidden" name="search" value="1" />
		<div id="ccm-search-box-title">
			<img src="<?php  echo ASSETS_URL_IMAGES?>/throbber_white_16.gif" width="16" height="16" id="ccm-search-loading" />
			<h2><?php  echo t('Search')?></h2>			
		</div>
		
		<div id="ccm-search-advanced-fields-inner">
			<div class="ccm-search-field">
				<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
					<?php  echo $form->label('keywords', t('Keywords'))?>
					<?php  echo $form->text('keywords', array('style' => 'width:200px')); ?>
					</td>
				</tr>
				</table>
			</div>
		
			<div class="ccm-search-field">
				<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td style="white-space: nowrap" align="right"><div style="width: 85px; padding-right:5px"><?php  echo t('Results Per Page')?></div></td>
					<td width="100%">
						<?php  echo $form->select('numResults', array(
							'10' => '10',
							'25' => '25',
							'50' => '50',
							'100' => '100',
							'500' => '500'
						), false, array('style' => 'width:65px'))?>
					</td>
					<td><a href="javascript:void(0)" id="ccm-core-commerce-product-search-add-option"><img src="<?php  echo ASSETS_URL_IMAGES?>/icons/add.png" width="16" height="16" /></a></td>
				</tr>	
				</table>
			</div>
			
			<div id="ccm-search-field-base">				
				<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td valign="top" style="padding-right: 4px">
						<?php  echo $form->select('searchField', $searchFields, array('style' => 'width: 85px'));
						?>
						<input type="hidden" value="" class="ccm-core-commerce-product-selected-field" name="selectedSearchField[]" />
						</td>
						<td width="100%" valign="top" class="ccm-selected-field-content">
						<?php  echo t('Select Search Field.')?>
						</td>
						<td valign="top">
						<a href="javascript:void(0)" class="ccm-search-remove-option"><img src="<?php  echo ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a>
						</td>
					</tr>
				</table>
			</div>
			
			<div id="ccm-search-fields-wrapper">			
			</div>
			
			<div id="ccm-search-fields-submit">
				<?php  echo $form->submit('ccm-search-core-commerce-products', t('Search'))?>
			</div>
		</div>
	
</div>

</form>

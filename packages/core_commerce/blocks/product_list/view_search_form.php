<?php   defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<?php   
extract($controller->block_args);
$search = $controller->block_args;
if (!is_array($_REQUEST['selectedSearchField'])) $_REQUEST['selectedSearchField'] = array();

$searchFields = array(
	//'' => '** ' . t('Fields'),
	//'date_added' => t('Created Between')
	'' => t('Select Search Field.'),
);

$uh = Loader::helper('concrete/urls');
Loader::model('attribute/categories/core_commerce_product', 'core_commerce');
$searchFieldAttributes = CoreCommerceProductAttributeKey::getSearchableList();
$validAttributes = array();
//filter unsopported attributes untill we fix them
foreach($searchFieldAttributes as $ak) {
	$type = $ak->getAttributeKeyType()->getAttributeTypeHandle();
	if (!in_array($type,array('text','textarea','boolean','number','select'))) continue;
	$validAttributes[] = $ak;
	$searchFields[$ak->getAttributeKeyID()] = $ak->getAttributeKeyName();
}

if ($baseSearchPath == "OTHER" && $searchUnderCID > 0) {
	$target = Page::getById($searchUnderCID);	
}
if (!$target || $target->getCollectionID() == 0) {
	$target = $c;
}
?>

<?php   $form = Loader::helper('form'); ?>
	<form method="get" class="product-list-core-commerce-product-advanced-search" action="<?php  echo $this->url($target->getCollectionPath());?>">
	<?php  // echo $form->hidden('mode', $mode); ?>
	<div id="product-list-core-commerce-product-search-advanced-fields" class="product-list-search-advanced-fields" >
		<input type="hidden" name="search" value="1" />
		<div id="product-list-search-box-title">
			<h2><?php  echo t('Search Products')?></h2>			
		</div>
		<div id="product-list-search-advanced-fields-inner">
			<div class="product-list-search-field">
				<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
					<?php  echo $form->label('keywords', t('Keywords'))?>
					<?php  echo $form->text('keywords', $_REQUEST['keywords'])?>
					<?php  echo $form->submit('product-list-search-core-commerce-products', t('Search'))?>
					</td>
				</tr>
				</table>
			</div>
			<?php   if ($options['search_mode'] == "advanced") { ?>
			<div class="product-list-search-field">
				<table border="0" cellspacing="2" cellpadding="0">
				<tr>
					<td style="white-space: nowrap" style="padding-right: 4px"><div><?php  echo t('Results Per Page')?></div></td>
					<td>
						<?php  echo $form->select('numResults', array(
							'10' => '10',
							'25' => '25',
							'50' => '50',
							'100' => '100',
							'500' => '500'
						), $_REQUEST['numResults'], array('style' => 'width:65px'))?>
					</td>
					<td><a href="javascript:void(0)" class="product-list-core-commerce-product-search-add-option"><img src="<?php  echo ASSETS_URL_IMAGES?>/icons/add.png" width="16" height="16" /></a></td>
				</tr>
				<tr class="product-list-search-field-base product-list-search-field" style="display:none">
						<td valign="top" colspan="2">
						<?php  echo $form->select('searchField', $searchFields, array('style' => 'width: 130px'));
						?>
						<input type="hidden" value="" class="product-list-core-commerce-product-selected-field" name="selectedSearchField[]" />
						</td>
						<td valign="top">
						<a href="javascript:void(0)" class="product-list-search-remove-option"><img src="<?php  echo ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a>
						</td>
				</tr>
				<?php    
					foreach ($searchFieldAttributes as $ak) { 
						$i = $ak->getAttributeKeyID();						
						if (in_array($i,$_REQUEST['selectedSearchField'])) {
							$display = "block";
							$selected = $i;
						}
						else {
							$selected = "";
							$display = "none";
						}
						?>
						<tr id="product-list-core-commerce-product-search-field-set<?php  echo  $i ?>" class="product-list-search-field" style="display:<?php  echo  $display ?>">
							<td valign="top" style="padding-right: 4px">
							<?php  echo  $ak->getAttributeKeyName(); ?>
							<input type="hidden" value="<?php  echo  $selected ?>" class="product-list-core-commerce-product-selected-field" name="selectedSearchField[]" />
							</td>
							<td valign="top" class="product-list-selected-field-content">
							<?php  
								$ak->render('search');
							?>
							</td>
							<td valign="top">
							<a href="javascript:void(0)" class="product-list-search-remove-option"><img src="<?php  echo ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a>
							</td>
						</tr>
				<?php   } ?>
				</table>
			</div>
			<?php   } ?>
		</div>
	
</div>
</form>
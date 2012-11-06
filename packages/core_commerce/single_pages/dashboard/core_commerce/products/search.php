<?php   
$ih = Loader::helper('concrete/interface');

if ($this->controller->getTask() == 'view_detail') { ?>

	<h1><span><?php  echo t('View Product')?></span></h1>
	
	<div class="ccm-dashboard-inner"> 
	
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top" style="padding-right: 10px"><?php  echo $product->outputThumbnail()?></td>
		<Td valign="top" width="100%">
			
			<h2><?php  echo $product->getProductName()?></h2>
			<p><?php  echo $product->getProductDescription()?></p>
			
			<h2><?php  echo $product->getProductDisplayPrice()?></h2>
			
			<h3><?php  echo t('Edit Product')?></h3>
			<?php  echo $ih->button(t('Properties'), $this->url('/dashboard/core_commerce/products/search', 'edit', $product->getProductID()), 'left')?>
			<?php  echo $ih->button(t('Images'), $this->url('/dashboard/core_commerce/products/images', $product->getProductID()), 'left')?>
			<?php  echo $ih->button(t('Customer Choices'), $this->url('/dashboard/core_commerce/products/options', 'view', $product->getProductID()), 'left')?>
			<?php   if (method_exists('AttributeKey', 'duplicate')) { ?>
				<form id="ccm-core-commerce-product-duplicate-form" style="display: inline" method="post" action="<?php  echo $this->url('/dashboard/core_commerce/products/search', 'duplicate', $product->getProductID())?>">
				<input type="hidden" id="cc-parent-page" name="cParentID" value="" />
				
				<?php  echo $ih->button_js(t('Duplicate'), 'javascript:ccm_coreCommerceAskAboutProductPage()', 'left')?>
				
				</form>
			<?php  
			}
			
			$valt = Loader::helper('validation/token');
			$ih = Loader::helper('concrete/interface');
			$delConfirmJS = t('Are you sure you want to remove this product?');
			$delPageConfirmJS = t('This product has a page associated in the sitemap. Do you want to remove that page aswell?');
			?>
			<script type="text/javascript">
			deleteProduct = function() {
				var url = "<?php  echo $this->url('/dashboard/core_commerce/products/search', 'delete_product', $product->getProductID(), $valt->generate('delete_product'))?>";
				<?php   if ($product->getProductCollectionID()>0) { ?>
				if (confirm('<?php  echo $delPageConfirmJS?>')) {
					url = "<?php  echo $this->url('/dashboard/core_commerce/products/search', 'delete_product', $product->getProductID(), $valt->generate('delete_product'),'delete_pages_too')?>";
				}
				<?php   } ?>
				if (confirm('<?php  echo $delConfirmJS?>')) { 
					location.href = url;				
				}
			}
			</script>
			<?php   print $ih->button_js(t('Delete'), "deleteProduct()", 'left');?>
		
		</td>
	</tr>
	</table>
	
	</form>
	</div>

	<script type="text/javascript">
	<?php   $uh = Loader::helper('urls', 'core_commerce'); ?>
		function ccm_coreCommerceAskAboutProductPage() {
            $.fn.dialog.open({
                href: "<?php  echo $uh->getToolsURL('create_page')?>",
                title: "<?php  echo t('Create a Product Page?')?>",
                width: 550,
                modal: true,
                onOpen:function(){},
                onClose: function(){$('#ccm-core-commerce-product-duplicate-form').get(0).submit()},
                height: 480
            });
		}
	</script>

<?php   } else if ($this->controller->getTask() == 'edit') { 

$valt = Loader::helper('validation/token');

?>
	<h1><span><?php  echo t('Update Product')?><a href="<?php  echo View::url('/dashboard/core_commerce/products/search', 'view_detail', $product->getProductID())?>" class="ccm-dashboard-header-option">View Product</a></span></h1>

	<div class="ccm-dashboard-inner"> 
	
	<form method="post" id="ccm-core-commerce-product-update-form" action="<?php  echo $this->url('/dashboard/core_commerce/products/search', 'edit')?>">
	
	<?php  echo $valt->output('update_product')?>
	
	<?php   Loader::packageElement('product/form', 'core_commerce', array('product' => $product)); ?>
	
	<div class="ccm-buttons">
		<a href="javascript:void(0)" onclick="$('#ccm-core-commerce-product-update-form').get(0).submit()" class="ccm-button-right accept"><span><?php  echo t('Update Product')?></span></a>
	</div>	

	<div class="ccm-spacer">&nbsp;</div>
	
	</form>
	</div>

<?php   } else { ?>
	
	<h1><span><?php  echo t('Product Search')?></span></h1>

	<div class="ccm-dashboard-inner">
	
		<table id="ccm-search-form-table" >
			<tr>
				<td valign="top" class="ccm-search-form-advanced-col">
					<?php   Loader::packageElement('product/search', 'core_commerce'); ?>
				</td>		
	
				<td valign="top" width="100%">	
					
					<div id="ccm-search-advanced-results-wrapper">
						
						<div id="ccm-search-results">
						
							<?php   Loader::packageElement('product/search_results', 'core_commerce', array('products' => $products, 'productList' => $productList, 'pagination' => $pagination)); ?>
						
						</div>
					
					</div>
				
					<br/>

					<div class="ccm-buttons">
						<a href="<?php  echo $this->url('/dashboard/core_commerce/products/add')?>" class="ccm-button-left"><span><?php  echo t('Add Product')?></span></a>
					</div>	
					<div class="ccm-spacer">&nbsp;</div>
						
				</td>	
			</tr>
		</table>		
	</div>
	
<?php   } ?>

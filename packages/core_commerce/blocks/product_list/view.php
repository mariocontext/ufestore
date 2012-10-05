<?php   
defined('C5_EXECUTE') or die(_("Access Denied."));  

global $c;

$uh = Loader::helper('urls', 'core_commerce');
$im = Loader::helper('image');
if ($options['show_search_form']) {
	$this->inc('view_search_form.php', array( 'c'=>$c, 'b'=>$b, 'controller'=>$controller,'block_args'=>$block_args ) );
}
?>
<?php   if ($options['show_products'] || $_REQUEST['search'] == '1') { ?>
	<?php  
	$nh = Loader::helper('navigation');
	
	$productList = $this->controller->getRequestedSearchResults();
	$products = $productList->getPage();
	$paginator = $productList->getPagination();
	?>
	<?php   if(count($products)>0) { ?>

		<table border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td>
			<?php   if($paging['show_top'] || $paging['show_bottom']) {
				echo '<div class="ccm-core-commerce-summary">';
				$productList->displaySummary();
				echo '</div>';
			}
			?>				
			</td><?php  
				if (count($sort_columns)>0) { ?>
					<td style="padding-left: 20px">
					<div class="product-list-sort-header"><?php  echo  t('Sort by:'); ?> <select class="product-list-sort-select">
					<?php  
					$current_col = $_REQUEST['ccm_order_by'];
					foreach ($sort_columns as $col => $name) {
						$selected = ($current_col == $col && $_REQUEST['ccm_order_dir'] == 'asc') ? "selected" : "";
						echo '<option '.$selected.' value="';
						$productList->getSortByURL($col, 'asc', $bu);
						echo '">'.$name.' ' . t('Ascending') . '</option>';
						$selected = ($current_col == $col && $_REQUEST['ccm_order_dir'] == 'desc') ? "selected" : "";
	
						echo '<option '.$selected.' value="';
						$productList->getSortByURL($col, 'desc', $bu);
						echo '">'.$name.' ' . t('Descending') . '</option>';
					}
					?></select>
	
					</div>
					</td>
					<?php  
				} ?>
		</tr>
		</table>
		<br/>
	<div style="clear: both"></div>
		<?php   if($paging['show_top'] && $paginator && strlen($paginator->getPages())>0){ ?>	
		<div class="pagination">	
			 <span class="pageLeft"><?php   echo $paginator->getPrevious(t('Previous'))?></span>
			 <?php   echo $paginator->getPages()?>
			 <span class="pageRight"><?php   echo $paginator->getNext(t('Next'))?></span>
		</div>
		<br/>
		<?php   } ?>
		
		<table width="100%" class="ccm-core-commerce-product-list-results" border="0" cellspacing="<?php  echo  (int) $layout['spacing'] ?>" cellpadding="<?php  echo  (int) $layout['padding'] ?>">
		<?php  
		if ($layout['records_per_row'] > 0) {
			$modwidth = round(100 / $layout['records_per_row']);
		}
		for ($i = 0; $i < count($products); $i++) {	
			
			if ($i % $layout['records_per_row'] == 0) {
				if ($i > 0) {
					print '</tr>';
				}
				if ($i + 1 < count($products)) {
					print '<tr>';
				}
			}
			
			$pr = $products[$i];
			$args['product'] = $pr;
			$args['valign'] = $layout['cell_vertical_align'];
			$args['halign'] = $layout['cell_horizontal_align'];
			$args['id'] = $pr->getProductID() . '-' . $b->getBlockID();
			foreach($this->controller->getSets() as $key => $value) {
				$args[$key] = $value;
			}
			if ($args['imagePosition'] == 'T') {
				$valign = 'top';
			} else if ($args['imagePosition'] == 'B') {
				$valign = 'bottom';
			}
			print '<td valign="' . $valign . '" width="' . $modwidth . '%" style="border:' . (int) $layout['table_border_width'] . 'px ' . $layout['table_border_style'] . ' ' . $table_border_color . ';">';
			Loader::packageElement('product/display', 'core_commerce', $args);
			print '</td>';
		}
		
		if ($i % $layout['records_per_row'] != 0) {
			while ($i % $layout['records_per_row'] != 0) {
				print '<td>&nbsp;</td>';
				$i++;
			}
			print '</tr>';
		}
	?>
	</table>
	
	<?php  
		
	} else { ?>
		<?php  echo  t('No products found'); ?>
	<?php   } ?>
<?php   }

if($paging['show_bottom'] && $paginator && strlen($paginator->getPages())>0){ ?>	
<div class="pagination">	
	 <span class="pageLeft"><?php   echo $paginator->getPrevious(t('Previous'))?></span>
	 <?php   echo $paginator->getPages()?>
	 <span class="pageRight"><?php   echo $paginator->getNext(t('Next'))?></span>
</div>	
<?php   } ?>

 

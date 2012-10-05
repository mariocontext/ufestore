<?php  

$form = Loader::helper('form');
$fh = Loader::helper('form/color'); 

extract($controller->block_args);

$allFields = $controller->getProductFieldsInOrder();
$sortFields = $controller->getProductsSortableFields();

if ($baseSearchPath == "") $baseSearchPath = "THIS";
if (!isset($options['show_search_form'])) $options['show_search_form'] = 1;

if ($addToCartText == "" && $this->bID==0) $addToCartText = t('Add to cart');

?>
<h2>Search Results</h2>
<?php  echo  $form->checkbox('options[show_products]',1, $options['show_products']) ?><?php  echo  t('Display search results'); ?>
<div class="ccm-block-field-group">
  <h3><?php  echo  t('Order products by')?></h3>
  	<select name="default_order_by">
	<?php  
	foreach ($this->controller->getProductsSortableFields() as $field => $label) {
		$selected = ($default_order_by == $field) ? "selected" : "";
		echo '<option '.$selected.' value="';
		echo $field;
		echo '">'.$label.'</option>';
	}
	?>
	<?php   /* <option value="sitemap_order"><?php  echo  t('Sitemap Order') ?></option> */ //note: unimplemeted ?>
	</select> <select name="default_sort_order">
		<option <?php  echo  ($default_sort_order == "asc") ? "selected" : ""; ?> value="asc"><?php  echo  t('Ascending') ?></option>
		<option <?php  echo  ($default_sort_order == "desc") ? "selected" : ""; ?> value="desc"><?php  echo  t('Descending') ?></option>
	</select>
</div>
<div class="ccm-block-field-group">
<h2><?php  echo t('Results Layout')?></h2>
<table cellspacing="0" cellpadding="0">
<tr><td>
<h3><?php  echo t('Table') ?></h3>
</td></tr>
<tr><td style="padding-right: 10px">
<?php   if ($layout['records_per_row'] == '') $layout['records_per_row'] = 1; ?>
Show </td><td><?php  echo  $form->text('layout[records_per_row]',$layout['records_per_row'],array('style'=>"width:25px")); ?> result(s) per row<br />
</td></tr><tr>
<td style="padding-right: 10px">Border width </td><td><?php  echo  $form->text('layout[table_border_width]',(int) $layout['table_border_width'],array('style'=>"width:25px")); ?> px<br />
</td></tr><tr>
<td style="padding-right: 10px">Border style </td><td><?php  echo  $form->select('layout[table_border_style]',array('solid'=>'solid','dotted'=>'dotted','dashed'=>'dashed','double'=>'double'), $layout['table_border_style']); ?><br />
</td></tr><tr>
<td style="padding-right: 10px">Border color </td><td><?php  echo  $fh->output( 'table_border_color', '',$table_border_color ) ?><br clear="both" />
</td></tr><tr>
<td colspan="2">
<h3><?php  echo t('Cells') ?></h3>
</td>
</tr><tr>
<td style="padding-right: 10px"><?php  echo  t('Vertical align') ?> </td><td><?php  echo  $form->select('layout[cell_vertical_align]',array('top'=>t('top'),'bottom'=>t('bottom'),'middle'=>t('middle')),$layout['cell_vertical_align']); ?><br />
</td></tr><tr>
<td style="padding-right: 10px"><?php  echo  t('Horizontal align') ?> </td><td><?php  echo  $form->select('layout[cell_horizontal_align]',array('left'=>t('left'),'right'=>t('right'),'center'=>t('center')),$layout['cell_horizontal_align']); ?><br />
</td></tr><tr>
<td style="padding-right: 10px"><?php  echo  t('Padding') ?> </td><td><?php  echo  $form->text('layout[padding]',(int)$layout['padding'],array('style'=>"width:25px")); ?> px.<br />
</td></tr><tr>
<td style="padding-right: 10px"><?php  echo  t('Spacing') ?> </td><td><?php  echo  $form->text('layout[spacing]',(int)$layout['spacing'],array('style'=>"width:25px")); ?> px.<br />
</tr></table>
</div>

<h2><?php  echo t('Paging & Sorting')?></h2>
<?php   if ($search['numResults'] == '') {
	$search['numResults'] = 10;
} ?>
<?php  echo  t('Show') ?> <?php  echo $form->text('numResults', $search['numResults'], array('style' => 'width:35px'))?> <?php  echo  t('per page') ?><br />
<br />
<?php  echo  t('Show paging controls on') ?>: <br />
<input type="hidden" name="paging[show_top]" value="0" /><?php  echo  $form->checkbox('paging[show_top]',1,$paging['show_top']); ?><?php  echo  t('top') ?><br />
<input type="hidden" name="paging[show_bottom]" value="0" /><?php  echo  $form->checkbox('paging[show_bottom]',1,$paging['show_bottom']); ?><?php  echo  t('bottom') ?><br />
<br />
<?php  echo  t('Allow sorting by') ?>: <br />
<select name="paging[sort_by][]" multiple="multiple" style="width:100px;height:50px">
	<?php   foreach ($this->controller->getProductsSortableFields() as $field=>$label) { ?>
		<?php  
			$selected = "";
			if (is_array($paging['sort_by']) && in_array($field,$paging['sort_by'])) {
				$selected = 'selected="selected"';
			}
		?>
		<option <?php  echo  $selected ?> value="<?php  echo  $field ?>"><?php  echo  $label ?></option>
	<?php   } ?>
</select>
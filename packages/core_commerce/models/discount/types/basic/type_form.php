<?php  
$form = Loader::helper('form');

$fixed_desc = "(e.g. 12.95)";
$percent_desc = "(e.g. 25 for 25%, not .25)";
?>

<table class="entry-form" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader"><?php  echo t('Discount')?> <span class="required">*</span></td>
	<td class="subheader"><?php  echo t('Type')?> <span class="required">*</span></td>
</tr>
<tr>
	<td>
		<?php  echo $form->text('amount', $amount)?><br/>
		<span class="ccm-discount-description explanation"><?php  echo $mode=='percent'?$percent_desc:$fixed_desc?></span>
	</td>
	<td><?php  echo $form->select('mode', array(
		'fixed' => t('Fixed Amount Off Order'),
		'percent' => t('Percent Off Order')
	), $mode,
	array('onchange' => "var txt='".$percent_desc."';if($('#mode').val() != 'percent') txt='".$fixed_desc."';$('.ccm-discount-description').text(txt)"))?></td>
</tr>
</table>

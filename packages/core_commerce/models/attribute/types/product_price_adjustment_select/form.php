<?php   defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<?php  
$options = $this->controller->getOptions();
$form = Loader::helper('form');

if ($akSelectAllowMultipleValues) { ?>

	<?php   foreach($options as $opt) { ?>
		<div>
			<?php  echo $form->checkbox($this->field('atSelectOptionID') . '[]', $opt->getSelectAttributeOptionID(), in_array($opt->getSelectAttributeOptionID(), $selectedOptions)); ?>
			<?php  echo $opt->getSelectAttributeOptionDisplayValue()?></div>
	<?php   } ?>
<?php   } else { 
	$opts = array('' => t('** None'));
	foreach($options as $opt) { 
		$opts[$opt->getSelectAttributeOptionID()] = $opt->getSelectAttributeOptionDisplayValue();
	}
	?>
	<?php  echo $form->select($this->field('atSelectOptionID') . '[]', $opts, $selectedOptions[0]); ?>

<?php   } ?>
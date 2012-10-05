<?php  

$form = Loader::helper('form');

extract($controller->block_args);

$allFields = $controller->getProductFieldsInOrder();

if ($baseSearchPath == "") $baseSearchPath = "THIS";
if (!isset($options['show_products'])) $options['show_products'] = 1;

?>
<div class="ccm-block-field-group">

<h2><?php  echo t("Search Form")?></h2>
<?php  echo  $form->checkbox('options[show_search_form]',1,$options['show_search_form']) ?><?php  echo  t('Display'); ?>
 <?php  echo  $form->select('options[search_mode]',array('simple'=>'Simple','advanced'=>'Advanced'),$options['search_mode']); ?> <?php  echo t('search form.')?>
<br/><br/>
<h3><?php  echo t('Submitting this form directs the user')?></h3>
<div> 
	<input type="radio" name="baseSearchPath" id="baseSearchPathThis" value="THIS" <?php  echo ($baseSearchPath=="THIS")?'checked':''?> onchange="productListBlock.pathSelector(this)" >
	<?php  echo t('Back to this page')?>
</div>

<div>
	<input type="radio" name="baseSearchPath" id="baseSearchPathOther" value="OTHER" onchange="productListBlock.pathSelector(this)" <?php  echo ($baseSearchPath=="OTHER")?'checked':''?>>
	<?php  echo t('To another page')?>
	<div id="basePathSelector" style="display:<?php  echo ($baseSearchPath=="OTHER")?'block':'none'?>" >

		<?php   $pform = Loader::helper('form/page_selector');
		if ($searchWithinOther) {
			$cpo = Page::getByPath($baseSearchPath);
			print $pform->selectPage('searchUnderCID', $cpo->getCollectionID());
		} else {
			print $pform->selectPage('searchUnderCID');
		}
		?>
	</div>
</div>
</div>

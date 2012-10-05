<?php  
defined('C5_EXECUTE') or die(_("Access Denied.")); 
$uh = Loader::helper('concrete/urls');
?>
<input type="hidden" name="blockToolsDir" value="<?php  echo $uh->getBlockTypeToolsURL($bt)?>/" />
<input type="hidden" name="currentCID" value="<?php  echo $c->getCollectionId()?>" />
<ul id="ccm-blockEditPane-tabs" class="ccm-dialog-tabs">
	<li class="ccm-nav-active"><a id="ccm-blockEditPane-tab-search" href="javascript:void(0);"><?php  echo t('Search') ?></a></li>
	<li class=""><a id="ccm-blockEditPane-tab-formatting"  href="javascript:void(0);"><?php  echo t('Data Formatting')?></a></li>
	<li class=""><a id="ccm-blockEditPane-tab-layout"  href="javascript:void(0);"><?php  echo t('Results Layout')?></a></li>
</ul>
<div id="ccm-blockEditPane-search" class="ccm-blockEditPane">
<?php  
$bt->inc('elements/form_setup_filter.php', array( 'c'=>$c, 'b'=>$b, 'controller'=>$controller,'block_args'=>$block_args   ) );
?>
</div>
<div id="ccm-blockEditPane-formatting" class="ccm-blockEditPane" style="display:none">
<?php  
$bt->inc('elements/form_setup_results.php', array( 'c'=>$c, 'b'=>$b, 'controller'=>$controller,'block_args'=>$block_args ) );
?>
</div>
<div id="ccm-blockEditPane-layout" class="ccm-blockEditPane" style="display:none">
<?php  
$bt->inc('elements/form_setup_layout.php', array( 'c'=>$c, 'b'=>$b, 'controller'=>$controller,'block_args'=>$block_args ) );
?>
</div>
<?php     
	defined('C5_EXECUTE') or die(_("Access Denied."));
	$textHelper = Loader::helper("text"); 
	// now that we're in the specialized content file for this block type, 
	// we'll include this block type's class, and pass the block to it, and get
	// the content
	
	if (count($cArray) > 0) { ?>
	
	<?php     
	for ($i = 0; $i < count($cArray); $i++ ) {
		$cobj = $cArray[$i]; 
		$title = $cobj->getCollectionName(); ?>
	<div class="news-item">
	<span><?php     echo date( 'M, d. Y',strtotime($cobj->getCollectionDatePublic())) ?></span>
		<a href="<?php     echo $nh->getLinkToCollection($cobj)?>" class="news-title"><?php     echo $title?></a>
	<?php      if ($cobj->getCollectionTypeHandle()=="Press Release") { ?>
		<h4><?php      echo $cobj->getCollectionAttributeValue('Press_Release_Type'); ?> - for release on <?php      echo strftime("%x %l:%M%p",strtotime($cobj->getCollectionAttributeValue('Release_Date'))); ?></h4>
	<?php      } ?>
	<p>
		<?php     
		if(!$controller->truncateSummaries){
			echo $cobj->getCollectionDescription();
		}else{
			echo $textHelper->shorten($cobj->getCollectionDescription(),$controller->truncateChars);
		}
		?>
	</p>
	</div>
<?php       } 
	if(!$previewMode && $controller->rss) { 
			$btID = $b->getBlockTypeID();
			$bt = BlockType::getByID($btID);
			$uh = Loader::helper('concrete/urls');
			$rssUrl = $controller->getRssUrl($b);
			?>
			<div class="rssIcon" style="position: absolute; top: 10px; right: 10px;">
				<a href="<?php     echo $rssUrl?>" title="View RSS" class="tooltip" target="_blank"><img src="<?php     echo $uh->getBlockTypeAssetsURL($bt, 'rss.png')?>" width="14" height="14" /></a>
				
			</div>
			<link href="<?php     echo $rssUrl?>" rel="alternate" type="application/rss+xml" title="<?php     echo $controller->rssTitle?>" />
		<?php      
	} 
	?>
<?php      } 
	
	if ($paginate && $num > 0 && is_object($pl)) {
		$pl->displayPaging();
	}
	
?>
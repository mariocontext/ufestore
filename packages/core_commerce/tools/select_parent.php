<?php  
	$c = Page::getByID($_REQUEST['destCID']);
?>

<div id="ccm-popup-select-parent">

		<p><?php  echo t('You have selected the page %s. If this is correct click okay.', $c->getCollectionName())?></p>

        <div class="ccm-buttons">
            <a onclick="$('#cc-parent-page-name').html('<?php  echo  $c->getCollectionName() ?>');$.fn.dialog.closeTop();$.fn.dialog.closeTop();" class="ccm-button"><span><?php  echo t('Okay')?></span></a>
            <a onclick="$.fn.dialog.closeTop();" class="ccm-button"><span><?php  echo t('Cancel')?></span></a>
        </div>

</div>

<div id="ccm-popup-create-page">

		<p><?php  echo t('If you would like to create a default detail page for this product now, select a location for that new page to live from the sitemap. If you choose not to create a page now you will still be able add the product to a page later.')?></p>

<?php  
	$uh = Loader::helper('urls', 'core_commerce');
	$sh = Loader::helper('form/page_selector'); 
    $args = array();
    $args['sitemap_disable_auto_open'] = true;
    $args['node_action'] = $uh->getToolsURL('select_parent');
    $args['dialog_title'] = t('Set Parent Page');
    $args['dialog_height'] = 80;
	$args['target_id'] = 'cc-parent-page';

	$args['select_mode'] = 'move_copy_delete';
	$args['display_mode'] = 'full';
	$args['instance_id'] = time();

    $sh->sitemap($args);
?>

        <div class="ccm-buttons">
            <a onclick="$('#<?php  echo  $args['target_id'] ?>').val('');$.fn.dialog.closeTop();" class="ccm-button"><span><?php  echo t('Skip Page Creation')?></span></a>
        </div>

</div>

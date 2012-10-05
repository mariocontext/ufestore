<h1><span><?php echo t('Vertical Streaming Settings')?></span></h1>
<div class="ccm-dashboard-inner">
	<form method="post" action="<?php echo $this->action('save_api')?>" id="ccm-commerce-vs-purchase-settings-form">
	<h2><span><?php echo t('API Key')?></span></h2>
	<div style="line-height:26px; margin-bottom:10px;">
		<?php echo $form->text('VS_API_KEY',$vs_api_key,array('size'=>'60'))?>
	</div>
	<h2><span><?php  echo t('URL')?></span></h2>
	<div style="line-height:26px">
		<?php echo $form->text('VS_API_URL',$vs_api_url,array('size'=>'60'))?>
	</div>
    <div class="ccm-buttons">
	<a href="javascript:void(0)" onclick="$('#ccm-commerce-vs-purchase-settings-form').get(0).submit()" class="ccm-button-right accept"><span><?php echo t('Update Settings')?></span></a>
	</div>
    <div class="ccm-spacer">&nbsp;</div>
	</form>
</div>
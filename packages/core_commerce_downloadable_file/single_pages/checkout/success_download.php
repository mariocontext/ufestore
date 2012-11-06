<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<div id="ccm-core-commerce-checkout-cart">

<?php  if (is_object($errorObj) && $errorObj->has()) { ?>
	<?php echo $errorObj->output();?>
<?php  }

$c = Page::getCurrentPage();
$cp = new Permissions($c); 

if (!$errorObj->has() || $cp->canWrite()) { ?>
	
	<p>Thank you for your purchase!</p>
	
	<div>
	<?php  $a = new Area('Thank You Message'); $a->display($c); ?>
	</div>
	
	<h3><?php echo t('File(s) to Download')?></h3>
	
	<?php  
	foreach($files as $d) { ?>
		<div>
			<a href="<?php  echo $d['url']?>" target="_blank"><?php  echo $d['name']?></a>
		</div>
		<br/>
		
	<?php  } ?>
	</div>

<?php 
}
?>
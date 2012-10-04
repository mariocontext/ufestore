<?php   
defined('C5_EXECUTE') or die(_("Access Denied."));
$this->inc('elements/header.php'); ?>

<div class="left">
<?php  
//  $a = new Area('Sidebar');
//	$a->display($c);
?>

<?php $this->inc('elements/left_nav.php'); ?>
</div>

<div class="center">
<?php
	$a = new Area('Main');
	$a->display($c);
?>
</div>

<div class="right">
<?php  
//	$a = new Area('Sidebar Right');
//	$a->display($c);
?>
<?php $this->inc('elements/right_nav.php'); ?>	
</div>

<?php $this->inc('elements/footer.php'); ?>
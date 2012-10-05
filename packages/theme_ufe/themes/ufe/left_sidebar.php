<?php   
defined('C5_EXECUTE') or die(_("Access Denied."));
$this->inc('elements/header.php'); ?>

<div class="left sidebar">
<?php  
  $a = new Area('Sidebar');
	$a->display($c);
?>
<?php $this->inc('elements/left_nav.php'); ?>
      	
</div>

<div class="center main">
<?php
	$a = new Area('Main');
	$a->display($c);
?>
</div>

<?php $this->inc('elements/footer.php'); ?>
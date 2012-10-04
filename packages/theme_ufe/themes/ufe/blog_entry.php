<?php   
defined('C5_EXECUTE') or die(_("Access Denied."));
$this->inc('elements/header.php'); ?>

<div class="clearfix"></div>
<div class="right_sidebar">
<!--
  <article class="lsidebar">
 <?php  
    $a = new Area('Sidebar');
    $a->display($c);
?>
  </article>

  <article class="rmain">
 <?php  
    $a = new Area('Main');
    $a->display($c);
?></article>
-->
  </section>

  <div class="clearfix"></div>
    
  <div class="main_testimonials">
 <?php
    $a = new Area('Main Testimonals');
    $a->display($c);
  ?>
  </div>


    <div class="clearfix"></div>
</div>

<?php $this->inc('elements/footer.php'); ?>
<?php   
defined('C5_EXECUTE') or die(_("Access Denied."));
$this->inc('elements/header.php'); ?>


    	<article class="full">
<?php  
print $innerContent;
?> 	</article>

    </section>
    <?php       $this->inc('elements/footer.php'); ?>
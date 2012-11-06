<?php   defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
	
			<?php  
			
			$a = new Area('Product');
			$a->display($c);

			$a = new Area('Main');
			$a->display($c);
			
			?>

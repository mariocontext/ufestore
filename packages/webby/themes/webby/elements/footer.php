<br style="clear: both;" />
<div id="footer">
	
	<div id="footer-inner">
		  <div id="footer-navigation">
	  	<?php   
		    $ft_nav = BlockType::getByHandle('autonav'); 
		    $ft_nav->controller->displayPages = 'top'; 
		    $ft_nav->controller->orderBy = 'display_asc'; 
		    $ft_nav->controller->displaySubPages = 'none';
		    $ft_nav->controller->displaySubPageLevels = 'none';  
		    $ft_nav->controller->displayPagesIncludeSelf = 1; 
		    $ft_nav->render('templates/webby_menu'); 
		?>
	  </div>
	  
			&copy; <?php   echo date('Y')?> <a href="<?php   echo DIR_REL?>/"><?php   echo SITE?></a>.
			&nbsp;&nbsp;
			<?php   echo t('All rights reserved.'); ?>		
	</div>
	
</div>
<?php    Loader::element('footer_required'); ?>
</div>
</body>
</html>
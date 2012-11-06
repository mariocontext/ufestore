<?php   
defined('C5_EXECUTE') or die(_("Access Denied."));
$this->inc('elements/header.php'); ?>
<?php   $pageTitle = $c->getCollectionName(); ?>

<body>
<div id="loading"><div class="indicator"></div></div>
<div id="bg-wrapper" class="sub">
<div id="wrapper">
<div id="wrapper-inner">

<div id="header">
	
	<div id="logo">
		<!--
			--><a href="<?php   echo DIR_REL?>/" class="tooltip" title="Take me Home!" ><?php   
				$block = Block::getByName('My_Site_Name');  
				if( $block && $block->bID ) $block->display();   
				else echo SITE;
			?></a><!--
		-->
	</div>
	
	<div id="navigation">
		  <?php   
		    $bt_main = BlockType::getByHandle('autonav'); 
		    $bt_main->controller->displayPages = 'top'; 
		    $bt_main->controller->orderBy = 'display_asc'; 
		    $bt_main->controller->displaySubPages = 'none';
		    $bt_main->controller->displaySubPageLevels = 'none';  
		    $bt_main->controller->displayPagesIncludeSelf = 1; 
		    $bt_main->render('templates/webby_menu'); 
		  ?>
	</div>
	
</div>

<div id="site-title">
	<h1><?php   echo $pageTitle?></h1>
	<div id="breadcrumbs">
	<?php   
		    $bt_main = BlockType::getByHandle('autonav'); 
		    $bt_main->controller->displayPages = 'top'; 
		    $bt_main->controller->orderBy = 'display_asc'; 
		    $bt_main->controller->displaySubPages = 'relevant_breadcrumb';
		    $bt_main->controller->displaySubPageLevels = 'all';  
		    $bt_main->controller->displayPagesIncludeSelf = 1; 
		    $bt_main->render('templates/breadcrumb'); 
		  ?>
	</div>
</div>

<div id="sub-content-full">
	<?php   print $innerContent; ?>
	<?php    $a = new Area('Main'); $a->display($c); ?>
	<br style="clear: both;" />
</div>

<br style="clear: both;" />

</div>
</div>

<?php   $this->inc('elements/footer.php'); ?>
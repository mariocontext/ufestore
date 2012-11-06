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
	
	<div id="search-area">
		
		<form action="<?php   echo $this->url('/search')?>" method="get">
			<input name="search_paths[]" type="hidden" value="">
			<input name="query" type="text" class="search-field" onblur="addText(this);" onfocus="clearText(this)" value="Keyword...">
			<input name="submit" type="image" src="<?php   echo $this->getThemePath()?>/images/searchinput_btn.png" class="search-button" value="Search">
		</form>
		
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

<div id="sub-content-right" class="right">
	<?php   $a = new Area('Main'); $a->display($c); ?>
</div>

<div id="sub-sidebar-left" class="sidebar-nav">
	<?php    $a = new Area('Sidebar'); $a->display($c); ?>
	<br style="clear: both;" /><br />
	<div class="sidebar-element">
		<?php    $a = new Area('Sidebar-Element'); $a->display($c); ?>
	</div>
</div>

<br style="clear: both;" />

</div>
</div>

<?php   $this->inc('elements/footer.php'); ?>
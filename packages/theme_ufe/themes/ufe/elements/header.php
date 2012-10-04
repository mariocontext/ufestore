<?php
$page = Page::getCurrentPage();
$typeHandle = $page->getCollectionTypeHandle();

$page_type_name = "pagetype_". $typeHandle;

$pagename = str_replace(" ", "",   strtolower($page->getCollectionName()));
$pagename = str_replace("+", "_",  strtolower($pagename));
$pagename = str_replace("-", "_",  strtolower($pagename));
$pagename = str_replace("'", "",   strtolower($pagename));
$pagename = str_replace("`", "",   strtolower($pagename));
$pagename = str_replace("\"", "",  strtolower($pagename));
$pagename = str_replace("__", "_", strtolower($pagename));
$pagename = "pagename_". $pagename;
define('CURRENT_PAGE_NAME', $pagename);
?>
<!DOCTYPE html>
<html>
<head>
<?php Loader::element('header_required'); ?>
<link href="<?php echo $this->getThemePath() ?>/master.css" rel="stylesheet" type="text/css" media="screen" />


</head>
<body class="<?php echo $page_type_name ." ". $pagename ?>">

<div id="wrap">
<div id="header">
  <h2>UFE Shows</h2>
</div>

<div id="navigation">
<?php 
  $bt = BlockType::getByHandle('autonav');
  $bt->controller->displayPages = 'top';
  $bt->controller->orderBy = 'display_asc';                    
  $bt->controller->displaySubPages = 'all'; 
  $bt->controller->displaySubPageLevels = 'custom';
  $bt->controller->displaySubPageLevelsNum = '0';   
  $bt->render('templates/header_links');
?>
</div>

<div id="wrap_content">
  <div id="main_content">

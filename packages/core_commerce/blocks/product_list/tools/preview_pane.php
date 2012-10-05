<?php   defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<?php   
if ($_GET['currentCID']>0) {
	//$c = Page::getById($_GET['currentCID']);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<!-- insert CSS for Default Concrete Theme //-->
<style type="text/css">@import "<?php  echo ASSETS_URL_CSS?>/ccm.default.theme.css";</style>
<?php   
if (is_object($c)) {
	$v = View::getInstance();
	$v->disableEditing();
 	Loader::element('header_required');
} else { 
	print Loader::helper('html')->css('ccm.base.css');
	print Loader::helper('html')->javascript('jquery.js');
}
?>
</head>
<body>
<?php  

$previewMode = true;
$nh = Loader::helper('navigation');
$controller = new ProductListBlockController($b);

$productList = $controller->getRequestedSearchResults();
$products = $productList->getPage();
$paginator = $productList->getPagination();

?>
<?php   if(count($products)>0) { ?>
	<?php  echo $productList->displaySummary();?>
	<table>
	<?php   foreach ($products as $pr) { ?>
		<tr>
			<td><?php  echo $pr->outputThumbnail()?></td>
			<td><?php  echo $pr->getProductName()?></td>
		</tr>
	<?php   } ?>
	</table>
	<?php   if($paginator && strlen($paginator->getPages())>0){ ?>	
	<div class="pagination">	
		 <span class="pageLeft"><?php   echo $paginator->getPrevious(t('Previous'))?></span>
		 <?php   echo $paginator->getPages()?>
		 <span class="pageRight"><?php   echo $paginator->getNext(t('Next'))?></span>
	</div>	
	<?php   } ?>	
<?php   } else { ?>
	<?php  echo  t('No products found'); ?>
<?php   } ?>
</body>
</html>
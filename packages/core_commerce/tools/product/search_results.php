<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
$c1 = Page::getByPath('/dashboard/core_commerce');
$cp1 = new Permissions($c1);
if (!$cp1->canRead()) { 
	die(_("Access Denied."));
}

$u = new User();
$cnt = Loader::controller('/dashboard/core_commerce/products/search');
$productList = $cnt->getRequestedSearchResults();

$products = $productList->getPage();
$pagination = $productList->getPagination();


Loader::packageElement('product/search_results', 'core_commerce', array('products' => $products, 'productList' => $productList, 'pagination' => $pagination));

<?php   

Loader::model('product/list', 'core_commerce');
Loader::model('product/model', 'core_commerce');
class DashboardCoreCommerceProductsSearchController extends Controller {

	public function view() {
		$html = Loader::helper('html');
		$form = Loader::helper('form');
		$this->set('form', $form);
		$this->addHeaderItem($html->javascript('ccm.core.commerce.search.js', 'core_commerce')); 
		$this->addHeaderItem($html->css('ccm.core.commerce.search.css', 'core_commerce')); 
		$productList = $this->getRequestedSearchResults();
		$products = $productList->getPage();
				
		$this->set('productList', $productList);		
		$this->set('products', $products);		
		$this->set('pagination', $productList->getPagination());
	}
	
	public function on_start() {
		$this->set('disableThirdLevelNav', true);
	}
	
	public function product_deleted() {
		$this->set('message', t('Product deleted.'));
		$this->view();
	}
	
	
	public function view_detail($productID, $task = false) {
		$this->set('product', CoreCommerceProduct::getByID($productID));
		switch($task) {
			case 'option_created':
				$this->set('message', t('Configurable option added to product.'));
				break;
			case 'option_deleted':
				$this->set('message', t('Configurable option removed from product.'));
				break;
			case 'option_updated':
				$this->set('message', t('Configurable option updated.'));
				break;
			case 'images_updated':
				$this->set('message', t('Images updated.'));
				break;
			case 'product_updated':
				$this->set('message', t('Product updated.'));
				break;
			case 'product_duplicated':
				$this->set('message', t('Product duplicated. The new product is below.'));
				break;
			case 'product_created':
				$this->set('message', t('Product added.'));
				break;
		}
	}
	
	public function edit($productID = false) {
		Loader::model('collection_types');
		
		if ($productID == false) {
			$productID = $this->post('productID');
		}
		
		$this->set('product', CoreCommerceProduct::getByID($productID));
		
		if ($this->post()) {
			$val = Loader::helper('validation/form');
			$vat = Loader::helper('validation/token');
			$val->setData($this->post());
			$val->addRequired("prName", t("Product name required."));
			$val->test();
	
			$error = $val->getError();
		
			if (!$vat->validate('update_product')) {
				$error->add($vat->getErrorMessage());
			}
			if ($this->post('parentCID')) {
				$productDetailType = CollectionType::getByHandle('product_detail');
				if (!$productDetailType) {
					$error->add('Unable to create product detail page.  The product detail page type is not defined.');
				}
				$parent = Page::getByID($this->post('parentCID'));
				if (!parent) {
					$error->add('Unable to create product detail page.  The parent page is not valid.');
				}
			}
			if ($error->has()) {
				$this->set('error', $error);
			} else {
				$product = $this->get('product');
				$product->update($this->post());			
				$product->setPurchaseGroups($this->post('gID'));
				
				Loader::model("attribute/categories/core_commerce_product", 'core_commerce');
				$aks = CoreCommerceProductAttributeKey::getList();
				foreach($aks as $uak) {
					$uak->saveAttributeForm($product);				
				}
				$this->redirect('/dashboard/core_commerce/products/search/', 'view_detail', $product->getProductID(), 'product_updated');
			}
		}
	}

	public function duplicate($productID) {
		$pr = CoreCommerceProduct::getByID($productID);
		if (is_object($pr)) {
			if ($_POST['cParentID'] > 0) {
				$newProduct = $pr->duplicate($_POST['cParentID']);
			} else {
				$newProduct = $pr->duplicate();
			}
			$this->redirect('/dashboard/core_commerce/products/search/', 'view_detail', $newProduct->getProductID(), 'product_duplicated');
		}
	}
	
	public function delete_product($prID, $token = null,$delete_pages=0){
		try {
			$pr = CoreCommerceProduct::getByID($prID); 
				
			if(!($pr instanceof CoreCommerceProduct)) {
				throw new Exception(t('Invalid product ID.'));
			}
	
			$valt = Loader::helper('validation/token');
			if (!$valt->validate('delete_product', $token)) {
				throw new Exception($valt->getErrorMessage());
			}
			if ($delete_pages) {
				$pr->deleteProductCollection();
			}
			$pr->delete();
			
			$this->redirect("/dashboard/core_commerce/products/search", 'product_deleted');
		} catch (Exception $e) {
			$this->set('error', $e);
		}
	}

	public function getRequestedSearchResults() {
		$productList = new CoreCommerceProductList();
		$productList->sortBy('prDateAdded', 'desc');
		
		if ($_GET['keywords'] != '') {
			$productList->filterByKeywords($_GET['keywords']);
		}	
		
		if ($_REQUEST['numResults']) {
			$productList->setItemsPerPage($_REQUEST['numResults']);
		}
		
		if (is_array($_REQUEST['selectedSearchField'])) {
			foreach($_REQUEST['selectedSearchField'] as $i => $item) {
				// due to the way the form is setup, index will always be one more than the arrays
				if ($item != '') {
					switch($item) {
						case "date_added":
							$dateFrom = $_REQUEST['date_from'];
							$dateTo = $_REQUEST['date_to'];
							if ($dateFrom != '') {
								$dateFrom = date('Y-m-d', strtotime($dateFrom));
								$productList->filterByDateAdded($dateFrom, '>=');
								$dateFrom .= ' 00:00:00';
							}
							if ($dateTo != '') {
								$dateTo = date('Y-m-d', strtotime($dateTo));
								$dateTo .= ' 23:59:59';
								
								$productList->filterByDateAdded($dateTo, '<=');
							}
							break;

						default:
							$akID = $item;
							$fak = CoreCommerceProductAttributeKey::getByID($akID);
							$type = $fak->getAttributeType();
							$cnt = $type->getController();
							$cnt->setAttributeKey($fak);
							$cnt->searchForm($productList);
							break;
					}
				}
			}
		}
		return $productList;
	}
	



}
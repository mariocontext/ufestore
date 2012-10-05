<?php   

Loader::model('order/list', 'core_commerce');
class DashboardCoreCommerceOrdersSearchController extends Controller {

	public function view() {
		$html = Loader::helper('html');
		$form = Loader::helper('form');
		$this->set('form', $form);
		$this->addHeaderItem($html->javascript('ccm.core.commerce.search.js', 'core_commerce')); 
		$orderList = $this->getRequestedSearchResults();
		$orders = $orderList->getPage();
				
		$this->set('orderList', $orderList);		
		$this->set('orders', $orders);		
		$this->set('pagination', $orderList->getPagination());
	}
	
	public function update() {
	
	}
	
	public function on_start() {
		$this->set('disableThirdLevelNav', true);
	}
	
	
	public function edit($orderID = false) {

	}
	
	public function detail($id, $update = false) {
		Loader::model('order/model', 'core_commerce');
		$order = CoreCommerceOrder::getByID($id);
		$this->set('order', $order);		
		
		if ($update != false) {
			switch($update) {
				case 'status_updated':
					$this->set('message', t('Order status updated.'));
					break;
			}
		}
    }
    
    public function update_order_status() {
		Loader::model('order/model', 'core_commerce');
		$order = CoreCommerceOrder::getByID($this->post('orderID'));
		if (is_object($order)) {
			$order->setOrderStatus($this->post('oStatus'));
		}
		$this->redirect('/dashboard/core_commerce/orders/search', 'detail', $this->post('orderID'), 'status_updated');
    }

	public function getRequestedSearchResults() {
		$orderList = new CoreCommerceOrderList();
		$orderList->sortBy('oDateAdded', 'desc');
		
		if(is_numeric($_GET['keywords'])) {
		
		}
		
		if ($_GET['keywords'] != '') {
			$orderList->filterByKeywords($_GET['keywords']);
		}	
		
		if ($_REQUEST['numResults']) {
			$orderList->setItemsPerPage($_REQUEST['numResults']);
		}
		
		if ($_REQUEST['oStatus']) {
			$orderList->filterByOrderStatus($_REQUEST['oStatus']);
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
								$orderList->filterByDateAdded($dateFrom, '>=');
								$dateFrom .= ' 00:00:00';
							}
							if ($dateTo != '') {
								$dateTo = date('Y-m-d', strtotime($dateTo));
								$dateTo .= ' 23:59:59';
								
								$orderList->filterByDateAdded($dateTo, '<=');
							}
							break;

						default:
							$akID = $item;
							$fak = CoreCommerceOrderAttributeKey::getByID($akID);
							$type = $fak->getAttributeType();
							$cnt = $type->getController();
							$cnt->setAttributeKey($fak);
							$cnt->searchForm($orderList);
							break;
					}
				}
			}
		}
		return $orderList;
	}
	
	public function getBaseUrl() {
        $pkg = Package::getByHandle('core_commerce');
        if ($pkg->config('SECURITY_USE_SSL') == 'true') {
            return Config::get('BASE_URL_SSL');
        } else {
            return BASE_URL;
        }
	}

}

<?php  

defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('attribute/categories/core_commerce_order', 'core_commerce');
class CoreCommerceOrderList extends DatabaseItemList { 

	protected $attributeFilters = array();
	protected $autoSortColumns = array('oDateAdded', 'oStatus');
	protected $itemsPerPage = 10;
	protected $attributeClass = 'CoreCommerceOrderAttributeKey';
	
	protected $oStatusFilter = false;
	
	/* magic method for filtering by attributes. */
	public function __call($nm, $a) {
		if (substr($nm, 0, 8) == 'filterBy') {
			$txt = Loader::helper('text');
			$attrib = $txt->uncamelcase(substr($nm, 8));
			if (count($a) == 2) {
				$this->filterByAttribute($attrib, $a[0], $a[1]);
			} else {
				$this->filterByAttribute($attrib, $a[0]);
			}
		}			
	}
	
	/** 
	 * Filters by public date
	 * @param string $date
	 */
	public function filterByDateAdded($date, $comparison = '=') {
		$this->filter('orders.oDateAdded', $date, $comparison);
	}
	
	// Filters by "keywords"
	public function filterByKeywords($keywords) {
		$db = Loader::db();
		$qkeywords = $db->quote('%' . $keywords . '%');
		$keys = CoreCommerceOrderAttributeKey::getSearchableIndexedList();
		$attribsStr = '';
		foreach ($keys as $ak) {
			$cnt = $ak->getController();			
			$attribsStr.=' OR ' . $cnt->searchKeywords($keywords);
		}
		
		$this->filter(false, '( orders.orderID = '.$db->quote($keywords). ' ' . $attribsStr . ')');	
	}
	

	protected function setBaseQuery() {
		$this->setQuery('SELECT orders.orderID from CoreCommerceOrders orders');
	}

	/** 
	 * Returns an array of page objects based on current settings
	 */
	public function get($itemsToGet = 0, $offset = 0) {
		$orders = array();
		Loader::model('order/model', 'core_commerce');
		$this->createQuery();
		$r = parent::get($itemsToGet, $offset);
		foreach($r as $row) {
			$o = CoreCommerceOrder::getByID($row['orderID']);			
			$orders[] = $o;
		}
		return $orders;
	}
	
	public function filterByOrderStatus($status) {
		$this->oStatusFilter = $status;
		 $this->filter('oStatus', $status);
	}
	
	public function getTotal(){
		$this->createQuery();
		return parent::getTotal();
	}
	
	//this was added because calling both getTotal() and get() was duplicating some of the query components
	protected function createQuery() {
		if(!$this->queryCreated) {
			$this->setBaseQuery();
			$this->setupAttributeFilters("left join CoreCommerceOrderSearchIndexAttributes on (orders.orderID = CoreCommerceOrderSearchIndexAttributes.orderID)");
			if ($this->oStatusFilter === false) {
				$this->filter('oStatus', 0, '>');
			}
			$this->queryCreated = 1;
		}
	}
	
	//$key can be handle or fak id
	public function sortByAttributeKey($key,$order='asc') {
		$this->sortBy($key, $order); // this is handled natively now
	}

}

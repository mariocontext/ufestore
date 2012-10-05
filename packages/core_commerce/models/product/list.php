<?php  

defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('attribute/categories/core_commerce_product', 'core_commerce');
class CoreCommerceProductList extends DatabaseItemList { 

	protected $attributeFilters = array();
	protected $autoSortColumns = array('prDateAdded', 'prName','prPrice','prStatus');
	protected $itemsPerPage = 10;
	protected $attributeClass = 'CoreCommerceProductAttributeKey';
	
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
		$this->filter('pr.prDateAdded', $date, $comparison);
	}
	
	// Filters by "keywords"
	public function filterByKeywords($keywords) {
		$db = Loader::db();
		$qkeywords = $db->quote('%' . $keywords . '%');
		$keys = CoreCommerceProductAttributeKey::getSearchableIndexedList();
		$attribsStr = '';
		foreach ($keys as $ak) {
			$cnt = $ak->getController();			
			$attribsStr.=' OR ' . $cnt->searchKeywords($keywords);
		}
		$this->filter(false, '( pr.prName like ' . $qkeywords . ' or pr.prDescription like ' . $qkeywords . ' ' . $attribsStr . ')');
	}

	protected function setBaseQuery() {
		$this->setQuery('SELECT pr.productID from CoreCommerceProducts pr');
	}

	/** 
	 * Returns an array of page objects based on current settings
	 */
	public function get($itemsToGet = 0, $offset = 0) {
		$products = array();
		Loader::model('product/model', 'core_commerce');
		$this->createQuery();
		$r = parent::get($itemsToGet, $offset);
		foreach($r as $row) {
			$pr = CoreCommerceProduct::getByID($row['productID']);			
			$products[] = $pr;
		}
		return $products;
	}
	
	public function getTotal(){
		$this->createQuery();
		return parent::getTotal();
	}
	
	//this was added because calling both getTotal() and get() was duplicating some of the query components
	protected function createQuery(){
		if(!$this->queryCreated){
			$this->setBaseQuery();
			$this->setupAttributeFilters("left join CoreCommerceProductSearchIndexAttributes on (pr.productID = CoreCommerceProductSearchIndexAttributes.productID)");
			$this->queryCreated = 1;
		}
	}
	
	//$key can be handle or fak id
	public function sortByAttributeKey($key,$order='asc') {
		$this->sortBy($key, $order); // this is handled natively now
	}
	
	public function filterByAttribute($column, $value, $comparison='=') {
		if (is_array($column)) {
			$column = $column[key($column)] . '_' . key($column);
		}
		
		if($comparison == '=') {
			$db = Loader::db();
			$this->attributeFilters[] = array(false,"(ak_{$column} = ".$db->quote($value)." OR TRIM(ak_{$column}) LIKE ".$db->quote("%\n".$value."\n%").")");
		} else {
			$this->attributeFilters[] = array('ak_' . $column, $value, $comparison);
		}
	}
	
}

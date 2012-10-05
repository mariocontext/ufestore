<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));

class CoreCommerceOrderAttributeKey extends AttributeKey {

	public function getIndexedSearchTable() {
		return 'CoreCommerceOrderSearchIndexAttributes';
	}

	protected $searchIndexFieldDefinition = 'orderID I(11) UNSIGNED NOTNULL DEFAULT 0 PRIMARY';
	const ORDER_ADJUSTMENT_ATTRIBUTE_TYPE_HANDLE = 'order_adjustment';
	
	public function load($akID) {
		parent::load($akID);
		$db = Loader::db();
		$row = $db->GetRow("select orakIsRequired from CoreCommerceOrderAttributeKeys where akID = ?", array($akID));
		$this->setPropertiesFromArray($row);
	}

	public function getAttributes($orderID, $method = 'getValue') {
		$db = Loader::db();
		$values = $db->GetAll("select akID, avID from CoreCommerceOrderAttributeValues where orderID = ?", array($orderID));
		$avl = new AttributeValueList();
		foreach($values as $val) {
			$ak = CoreCommerceOrderAttributeKey::getByID($val['akID']);
			if (is_object($ak)) {
				$value = $ak->getAttributeValue($val['avID'], $method);
				$avl->addAttributeValue($ak, $value);
			}
		}
		return $avl;
	}
	
	public static function getColumnHeaderList() {
		return parent::getList('core_commerce_order', array('akIsColumnHeader' => 1));	
	}

	public static function getSearchableList() {
		return parent::getList('core_commerce_order', array('akIsSearchable' => 1));	
	}
	public static function getSearchableIndexedList() {
		return parent::getList('core_commerce_order', array('akIsSearchableIndexed' => 1));	
	}

	public function getAttributeValue($avID, $method = 'getValue') {
		$av = CoreCommerceOrderAttributeValue::getByID($avID);
		$av->setAttributeKey($this);
		return call_user_func_array(array($av, $method), array());
	}

	public function isAttributeKeySubTotalLineItem() {
		return $this->atHandle == CoreCommerceOrderAttributeKey::ORDER_ADJUSTMENT_ATTRIBUTE_TYPE_HANDLE;
	}
	
	public function isOrderAttributeKeyRequired() {
		return $this->orakIsRequired;
	}
	
	public static function getByID($akID) {
		$ak = new CoreCommerceOrderAttributeKey();
		$ak->load($akID);
		if ($ak->getAttributeKeyID() > 0) {
			return $ak;	
		}
	}

	public static function getByHandle($akHandle) {
		$db = Loader::db();
		$akID = $db->GetOne('select akID from AttributeKeys inner join AttributeKeyCategories on AttributeKeys.akCategoryID = AttributeKeyCategories.akCategoryID where akHandle = ? and akCategoryHandle = \'core_commerce_order\'', array($akHandle));
		$ak = new CoreCommerceOrderAttributeKey();
		$ak->load($akID);
		if ($ak->getAttributeKeyID() > 0) {
			return $ak;	
		}
	}
	
	public static function getList() {
		return parent::getList('core_commerce_order');	
	}
	
	protected function saveAttribute($order, $value = false) {
		$av = $order->getAttributeValueObject($this, true);
		parent::saveAttribute($av, $value);
		$db = Loader::db();
		$v = array($order->getOrderID(), $this->getAttributeKeyID(), $av->getAttributeValueID());
		$db->Replace('CoreCommerceOrderAttributeValues', array(
			'orderID' => $order->getOrderID(), 
			'akID' => $this->getAttributeKeyID(), 
			'avID' => $av->getAttributeValueID()
		), array('orderID', 'akID'));
		unset($av);
		$order->reindex();
	}
	
	public function add($type, $args, $pkg = false) {
		$ak = parent::add('core_commerce_order', $type, $args, $pkg);
		
		extract($args);
		
		if ($orakIsRequired != 1) {
			$orakIsRequired = 0;
		}

		$v = array($ak->getAttributeKeyID(), $orakIsRequired);
		$db = Loader::db();
		$db->Execute('REPLACE INTO CoreCommerceOrderAttributeKeys (akID, orakIsRequired) VALUES (?, ?)', $v);
		
		$nak = new CoreCommerceOrderAttributeKey();
		$nak->load($ak->getAttributeKeyID());
		return $ak;
	}
	
	public function update($args) {
		$ak = parent::update($args);	
		extract($args);
		if ($orakIsRequired != 1) {
			$orakIsRequired = 0;
		}
		$v = array($ak->getAttributeKeyID(), $orakIsRequired);
		$db = Loader::db();
		$db->Execute('REPLACE INTO CoreCommerceOrderAttributeKeys (akID, orakIsRequired) VALUES (?, ?)', $v);
	}

	public function delete() {
		parent::delete();
		$db = Loader::db();
		//$db->Execute('delete from CoreCommerceOrderAttributeKeys where akID = ?', array($this->getAttributeKeyID()));
		$r = $db->Execute('select avID from CoreCommerceOrderAttributeValues where akID = ?', array($this->getAttributeKeyID()));
		while ($row = $r->FetchRow()) {
			$db->Execute('delete from AttributeValues where avID = ?', array($row['avID']));
		}
		$db->Execute('delete from CoreCommerceOrderAttributeValues where akID = ?', array($this->getAttributeKeyID()));
	}

}

class CoreCommerceOrderAttributeValue extends AttributeValue {

	public function setOrder($order) {
		$this->order = $order;
	}
	
	public static function getByID($avID) {
		$cav = new CoreCommerceOrderAttributeValue();
		$cav->load($avID);
		if ($cav->getAttributeValueID() == $avID) {
			return $cav;
		}
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from CoreCommerceOrderAttributeValues where orderID = ? and akID = ? and avID = ?', array(
			$this->order->getOrderID(), 
			$this->attributeKey->getAttributeKeyID(),
			$this->getAttributeValueID()
		));

		// Before we run delete() on the parent object, we make sure that attribute value isn't being referenced in the table anywhere else
		$num = $db->GetOne('select count(avID) from CoreCommerceOrderAttributeValues where avID = ?', array($this->getAttributeValueID()));
		if ($num < 1) {
			parent::delete();
		}
	}
}

class CoreCommerceCurrentOrderAttributeKey extends CoreCommerceOrderAttributeKey {}
class CoreCommerceCurrentOrderAttributeValue extends CoreCommerceOrderAttributeValue {}
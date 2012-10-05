<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
class CoreCommerceOrderStatusHistory extends Object {

	
	public static function getByID($id) {
		$db = Loader::db();
		$row = $db->GetRow('select * from CoreCommerceOrderStatusHistory where orderStatusHistoryID = ?', $id);
		if (is_array($row) && $row['orderStatusHistoryID'] == $id) {
			$obj = new CoreCommerceOrderStatusHistory();
			$obj->setPropertiesFromArray($row);
			return $obj;
		}
	}
	
	public function getOrderStatusHistoryID() {return $this->orderStatusHistoryID;}
	public function getOrderStatusHistoryStatus() {return $this->oshStatus;}
	public function getOrderStatusHistoryStatusText() {
		return CoreCommerceOrder::getOrderStatusText($this->oshStatus);
	}
	public function getOrderStatusHistoryUserID() {return $this->uID;}
	public function getOrderStatusHistoryDateTime() {return $this->oshDateSet;}
	

}
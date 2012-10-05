<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));

class OrderAdjustmentAttributeTypeController extends AttributeTypeController  {

	protected $searchIndexFieldDefinition = array(
		'value' => 'N 14.4 DEFAULT 0 NULL',
		'label' => 'C 255 NULL',
		'type' => 'C 1 NULL'
	);

	public function getValue() {
		Loader::model('order/line_item', 'core_commerce');
		$db = Loader::db();
		$r = $db->GetRow("select label, value, type from atCoreCommerceOrderAdjustment where avID = ?", array($this->getAttributeValueID()));
		$eol = new CoreCommerceOrderLineItem($r['label'], $r['value'], $r['type']);
		return $eol;
	}

	// run when we call setAttribute(), instead of saving through the UI
	public function saveValue($data) {
		$db = Loader::db();
		$db->Replace('atCoreCommerceOrderAdjustment', array('avID' => $this->getAttributeValueID(), 'value' => $data['value'], 'type' => $data['type'], 'label' => $data['label']), 'avID', true);
	}
	
	public function deleteKey() {
		$db = Loader::db();
		$arr = $this->attributeKey->getAttributeValueIDList();
		foreach($arr as $id) {
			$db->Execute('delete from atCoreCommerceOrderAdjustment where avID = ?', array($id));
		}
	}

	public function deleteValue() {
		$db = Loader::db();
		$db->Execute('delete from atCoreCommerceOrderAdjustment where avID = ?', array($this->getAttributeValueID()));
	}
	
}
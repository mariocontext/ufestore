<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
class CoreCommerceOrderLineItem extends Object {

	public function __construct($name, $total, $type = '+') {
		$this->name = $name;
		$this->total = $total;
		$this->type = $type;
	}
	
	public function getLineItemName() {return $this->name;}
	public function getLineItemType() {return $this->type;}
	public function getLineItemTotal() {return $this->total;}
	public function getLineItemDisplayTotal() {
		Loader::library('price', 'core_commerce');
		if ($this->type == '-') { 
			return '(' . CoreCommercePrice::format($this->total) . ')';
		} else {
			return CoreCommercePrice::format($this->total);
		}
	}
	
	public function __toString() {
		return $this->getLineItemName() . ': ' . $this->getLineItemDisplayTotal();
	}
}
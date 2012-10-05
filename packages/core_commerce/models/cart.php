<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::library('price', 'core_commerce');
Loader::model('product/model', 'core_commerce');
Loader::model('order/model', 'core_commerce');
Loader::model('order/current', 'core_commerce');

class CoreCommerceCart extends Object {
	
	/** 
	 * Returns the current order object from session
	 */
	public function get() {
		return CoreCommerceCurrentOrder::get();
	}	
	
	
}
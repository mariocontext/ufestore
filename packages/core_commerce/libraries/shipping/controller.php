<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
class CoreCommerceShippingController extends Controller {
	
	protected $shippingType;
	protected $shippingMethods;
	
	public function setShippingType($t) { $this->shippingType = $t;}
	public function getShippingType() {return $this->shippingType;}
	public function getShippingMethods() {return $this->shippingMethods;}
	
	public function setupAndRun($method) {
		$args = func_get_args();
		$args = array_slice($args, 1);
		if ($method) {
			$this->task = $method;
		}
		if (method_exists($this, 'on_start')) {
			call_user_func_array(array($this, 'on_start'), array($method));
		}
		if ($method) {
			$this->runTask($method, $args);
		}
		
		if (method_exists($this, 'on_before_render')) {
			call_user_func_array(array($this, 'on_before_render'), array($method));
		}
	}
	
	public function validate() {
		$valt = Loader::helper('validation/token');
		$error = Loader::helper('validation/error');
		
		if (!$valt->validate('update_shipping_type')) {
			$error->add($valt->getErrorMessage());
		}

		return $error;		
	}
	
	public function save() {}

}
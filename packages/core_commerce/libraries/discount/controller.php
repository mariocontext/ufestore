<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
class CoreCommerceDiscountController extends Controller {
	
	protected $discountType;
	
	public function setDiscountType($t) { $this->discountType = $t;}
	public function getDiscountType() {return $this->discountType;}
	public function setDiscount($d) {$this->discount = $d;}
	public function getDiscount() {return $this->discount;}
	public function getDiscountMethods() {return $this->discountMethods;}
	
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
	
	public function validateDiscount() {
		$val = Loader::helper('validation/form');
		$valt = Loader::helper('validation/token');
		$val->setData($this->post());
		$val->addRequired("discountHandle", t("Handle required."));
		$val->addRequired("discountName", t('Name required.'));
		$val->addRequired("discountTypeID", t('Type required.'));
		$val->test();
		$error = $val->getError();		

		if (!$valt->validate('add_or_update_discount')) {
			$error->add($valt->getErrorMessage());
		}

		return $error;		
	}
	
	public function validate() {}	
	public function save() {}
	public function delete() {}
}
<?php   

Loader::model('payment/method', 'core_commerce');
Loader::model('sales/tax/rate', 'core_commerce');
class DashboardCoreCommercePaymentController extends Controller {

	public function view() {		
		$this->set('methods', CoreCommercePaymentMethod::getList());
	}
	
	public function edit_method($methodID) {
		$est = CoreCommercePaymentMethod::getByID($methodID);
		$this->set("method", $est);
	}
	
	public function on_start() {
		$this->set('ih', Loader::helper('concrete/interface'));
		$this->set('form', Loader::helper('form'));
		$this->set('disableThirdLevelNav', true);
	}
	
	public function add_payment_method() {
		$pat = CoreCommercePendingPaymentMethod::getByHandle($this->post('paymentMethodHandle'));
		if (is_object($pat)) {
			$pat->install();
		}
		$this->redirect('dashboard/core_commerce/payment', 'payment_method_added');
	}
	
	public function save() {
		$paymentMethodID = $this->post('paymentMethodID');
		$method = CoreCommercePaymentMethod::getByID($paymentMethodID);
		
		$cnt = $method->getController();
		$e = $cnt->validate();
		if ($e->has()) {
			$this->set('error', $e);
			$this->edit_method($paymentMethodID);
		} else {
			$method->update($this->post());
			$this->redirect('/dashboard/core_commerce/payment/', 'payment_method_updated');
		}		
	}
	
	public function payment_method_updated() {
		$this->set('message', t('Payment Method Saved'));
		$this->view();
	}

	public function payment_method_added() {
		$this->set('message', t('Payment Method Installed'));
		$this->view();
	}

	public function sales_tax_added() {
		$this->set('message', t('Sales Tax Added'));
		$this->view();
	}
	public function sales_tax_updated() {
		$this->set('message', t('Sales Tax Updated'));
		$this->view();
	}
	public function sales_tax_deleted() {
		$this->set('message', t('Sales Tax Deleted'));
		$this->view();
	}

}
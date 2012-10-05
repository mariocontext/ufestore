<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::library('payment/controller', 'core_commerce');
class CoreCommerceDefaultPaymentMethodController extends CoreCommercePaymentController {

    public function method_form() {
        $pkg = Package::getByHandle('core_commerce');
        $this->set('PAYMENT_METHOD_DEFAULT_TRANSACTION_TYPE', $pkg->config('PAYMENT_METHOD_DEFAULT_TRANSACTION_TYPE'));
        $this->set('PAYMENT_METHOD_DEFAULT_EMAIL_RECEIPT', $pkg->config('PAYMENT_METHOD_DEFAULT_EMAIL_RECEIPT'));
    }
    
    public function validate() {
        $e = parent::validate();
        return $e;
    }
    
    public function form() {
        $this->set('action', $this->action('submit'));
        return;
    }
        
    public function action_submit() {
		Loader::controller('/checkout');
		// This is necessary so that we can 
		// call the various "on checkout complete" events
		$cnt = new CheckoutController();
		$cnt->testStep = false;
		$cnt->on_start();
		
		$ch = Loader::helper('/checkout/step', 'core_commerce');
		$steps = $ch->getSteps();		
		
        Loader::model('order/current', 'core_commerce');
	    $o = CoreCommerceCurrentOrder::get();

        $u = new User();
        $ui = UserInfo::getByID($u->getUserID());
        $pkg = Package::getByHandle('core_commerce');
        if ($ui && $ui->getUserEmail() && $pkg->config('PAYMENT_METHOD_DEFAULT_EMAIL_RECEIPT') == 'true') {
            $mh = Loader::helper('mail');
            $mh->to($ui->getUserEmail());
    		$mh->setSubject("Customer Receipt");
			$mh->setBody("Thank you for your order! Your order number is #" . $o->getOrderID() . ".");
			try {
				$mh->sendMail();
			} catch (Exception $e) {
                Log::addEntry('Error sending receipt email for order #'.$o->getOrderID().': '.$e->getMessage());
			}
        }

        $pkg = Package::getByHandle('core_commerce');
        if (strtolower($pkg->config('PAYMENT_METHOD_DEFAULT_TRANSACTION_TYPE')) == 'authorization') {
            $o->setStatus(CoreCommerceOrder::STATUS_PENDING);
        } else {
            $o->setStatus(CoreCommerceOrder::STATUS_AUTHORIZED);
        }

		parent::finishOrder($o, 'Default Gateway');
		$ch->setCurrentPagePath('/checkout/payment/form');
		$sch = $ch->getNextCheckoutStep();		
		$this->redirect($sch->getRedirectURL());
	}

    public function save() {
        $pkg = Package::getByHandle('core_commerce');
        $pkg->saveConfig('PAYMENT_METHOD_DEFAULT_TRANSACTION_TYPE', $this->post('PAYMENT_METHOD_DEFAULT_TRANSACTION_TYPE'));
        $pkg->saveConfig('PAYMENT_METHOD_DEFAULT_EMAIL_RECEIPT', $this->post('PAYMENT_METHOD_DEFAULT_EMAIL_RECEIPT'));
    }
    
}

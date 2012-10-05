<?php   

Loader::model('sales/tax/rate', 'core_commerce');
class DashboardCoreCommercePaymentTaxController extends Controller {
	
	public function on_start() {
		$this->set('disableThirdLevelNav', true);
		$at = AttributeType::getByHandle('address');
		$this->addHeaderItem(Loader::helper('html')->javascript($at->getAttributeTypeFileURL('country_state.js')));
		$this->addHeaderItem(Loader::helper('html')->javascript($at->getController()->getView()->action('load_provinces_js')));
		$this->addHeaderItem('<script type="text/javascript">$(function() { ccm_setupAttributeTypeAddressSetupStateProvinceSelector(\'ccm-core-commerce-sales-tax-location\'); });</script>');
	}
	
	public function delete($rateID, $token = null){
		try {
			$rate = CoreCommerceSalesTaxRate::getByID($rateID); 
				
			if(!($rate instanceof CoreCommerceSalesTaxRate)) {
				throw new Exception(t('Invalid tax rate ID.'));
			}
	
			$valt = Loader::helper('validation/token');
			if (!$valt->validate('delete_rate', $token)) {
				throw new Exception($valt->getErrorMessage());
			}
			
			$rate->delete();
			
			$this->redirect("/dashboard/core_commerce/payment", 'sales_tax_deleted');
		} catch (Exception $e) {
			$this->set('error', $e);
		}
	}

	public function add_rate() {
		
		if ($this->isPost()) {
			$rate = CoreCommerceSalesTaxRate::add($this->post());
			$this->redirect('/dashboard/core_commerce/payment/', 'sales_tax_added');
		}
	}
	
	public function edit($rateID = false) {
		if ($rateID > 0) {
			$rate = CoreCommerceSalesTaxRate::getByID($rateID);
		} else {
			$rate = CoreCommerceSalesTaxRate::getByID($this->post('rateID'));
		}
		
		if (!is_object($rate)) {
			$this->redirect("/dashboard/core_commerce/payment");
		} else if ($this->isPost()) {
			$rate->update($this->post());
			$this->redirect('/dashboard/core_commerce/payment/', 'sales_tax_updated');
		}
		
		$this->set('rate', $rate);
	}

}
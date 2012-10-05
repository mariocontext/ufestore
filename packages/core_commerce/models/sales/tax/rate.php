<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));

if(!function_exists('fnmatch')) {
    function fnmatch($pattern, $string) {
        return preg_match("#^".strtr(preg_quote($pattern, '#'), array('\*' => '.*', '\?' => '.', '\[' => '[', '\]' => ']'))."$#i", $string);
    } // end
}

class CoreCommerceSalesTaxRate extends Object {

	public function getSalesTaxRateID() {return $this->salesTaxRateID;}
	public function isSalesTaxRateEnabled() { return $this->salesTaxRateIsEnabled;}
	public function getSalesTaxRateName() {return $this->salesTaxRateName;}
	public function getSalesTaxRateStateProvince() {return $this->salesTaxRateStateProvince;}
	public function getSalesTaxRateCountry() {return $this->salesTaxRateCountry;}
	public function getSalesTaxRatePostalCode() {return $this->salesTaxRatePostalCode;}
	public function isSalesTaxIncludedInProduct() {return $this->salesTaxRateIncludedInProduct;}
	public function includeShippingInSalesTaxRate() {return $this->salesTaxRateIncludeShipping;}
	public function getSalesTaxRateAmount() {return Loader::helper('number')->flexround($this->salesTaxRateAmount);}
	
	protected function load($salesTaxRateID) {
		$db = Loader::db();
		$row = $db->GetRow('select * from CoreCommerceSalesTaxRates where salesTaxRateID = ?', array($salesTaxRateID));
		$this->setPropertiesFromArray($row);
	}
	
	public static function getByID($salesTaxRateID) {
		$ed = new CoreCommerceSalesTaxRate();
		$ed->load($salesTaxRateID);
		if ($ed->getSalesTaxRateID() > 0) {
			return $ed;
		}
	}

	public static function getList($filters = array()) {
		$db = Loader::db();
		$q = 'select salesTaxRateID from CoreCommerceSalesTaxRates where 1=1';
		foreach($filters as $key => $value) {
			if (is_string($key)) {
				$q .= ' and ' . $key . ' = ' . $value . ' ';
			} else {
				$q .= ' and ' . $value . ' ';
			}
		}
		$r = $db->Execute($q);
		$list = array();
		while ($row = $r->FetchRow()) {
			$list[] = CoreCommerceSalesTaxRate::getByID($row['salesTaxRateID']);
		}
		$r->Close();
		return $list;
	}
	
	public function add($args) {
		$txt = Loader::helper('text');
		
		extract($args);
		
		$_salesTaxRateIsEnabled = 0;
		$_salesTaxRateIncludedInProduct = 0;
		$_salesTaxRateIncludeShipping = 0;
		
		if ($salesTaxRateIsEnabled) {
			$_salesTaxRateIsEnabled = 1;
		}
		if ($salesTaxRateIncludedInProduct) {
			$_salesTaxRateIncludedInProduct = 1;
		}
		if ($salesTaxRateIncludeShipping) {
			$_salesTaxRateIncludeShipping = 1;
		}
		
		$db = Loader::db();
		$a = array($salesTaxRateName, $_salesTaxRateIsEnabled, $salesTaxRateAmount, $salesTaxRateCountry, $salesTaxRateStateProvince, $salesTaxRatePostalCode, $_salesTaxRateIncludedInProduct, $_salesTaxRateIncludeShipping);
		$r = $db->query("insert into CoreCommerceSalesTaxRates (salesTaxRateName, salesTaxRateIsEnabled, salesTaxRateAmount, salesTaxRateCountry, salesTaxRateStateProvince, salesTaxRatePostalCode, salesTaxRateIncludedInProduct, salesTaxRateIncludeShipping) values (?, ?, ?, ?, ?, ?, ?, ?)", $a);
		
		if ($r) {
			$salesTaxRateID = $db->Insert_ID();
			$rate = CoreCommerceSalesTaxRate::getByID($salesTaxRateID);
			return $rate;
		}
	}

	public function update($args) {
		$txt = Loader::helper('text');
		
		extract($args);
		
		$_salesTaxRateIsEnabled = 0;
		$_salesTaxRateIncludedInProduct = 0;
		$_salesTaxRateIncludeShipping = 0;
		
		if ($salesTaxRateIsEnabled) {
			$_salesTaxRateIsEnabled = 1;
		}
		if ($salesTaxRateIncludedInProduct) {
			$_salesTaxRateIncludedInProduct = 1;
		}
		if ($salesTaxRateIncludeShipping) {
			$_salesTaxRateIncludeShipping = 1;
		}
		
		$db = Loader::db();
		$a = array($salesTaxRateName, $_salesTaxRateIsEnabled, $salesTaxRateAmount, $salesTaxRateCountry, $salesTaxRateStateProvince, $salesTaxRatePostalCode, $_salesTaxRateIncludedInProduct, $_salesTaxRateIncludeShipping, $this->getSalesTaxRateID());
		$r = $db->query("update CoreCommerceSalesTaxRates set salesTaxRateName = ?, salesTaxRateIsEnabled = ?, salesTaxRateAmount = ?, salesTaxRateCountry = ?, salesTaxRateStateProvince = ?, salesTaxRatePostalCode =?, salesTaxRateIncludedInProduct = ?, salesTaxRateIncludeShipping = ? where salesTaxRateID = ?", $a);
		
		if ($r) {
			$newrate = CoreCommerceSalesTaxRate::getByID($salesTaxRateID);
			return $newrate;
		}
	}

	public static function setupEnabledRates($order) {
		$address = $order->getAttribute('shipping_address');
		if (!is_object($address)) {
			$address = $order->getAttribute('billing_address');
		}
		if (!is_object($address)) {
			return false;
		}
		$list = self::getList();
		$order->clearAttribute('sales_tax');
		foreach($list as $rate) {
			$doTax = false;
			$amount = 0;

			if ($rate->isSalesTaxRateEnabled()) {
				if ($rate->getSalesTaxRateCountry() != '' && $rate->getSalesTaxRateStateProvince() && $rate->getSalesTaxRatePostalCode() != '') {
					// they have to be in all three
					$doTax = ($rate->getSalesTaxRateCountry() == $address->getCountry() && $rate->getSalesTaxRateStateProvince() == $address->getStateProvince() && fnmatch($rate->getSalesTaxRatePostalCode(), $address->getPostalCode()));
				} else if ($rate->getSalesTaxRateCountry() != '' && $rate->getSalesTaxRateStateProvince()) {
					$doTax = ($rate->getSalesTaxRateCountry() == $address->getCountry() && $rate->getSalesTaxRateStateProvince() == $address->getStateProvince());
				} else if ($rate->getSalesTaxRateCountry() != '') {
					$doTax = ($rate->getSalesTaxRateCountry() == $address->getCountry());
				}
			}
			
			if ($doTax) {
				foreach($order->getProducts() as $product) {
					if ($product->productRequiresSalesTax()) {
						$amount += round(($rate->getSalesTaxRateAmount() / 100) * $product->getProductCartQuantizedPrice(), 2);
					}
				}

				if ($rate->includeShippingInSalesTaxRate()) {
        			/* Get the shipping method name so we can pick it out of the line items */
        			$shipMethod = $order->getOrderShippingMethod();
					if ($shipMethod) {
        				$shipMethodName = $order->getOrderShippingMethod()->getName();

        				/* Get the shipping cost */
        				$shippingPrice = 0.00;
        				$items = $order->getOrderLineItems();
        				foreach($items as $it) {
            				$itName = $it->getLineItemName();
            				if ($itName == $shipMethodName) {
                				$shippingPrice += $it->getLineItemTotal();
            				}
        				}

						/* Add tax based on the shipping cost */
						$amount += round(($rate->getSalesTaxRateAmount() / 100) * $shippingPrice, 2);
					}
				}
			}
			
			if ($amount > 0) {
				$type = '+';
				if ($rate->isSalesTaxIncludedInProduct()) {
					$type = '=';
				}
				$order->setAttribute('sales_tax', array('label' => $rate->getSalesTaxRateName(), 'type' => $type, 'value' => $amount));
			}
		}
	}
	
	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from CoreCommerceSalesTaxRates where salesTaxRateID = ?', array($this->getSalesTaxRateID()));
	}

}
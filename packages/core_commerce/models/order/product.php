<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
class CoreCommerceOrderProduct extends Object {
	
	public function __call($method, $args) {
		return call_user_func_array(array($this->product, $method), $args);
	}
	
	public function getByID($orderProductID) {
		$db = Loader::db();
		$row = $db->GetRow("select productID, orderID, quantity, prPricePaid, prName from CoreCommerceOrderProducts where orderProductID = ?", $orderProductID);
		if (is_array($row) && $row['productID']) {
			Loader::model('product/model', 'core_commerce');
			$ccp = new CoreCommerceOrderProduct();
			$ccp->setOrderProductID($orderProductID);
			$ccp->product = CoreCommerceProduct::getByID($row['productID']);
			$ccp->productID = $row['productID'];
			$ccp->order = CoreCommerceOrder::getByID($row['orderID']);
			$ccp->prPricePaid = $row['prPricePaid'];
			$ccp->prName = $row['prName'];
			$ccp->quantity = $row['quantity'];
			return $ccp;
		}
	}

	public function getByOrderID($orderID) {
		$order = CoreCommerceOrder::getByID($orderID);
		if (!order)
			return null;

		$products = array();
		$db = Loader::db();
		$r = $db->query("select orderProductID, productID, quantity, prPricePaid, dateAdded, prName from CoreCommerceOrderProducts where orderID = ? order by orderProductID", $orderID);
		while ($r && $row = $r->fetchRow()) {
			Loader::model('product/model', 'core_commerce');
			$ccp = new CoreCommerceOrderProduct();
			$ccp->setOrderProductID($row['orderProductID']);
			$ccp->product = CoreCommerceProduct::getByID($row['productID']);
			$ccp->order = $order;
			$ccp->productID = $row['productID'];
			$ccp->quantity = $row['quantity'];			
			$ccp->dateAdded = $row['dateAdded'];
			$ccp->prName = $row['prName'];
			$ccp->prPricePaid = $row['prPricePaid'];
			$products[] = $ccp;
		}
		return $products;
	}
	
	protected function setOrderProductID($orderProductID) {
		$this->orderProductID = $orderProductID;
	}
	
	public function getProductID() {return $this->productID;}
	
	public function getOrderProductID() {return $this->orderProductID;}
	public function getProductObject() {
		return $this->product;
	}
	public function getProductName() {
		return $this->prName;
	}
	public function getQuantity() {return $this->quantity;}
	public function getOrderID() {return $this->order->getOrderID();}
	
	public function add($order, $product, $quantity) {
		$db = Loader::db();
		$dt = Loader::helper('date');
		$db->Execute('insert into CoreCommerceOrderProducts (productID, orderID, quantity, prPricePaid, dateAdded, prName) values (?, ?, ?, ?, ?, ?)', array($product->getProductID(), $order->getOrderID(), $quantity, $product->getProductPriceToPay(), $dt->getLocalDateTime(), $product->getProductName()));
		//record purchase
		$db->Replace('CoreCommerceProductStats', array('productID' => $product->getProductID(), 'totalPurchases' => 'totalPurchases + 1'), 'productID', false);
		$ccp =  CoreCommerceOrderProduct::getByID($db->Insert_ID());
		
		return $ccp;
	}
	
	public function appendAttributePrice($price) {
		$price = $this->getOrderProductPrice() + $price;
		$this->prPricePaid = $price;
		$db = Loader::db();
		$db->Execute('update CoreCommerceOrderProducts set prPricePaid = ? where orderProductID = ?', array($price, $this->getOrderProductID()));	
	}

	public function getOrderProductPrice() {
		return $this->prPricePaid;
	}

	public function getOrderProductDisplayPrice() {
		return CoreCommercePrice::format($this->prPricePaid);
	}

	public function getOrderProductQuantizedPrice() {
		return $this->prPricePaid * $this->quantity;
	}
	
	public function getProductCartDisplayPrice() {
		return CoreCommercePrice::format($this->prPricePaid);
	}

	public function getProductCartPrice() {
		return $this->prPricePaid;
	}

	public function getProductCartQuantizedPrice() {
		return $this->prPricePaid * $this->quantity;
	}

	public function getQuantityField() {
		$form = Loader::helper('form');
		if ($this->product->productIsPhysicalGood()) {
			$text = $form->hidden('max_quantity_' . $this->getOrderProductID(), $this->product->getProductQuantity());
			if ($this->quantity > $this->product->getProductQuantity()) {
				$this->quantity = $this->product->getProductQuantity();
			}
			$text .= $form->text('quantity_' . $this->getOrderProductID(), $this->quantity, array('class'=>'ccm-core-commerce-max-quantity'));
			return $text;
		} else {
			return $this->getQuantity();
		}
	}

	public function setQuantity($num) {
		$this->quantity = $num;
		$db = Loader::db();
		$db->Execute('update CoreCommerceOrderProducts set quantity = ? where orderProductID = ?', array($num, $this->getOrderProductID()));
	}
	
	public function setAttribute($ak, $value) {
		Loader::model('attribute/categories/core_commerce_product_option', 'core_commerce');
		if (!is_object($ak)) {
			$ak = CoreCommerceProductOptionAttributeKey::getByHandle($this->getProductID() . '_' . $ak);
		}
		$ak->setAttribute($this, $value);
	}

	/**
	 * Compares the current product against another product's ($prod param) attributes to see if they're equal
	 * @param CoreCommerceOrderProduct $prod
	 * @return boolean
	 */
	public function hasEqualAttributes($prod) {
		$attribs = $this->getProductConfigurableAttributes();
		foreach($attribs as $ak) {
			if ($this->getAttribute($ak) != $prod->getAttribute($ak)) {
				return false;
			} elseif($this->getAttribute($ak,'price') != $prod->getAttribute($ak,'price')) {// if it modifies the price, make sure those are the same
				return false;
			}
		}
		return true;
	}
		
	/** 
	 * Gets the value of the attribute for the user
	 */
	public function getAttribute($ak, $displayMode = false) {
		Loader::model('attribute/categories/core_commerce_product_option', 'core_commerce');
		if (!is_object($ak)) {
			$ak = CoreCommerceProductOptionAttributeKey::getByHandle($this->getProductID() . '_' . $ak);
		}
		if (is_object($ak)) {
			$av = $this->getAttributeValueObject($ak);
			if (is_object($av)) {
				return $av->getValue($displayMode);
			}
		}
	}
	
	public function getAttributeValueObject($ak, $createIfNotFound = false) {
		$db = Loader::db();
		$av = false;
		$v = array($this->getOrderProductID(), $ak->getAttributeKeyID());
		$avID = $db->GetOne("select avID from CoreCommerceProductOptionAttributeValues where orderProductID = ? and akID = ?", $v);
		if ($avID > 0) {
			$av = CoreCommerceProductOptionAttributeValue::getByID($avID);
			if (is_object($av)) {
				$av->setProduct($this);
				$av->setAttributeKey($ak);
			}
		}
		
		if ($createIfNotFound) {
			$cnt = 0;
		
			// Is this avID in use ?
			if (is_object($av)) {
				$cnt = $db->GetOne("select count(avID) from CoreCommerceProductOptionAttributeValues where avID = ?", $av->getAttributeValueID());
			}
			
			if ((!is_object($av)) || ($cnt > 1)) {
				$av = $ak->addAttributeValue();
			}
		}
		
		return $av;
	}

}

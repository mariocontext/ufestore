<?php   

Loader::model('attribute/categories/core_commerce_order','core_commerce');
Loader::model('cart', 'core_commerce');
Loader::model('order/current', 'core_commerce');
Loader::model('order/product', 'core_commerce');
class CartController extends Controller {
	
	public function on_start() {
		$this->addHeaderItem(Loader::helper('html')->css('ccm.core.commerce.cart.css', 'core_commerce'));
		$this->addHeaderItem(Loader::helper('html')->css('ccm.core.commerce.checkout.css', 'core_commerce'));
		$this->addHeaderItem(Loader::helper('html')->javascript('ccm.core.commerce.cart.js', 'core_commerce'));
		$this->error = Loader::helper('validation/error');
	}
	
	
	public function update() {
		$cart = CoreCommerceCurrentOrder::get();
		$js = Loader::helper('json');
		$obj = new stdClass;
		$obj->error = false;
		$obj->message = '';

		if ($_POST['productID']) {
			$pr = CoreCommerceProduct::getByID($_POST['productID']);
			$attribs = $pr->getProductConfigurableAttributesRequired();
			foreach($attribs as $at) { 
				$e1 = $at->validateAttributeForm();
				if ($e1 == false) {
					$this->error->add(t('The field "%s" is required', $at->getAttributeKeyName()));
				} else if ($e1 instanceof ValidationErrorHelper) {
					$this->error->add($e1->getList());
				}
			}
			$quantity = 1;
			if (isset($_POST['quantity'])) {
				$quantity = $_POST['quantity'];
			}

			if ($quantity + $cart->getProductTotalQuantityInOrder($pr) > $pr->getProductQuantity()) {
				$quantity = $pr->getProductQuantity();
				$this->error->add(t("Not enough stock to complete your request.\nAvailable stock is %s", $pr->getProductQuantity()));
			}
			
			if ($this->error->has()) {
				$obj->error = true;
				foreach($this->error->getList() as $s) {
					$obj->message .= $s . "\n";
				}
			} else {
				// add the product to the cart
				$pr = $cart->addProduct($pr, $quantity);
				// append attributes to order product
				$attribs = $pr->getProductConfigurableAttributes();
				foreach($attribs as $at) { 
					$at->saveAttributeForm($pr);
				}
				
				// calculate the price after attributes are considered
				$adjustment = 0;
				
				foreach($attribs as $at) { 
					$type = $at->getAttributeType();
					if(strstr($type->getAttributeTypeHandle(),'product_price_adjustment_')) {
						$res = $pr->getAttribute($at,'price');
						if(isset($res) && is_numeric($res)) {
							$adjustment += $res;
						}
					}
				}
				$pr->appendAttributePrice($adjustment);
				
				// decide wether to increment quantity or add additional line item
				if ($other = $cart->orderContainsOtherProduct($pr)) {
					$cart->removeProduct($pr);	
					if (!$other->productIsPhysicalGood()) {
						$newQuant = 1;
					}
					else {
						$newQuant = $other->getQuantity()+$quantity;
					}
					$other->setQuantity($newQuant);
				}
			}
			
			if ($_POST['method'] == 'JSON') { 
				print $js->encode($obj);
				exit;
			}

			
		} else { 
			
			$products = $cart->getProducts();
			foreach($products as $ecp) {
				if (!$ecp->productIsPhysicalGood()) {
					$ecp->setQuantity(1);
				} else if ($_POST['quantity_' . $ecp->getOrderProductID()] > 0) {
					if ($_POST['quantity_' . $ecp->getOrderProductID()]+$cart->getProductTotalQuantityInOrder($ecp)-$ecp->getQuantity()>$ecp->product->getProductQuantity()) {
						//get full product description for error
						$name = $ecp->getProductName();
						$attribs = $ecp->getProductConfigurableAttributes();
						foreach($attribs as $ak) {
							$name .= ", ".$ecp->getAttribute($ak);
						}
						$this->error->add(t('Not enough stock to update "%s", current stock is %d',$name,$ecp->product->getProductQuantity()));
						$_POST['quantity_' . $ecp->getOrderProductID()] = $ecp->product->getProductQuantity();
					}
					else {
						$ecp->setQuantity($_POST['quantity_' . $ecp->getOrderProductID()]);
					}
				} else if ($_POST['quantity_' . $ecp->getOrderProductID()] == 0) {
					$cart->removeProduct($ecp);
				}
			}
			
			if ($_POST['method'] == 'JSON' && (!$this->error->has())) { 
				print $js->encode($obj);
				exit;
			}
		}
		
		if (!$this->error->has()) {
			if (isset($_POST['checkout_no_dialog'])) { 
				$this->redirect('/checkout');		
			} else {
				$this->redirect('/cart');
			}
		}
		
	}

	public function on_before_render() {
		if ($_POST['method'] == 'JSON') { 
			if ($this->error->has()) {
				$js = Loader::helper('json');
				$obj = new stdClass;
				$obj->error = false;
				$obj->message = '';
				if ($this->error->has()) {
					$obj->error = true;
					foreach($this->error->getList() as $s) {
						$obj->message .= $s . "\n";
					}
				}
				print $js->encode($obj);
				exit;
			}
		}
		$this->set('error', $this->error);
	}

	public function remove_product($productID = 0) {
		$js = Loader::helper('json');
		$obj = new stdClass;
		$obj->error = false;
		$obj->message = '';

		if ($productID > 0) {
			$pr = CoreCommerceOrderProduct::getByID($productID);
			if (is_object($pr)) {
				$cart = CoreCommerceCart::get();
				$cart->removeProduct($pr);
				if ($_REQUEST['method'] == 'JSON') { 
					print $js->encode($obj);
					exit;
				}
			} else {
				$this->error->add(t('Invalid product ID.'));
			}
		}
	}
	
	public function submit() {
		parent::submit();
		/*$t = Loader::helper('validation/strings');
		if (!$t->email($this->post('oEmail'))) {
			$this->error->add(t('You must specify a valid email address.'));
		}
		
		$validAttributes = array(
			CoreCommerceOrderAttributeKey::getByHandle('billing_first_name'),
			CoreCommerceOrderAttributeKey::getByHandle('billing_last_name'),
			CoreCommerceOrderAttributeKey::getByHandle('billing_address'),
			CoreCommerceOrderAttributeKey::getByHandle('billing_phone')
		);
		
		foreach($validAttributes as $eak) {
			if (!$eak->validateAttributeForm()) {
				$this->error->add(t('The field "%s" is required', $eak->getAttributeKeyName()));
			}
		}
		
		if (!$this->error->has()) {
			$o = CoreCommerceCurrentOrder::get();
			$attributes = AttributeSet::getByHandle('billing')->getAttributeKeys();
			foreach($attributes as $eak) {
				$eak->saveAttributeForm($o);				
			}
			$o->setOrderEmail($this->post('oEmail'));
			$this->redirect($this->getNextCheckoutStep()->getRedirectURL());
		}
		*/
	}
	
}
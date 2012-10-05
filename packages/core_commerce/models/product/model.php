<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::library('price', 'core_commerce');

class CoreCommerceProduct extends Object {

	public function update($data) {
		extract($data);
		if (!$prRequiresShipping) {
			$prRequiresShipping = 0;
		}
		if (!$prPhysicalGood) {
			$prPhysicalGood = 0;
		}
		if (!$prQuantityUnlimited) {
			$prQuantityUnlimited = 0;
		}
		if (!$prQuantity) {
			$prQuantity = 0;
		}
		if (!$prStatus) {
			$prStatus = 0;
		}
		if (!$prSpecialPrice) {
			$prSpecialPrice = 0;
		}
		if (!$prPrice) {
			$prPrice = 0;
		}

		if (!$prRequiresTax) {
			$prRequiresTax = 0;
		}

		if (!$prDimL) {
			$prDimL = 0;
		}
		if (!$prDimW) {
			$prDimW = 0;
		}
		if (!$prDimH) {
			$prDimH = 0;
		}
		
		if (!$cID) {
			$cID = 0;
		}
		
		$dt = Loader::helper('date');
		
		$db = Loader::db();
		$v = array(
			$prName,
			$prDescription,
			$prRequiresShipping,
			$prPhysicalGood,
			$prQuantity,
			$prQuantityUnlimited,
			$prStatus,
			$prPrice,
			$prSpecialPrice,
			$prWeight,
			$prWeightUnits,
			$prDimL,
			$prDimW,
			$prDimH,
			$prDimUnits,
			$cID,
			$prRequiresTax,
			$prShippingModifier,
			$this->productID
		);
		$db->Execute('update CoreCommerceProducts set prName = ?,  prDescription = ?,  prRequiresShipping = ?,  prPhysicalGood = ?,  prQuantity = ?,  prQuantityUnlimited = ?, prStatus = ?,  prPrice = ?,  prSpecialPrice = ?,  prWeight = ?,  prWeightUnits = ?,  prDimL = ?,  prDimW = ?,  prDimH = ?,  prDimUnits = ?, cID = ?, prRequiresTax = ?, prShippingModifier = ? where productID = ?', $v);
	
		$this->load($this->productID);
	}
	
	public function add($data) {
		extract($data);
		if (!$prRequiresShipping) {
			$prRequiresShipping = 0;
		}
		if (!$prPhysicalGood) {
			$prPhysicalGood = 0;
		}
		if (!$prQuantityUnlimited) {
			$prQuantityUnlimited = 0;
		}
		if (!$prQuantity) {
			$prQuantity = 0;
		}
		if (!$prStatus) {
			$prStatus = 0;
		}
		if (!$prSpecialPrice) {
			$prSpecialPrice = 0;
		}
		if (!$prPrice) {
			$prPrice = 0;
		}
		if (!$prRequiresTax) {
			$prRequiresTax = 0;
		}
		if (!$prDimL) {
			$prDimL = 0;
		}
		if (!$prDimW) {
			$prDimW = 0;
		}
		if (!$prDimH) {
			$prDimH = 0;
		}
		
		$dt = Loader::helper('date');
		$prDateAdded = $dt->getLocalDateTime();
		
		$db = Loader::db();
		$v = array(
			'prName' => $prName,
			'prDescription' => $prDescription,
			'prRequiresShipping' => $prRequiresShipping,
			'prPhysicalGood' => $prPhysicalGood,
			'prQuantity' => $prQuantity,
			'prQuantityUnlimited' => $prQuantityUnlimited,
			'prStatus' => $prStatus,
			'prPrice' => $prPrice,
			'prSpecialPrice' => $prSpecialPrice,
			'prWeight' => $prWeight,
			'prWeightUnits' => $prWeightUnits,
			'prDimL' => $prDimL,
			'prDimW' => $prDimW,
			'prDimH' => $prDimH,
			'prDimUnits' => $prDimUnits,
			'prRequiresTax' => $prRequiresTax,
			'prShippingModifier' => $prShippingModifier,
			'prDateAdded' => $prDateAdded
		);
		$db->Execute('insert into CoreCommerceProducts (prName, prDescription, prRequiresShipping, prPhysicalGood, prQuantity, prQuantityUnlimited, prStatus, prPrice, prSpecialPrice, prWeight, prWeightUnits, prDimL, prDimW, prDimH, prDimUnits, prRequiresTax, prShippingModifier, prDateAdded) values (
			?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', $v);
	
		$id = $db->Insert_ID();
		$pr = new CoreCommerceProduct();
		$pr->load($id);
		if ($parentCID>0) {
			$pr->addOrUpdateCollectionID($parentCID);
		}
		return $pr;
	}
	
	public function duplicate($cParentID = 0) {
		// step 1, duplicate core table
		$ar = new ADODB_Active_Record('CoreCommerceProducts');
		$ar->Load('productID=?', array($this->productID));
		
		$ar2 = clone $ar;
		$ar2->productID = null;
		$ar2->prName = $this->getProductName() . ' ' . t('Copy');
		$ar2->cID = 0;
		$ar2->prDateAdded = Loader::helper('date')->getLocalDateTime();
		$ar2->Insert();

		$db = Loader::db();
		$newProductID = $db->Insert_ID();
		$newProduct = CoreCommerceProduct::getByID($newProductID);

		if ($cParentID > 0) {
			$newProduct->addOrUpdateCollectionID($cParentID);
		}

		// now customer choices
		Loader::model('attribute/categories/core_commerce_product_option', 'core_commerce');
		$attribs = CoreCommerceProductOptionAttributeKey::getList($this);
		foreach($attribs as $ak) {
			$ak->duplicate($newProduct);
		}
		
		// now we duplicate the attribute values
		$v = array($this->productID);
		$q = "select akID, avID from CoreCommerceProductAttributeValues where productID = ?";
		$r = $db->query($q, $v);
		while ($row = $r->fetchRow()) {
			$v2 = array($row['akID'], $row['avID'], $newProductID);
			$db->query("insert into CoreCommerceProductAttributeValues (akID, avID, productID) values (?, ?, ?)", $v2);
		}
		
		// now we duplicate images
		$r = $db->query('select fID, displayOrder from CoreCommerceProductImages where productID = ?', $v);
		while ($row = $r->fetchRow()) {
			$v2 = array($row['fID'], $row['displayOrder'], $newProductID);
			$db->query("insert into CoreCommerceProductImages (fID, displayOrder, productID) values (?, ?, ?)", $v2);
		}
		
		// now purchased groups
		$r = $db->query('select gID from CoreCommerceProductSearchPurchaseGroups where productID = ?', $v);
		while ($row = $r->fetchRow()) {
			$v2 = array($row['gID'], $newProductID);
			$db->query("insert into CoreCommerceProductSearchPurchaseGroups (gID, productID) values (?, ?)", $v2);
		}
		
		
		// now grab, reindex, and return		
		$newProduct->reindex();
		return $newProduct;
	}
	
	
	private function addOrUpdateCollectionID($parentCID=0) {
		$cID = $this->cID;
		$productDetailType = null;
		try {
			$th = Loader::helper('text');
			$data = array();
			$u = new User();
			$data['uID'] = $u->getUserID();
			$data['cName'] = $this->getProductName();
			$data['cDescription'] = $th->sanitize($this->getProductDescription());
			//parent exists, do we have a cID or we need to create one ?
			if ($cID>0) {
				$current = Page::getByID($cID);
				if (!$current->getCollectionID()) $current = null; //page was probably deleted in the meantime
			}
			if ($parentCID>0) {
				//a new parent
				Loader::model('collection_types');
				$productDetailType = CollectionType::getByHandle('product_detail');
				if (!$productDetailType) {
					throw new Exception(t('Unable to create product detail page.  The product detail page type is not defined.'));
				}
				$parent = Page::getByID($parentCID);
				if (!$parent) {
					throw new Exception(t('Unable to create product detail page.  The parent page is not valid.'));
				}
				$ppp = new Permissions($parent);
				if (!$ppp->canAddSubCollection($productDetailType)) {
					throw new Exception(t('Insufficient permissions to add a product detail page to this area of the site.'));
				}
				if ($productDetailType) {
					if (!$current) {
						//need to add the page
						$current = $parent->add($productDetailType, $data);
						$cID = $current->getCollectionID();
						$db = Loader::db();
						$v = array(
							(int) $cID,
							$this->productID
						);
						$db->Execute('update CoreCommerceProducts set cID = ? where productID = ?', $v);
					}
					else {
						//existing page, but new parent
						if ($current->getCollectionParentID() != $parentID) {
							$current->move($parent);
						}
					}
				}
			}
			if ($current) {
				$current->update($data);
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function updateCoreProductImages($data) {
		$db = Loader::db();	
		$db->Execute('update CoreCommerceProducts set prThumbnailImageFID = ?, prAltThumbnailImageFID = ?, prFullImageFID = ? where productID = ?', array(
			$data['prThumbnailImageFID'],
			$data['prAltThumbnailImageFID'],
			$data['prFullImageFID'],
			$this->getProductID()			
		));
	}
	
	public function getAdditionalProductImages() {
		$db = Loader::db();
		$r = $db->Execute('select fID from CoreCommerceProductImages where productID = ? order by displayOrder asc', array($this->productID));
		$images = array();
		while ($row = $r->FetchRow()) {
			$images[] = File::getByID($row['fID']);
		}
		return $images;
	}
	
	public function setAdditionalProductImages($data) {
		$db = Loader::db();
		$db->Execute('delete from CoreCommerceProductImages where productID = ?', array($this->productID));
		for ($i = 0; $i < count($data); $i++) {
			$db->Execute('insert into CoreCommerceProductImages (productID, fID, displayOrder) values (?, ?, ?)', array(
				$this->productID,
				$data[$i],
				$i
			));
		}
	}
	
	public function delete() {
		$db = Loader::db();
		Loader::model('attribute/categories/core_commerce_product', 'core_commerce');			
		$options = $this->getProductConfigurableAttributes();
		foreach($options as $opt) {
			$opt->delete();		
		}		
		$r = $db->Execute('select avID, akID from CoreCommerceProductAttributeValues where productID = ?', array($this->productID));
		while ($row = $r->FetchRow()) {
			$cak = CoreCommerceProductAttributeKey::getByID($row['akID']);
			$cav = $this->getAttributeValueObject($cak);
			if (is_object($cav)) {
				$cav->delete();
			}
		}
		$db->Execute('delete from CoreCommerceProducts where productID = ?', array($this->productID));
		$db->Execute('delete from CoreCommerceProductSearchIndexAttributes where productID = ?', array($this->productID));
		$db->Execute('delete from CoreCommerceProductSearchPurchaseGroups where productID = ?', array($this->productID));
	}
	
	public function deleteProductCollection() {
		if ($this->getProductCollectionID()>0) {
			$p = Page::getById($this->getProductCollectionID());
			$p->delete();
		}
	}
	
	public function setPurchaseGroups($gIDArray) {
		if (!is_array($gIDArray)) {
			return false;
		}
		$db = Loader::db();
		$db->Execute('delete from CoreCommerceProductSearchPurchaseGroups where productID = ?', $this->getProductID());
		foreach($gIDArray as $gID) {
			$db->Execute('insert into CoreCommerceProductSearchPurchaseGroups (productID, gID) values (?, ?)', array($this->getProductID(), $gID));
		}
	}
	
	public function load($id) {
		$db = Loader::db();
		$row = $db->GetRow('select prName, productID, prDescription, prThumbnailImageFID, prAltThumbnailImageFID, prFullImageFID, prQuantityUnlimited, prRequiresShipping, prDateAdded, prQuantity, prPhysicalGood, prStatus, prPrice, prSpecialPrice, prWeight, prWeightUnits, prDimL, prDimW, prDimH, prDimUnits, prRequiresTax, prShippingModifier, cID, productID from CoreCommerceProducts where productID = ?', array($id));
		if ($row['productID']) { 
			$this->setPropertiesFromArray($row);
			return true;
		} else {
			return false;
		}
	}
	
	public static function getByID($id) {
		$pr = new CoreCommerceProduct();
		if ($pr->load($id)) {
			return $pr;
		}
	}
	
	public function getProductID() {return $this->productID;}
	public function getProductDisplayPrice() {
		return CoreCommercePrice::format($this->prPrice);
	}
	public function getProductPrice() {
		$n = Loader::helper('number');
		return $n->flexround($this->prPrice);
	}
	public function getProductName() {return $this->prName;}
	public function isProductEnabled() {return $this->prStatus;;}
	public function getProductQuantity() {
		if ($this->productHasUnlimitedQuantity()) {
			//note: by returning a really large number here, we avoid having to check the unlimited flag every time
			return 999999999;
		}
		return $this->prQuantity;
	}
	public function getProductDescription() {return $this->prDescription;}
	public function getProductStatus() {return $this->prStatus;}
	public function productIsPhysicalGood() {return $this->prPhysicalGood;}
	public function productRequiresShipping() {return $this->prRequiresShipping;}
	public function getProductThumbnailImageID() {return $this->prThumbnailImageFID;}
	public function getProductThumbnailImageObject() {
		if ($this->prThumbnailImageFID > 0) {
			return File::getByID($this->prThumbnailImageFID);
		}
	}
	public function outputProductThumbnailImage() {
		return $this->outputThumbnail();
	}
	public function getProductAlternateThumbnailImageID() {return $this->prAltThumbnailImageFID;}
	public function getProductAlternateThumbnailImageObject() {
		if ($this->prAltThumbnailImageFID > 0) {
			return File::getByID($this->prAltThumbnailImageFID);
		}
	}
	public function outputProductMidImage() {
		if ($this->prFullImageFID) {
			$f = File::getByID($this->prFullImageFID);
			$im = Loader::helper('image');
			$im->outputThumbnail($f, ECOMMERCE_PRODUCT_MID_WIDTH, ECOMMERCE_PRODUCT_MID_HEIGHT);
		}
	}

	public function getProductFullImageID() {return $this->prFullImageFID;}
	public function getProductFullImageObject() {
		if ($this->prFullImageFID > 0) {
			return File::getByID($this->prFullImageFID);
		}
	}
	
	public function outputProductFullImage() {
		if ($this->prMidImageFID) {
			$f = File::getByID($this->prMidImageFID);
			$im = Loader::helper('image');
			$im->outputThumbnail($f, ECOMMERCE_PRODUCT_FULL_WIDTH, ECOMMERCE_PRODUCT_FULL_HEIGHT);
		}
	}
	

	
	public function productHasUnlimitedQuantity() {return $this->prQuantityUnlimited;}
	
	public function getProductSpecialPrice() {
		$n = Loader::helper('number');
		return $n->flexround($this->prSpecialPrice);
	}
	
	public function getProductSpecialDisplayPrice() {
		return CoreCommercePrice::format($this->prSpecialPrice);
	}
	
	/**
	 * Returns price to pay after special price and attribute modifiers have been applied
	 * @return number
	*/
	public function getProductPriceToPay() {
		$price = 0;
		if (($this->getProductSpecialPrice() != $this->getProductPrice()) && $this->getProductSpecialPrice() > 0) {
			$price =  $this->getProductSpecialPrice();
		} else {
			$price = $this->getProductPrice();
		}
		//$attributeAdjustment = $this->getProductPriceAttributeAdjustment();
		//return $price + $attributeAdjustment;
		return $price;
	}
		
	/**
	 * returns the amount to modify the product's price based on attributes that affect price
	 * @return float
	*/
	public function getProductPriceAttributeAdjustment() {
		$adjustment = 0;
		$attribs = $this->getProductConfigurableAttributes();
		foreach($attribs as $attr) {
			$adj = $this->getAttribute($attr,'price');
			if(is_numeric($adj)) {
				$adjustment += $adj;
			}
		}
		return $adjustment;
	}	
	
	public function getProductWeightAdjustment() {
		return 0;
	}
	
	public function getProductWeight() {
		$n = Loader::helper('number');
		return $n->flexround($this->prWeight);	
	}
	public function getProductWeightUnits() {return $this->prWeightUnits;}
	public function getProductDimensionLength() {
		$n = Loader::helper('number');
		return $n->flexround($this->prDimL);
	}
	public function getProductDimensionWidth() {
		$n = Loader::helper('number');
		return $n->flexround($this->prDimW);	
	}
	public function getProductDimensionHeight() {
		$n = Loader::helper('number');
		return $n->flexround($this->prDimH);
	}
	public function getProductDimensionUnits() {return $this->prDimUnits;}
	public function getProductShippingModifier() {
		$n = Loader::helper('number');
		return $n->flexround($this->prShippingModifier);
	}
	public function productRequiresSalesTax() {return $this->prRequiresTax;}
	
	public function getProductPurchaseGroupIDArray() {
		$db = Loader::db();
		$groups = $db->GetCol("select gID from CoreCommerceProductSearchPurchaseGroups where productID = ?", array($this->productID));
		return $groups;
	}

	public function outputThumbnail($width = ECOMMERCE_PRODUCT_THUMBNAIL_WIDTH, $height = ECOMMERCE_PRODUCT_THUMBNAIL_HEIGHT) {
		$im = Loader::helper('image');
		if ($this->prThumbnailImageFID) {
			$f = File::getByID($this->prThumbnailImageFID);
			$im->outputThumbnail($f, $width, $height);
		} else {
			print '<img src="' . ASSETS_URL_THEMES_NO_THUMBNAIL . '" width="' . $width . '" height="' . $height . '" />';
		}
	}
	function getProductDateAdded($type = 'system') {
		if(ENABLE_USER_TIMEZONES && $type == 'user') {
			$dh = Loader::helper('date');
			return $dh->getLocalDateTime($this->prDateAdded);
		} else {
			return $this->prDateAdded;
		}
	}

	function getProductCollectionID() {
		return $this->cID;
	}
		
	public function decreaseProductQuantity($num) {
		if (!$this->productHasUnlimitedQuantity()) {
			$db = Loader::db();
			$quantity = $this->getProductQuantity() - $num;
			if ($quantity > -1) {
				$db->Execute('update CoreCommerceProducts set prQuantity = ? where productID = ?', array($quantity, $this->productID));
			} else {
				$quantity = 0;
			}
			
			$this->prQuantity = $quantity;
		}
	}
	
	public function setAttribute($ak, $value) {
		Loader::model('attribute/categories/core_commerce_product', 'core_commerce');
		if (!is_object($ak)) {
			$ak = CoreCommerceProductAttributeKey::getByHandle($ak);
		}
		$ak->setAttribute($this, $value);
		$this->reindex();
	}

	public function reindex() {
		$attribs = CoreCommerceProductAttributeKey::getAttributes($this->getProductID(), 'getSearchIndexValue');
		$db = Loader::db();

		$db->Execute('delete from CoreCommerceProductSearchIndexAttributes where productID = ?', array($this->getProductID()));
		$searchableAttributes = array('productID' => $this->getProductID());
		$rs = $db->Execute('select * from CoreCommerceProductSearchIndexAttributes where productID = -1');
		AttributeKey::reindex('CoreCommerceProductSearchIndexAttributes', $searchableAttributes, $attribs, $rs);
	}
	
	public function getProductConfigurableAttributes() {
		Loader::model('attribute/categories/core_commerce_product_option', 'core_commerce');
		return CoreCommerceProductOptionAttributeKey::getList($this);	
	}
	public function getProductConfigurableAttributesRequired() {
		Loader::model('attribute/categories/core_commerce_product_option', 'core_commerce');
		return CoreCommerceProductOptionAttributeKey::getRequiredList($this);	
	}

		
	/** 
	 * Gets the value of the attribute for the user
	 */
	public function getAttribute($ak, $displayMode = false) {
		Loader::model('attribute/categories/core_commerce_product', 'core_commerce');
		if (!is_object($ak)) {
			$ak = CoreCommerceProductAttributeKey::getByHandle($ak);
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
		$v = array($this->getProductID(), $ak->getAttributeKeyID());
		$avID = $db->GetOne("select avID from CoreCommerceProductAttributeValues where productID = ? and akID = ?", $v);
		if ($avID > 0) {
			$av = CoreCommerceProductAttributeValue::getByID($avID);
			if (is_object($av)) {
				$av->setProduct($this);
				$av->setAttributeKey($ak);
			}
		}
		
		if ($createIfNotFound) {
			$cnt = 0;
		
			// Is this avID in use ?
			if (is_object($av)) {
				$cnt = $db->GetOne("select count(avID) from CoreCommerceProductAttributeValues where avID = ?", $av->getAttributeValueID());
			}
			
			if ((!is_object($av)) || ($cnt > 1)) {
				$av = $ak->addAttributeValue();
			}
		}
		
		return $av;
	}
	
	
	public function recordView() {
		$db = Loader::db();
		$db->Replace('CoreCommerceProductStats', array('productID' => $this->getProductID(), 'totalViews' => 'totalViews + 1'), 'productID', false);
		//also record session views
		if (!isset($_SESSION['CoreCommerceProductViews'])) $_SESSION['CoreCommerceProductViews'] = array();
		$_SESSION['CoreCommerceProductViews'][] = $this->getProductID();
	}

	public static function getUserViewedProducts() {
		if (!isset($_SESSION['CoreCommerceProductViews'])) $_SESSION['CoreCommerceProductViews'] = array();
		return $_SESSION['CoreCommerceProductViews'];
	}
	
	
	/**
	 * returns a file object for the image option as it was saved in the db
	 * @param string $option
	 * @return File
	 */
	public function getFileObjectFromImageOption($option) {
		if(!strlen($option) || $option == '-') {
			$f = NULL;
		} else {
			$parts = explode('_',$option);
			if(is_array($parts)) {
				switch($parts[0]) {
					case "prThumbnailImage":
						$f = $this->getProductThumbnailImageObject();
					break;
					case "prAltThumbnailImage":
						$f = $this->getProductAlternateThumbnailImageObject();
						break;
					case "prFullImage":
						$f = $this->getProductFullImageObject();
					break;
					case 'fID':
						$f = File::getByID($parts[1]);
					break;
					case 'akID':
						$ak = CoreCommerceProductAttributeKey::getByID($parts[1]);
						$f =  $this->getAttribute($ak);
					break;
				}
			}
		}		
		return $f;		
	}

}
<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
class ProductBlockController extends BlockController {
		
		public function getBlockTypeDescription() {
			return t("Embeds a product in your website.");
		}

		public function getBlockTypeName() {
			return t("Product");
		}

		protected $btTable = 'btCoreCommerceProduct';
		protected $btInterfaceWidth = "500";
		protected $btInterfaceHeight = "400";
		protected $attributes = array();
		protected $attributesL = array();
		protected $attributesC = array();
		protected $attributesP = array();
		
		public function getAttributes($mode, $fullKeys = true) {
			switch($mode) {
				case 'L':
					$a = $this->attributesL;
					break;
				case 'C':
					$a = $this->attributesC;
					break;
				case 'P':
					$a = $this->attributesP;
					break;				
			}
			if (!$fullKeys) {
				return $a;
			} else {
				Loader::model('attribute/categories/core_commerce_product', 'core_commerce');
				$ak = array();
				foreach($a as $akID) {
					$ak[] = CoreCommerceProductAttributeKey::getByID($akID);
				}
				return $ak;
			}
		}
		
		public function on_page_view() {
			$this->addHeaderItem(Loader::helper('html')->css('ccm.core.commerce.cart.css', 'core_commerce'));
			$this->addHeaderItem(Loader::helper('html')->css('ccm.dialog.css'));
			$this->addHeaderItem(Loader::helper('html')->css('jquery.rating.css'));
			$this->addHeaderItem(Loader::helper('html')->javascript('ccm.dialog.js'));
			$this->addHeaderItem(Loader::helper('html')->javascript('jquery.form.js'));
			$this->addHeaderItem(Loader::helper('html')->javascript('jquery.rating.js'));
			$this->addHeaderItem(Loader::helper('html')->javascript('ccm.core.commerce.cart.js', 'core_commerce'));
			$this->loadDisplayProperties('useOverlays');
			if ($this->useOverlaysL) {
				$h = Loader::helper('concrete/urls');
				$b = $this->getBlockObject();
				if (is_object($b)) {
					$bt = $this->getBlockObject()->getBlockTypeObject();
					if (is_object($bt)) {
						$this->set('lightboxURL', $h->getBlockTypeAssetsURL($bt, 'lightbox/'));
						$this->addHeaderItem(Loader::helper('html')->css($h->getBlockTypeAssetsURL($bt, 'lightbox/jquery.lightbox.css')));
						$this->addHeaderItem(Loader::helper('html')->javascript($h->getBlockTypeAssetsURL($bt, 'lightbox/jquery.lightbox.js')));
					}
				}
			}
			if ($this->useOverlaysC) {
				$this->addHeaderItem(Loader::helper('html')->javascript('jquery.corner.js', 'core_commerce'));
			}
		}
		
		/**
		 * Loads product attributes
		 * @return null
		 */
		protected function loadAttributes() {
			$db = Loader::db();
			$r = $db->Execute("select akID, display from btCoreCommerceDisplayProductAttributes where bID = ?", $this->bID);
			while ($row = $r->FetchRow()) {
				$display = explode('_', $row['display']);
				if (in_array('L', $display)) {
					$this->attributesL[] = $row['akID'];
				}
				if (in_array('C', $display)) {
					$this->attributesC[] = $row['akID'];
				}
				if (in_array('P', $display)) {
					$this->attributesP[] = $row['akID'];
				}
			}
		}
		
		public function on_start() {
			$this->set('prh', Loader::helper('form/product', 'core_commerce'));
			$this->addHeaderItem(Loader::helper('html')->javascript('ccm.core.commerce.search.js', 'core_commerce'));
			$this->addHeaderItem(Loader::helper('html')->css('ccm.core.commerce.search.css', 'core_commerce'));
		}
		
		public function view() {
			$db = Loader::db();
			$this->loadProduct();
			$this->loadAttributes();
			
			$pr = $this->product;			
			if ($pr && (!$this->getCollectionObject() || !$this->getCollectionObject()->isEditMode())) $pr->recordView();
			
			Loader::model('attribute/categories/core_commerce_product', 'core_commerce');
			$this->loadDisplayProperties('displayName');
			$this->loadDisplayProperties('displayDescription');
			$this->loadDisplayProperties('displayDiscount');
			$this->loadDisplayProperties('displayDimensions');
			$this->loadDisplayProperties('displayQuantityInStock');
			$this->loadDisplayProperties('displayPrice');
			$this->loadDisplayProperties('useOverlays');
			$this->set('attributesC', $this->getAttributes('C'));
			$this->set('attributesP', $this->getAttributes('P'));
			$this->set('attributesL', $this->getAttributes('L'));
			
			$this->set('primaryImage', $this->getPrimaryImageFileObject());
			$this->set('primaryHoverImage', $this->getHoverImageFileObject());
			
			$this->set('overlayCalloutImage', $this->getOverlayCalloutImageFileObject());
			// set defaults so if there's no value things don't die
			if($this->overlayCalloutImageMaxWidth) {
				$this->set('overlayCalloutImageMaxWidth', $this->overlayCalloutImageMaxWidth);
			} else { 
				$this->set('overlayCalloutImageMaxWidth', ECOMMERCE_PRODUCT_THUMBNAIL_HEIGHT);
			}
			if($this->overlayCalloutImageMaxHeight) {
				$this->set('overlayCalloutImageMaxHeight', $this->overlayCalloutImageMaxHeight);
			} else { 
				$this->set('overlayLightboxImageMaxHeight', ECOMMERCE_PRODUCT_FULL_HEIGHT);
			}
			if($this->overlayLightboxImageMaxWidth) {
				$this->set('overlayLightboxImageMaxWidth', $this->overlayLightboxImageMaxWidth);
			} else {
				$this->set('overlayLightboxImageMaxWidth', ECOMMERCE_PRODUCT_FULL_WIDTH);
			}		
			if($this->overlayLightboxImageMaxHeight) {
				$this->set('overlayLightboxImageMaxHeight', $this->overlayLightboxImageMaxHeight);
			} else {
				$this->set('overlayLightboxImageMaxHeight', ECOMMERCE_PRODUCT_FULL_HEIGHT);
			}
		}
		
		public function add() {
			$pkg = Package::getByHandle('core_commerce');
			Loader::model('attribute/categories/core_commerce_product', 'core_commerce');
			$this->set('attributes', CoreCommerceProductAttributeKey::getList());
			// set defaults
			$this->set('addToCartText', t('Add to Cart'));
			
			$this->set('imageMaxWidth', ECOMMERCE_PRODUCT_THUMBNAIL_WIDTH);
			$this->set('imageMaxHeight', ECOMMERCE_PRODUCT_THUMBNAIL_HEIGHT);
			$this->set('overlayCalloutImageMaxHeight', ECOMMERCE_PRODUCT_THUMBNAIL_HEIGHT);
			$this->set('overlayCalloutImageMaxWidth', ECOMMERCE_PRODUCT_THUMBNAIL_WIDTH);
			$this->set('overlayLightboxImageMaxHeight', ECOMMERCE_PRODUCT_FULL_HEIGHT);
			$this->set('overlayLightboxImageMaxWidth', ECOMMERCE_PRODUCT_FULL_WIDTH);
			$this->set('inheritProductIDFromCurrentPage', 0);
		}
		
		protected function loadProduct() {
			Loader::model('product/model', 'core_commerce');
			if ($this->productID) {
				$product = CoreCommerceProduct::getByID($this->productID);
			} else if ($this->inheritProductIDFromCurrentPage) {
				$c = Page::getCurrentPage();
				$db = Loader::db();
				$productID = $db->GetOne('select productID from CoreCommerceProducts where cID = ?', array($c->getCollectionID()));
				if ($productID > 0) {
					$product = CoreCommerceProduct::getByID($productID);
				}
			}
			
			if (is_object($product)) {
				$this->product = $product;
				$this->set('product', $product);
			}

		}
		
		public function delete() {
			$db = Loader::db();
			$db->Execute('delete from btCoreCommerceDisplayProductAttributes where bID = ?', $this->bID);
			parent::delete();
		}
		
		public function edit() {
			$this->loadProduct();
			$this->loadAttributes();
			$db = Loader::db();
			Loader::model('attribute/categories/core_commerce_product', 'core_commerce');
			$this->set('attributes', CoreCommerceProductAttributeKey::getList());

			// legacy support
			if(!strlen($this->primaryImage)) {
				$this->primaryImage = "prThumbnailImage";
				$this->set('primaryImage', $this->primaryImage);
			}

			if ($this->primaryHoverImage == '' && $this->displayHoverImage) { // legacy support
				$this->primaryHoverImage = 'prAltThumbnailImage';
				$this->set('primaryHoverImage', $this->primaryHoverImage);
			}

			if ($this->useOverlaysC) {
				if ($this->overlayCalloutImage == '') {
					$this->overlayCalloutImage = 'prFullImage';
					$this->set('overlayCalloutImage', $this->overlayCalloutImage);
				}
			}
			
			$this->loadDisplayProperties('displayName');
			$this->loadDisplayProperties('displayDescription');
			$this->loadDisplayProperties('displayDiscount');
			$this->loadDisplayProperties('displayDimensions');
			$this->loadDisplayProperties('displayQuantityInStock');
			$this->loadDisplayProperties('displayPrice');
			$this->loadDisplayProperties('useOverlays');
			
			$akIDs = $db->GetCol("select akID from btCoreCommerceDisplayProductAttributes where bID = ?", $this->bID);
			$this->set('akIDs', $akIDs);
		}
		
		/* returns the available imaages for the different image display options */
		public function getAvailableImageOptions() {
			
			$values = array( 
				'prFullImage' => t('Full Image'),
				'prThumbnailImage' => t('Thumbnail Image'),
				'prAltThumbnailImage' => t('Alternate Thumbnail')
			);
			
			// add additional product images if available
			if(is_object($this->product)) {
				$images = $this->product->getAdditionalProductImages();
				if(is_array($images) && count($images)) {
					foreach($images as $fi) {
						$values['fID_'.$fi->getFileID()] = $fi->getFileName();					
					}
				}
			}
			
			Loader::model('attribute/categories/core_commerce_product', 'core_commerce');
			$attributes = CoreCommerceProductAttributeKey::getList();
			
			foreach($attributes as $ak) {			
				if($ak->getAttributeKeyType()->getAttributeTypeHandle() == 'image_file') {
					$values['akID_'.$ak->getAttributeKeyID()] = $ak->getAttributeKeyName();
				}
			}
			return $values;
		}

		
		/* 
		 * gets the file object for the primary image
		 * @return File
		 */
		public function getPrimaryImageFileObject() {
			if(is_object($this->product)) {
				if(!strlen($this->primaryImage)) {
					$this->primaryImage = "prThumbnailImage";
				}
				return $this->product->getFileObjectFromImageOption($this->primaryImage);
			}
		}
		
		/**
		 * prints the html for the primary image with width & height as specified
		 * @return void
		 */
		public function outputPrimaryImage() {
			$f = $this->getPrimaryImageFileObject();
			if(is_object($f)) {
				$im = Loader::helper('image');
				$im->outputThumbnail($f, $this->imageMaxWidth, $this->imageMaxHeight);
			}
		}
		
		/* 
		 * gets the file object for the primary hover image
		 * @return File
		*/
		public function getHoverImageFileObject() {
			if(is_object($this->product)) { 
				return $this->product->getFileObjectFromImageOption($this->primaryHoverImage);
			}
		}
		
		/**
		 * prints the html for the primary image with width & height as specified
		 * not really that useful as you'd just need the src to hover
		 * @return void
		*/
		public function outputHoverImage() {
			$f = $this->getHoverImageFileObject();
			if(is_object($f)) {
				$im = Loader::helper('image');
				$im->outputThumbnail($f, $this->imageMaxWidth, $this->imageMaxHeight);
			}
		}
		
		/* 
		 * gets the file object for the primary hover image
		 * @return File
		*/
		public function getOverlayCalloutImageFileObject() {
			if(is_object($this->product)) {
				return $this->product->getFileObjectFromImageOption($this->overlayCalloutImage);
			}
		}
		
		/**
		 * prints the html for the overlay callout image with width & height as specified
		 * @return void
		*/
		public function outputOverlayCalloutImage() {
			$f = $this->getOverlayCalloutImageFileObject();
			if(is_object($f)) {
				$im = Loader::helper('image');
				$im->outputThumbnail($f, $this->overlayCalloutImageMaxWidth, $this->overlayCalloutImageMaxHeight);
			}
		}
		
		
		protected function loadDisplayProperties($col) {
			$ca = explode('_', $this->$col);
			if (is_array($ca)) {
				foreach($ca as $i) {
					$this->set($col . $i, true);
					$var = $col . $i;
					$this->$var = true;
				}
			}
		}

		protected function processDisplay($val, $arr) {
			if (is_array($arr)) {
				$val = $arr[$val];
			}
			if (is_array($val) && count($val) > 0) {
				return implode('_', $val);
			} else {
				return '';
			}
		}
		
		public function save($data) {
			$data['displayName'] = $this->processDisplay('displayName', $data);
			$data['displayDescription'] = $this->processDisplay('displayDescription', $data);
			$data['displayDiscount'] = $this->processDisplay('displayDiscount', $data);
			$data['displayDimensions'] = $this->processDisplay('displayDimensions', $data);
			$data['displayQuantityInStock'] = $this->processDisplay('displayQuantityInStock', $data);
			$data['displayPrice'] = $this->processDisplay('displayPrice', $data);
			$data['displayImage'] = ($data['displayImage'] == '') ? 0 : 1;
			$data['displayQuantity'] = ($data['displayQuantity'] == '') ? 0 : 1;
			$num = Loader::helper('validation/numbers');
			
			if (!$data['displayLinkToFullPage']) {
				$data['displayLinkToFullPage'] = 0;
			}

			if (!$data['displayAddToCart']) {
				$data['displayAddToCart'] = 0;
			}
			
			if (!$data['inheritProductIDFromCurrentPage']) {
				$data['inheritProductIDFromCurrentPage'] = 0;
			} else {
				$data['inheritProductIDFromCurrentPage'] = 1;
				$data['productID'] = 0;
			}
			
			if ($data['displayImage'] == 0) { // no primary image
				$data['imageMaxWidth'] = 0;
				$data['imageMaxHeight'] = 0;
				$data['primaryImageFID'] = 0;
			}
			
			// validate the width/height of images for type
			$image_dimentions = array(
				'imageMaxWidth', 'imageMaxHeight', 'overlayCalloutImageMaxHeight', 'overlayCalloutImageMaxWidth',
				'overlayLightboxImageMaxHeight', 'overlayLightboxImageMaxWidth');
			foreach($image_dimentions as $k) {
				if (!$num->integer($data[$k])) { 
					$data[$k] = 0; //set non-numeric values to 0
				}
			}
			
			
			if ($data['displayImage'] == 1 && ($data['imageMaxWidth'] == 0 || $data['imageMaxHeight'] == 0)) {
				$data['imageMaxWidth'] = ECOMMERCE_PRODUCT_THUMBNAIL_WIDTH;
				$data['imageMaxHeight'] = ECOMMERCE_PRODUCT_THUMBNAIL_HEIGHT;
			}
			
			if(strlen($data['primaryHoverImage']) && $data['primaryHoverImage'] != '-') {
				$data['displayHoverImage'] = 1;
			} else {
				$data['displayHoverImage'] = 0;
			}
			
			
			if($data['useOverlaysL'] != '' && ($data['overlayLightboxImageMaxWidth'] == 0 || $data['overlayLightboxImageMaxHeight']==0)) {
				$data['overlayLightboxImageMaxWidth'] = ECOMMERCE_PRODUCT_THUMBNAIL_WIDTH;
				$data['overlayLightboxImageMaxHeight'] = ECOMMERCE_PRODUCT_THUMBNAIL_HEIGHT;
			}
			if($data['useOverlaysC'] != '' && ($data['overlayCalloutImageMaxWidth'] == 0 || $data['overlayCalloutImageMaxHeight']==0)) {
				$data['overlayCalloutImageMaxWidth'] = ECOMMERCE_PRODUCT_THUMBNAIL_WIDTH;
				$data['overlayCalloutImageMaxHeight'] = ECOMMERCE_PRODUCT_THUMBNAIL_HEIGHT;			
			}
			
			if ($data['addToCartText'] == '') {
				$data['addToCartText'] = t('Add to Cart');
			}
			
			if (!isset($data['useOverlays']) && !is_array($data['useOverlays'])) {
				$data['useOverlays'] = array($data['useOverlaysL'], $data['useOverlaysC']);// mimic the old post val
			}
			
			$data['useOverlays'] = $this->processDisplay('useOverlays', $data);			
			parent::save($data);
			
			$db = Loader::db();
			$db->Execute('delete from btCoreCommerceDisplayProductAttributes where bID = ?', $this->bID);

			if (is_array($data['displayAKID'])) {
				foreach($data['displayAKID'] as $akID => $akColumn) {
					$display = $this->processDisplay($akColumn, false);
					$db->Execute('insert into btCoreCommerceDisplayProductAttributes (bID, akID, display) values (?, ?, ?)', array($this->bID, $akID, $display));
				}
			}
		}
	}
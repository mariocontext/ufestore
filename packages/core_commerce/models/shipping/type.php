<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
class CoreCommerceShippingType extends Object {

	public function getShippingTypeID() {return $this->shippingTypeID;}
	public function getShippingTypeHandle() {return $this->shippingTypeHandle;}
	public function getShippingTypeName() {return $this->shippingTypeName;}
	public function isShippingTypeEnabled() {return $this->shippingTypeIsEnabled;}
	public function hasShippingTypeCustomCountries() {return $this->shippingTypeHasCustomCountries;}

	public function getController() {return $this->controller;}
	
	public static function getByID($shippingTypeID) {
		$db = Loader::db();
		$row = $db->GetRow('select shippingTypeID, shippingTypeHandle, shippingTypeName, shippingTypeIsEnabled, shippingTypeHasCustomCountries, pkgID from CoreCommerceShippingTypes where shippingTypeID = ?', array($shippingTypeID));
		$at = new CoreCommerceShippingType();
		$at->setPropertiesFromArray($row);
		$at->loadController();
		return $at;
	}

	public static function getByHandle($shippingTypeHandle) {
		$db = Loader::db();
		$row = $db->GetRow('select shippingTypeID, shippingTypeHandle, shippingTypeName, shippingTypeIsEnabled, shippingTypeHasCustomCountries, pkgID from CoreCommerceShippingTypes where shippingTypeHandle = ?', array($shippingTypeHandle));
		$at = new CoreCommerceShippingType();
		$at->setPropertiesFromArray($row);
		$at->loadController();
		return $at;
	}
	
	public function __destruct() {
		unset($this->controller);
	}
	
	public function getEnabledList() {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select shippingTypeID from CoreCommerceShippingTypes where shippingTypeIsEnabled = 1 order by shippingTypeID asc');
		
		while ($row = $r->FetchRow()) {
			$list[] = CoreCommerceShippingType::getByID($row['shippingTypeID']);
		}
		$r->Close();
		return $list;
	}
	
	public static function getList() {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select shippingTypeID from CoreCommerceShippingTypes order by shippingTypeID asc');
		
		while ($row = $r->FetchRow()) {
			$list[] = CoreCommerceShippingType::getByID($row['shippingTypeID']);
		}
		$r->Close();
		return $list;
	}
	
	
	public static function add($handle, $name, $enabled = 0, $pkg = false) {
		$pkgID = 0;
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}
		$db = Loader::db();
		$db->Execute('insert into CoreCommerceShippingTypes (shippingTypeHandle, shippingTypeName, shippingTypeIsEnabled, pkgID) values (?, ?, ?, ?)', array($handle, $name, $enabled, $pkgID));
		$id = $db->Insert_ID();
		$est = CoreCommerceShippingType::getByID($id);
	
		$path = $est->getShippingTypeFilePath(FILENAME_ECOMMERCE_SHIPPING_DB);
		if ($path) {
			Package::installDB($path);
		}
	}

	public static function delete($handle) {
		$db = Loader::db();
		$db->Execute('delete from CoreCommerceShippingTypes where shippingTypeHandle=?', array($handle));
	}

	public function update($args) {

		extract($args);

		if (!$shippingTypeIsEnabled) {
			$shippingTypeIsEnabled = 0;
		}
		
		if (!$shippingTypeHasCustomCountries) {
			$shippingTypeHasCustomCountries = 0;
		}
		
				
		$db = Loader::db();

		$a = array($shippingTypeIsEnabled, $shippingTypeHasCustomCountries, $this->getShippingTypeID());
		$r = $db->query("update CoreCommerceShippingTypes set shippingTypeIsEnabled = ?, shippingTypeHasCustomCountries = ? where shippingTypeID = ?", $a);
		
		if ($r) {
			$t = CoreCommerceShippingType::getByID($this->getShippingTypeID());
			$t->setShippingTypeCustomCountries($args['shippingTypeHasCustomCountriesSelected']);
			$t->getController()->save();
			return $t;
		}
	}
	
	public function canShipToShippingAddress($order) {
		$shippingAddress = $order->getAttribute('shipping_address');
		if (!$shippingAddress) $shippingAddress = $order->getAttribute('billing_address');
		if (is_object($shippingAddress) && $this->hasShippingTypeCustomCountries()) {
			$countries = $this->getShippingTypeCustomCountries();
			if (is_array($countries) && (in_array($shippingAddress->getCountry(), $countries))) {
				return true;
			}
		} else {
			return true;
		}		
		return false;
	}
	
	public function getShippingTypeCustomCountries() {
		$db = Loader::db();
		$col = $db->GetCol('select country from CoreCommerceShippingTypeCustomCountries where shippingTypeID = ?', array($this->getShippingTypeID()));
		return $col;
	}
	
	public function setShippingTypeCustomCountries($countries) {
		$db = Loader::db();
		$db->Execute('delete from CoreCommerceShippingTypeCustomCountries where shippingTypeID = ?', array($this->getShippingTypeID()));
		if ($this->shippingTypeHasCustomCountries) {
			if (is_array($countries)) {
				foreach($countries as $cnt) {
					$db->Execute('insert into CoreCommerceShippingTypeCustomCountries (shippingTypeID, country) values (?, ?)', array($this->getShippingTypeID(), $cnt));
				}
			}
		}
	}

	public function getPackageID() { return $this->pkgID;}
	public function getPackageHandle() {
		return PackageList::getHandle($this->pkgID);
	}
	
	public function getShippingTypeFilePath($_file) {
		$f = $this->mapShippingTypeFilePath($_file);
		if (is_object($f)) {
			return $f->file;
		}
	}

	public function getShippingTypeFileURL($_file) {
		$f = $this->mapShippingTypeFilePath($_file);
		if (is_object($f)) {
			return $f->url;
		}
	}
	
	public function render($view) {
		$js = $this->getShippingTypeFileURL($view . '.js');
		$css = $this->getShippingTypeFileURL($view . '.css');
		
		$html = Loader::helper('html');
		if ($js != false) { 
			$this->controller->addHeaderItem($html->javascript($js));
		}
		if ($css != false) { 
			$this->controller->addHeaderItem($html->css($css));
		}

		$this->controller->setupAndRun($view);
		extract($this->controller->getSets());
		extract($this->controller->getHelperObjects());
		
		$file = $this->getShippingTypeFilePath($view . '.php');
		if ($file) {
			include($file);
		}
	}
	
	protected function mapShippingTypeFilePath($_file) {
		$shippingTypeHandle = $this->shippingTypeHandle;
		if (file_exists(DIR_MODELS . '/' . DIRNAME_ECOMMERCE_LOCAL . '/' . DIRNAME_ECOMMERCE_SHIPPING . '/' . DIRNAME_ECOMMERCE_SHIPPING_TYPES . '/' . $shippingTypeHandle . '/' . $_file)) {
			$file = DIR_MODELS . '/' . DIRNAME_ECOMMERCE_LOCAL . '/' . DIRNAME_ECOMMERCE_SHIPPING . '/' .  DIRNAME_ECOMMERCE_SHIPPING_TYPES . '/' . $shippingTypeHandle . '/' . $_file;
			$url = BASE_URL . DIR_REL . '/' . DIRNAME_MODELS . '/' . DIRNAME_ECOMMERCE_LOCAL . '/' . DIRNAME_ECOMMERCE_SHIPPING . '/' .  DIRNAME_ECOMMERCE_SHIPPING_TYPES . '/' . $shippingTypeHandle . '/' . $_file;
		}
		
		$pkgID = $this->pkgID;
		if (!isset($file) && $pkgID > 0) {
			$pkgHandle = PackageList::getHandle($pkgID);
			$dirp = is_dir(DIR_PACKAGES . '/' . $pkgHandle) ? DIR_PACKAGES . '/' . $pkgHandle : DIR_PACKAGES_CORE . '/' . $pkgHandle;
			if (file_exists($dirp . '/' . DIRNAME_MODELS . '/' . DIRNAME_ECOMMERCE_LOCAL . '/' . DIRNAME_ECOMMERCE_SHIPPING . '/' . DIRNAME_ECOMMERCE_SHIPPING_TYPES . '/' . $shippingTypeHandle . '/' . $_file)) {
				$file = $dirp . '/' . DIRNAME_MODELS . '/' . DIRNAME_ECOMMERCE_LOCAL . '/' . DIRNAME_ECOMMERCE_SHIPPING . '/' . DIRNAME_ECOMMERCE_SHIPPING_TYPES . '/' . $shippingTypeHandle . '/' . $_file;
				$url = BASE_URL . DIR_REL . '/' . $pkgHandle . '/' . DIRNAME_MODELS . '/' . DIRNAME_ECOMMERCE_LOCAL . '/' . DIRNAME_ECOMMERCE_SHIPPING . '/' . DIRNAME_ECOMMERCE_SHIPPING_TYPES . '/' .  $shippingTypeHandle . '/' . $_file;
			}
		}
		
		if (!isset($file)) {
			$pkg = Package::getByHandle('core_commerce');
			$ph = Loader::helper('concrete/urls');
			if (file_exists($pkg->getPackagePath() . '/' . DIRNAME_MODELS . '/' . DIRNAME_ECOMMERCE_SHIPPING . '/' . DIRNAME_ECOMMERCE_SHIPPING_TYPES . '/' . $shippingTypeHandle . '/' . $_file)) {
				$file = $pkg->getPackagePath() . '/' . DIRNAME_MODELS . '/' . DIRNAME_ECOMMERCE_SHIPPING . '/' . DIRNAME_ECOMMERCE_SHIPPING_TYPES . '/' . $shippingTypeHandle . '/' . $_file;
				$url = $ph->getPackageURL($pkg) . '/' . DIRNAME_MODELS . '/' . DIRNAME_ECOMMERCE_SHIPPING . '/' . DIRNAME_ECOMMERCE_SHIPPING_TYPES . '/' . $shippingTypeHandle . '/' . $_file;
			}
		}
		
		if (isset($file)) {
			$obj = new stdClass;
			$obj->file = $file;
			$obj->url = $url;
			return $obj;
		} else {
			return false;
		}
	}
	
	protected function loadController() {
		// local scope
		$shippingTypeHandle = $this->shippingTypeHandle;
		$txt = Loader::helper('text');
		$className = 'CoreCommerce' . $txt->camelcase($this->shippingTypeHandle) . 'ShippingTypeController';
		$file = $this->getShippingTypeFilePath(FILENAME_ECOMMERCE_SHIPPING_CONTROLLER);
		require_once($file);
		$this->controller = new $className($this);
		$this->controller->setShippingType($this);
	}
	
}

class CoreCommercePendingShippingType extends CoreCommerceShippingType {

	public static function getList() {
		$db = Loader::db();
		$shippingTypeHandles = $db->GetCol("select shippingTypeHandle from CoreCommerceShippingTypes");
		
		$dh = Loader::helper('file');
		$available = array();
		if (is_dir(DIR_MODELS . '/' . DIRNAME_ECOMMERCE_LOCAL . '/' . DIRNAME_ECOMMERCE_SHIPPING . '/' . DIRNAME_ECOMMERCE_SHIPPING_TYPES)) {
			$contents = $dh->getDirectoryContents(DIR_MODELS . '/' . DIRNAME_ECOMMERCE_LOCAL . '/' . DIRNAME_ECOMMERCE_SHIPPING . '/' . DIRNAME_ECOMMERCE_SHIPPING_TYPES );
			foreach($contents as $shippingTypeHandle) {
				if (!in_array($shippingTypeHandle, $shippingTypeHandles)) {
					$available[] = CoreCommercePendingShippingType::getByHandle($shippingTypeHandle);
				}
			}
		}
		return $available;
	}

	public static function getByHandle($shippingTypeHandle) {
		$th = Loader::helper('text');
		if (file_exists(DIR_MODELS . '/' . DIRNAME_ECOMMERCE_LOCAL . '/' . DIRNAME_ECOMMERCE_SHIPPING . '/' . DIRNAME_ECOMMERCE_SHIPPING_TYPES . '/' . $shippingTypeHandle)) {
			$at = new CoreCommercePendingShippingType();
			$at->shippingTypeID = 0;
			$at->shippingTypeHandle = $shippingTypeHandle;
			$at->shippingTypeName = $th->unhandle($shippingTypeHandle);
			return $at;
		}
	}
	
	public function install() {
		$at = parent::add($this->shippingTypeHandle, $this->shippingTypeName);
	}

}

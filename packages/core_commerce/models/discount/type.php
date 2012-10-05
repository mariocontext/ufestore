<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
class CoreCommerceDiscountType extends Object {

	public function getDiscountTypeID() {return $this->discountTypeID;}
	public function getDiscountTypeHandle() {return $this->discountTypeHandle;}
	public function getDiscountTypeName() {return $this->discountTypeName;}
	public function getController() {return $this->controller;}
	
	public static function getByID($discountTypeID) {
		$db = Loader::db();
		$row = $db->GetRow('select discountTypeID, discountTypeHandle, discountTypeName, pkgID from CoreCommerceDiscountTypes where discountTypeID = ?', array($discountTypeID));
		$at = new CoreCommerceDiscountType();
		$at->setPropertiesFromArray($row);
		$at->loadController();
		return $at;
	}

	public static function getByHandle($discountTypeHandle) {
		$db = Loader::db();
		$row = $db->GetRow('select discountTypeID, discountTypeHandle, discountTypeName, pkgID from CoreCommerceDiscountTypes where discountTypeHandle = ?', array($discountTypeHandle));
		$at = new CoreCommerceDiscountType();
		$at->setPropertiesFromArray($row);
		$at->loadController();
		return $at;
	}
	
	public function __destruct() {
		unset($this->controller);
	}
	
	public static function getList() {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select discountTypeID from CoreCommerceDiscountTypes order by discountTypeID asc');
		
		while ($row = $r->FetchRow()) {
			$list[] = CoreCommerceDiscountType::getByID($row['discountTypeID']);
		}
		$r->Close();
		return $list;
	}
	
	
	public static function add($handle, $name, $pkg = false) {
		$pkgID = 0;
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}
		$db = Loader::db();
		$db->Execute('insert into CoreCommerceDiscountTypes (discountTypeHandle, discountTypeName) values (?, ?)', array($handle, $name));
		$id = $db->Insert_ID();
		$est = CoreCommerceDiscountType::getByID($id);
	
		$path = $est->getDiscountTypeFilePath(FILENAME_ECOMMERCE_DISCOUNT_DB);
		if ($path) {
			Package::installDB($path);
		}
	}

	public function getPackageID() { return $this->pkgID;}
	public function getPackageHandle() {
		return PackageList::getHandle($this->pkgID);
	}
	
	public function getDiscountTypeFilePath($_file) {
		$f = $this->mapDiscountTypeFilePath($_file);
		if (is_object($f)) {
			return $f->file;
		}
	}

	public function getDiscountTypeFileURL($_file) {
		$f = $this->mapDiscountTypeFilePath($_file);
		if (is_object($f)) {
			return $f->url;
		}
	}
	
	public function render($view, $discount = false) {
		$js = $this->getDiscountTypeFileURL($view . '.js');
		$css = $this->getDiscountTypeFileURL($view . '.css');
		if (is_object($discount)) {
			$this->controller->setDiscount($discount);
		}
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
		
		$file = $this->getDiscountTypeFilePath('type_form.php');
		if ($file) {
			include($file);
		}
	}
	
	protected function mapDiscountTypeFilePath($_file) {
		$discountTypeHandle = $this->discountTypeHandle;
		if (file_exists(DIR_MODELS . '/' . DIRNAME_ECOMMERCE_LOCAL . '/' . DIRNAME_ECOMMERCE_DISCOUNT . '/' . DIRNAME_ECOMMERCE_DISCOUNT_TYPES . '/' . $discountTypeHandle . '/' . $_file)) {
			$file = DIR_MODELS . '/' . DIRNAME_ECOMMERCE_LOCAL . '/' . DIRNAME_ECOMMERCE_DISCOUNT . '/' .  DIRNAME_ECOMMERCE_DISCOUNT_TYPES . '/' . $discountTypeHandle . '/' . $_file;
			$url = BASE_URL . DIR_REL . '/' . DIRNAME_MODELS . '/' . DIRNAME_ECOMMERCE_LOCAL . '/' . DIRNAME_ECOMMERCE_DISCOUNT . '/' .  DIRNAME_ECOMMERCE_DISCOUNT_TYPES . '/' . $discountTypeHandle . '/' . $_file;
		}
		
		$pkgID = $this->pkgID;
		if (!isset($file) && $pkgID > 0) {
			$pkgHandle = PackageList::getHandle($pkgID);
			$dirp = is_dir(DIR_PACKAGES . '/' . $pkgHandle) ? DIR_PACKAGES . '/' . $pkgHandle : DIR_PACKAGES_CORE . '/' . $pkgHandle;
			if (file_exists($dirp . '/' . DIRNAME_MODELS . '/' . DIRNAME_ECOMMERCE_LOCAL . '/' . DIRNAME_ECOMMERCE_DISCOUNT . '/' . DIRNAME_ECOMMERCE_DISCOUNT_TYPES . '/' . $discountTypeHandle . '/' . $_file)) {
				$file = $dirp . '/' . DIRNAME_MODELS . '/' . DIRNAME_ECOMMERCE_LOCAL . '/' . DIRNAME_ECOMMERCE_DISCOUNT . '/' . DIRNAME_ECOMMERCE_DISCOUNT_TYPES . '/' . $discountTypeHandle . '/' . $_file;
				$url = BASE_URL . DIR_REL . '/' . $pkgHandle . '/' . DIRNAME_MODELS . '/' . DIRNAME_ECOMMERCE_LOCAL . '/' . DIRNAME_ECOMMERCE_DISCOUNT . '/' . DIRNAME_ECOMMERCE_DISCOUNT_TYPES . '/' .  $discountTypeHandle . '/' . $_file;
			}
		}
		
		if (!isset($file)) {
			$pkg = Package::getByHandle('core_commerce');
			$ph = Loader::helper('concrete/urls');
			if (file_exists($pkg->getPackagePath() . '/' . DIRNAME_MODELS . '/' . DIRNAME_ECOMMERCE_DISCOUNT . '/' . DIRNAME_ECOMMERCE_DISCOUNT_TYPES . '/' . $discountTypeHandle . '/' . $_file)) {
				$file = $pkg->getPackagePath() . '/' . DIRNAME_MODELS . '/' . DIRNAME_ECOMMERCE_DISCOUNT . '/' . DIRNAME_ECOMMERCE_DISCOUNT_TYPES . '/' . $discountTypeHandle . '/' . $_file;
				$url = $ph->getPackageURL($pkg) . '/' . DIRNAME_MODELS . '/' . DIRNAME_ECOMMERCE_DISCOUNT . '/' . DIRNAME_ECOMMERCE_DISCOUNT_TYPES . '/' . $discountTypeHandle . '/' . $_file;
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
		$discountTypeHandle = $this->discountTypeHandle;
		$txt = Loader::helper('text');
		$className = 'CoreCommerce' . $txt->camelcase($this->discountTypeHandle) . 'DiscountTypeController';
		$file = $this->getDiscountTypeFilePath(FILENAME_ECOMMERCE_DISCOUNT_CONTROLLER);
		require_once($file);
		$this->controller = new $className($this);
		$this->controller->setDiscountType($this);
	}
	
}

class CoreCommercePendingDiscountType extends CoreCommerceDiscountType {

	public static function getList() {
		$db = Loader::db();
		$discountTypeHandles = $db->GetCol("select discountTypeHandle from CoreCommerceDiscountTypes");
		
		$dh = Loader::helper('file');
		$available = array();
		if (is_dir(DIR_MODELS . '/' . DIRNAME_ECOMMERCE_LOCAL . '/' . DIRNAME_ECOMMERCE_DISCOUNT . '/' . DIRNAME_ECOMMERCE_DISCOUNT_TYPES)) {
			$contents = $dh->getDirectoryContents(DIR_MODELS . '/' . DIRNAME_ECOMMERCE_LOCAL . '/' . DIRNAME_ECOMMERCE_DISCOUNT . '/' . DIRNAME_ECOMMERCE_DISCOUNT_TYPES );
			foreach($contents as $discountTypeHandle) {
				if (!in_array($discountTypeHandle, $discountTypeHandles)) {
					$available[] = CoreCommercePendingDiscountType::getByHandle($discountTypeHandle);
				}
			}
		}
		return $available;
	}

	public static function getByHandle($discountTypeHandle) {
		$th = Loader::helper('text');
		if (file_exists(DIR_MODELS . '/' . DIRNAME_ECOMMERCE_LOCAL . '/' . DIRNAME_ECOMMERCE_DISCOUNT . '/' . DIRNAME_ECOMMERCE_DISCOUNT_TYPES . '/' . $discountTypeHandle)) {
			$at = new CoreCommercePendingDiscountType();
			$at->discountTypeID = 0;
			$at->discountTypeHandle = $discountTypeHandle;
			$at->discountTypeName = $th->unhandle($discountTypeHandle);
			return $at;
		}
	}
	
	public function install() {
		$at = parent::add($this->discountTypeHandle, $this->discountTypeName);
	}

}
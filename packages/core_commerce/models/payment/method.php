<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
class CoreCommercePaymentMethod extends Object {

	public function getPaymentMethodID() {return $this->paymentMethodID;}
	public function getPaymentMethodHandle() {return $this->paymentMethodHandle;}
	public function getPaymentMethodName() {return $this->paymentMethodName;}
	public function isPaymentMethodEnabled() {return $this->paymentMethodIsEnabled;}
	public function getController() {return $this->controller;}
	
	public static function getByID($paymentMethodID) {
		if (!$paymentMethodID) {
			return null;
		}

		$db = Loader::db();
		$row = $db->GetRow('select paymentMethodID, paymentMethodHandle, paymentMethodName, paymentMethodIsEnabled, pkgID from CoreCommercePaymentMethods where paymentMethodID = ?', array($paymentMethodID));
		$at = new CoreCommercePaymentMethod();
		$at->setPropertiesFromArray($row);
		if ($at->loadController()) {
			return $at;
		} else {
			return null;
		}
	}

	public static function getByHandle($paymentMethodHandle) {
		$db = Loader::db();
		$row = $db->GetRow('select paymentMethodID, paymentMethodHandle, paymentMethodName, paymentMethodIsEnabled, pkgID from CoreCommercePaymentMethods where paymentMethodHandle = ?', array($paymentMethodHandle));
		$at = new CoreCommercePaymentMethod();
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
		$r = $db->Execute('select paymentMethodID from CoreCommercePaymentMethods where paymentMethodIsEnabled = 1 order by paymentMethodID asc');
		
		while ($row = $r->FetchRow()) {
			$list[] = CoreCommercePaymentMethod::getByID($row['paymentMethodID']);
		}
		$r->Close();
		return $list;
	}
	
	public static function getList() {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select paymentMethodID from CoreCommercePaymentMethods order by paymentMethodID asc');
		
		while ($row = $r->FetchRow()) {
			$list[] = CoreCommercePaymentMethod::getByID($row['paymentMethodID']);
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
		$db->Execute('insert into CoreCommercePaymentMethods (paymentMethodHandle, paymentMethodName, paymentMethodIsEnabled, pkgID) values (?, ?, ?, ?)', array($handle, $name, $enabled, $pkgID));
		$id = $db->Insert_ID();
		$est = CoreCommercePaymentMethod::getByID($id);
	
		$path = $est->getPaymentMethodFilePath(FILENAME_ECOMMERCE_PAYMENT_DB);
		if ($path) {
			Package::installDB($path);
		}
	}

	public function update($args) {

		extract($args);

		if (!$paymentMethodIsEnabled) {
			$paymentMethodIsEnabled = 0;
		}
		$db = Loader::db();

		$a = array($paymentMethodIsEnabled, $this->getPaymentMethodID());
		$r = $db->query("update CoreCommercePaymentMethods set paymentMethodIsEnabled = ? where paymentMethodID = ?", $a);
		
		if ($r) {
			$t = CoreCommercePaymentMethod::getByID($this->getPaymentMethodID());
			$t->getController()->save();
			return $t;
		}
	}

	public function getPackageID() { return $this->pkgID;}
	public function getPackageHandle() {
		return PackageList::getHandle($this->pkgID);
	}
	
	public function getPaymentMethodFilePath($_file) {
		$f = $this->mapPaymentMethodFilePath($_file);
		if (is_object($f)) {
			return $f->file;
		}
	}

	public function getPaymentMethodFileURL($_file) {
		$f = $this->mapPaymentMethodFilePath($_file);
		if (is_object($f)) {
			return $f->url;
		}
	}
	
	public function render($view) {
		$js = $this->getPaymentMethodFileURL($view . '.js');
		$css = $this->getPaymentMethodFileURL($view . '.css');
		
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
		
		$file = $this->getPaymentMethodFilePath($view . '.php');
		if ($file) {
			include($file);
		}
	}
	
	protected function mapPaymentMethodFilePath($_file) {
		$paymentMethodHandle = $this->paymentMethodHandle;
		if (file_exists(DIR_MODELS . '/' . DIRNAME_ECOMMERCE_LOCAL . '/' . DIRNAME_ECOMMERCE_PAYMENT . '/' . DIRNAME_ECOMMERCE_PAYMENT_METHODS . '/' . $paymentMethodHandle . '/' . $_file)) {
			$file = DIR_MODELS . '/' . DIRNAME_ECOMMERCE_LOCAL . '/' . DIRNAME_ECOMMERCE_PAYMENT . '/' .  DIRNAME_ECOMMERCE_PAYMENT_METHODS . '/' . $paymentMethodHandle . '/' . $_file;
			$url = BASE_URL . DIR_REL . '/' . DIRNAME_MODELS . '/' . DIRNAME_ECOMMERCE_LOCAL . '/' . DIRNAME_ECOMMERCE_PAYMENT . '/' .  DIRNAME_ECOMMERCE_PAYMENT_METHODS . '/' . $paymentMethodHandle . '/' . $_file;
		}
		
		$pkgID = $this->pkgID;
		if (!isset($file) && $pkgID > 0) {
			$pkgHandle = PackageList::getHandle($pkgID);
			$dirp = is_dir(DIR_PACKAGES . '/' . $pkgHandle) ? DIR_PACKAGES . '/' . $pkgHandle : DIR_PACKAGES_CORE . '/' . $pkgHandle;
			if (file_exists($dirp . '/' . DIRNAME_MODELS . '/' . DIRNAME_ECOMMERCE_LOCAL . '/' . DIRNAME_ECOMMERCE_PAYMENT . '/' . DIRNAME_ECOMMERCE_PAYMENT_METHODS . '/' . $paymentMethodHandle . '/' . $_file)) {
				$file = $dirp . '/' . DIRNAME_MODELS . '/' . DIRNAME_ECOMMERCE_LOCAL . '/' . DIRNAME_ECOMMERCE_PAYMENT . '/' . DIRNAME_ECOMMERCE_PAYMENT_METHODS . '/' . $paymentMethodHandle . '/' . $_file;
				$url = BASE_URL . DIR_REL . '/' . DIRNAME_MODELS . '/' . DIRNAME_ECOMMERCE_LOCAL . '/' . DIRNAME_ECOMMERCE_PAYMENT . '/' . DIRNAME_ECOMMERCE_PAYMENT_METHODS . '/' .  $paymentMethodHandle . '/' . $_file;
			}
		}
		
		if (!isset($file)) {
			$pkg = Package::getByHandle('core_commerce');
			$ph = Loader::helper('concrete/urls');
			if (file_exists($pkg->getPackagePath() . '/' . DIRNAME_MODELS . '/' . DIRNAME_ECOMMERCE_PAYMENT . '/' . DIRNAME_ECOMMERCE_PAYMENT_METHODS . '/' . $paymentMethodHandle . '/' . $_file)) {
				$file = $pkg->getPackagePath() . '/' . DIRNAME_MODELS . '/' . DIRNAME_ECOMMERCE_PAYMENT . '/' . DIRNAME_ECOMMERCE_PAYMENT_METHODS . '/' . $paymentMethodHandle . '/' . $_file;
				$url = $ph->getPackageURL($pkg) . '/' . DIRNAME_MODELS . '/' . DIRNAME_ECOMMERCE_PAYMENT . '/' . DIRNAME_ECOMMERCE_PAYMENT_METHODS . '/' . $paymentMethodHandle . '/' . $_file;
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
		if (empty($this->paymentMethodHandle)) {
			return false;
		}

		// local scope
		$paymentMethodHandle = $this->paymentMethodHandle;
		$txt = Loader::helper('text');
		$className = 'CoreCommerce' . $txt->camelcase($this->paymentMethodHandle) . 'PaymentMethodController';
		$file = $this->getPaymentMethodFilePath(FILENAME_ECOMMERCE_PAYMENT_CONTROLLER);
		
		require_once($file);
		$this->controller = new $className($this);
		$this->controller->setPaymentMethod($this);
		return true;
	}
	
}

class CoreCommercePendingPaymentMethod extends CoreCommercePaymentMethod {

	public static function getList() {
		$db = Loader::db();
		$paymentMethodHandles = $db->GetCol("select paymentMethodHandle from CoreCommercePaymentMethods");
		
		$dh = Loader::helper('file');
		$available = array();
		$dir = DIR_MODELS . '/' . DIRNAME_ECOMMERCE_LOCAL . '/' . DIRNAME_ECOMMERCE_PAYMENT . '/' . DIRNAME_ECOMMERCE_PAYMENT_METHODS;
		if (is_dir($dir)) {
			$contents = $dh->getDirectoryContents($dir);
			foreach($contents as $paymentMethodHandle) {
				if (!in_array($paymentMethodHandle, $paymentMethodHandles)) {
					$available[] = CoreCommercePendingPaymentMethod::getByHandle($paymentMethodHandle);
				}
			}
		}
		return $available;
	}

	public static function getByHandle($paymentMethodHandle) {
		$th = Loader::helper('text');
		if (file_exists(DIR_MODELS . '/' . DIRNAME_ECOMMERCE_LOCAL . '/' . DIRNAME_ECOMMERCE_PAYMENT . '/' . DIRNAME_ECOMMERCE_PAYMENT_METHODS . '/' . $paymentMethodHandle)) {
			$at = new CoreCommercePendingPaymentMethod();
			$at->paymentMethodID = 0;
			$at->paymentMethodHandle = $paymentMethodHandle;
			$at->paymentMethodName = $th->unhandle($paymentMethodHandle);
			return $at;
		}
	}
	
	public function install() {
		$at = parent::add($this->paymentMethodHandle, $this->paymentMethodName);
	}

}

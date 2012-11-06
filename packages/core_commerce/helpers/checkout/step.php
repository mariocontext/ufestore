<?php  
	defined('C5_EXECUTE') or die(_("Access Denied."));
	class CoreCommerceCheckoutStepHelper {

		protected static $steps = array();
		public $testStep = true;
		protected $cPath = '';
		
		public function __construct() {
			$c = Page::getCurrentPage();
			if(is_object($c)) {
				$this->cPath = $c->getCollectionPath();
			}

			$this->setupSteps();		
		}
		
		protected function setupSteps() {
			if (count(self::$steps) > 0) {
				return false;
			}
			
			Loader::model('cart', 'core_commerce');
			
			$cart = CoreCommerceCart::get();
			$eh = Loader::helper('discount', 'core_commerce');
			$steps = array();
			if ($eh->askUserForCouponCode($cart)) {
				$steps[] = new CoreCommerceCheckoutStep('/checkout/discount');
			}
			$steps[] = new CoreCommerceCheckoutStep('/checkout/billing');
			if ($cart->requiresShipping()) {
				$steps[] = new CoreCommerceCheckoutStep('/checkout/shipping/address');
				$steps[] = new CoreCommerceCheckoutStep('/checkout/shipping/method');
			}
			Loader::model('shipping/method', 'core_commerce');
			$methods = $cart->getAvailableShippingMethods();
			if (count($methods) == 1) {
				$this->disableStep('/checkout/shipping/method');
			}
			$steps[] = new CoreCommerceCheckoutStep('/checkout/payment/method');
			$methods = $cart->getAvailablePaymentMethods();
			if (count($methods) == 1) {
				$this->disableStep('/checkout/payment/method');
			}
	
			$steps[] = new CoreCommerceCheckoutStep('/checkout/payment/form');
			//$steps[] = new CoreCommerceCheckoutStep('/checkout/review');
			$steps[] = new CoreCommerceCheckoutStep('/checkout/finish');
			
			$this->setSteps($steps);
		}
		
		public function getSteps() {
			return self::$steps;
			/*
			static $steps;
			if (!isset($steps)) {
				$s = __CLASS__;
				$steps = new $s;
			}
			return $steps;
			*/
		}
		
		public function setSteps($steps, $overwrite = true) {
			if(is_array(self::$steps) || !count(self::$steps) || $overwrite) {
				self::$steps = $steps;
			}
		}

		public function setCurrentPagePath($cPath) {
			$this->cPath = $cPath;
		}
		
		public function testCurrentStep() {
			if ($this->cPath == '/checkout') {
				Controller::redirect($this->getNextCheckoutStep()->getRedirectURL());
			} else {
				$steps = $this->getSteps();
				for ($i = 0; $i < count($steps); $i++) {
					if ($steps[$i]->getPath() == $this->cPath) {
						return true;
					}
				}
			}
			Controller::redirect("/checkout");
		}
	
		public function getCheckoutStep() {
			for ($i = 0; $i < count(self::$steps); $i++) {
				if (self::$steps[$i]->getPath() == $this->cPath) {
					$obj = self::$steps[$i];
				}
			}
			return $obj;
		}
		
		public function getPreviousCheckoutStep() {
			$getStep = false;
			for ($i = (count(self::$steps) - 1); $i >= 0; $i--) {
				$obj = self::$steps[$i];
				if ($getStep && ($obj instanceof CoreCommerceCheckoutStep) && $obj->isEnabled()) {
					return $obj;
				}
				if (self::$steps[$i]->getPath() == $this->cPath) {
					$getStep = true;
				}
			}	
		}
	
		public function getNextCheckoutStep() {
			for ($i = 0; $i < count(self::$steps); $i++) {
				if (self::$steps[$i]->getPath() == $this->cPath) {
					$obj = self::$steps[$i+1];
				}
			}
			
			if ($obj instanceof CoreCommerceCheckoutStep) {
				return $obj;
			} else {
				return self::$steps[0];
			}
		}
		
		public function getPreviousCheckoutStepURL($redirectURL = true) {
			$obj = $this->getPreviousCheckoutStep();
			if ($obj instanceof CoreCommerceCheckoutStep) {
				if ($redirectURL) {
					return $obj->getRedirectURL() .'?previous=1';
				} else { 
					return $obj->getURL() .'?previous=1';
				}
			}
		}
	
	
		public function getNextCheckoutStepURL($redirectURL = true) {
			for ($i = 0; $i < count(self::$steps); $i++) {
				if (self::$steps[$i]->getPath() == $this->cPath) {
					$obj = self::$steps[$i+1];
				}
			}
			
			if ($obj instanceof CoreCommerceCheckoutStep) {
				if ($redirectURL) {
					return $obj->getRedirectURL();
				} else { 
					return $obj->getURL();
				}
			}
		}
	
		public function getCheckoutPreviousStepButton() {
			$obj = $this->getPreviousCheckoutStep();
			if ($obj instanceof CoreCommerceCheckoutStep) {
				return '<input type="button" name="submit_previous" onclick="window.location.href=\'' . $obj->getURL() . '?previous=1\'; return false" class="ccm-core-commerce-checkout-button-previous" value="' . t('Previous') . '" />';
			}
		}
	
	
		public function getCheckoutNextStepButton() {
			for ($i = 0; $i < count(self::$steps); $i++) {
				if (self::$steps[$i]->getPath() == $this->cPath) {
					$obj = self::$steps[$i+1];
				}
			}
			
			if ($obj instanceof CoreCommerceCheckoutStep) {
				return Loader::helper('form')->submit('submit_next', t('Next'), array('class' => 'ccm-core-commerce-checkout-button-next'));
			}
		}
		
		/**
		 * returns the array key for given step path 
		 * @param string $path
		 * @return number|boolean
		 */
		protected function getStepIndexByPath($path) {
			for ($i = 0; $i < count(self::$steps); $i++) {
				if (self::$steps[$i]->getPath() == $path) {
					return $i;
				}
			}
			return false;
		}
		
		/**
		 * disables a checkout step for a given path ex: /checkout/finish
		 * @param string $path
		 * @return boolean
		*/
		public function disableStep($path) {
			$key = $this->getStepIndexByPath($path);
			if($key !== false) {
				$obj = self::$steps[$key];
			}
			
			if ($obj instanceof CoreCommerceCheckoutStep) {
				$obj->disableStep();
				return true;
			} else {
				return false;
			}
		}
	
		/**
		 * enables a checkout step for a given path ie checkout/finish
		 * @param string $path
		 * @return boolean
		*/
		public function enableStep($path) {
			$key = $this->getStepIndexByPath($path);
			if($key !== false) {
				$obj = self::$steps[$key];
			}
			
			if ($obj instanceof CoreCommerceCheckoutStep) {
				$obj->enableStep();
				return true;
			} else {
				return false;
			}
		}
		
		
		/**
		 * removes a step and reindexes the steps array
		 * @param string $path
		 * @return boolean
		 */
		public function removeStep($path) {
			$key = $this->getStepIndexByPath($path);
			if($key !== false) {
				unset(self::$steps[$key]);
				self::$steps = array_values(self::$steps); 
				return true;
			} else {
				return false;
			}
		}
		
		/**
		 * @param CoreCommerceCheckoutStep $checkoutStep
		 * @param string $path
		 * @return boolean
		 */
		public function replaceStep($checkoutStep, $path) {
			$key = $this->getStepIndexByPath($path);
			if($key !== false) {
				self::$steps[$key] = $checkoutStep;
				return true;
			} else {
				return false;
			}
		}
		
		/**
		 * Adds a step before the specified  step
		 * @param CoreCommerceCheckoutStep $checkoutStep
		 * @param string $newPath
		 * @param string $existingPath
		 * @return boolean
		*/
		public function addStepBefore($checkoutStep, $newPath, $existingPath) {
			$key = $this->getStepIndexByPath($existingPath);
			$newSteps = array();
			if($key !== false) {
				for($i=0; $i<count(self::$steps); $i++) {
					if($i==$key) {
						$newSteps[] = $checkoutStep;
					}
					$newSteps[] = self::$steps[$i];
				}
				self::$steps = $newSteps;
				return true;
			} else {
				return false;
			}
		}
		
		/**
		 * Adds a step after the specified step, if $existingPath is specified, it'll add it to the end
		 * @param CoreCommerceCheckoutStep $checkoutStep
		 * @param string $newPath
		 * @param string $existingPath
		 * @return boolean
		*/
		public function addStepAfter($checkoutStep, $newPath, $existingPath = NULL ) {
			if(!isset($existingPath)) {
				self::$steps[] = $checkoutStep;
				return true;
			} else {
				$key = $this->getStepIndexByPath($existingPath);
				$newSteps = array();
				if($key !== false) {
					for($i=0; $i<count(self::$steps); $i++) {
						$newSteps[] = self::$steps[$i];
						if($i==$key) {
							$newSteps[] = $checkoutStep;
						}
					}
					self::$steps = $newSteps;
					return true;
				} else {
					return false;
				}
			}
		}	
		
	}
	
	class CoreCommerceCheckoutStep {
	
		public function __construct($path) {
			//$this->submitTask = $submitTask;
			$this->path = $path;
			$this->enabled = true;
		}
		public function getBase() {
			$pkg = Package::getByHandle('core_commerce');
			if ($pkg->config('SECURITY_USE_SSL') == 'true') {
				return Config::get('BASE_URL_SSL');
			} else {
				return BASE_URL;
			}
		}
		public function getSubmitURL() {return $this->getBase() . View::url($this->path, 'submit');}
		public function getPath() {return $this->path;}
		public function getURL() {return $this->getBase() . View::url($this->path);}
		public function getRedirectURL() {return $this->path;}
		public function disableStep() {$this->enabled = false;}
		public function isEnabled() {return $this->enabled;}
		public function enableStep() { $this->enabled = true; }
	
	}
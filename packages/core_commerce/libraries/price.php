<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
class CoreCommercePrice {

	static $symbol = '';
	static $thousands = '';
	static $decimal = '';
	
	public static function getThousandsSeparator() {
		if (empty(self::$thousands)) {
			$pkg = Package::getByHandle('core_commerce');
			self::$thousands = $pkg->config('CURRENCY_THOUSANDS_SEPARATOR');
			if (empty(self::$thousands)) {
				self::$thousands = ',';
			}
		}
		return self::$thousands;
	}
	
	public static function getDecimalPoint() {
		if (empty(self::$decimal)) {
			$pkg = Package::getByHandle('core_commerce');
			self::$decimal = $pkg->config('CURRENCY_DECIMAL_POINT');
			if (empty(self::$decimal)) {
				self::$decimal = '.';
			}
		}
		return self::$decimal;
	}

	
	public function format($number) {
		if (empty(self::$symbol)) {
			$pkg = Package::getByHandle('core_commerce');
			self::$symbol = $pkg->config('CURRENCY_SYMBOL');
			if (empty(self::$symbol)) {
				self::$symbol = '$';
			}
		}
		return self::$symbol . number_format($number, 2, self::getDecimalPoint(), self::getThousandsSeparator());
	}
	
}

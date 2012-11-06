<?php  
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Functions useful for adding asset library access to your blocks and applications.
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

	defined('C5_EXECUTE') or die(_("Access Denied."));
	class FormProductHelper {
	
		public function selectOne($postname, $chooseText, $product = null) {
			Loader::model('product/model', 'core_commerce');
			
			$selectedDisplay = 'none';
			$resetDisplay = 'block';
			$productID = 0;
			
			if (isset($_POST[$postname])) {
				$product = CoreCommerceProduct::getByID($_POST[$postname]);
			}
			
			if (is_object($product)) {
				$productID = $product->getProductID();
				$selectedDisplay = 'block';
				$resetDisplay = 'none';
			}
				
			$html = '<div id="' . $postname . '-core-commerce-product-selected" class="ccm-core-commerce-product-selected-wrapper" style="display: ' . $selectedDisplay . '"><img src="' . ASSETS_URL_IMAGES . '/throbber_white_16.gif" /></div>';
			$html .= '<div class="ccm-core-commerce-product-select" id="' . $postname . '-core-commerce-product-display" ccm-core-commerce-product-field="' . $postname . '" style="display: ' . $resetDisplay . '">';
			$html .= '<a href="javascript:void(0)" style="background-image: url(' . ASSETS_URL_IMAGES . '/icons/add.png)" onclick="ccm_coreCommerceLaunchProductSelector(\'' . $postname . '\');">' . $chooseText . '</a>';
			$html .= '</div><input id="' . $postname . '-core-commerce-product-value" type="hidden" name="' . $postname . '" value="' . $productID . '" />';

			$uh = Loader::helper('urls', 'core_commerce');
			$html .= '<script type="text/javascript">if (typeof(ccm_coreCommerceProductManagerURL) == \'undefined\') { ';
			$html .= 'var ccm_coreCommerceProductManagerURL = \'' . $uh->getToolsURL('product/search_dialog') . '?mode=choose_one\';';
			$html .= 'var ccm_coreCommerceProductManagerSelectorDataURL = \'' . $uh->getToolsURL('product/selector_data') . '\';';
			$html .= '}</script>';

			if (is_object($product)) {
				$html .= '<script type="text/javascript">$(function() { ccm_coreCommerceTriggerSelectProduct(' . $productID . ', \'' . $postname . '\'); });</script>';
			}
			

			return $html;
		}
	}
	
?>

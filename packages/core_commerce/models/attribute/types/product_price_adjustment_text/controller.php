<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));

class ProductPriceAdjustmentTextAttributeTypeController extends TextAttributeTypeController  {

	/* (non-PHPdoc)
	 * @see concrete/models/attribute/types/boolean/BooleanAttributeTypeController#saveKey($data)
	*/
	public function saveKey($data) {
		$ak = $this->getAttributeKey();
		$db = Loader::db();
		$akAdjustmentDefault = $data['akAdjustmentDefault'];
		
		$db->Replace('atCoreCommerceProductAdjustmentTextSettings', array(
			'akID' => $ak->getAttributeKeyID(), 
			'akAdjustmentDefault' => $akAdjustmentDefault
			), array('akID'), true);
	}
	
	public function getValue() {
		$db = Loader::db();
		$value = $db->GetOne("select value from atCoreCommerceProductAdjustmentText where avID = ?", array($this->getAttributeValueID()));
		return $value;	
	}
	
	public function getPriceValue() {
		return $this->getValue();
	}

	public function getDisplayValue() {
		return "+ ".CoreCommercePrice::format($this->getValue());
	}
	
	public function form() {
		$this->load();
		$pkg = Package::getByHandle('core_commerce');
		if (is_object($this->attributeValue)) {
			$value = $this->getAttributeValue()->getValue();
		} else { // set the default
			$value = $this->akAdjustmentDefault;
		}
		print $pkg->config('CURRENCY_SYMBOL'). Loader::helper('form')->text($this->field('value'), $value, array('size'=>6));
	}
	
	
	/**
	 * Loads up the type data for the attribute key
	 * @return boolean
	 */
	protected function load() {
		$ak = $this->getAttributeKey();
		if (!is_object($ak)) {
			return false;
		} else {
			$db = Loader::db();
			$row = $db->GetRow('select akAdjustmentDefault from atCoreCommerceProductAdjustmentTextSettings where akID = ?', $ak->getAttributeKeyID());	
			$this->akAdjustmentDefault = $row['akAdjustmentDefault'];		
			$this->set('akAdjustmentDefault',$this->akAdjustmentDefault);
			return true;
		}
	}
	
	
	public function type_form() {
		$pkg = Package::getByHandle('core_commerce');
		$form = Loader::helper('form');
		$this->load();
		?>
		<table class="entry-form" cellspacing="1" cellpadding="0">
		<tr>
			<td class="subheader"><?php  echo t('Default Value')?></td>
		</tr>
		<tr>
			<td>
				<?php   echo $pkg->config('CURRENCY_SYMBOL'); ?>
				<?php   echo $form->text('akAdjustmentDefault', $this->akAdjustmentDefault);	?>
			</td>
		</tr>
		</table>
		<?php  		
	}

	public function searchForm($list) {
		$db = Loader::db();
		$list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), '%' . $this->request('value') . '%', 'like');
		return $list;
	}
	
	public function search() {
		$f = Loader::helper('form');
		print $f->text($this->field('value'), $this->request('value'));
	}
	
	// run when we call setAttribute(), instead of saving through the UI
	public function saveValue($value) {
		$db = Loader::db();
		$db->Replace('atCoreCommerceProductAdjustmentText', array('avID' => $this->getAttributeValueID(), 'value' => $value), 'avID', true);
	}
	
	public function saveForm($data) {
		$db = Loader::db();
		$this->saveValue($data['value']);
	}
	
	public function deleteKey() {
		$db = Loader::db();
		$arr = $this->attributeKey->getAttributeValueIDList();
		foreach($arr as $id) {
			$db->Execute('delete from atCoreCommerceProductAdjustmentText where avID = ?', array($id));
		}
	}

	
	/* (non-PHPdoc)
	 * validates for a larger than or equal to 0 value
	 * @see concrete/models/attribute/types/text/TextAttributeTypeController#validateForm($data)
	 */
	public function validateForm($data) {
		$vh = Loader::helper('validation/error');
		
		if(strlen($data['value']) && is_numeric($data['value'])) {
			if($data['value'] < 0) {
				return $vh->add(t('Must be greater than 0'));
			} else {
				return true;
			}
		} else {
			return true;
		}
	}


	public function deleteValue() {
		$db = Loader::db();
		$db->Execute('delete from atCoreCommerceProductAdjustmentText where avID = ?', array($this->getAttributeValueID()));
	}
	
}

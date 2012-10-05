<?php   defined('C5_EXECUTE') or die(_("Access Denied."));

/**
 * Attribute that modifies the product price based on wether a checckbox is selected or not.
 * @author rtyler
 *
 */
class ProductPriceAdjustmentBooleanAttributeTypeController extends BooleanAttributeTypeController  {
	
	/* (non-PHPdoc)
	 * @see concrete/models/attribute/types/boolean/BooleanAttributeTypeController#getValue()
	 */
	public function getValue() {
		$db = Loader::db();
		$value = $db->GetOne("select value from atCoreCommerceProductAdjustmentBoolean where avID = ?", array($this->getAttributeValueID()));
		return $value;	
	}
		
	/* (non-PHPdoc)
	 * @see concrete/models/attribute/types/boolean/BooleanAttributeTypeController#getDisplayValue()
	 */
	public function getDisplayValue() {
		$v = $this->getValue();
		$txt = ($v == 1) ? t('Yes') : t('No'). " ";
		if($this->adjustmentValue != 0) {
			if($this->adjustmentValue > 0) {
				$txt .= "+";
			} else {
				$txt .= "-";
			}
			$txt .= " ";
			$txt .= CoreCommercePrice::format($this->adjustmentValue);
		}
		return $txt;
	}
	
	/*
	 * gets amount to adjust the price based on the selected option
	 * @return double
	*/
	public function getPriceValue() {
		$this->load();
		if($this->getValue()) {
			return $this->adjustmentValue;
		} else {
			return 0;			
		}
	}
	
	/* (non-PHPdoc)
	 * @see concrete/models/attribute/types/boolean/BooleanAttributeTypeController#load()
	 */
	protected function load() {
		$ak = $this->getAttributeKey();
		if (!is_object($ak)) {
			return false;
		}
		
		$db = Loader::db();
		$row = $db->GetRow('select akCheckedByDefault, adjustmentValue from atCoreCommerceProductAdjustmentBooleanSettings where akID = ?', $ak->getAttributeKeyID());
		
		$this->akCheckedByDefault = $row['akCheckedByDefault'];
		$this->adjustmentValue = $row['adjustmentValue'];
		
		$this->set('adjustmentValue',$this->adjustmentValue);
		$this->set('akCheckedByDefault', $this->akCheckedByDefault);
	}
	
	/* (non-PHPdoc)
	 * @see concrete/models/attribute/types/boolean/BooleanAttributeTypeController#form()
	 */
	public function form() {
		parent::form();	
		echo " (";
		if($this->adjustmentValue > 0) {
			echo "+";
		} else {
			echo "-";
		}
		echo " ";
		echo CoreCommercePrice::format($this->adjustmentValue);
		echo ")";
	}
	
	
	/* (non-PHPdoc)
	 * @see concrete/models/attribute/types/boolean/BooleanAttributeTypeController#type_form()
	 */
	public function type_form() {
		$pkg = Package::getByHandle('core_commerce');
		$form = Loader::helper('form');
		$this->load();
		?>
		<table class="entry-form" cellspacing="1" cellpadding="0">
		<tr>
			<td class="subheader"><?php  echo t('Default Value')?></td>
			<td class="subheader"><?php  echo t('Adjustment Amount')?></td>
		</tr>
		<tr>
			<td><?php  echo $form->checkbox('akCheckedByDefault', 1, $this->akCheckedByDefault)?>
			<?php  echo t('The checkbox will be checked by default.')?>
			</td>
			<td>
			<?php   echo $pkg->config('CURRENCY_SYMBOL'); ?>
			<?php   echo $form->text('adjustmentValue', $this->adjustmentValue);	?>
			</td>
		</tr>
		</table>
		<?php  		
	}
	
	/* (non-PHPdoc)
	 * run when we call setAttribute(), instead of saving through the UI
	 * @see concrete/models/attribute/types/boolean/BooleanAttributeTypeController#saveValue($value)
	 */
	public function saveValue($value) {
		$db = Loader::db();
		$value = ($value == false || $value == '0') ? 0 : 1;
		$db->Replace('atCoreCommerceProductAdjustmentBoolean', array('avID' => $this->getAttributeValueID(), 'value' => $value), 'avID', true);
	}
	
	/* (non-PHPdoc)
	 * @see concrete/models/attribute/types/boolean/BooleanAttributeTypeController#deleteKey()
	 */
	public function deleteKey() {
		$db = Loader::db();
		$db->Execute('delete from atCoreCommerceProductAdjustmentBooleanSettings where akID = ?', array($this->getAttributeKey()->getAttributeKeyID()));

		$arr = $this->attributeKey->getAttributeValueIDList();
		foreach($arr as $id) {
			$db->Execute('delete from atCoreCommerceProductAdjustmentBoolean where avID = ?', array($id));
		}
	}
	
	/* (non-PHPdoc)
	 * @see concrete/models/attribute/types/boolean/BooleanAttributeTypeController#duplicateKey($newAK)
	 */
	public function duplicateKey($newAK) {
		$this->load();
		$db = Loader::db();
		$db->Execute('insert into atCoreCommerceProductAdjustmentBooleanSettings (akID, akCheckedByDefault) values (?, ?)', array($newAK->getAttributeKeyID(), $this->akCheckedByDefault));	
	}
	
	/* (non-PHPdoc)
	 * @see concrete/models/attribute/types/boolean/BooleanAttributeTypeController#saveKey($data)
	*/
	public function saveKey($data) {
		$ak = $this->getAttributeKey();
		$db = Loader::db();
		$akCheckedByDefault = $data['akCheckedByDefault'];
		$adjustmentValue = $data['adjustmentValue'];
		
		if ($data['akCheckedByDefault'] != 1) {
			$akCheckedByDefault = 0;
		}

		$db->Replace('atCoreCommerceProductAdjustmentBooleanSettings', array(
			'akID' => $ak->getAttributeKeyID(), 
			'akCheckedByDefault' => $akCheckedByDefault,
			'adjustmentValue' => $adjustmentValue
		), array('akID'), true);
	}
	
	
	/* (non-PHPdoc)
	 * @see concrete/models/attribute/types/boolean/BooleanAttributeTypeController#validateForm($data)
	 */
	public function validateForm($data) {
		return $data['value'] == 1;
	}
	
	
	/* (non-PHPdoc)
	 * @see concrete/models/attribute/types/boolean/BooleanAttributeTypeController#deleteValue()
	*/
	public function deleteValue() {
		$db = Loader::db();
		$db->Execute('delete from atCoreCommerceProductAdjustmentBoolean where avID = ?', array($this->getAttributeValueID()));
	}
	
}
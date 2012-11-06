<?php   
$form = Loader::helper('form'); 
$ih = Loader::helper("concrete/interface");
$valt = Loader::helper('validation/token');

$salesTaxRateName = '';
$salesTaxRateIsEnabled = 0;
$salesTaxRateIncludeShipping = 0;

if (is_object($rate)) {
	$salesTaxRateName = $rate->getSalesTaxRateName();
	$salesTaxRateIsEnabled = $rate->isSalesTaxRateEnabled();
	$salesTaxRateAmount = $rate->getSalesTaxRateAmount();
	$salesTaxRateCountry = $rate->getSalesTaxRateCountry();
	$salesTaxRateStateProvince = $rate->getSalesTaxRateStateProvince();
	$salesTaxRatePostalCode = $rate->getSalesTaxRatePostalCode();
	$salesTaxRateIncludedInProduct = $rate->isSalesTaxIncludedInProduct();
	$salesTaxRateIncludeShipping = $rate->includeShippingInSalesTaxRate();
	print $form->hidden('rateID', $rate->getSalesTaxRateID());
} else if ($this->controller->isPost()) {

} else {
	$salesTaxRateCountry = 'US';
}

$spreq = $form->getRequestValue('salesTaxRateStateProvince');
if ($spreq != false) {
	$salesTaxRateStateProvince = $spreq;
}
$creq = $form->getRequestValue('salesTaxRateCountry');
if ($creq != false) {
	$salesTaxRateCountry = $creq;
}

$co = Loader::helper('lists/countries');
$countries = array_merge(array('' => t('Choose Country')), $co->getCountries());

?>

<div class="ccm-core-commerce-sales-tax-location">

<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader" width="40%"><?php  echo t('Name')?> <span class="required">*</span> <span class="explanation"><?php  echo t('(displayed in the cart at checkout)')?></span></td>
	<td class="subheader" width="40%"><?php  echo t('Enabled?')?> <span class="required">*</span></td>
	<td class="subheader" width="20%"><?php  echo t('Rate')?> <span class="required">*</span></td>
</tr>	
<tr>
	<td style="padding-right: 15px" valign="top"><?php  echo $form->text('salesTaxRateName', $salesTaxRateName, array('style' => 'width: 100%'))?></td>
	<td style="padding-right: 15px" valign="top">
	<?php  echo $form->select('salesTaxRateIsEnabled', array(
			'1' => t('Enabled'), 
			'0' => t('Disabled')
		), $salesTaxRateIsEnabled);?>
	</td>
	<td style="padding-right: 15px" valign="top"><?php  echo $form->text('salesTaxRateAmount', $salesTaxRateAmount, array('style' => 'width: 30px'))?>%</td>
</tr>
<tr>
	<td class="subheader"><?php  echo t('Country')?></td>
	<td class="subheader"><?php  echo t('State/Province')?></td>
	<td class="subheader"><?php  echo t('Postal Code')?></td>
</tr>
<tr>
	<td>
	<div class="ccm-attribute-address-line ccm-attribute-address-country">
	<?php  echo $form->select('salesTaxRateCountry', $countries, $salesTaxRateCountry); ?>
	</div>
	</td>
	<td>
	<div class="ccm-attribute-address-line ccm-attribute-address-state-province">
	<?php  echo $form->select('salesTaxRateStateProvinceSelect', array('' => t('Choose State/Province')), $salesTaxRateStateProvince, array('ccm-attribute-address-field-name' => 'salesTaxRateStateProvince'))?>
	<?php  echo $form->text('salesTaxRateStateProvinceText', $salesTaxRateStateProvince, array('style' => 'display: none', 'ccm-attribute-address-field-name' => 'salesTaxRateStateProvince'))?>
	</div>

	</td>
	<td><?php  echo $form->text('salesTaxRatePostalCode', $salesTaxRatePostalCode, array('style' => 'width: 90px'))?></td>
</tr>
<tr>
	<td colspan="2" class="subheader"><?php   echo t('Sales Tax Already Included in Product Price')?></td>
	<td class="subheader"><?php   echo t('Include Shipping in Tax')?></td>
</tr>
<tr>
	<td colspan="2">
	<?php   echo $form->select('salesTaxRateIncludedInProduct', array(
			'0' => t('No, bill sales tax during checkout'), 
			'1' => t('Yes, display sales tax but do not add it to order')
		), $salesTaxRateIncludedInProduct);?>
	</td>
	<td>
		<?php   echo $form->select('salesTaxRateIncludeShipping', array(
				'1' => t('Yes'), 
				'0' => t('No')
			), $salesTaxRateIncludeShipping);?>
	</td>
</tr>
</table>


<?php  echo $valt->output('add_or_update_sales_tax_rate')?>
<?php   if (is_object($rate)) { ?>
	<?php  echo $ih->submit(t('Update Rate'), 'ccm-core-commerce-sales-tax-form')?>
<?php   } else { ?>
	<?php  echo $ih->submit(t('Add Rate'), 'ccm-core-commerce-sales-tax-form')?>
<?php   } ?>

<div class="ccm-spacer">&nbsp;</div>

</div>

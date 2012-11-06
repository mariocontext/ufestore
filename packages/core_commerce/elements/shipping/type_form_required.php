<?php   
$form = Loader::helper('form'); 
$ih = Loader::helper("concrete/interface");
$valt = Loader::helper('validation/token');
$co = Loader::helper('lists/countries'); 
$countries = array_merge(array('' => t('Choose Country')), $co->getCountries());

if (isset($_POST['shippingTypeHasCustomCountriesSelected'])) {
	$shippingTypeHasCustomCountriesSelected = $_POST['shippingTypeHasCustomCountriesSelected'];
} else {
	$shippingTypeHasCustomCountriesSelected = $type->getShippingTypeCustomCountries();
}

if (isset($_POST['shippingTypeHasCustomCountries'])) {
	$shippingTypeHasCustomCountries = $_POST['shippingTypeHasCustomCountries'];
}  else {
	$shippingTypeHasCustomCountries = $type->hasShippingTypeCustomCountries();
} 

?>

<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader" width="33%"><?php  echo t('Handle')?> <span class="required">*</span></td>
	<td class="subheader" width="34%"><?php  echo t('Name')?> <span class="required">*</span></td>
	<td class="subheader" width="33%"><?php  echo t("Enabled")?></td>
</tr>	
<tr>
	<td style="padding-right: 15px" valign="top"><?php  echo $type->getShippingTypeHandle()?></td>
	<td style="padding-right: 15px" valign="top"><?php  echo $type->getShippingTypeName()?></td>
	<td style="padding-right: 10px" valign="top"><?php  
		print $form->select('shippingTypeIsEnabled', array('0' => t('No'), '1' => t('Yes')), $type->isShippingTypeEnabled());
	?>
	</td>
</tr>
<tr>
	<td class="subheader" colspan="3"><?php  echo t("Shipping Available To")?></td>
</tr>
<tr>
	<td colspan="3" style="padding-right: 15px">
		<div><?php  echo $form->radio('shippingTypeHasCustomCountries', 0, $type->hasShippingTypeCustomCountries())?> <?php  echo $form->label('shippingTypeHasCustomCountries1', t('All Available Countries'))?></div>
		<div><?php  echo $form->radio('shippingTypeHasCustomCountries', 1, $type->hasShippingTypeCustomCountries())?> <?php  echo $form->label('shippingTypeHasCustomCountries2', t('Selected Countries'))?></div>
		
		<select id="shippingTypeHasCustomCountriesSelected" name="shippingTypeHasCustomCountriesSelected[]" multiple size="7" style="width:100%" disabled>
			<?php   foreach ($countries as $key=>$val) { ?>
				<?php   if (empty($key) || empty($val)) continue; ?>
				<option <?php  echo (in_array($key, $shippingTypeHasCustomCountriesSelected) || $shippingTypeHasCustomCountries == 0 ?'selected ':'')?>value="<?php  echo $key?>"><?php  echo $val?></option>
			<?php   } ?>
		</select>
	</td>
	
	</td>
</tr>
</table>

<?php  echo $form->hidden('shippingTypeID', $type->getShippingTypeID())?>
<?php  echo $valt->output('update_shipping_type')?>

<?php   $type->render('type_form'); ?>

<?php  echo $ih->submit(t('Update Shipping Type'), 'ccm-core-commerce-shipping-type-form')?>

<div class="ccm-spacer">&nbsp;</div>

<script type="text/javascript">
$(function() {
	$("input[name=shippingTypeHasCustomCountries]").click(function() {
		ccm_coreCommerceShippingTypeCountries($(this));
	});
	
	ccm_coreCommerceShippingTypeCountries();
});

ccm_coreCommerceShippingTypeCountries = function(obj) {
	if (!obj) {
		var obj = $("input[name=shippingTypeHasCustomCountries][checked=checked]");
	}
	if (obj.attr('value') == 1) {
		$("#shippingTypeHasCustomCountriesSelected").attr('disabled' , false);
	} else {
		$("#shippingTypeHasCustomCountriesSelected").attr('disabled' , true);
		$("#shippingTypeHasCustomCountriesSelected option").attr('selected', true);
	}
}

</script>
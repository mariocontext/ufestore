<?php  defined('C5_EXECUTE') or die(_("Access Denied."));

$subject = SITE." - ".t("Order# %s Files available for download",$orderID);
$body .= t("The following files are available for download")."

";
foreach($downloads as $d) {
$body.= $d['name']."
".$d['url']."

";
}

$body .="

".t('Thanks for your order');
?>
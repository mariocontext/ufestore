<?php  
$p = Page::getCurrentPage();
Loader::model('groups');
$u = new User();
$g = Group::getByName("Affiliate");
if($u->inGroup($g)){ ?>
<h3>Affiliate Link</h3>
<?php 
  Loader::model('scottc_commerce_affiliate',SCOTTECOMAFFILATESPACKAGEHANDLE);
  $aff = ScottcCommerceAffiliate::getByUserID($u->getUserID());
 
  Loader::helper('navigation');
  $nh = new NavigationHelper();
  $co = new Config();
  $co->setPackageObject(Package::getByHandle(SCOTTECOMAFFILATESPACKAGEHANDLE));
  $prefix = $co->get('SCOTTC_AFFILIATES_GET_VAR_PREFIX');
  $link = $nh->getLinkToCollection($p, true);
  if($p->getCollectionID() == HOME_CID) $link = BASE_URL.DIR_REL;
  printf('<input type="text" id="scottc-affiliate-link" value="%s" />',$link.'?'.$prefix.'='.$aff->affiliateCode);
}else{
}
if($p->isEditMode()){
  print "Content Disabled in Edit Mode. This is a textbox linking to an affiliate code for whichever page this is placed on.";
}
?>

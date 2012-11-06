<?php 
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Loader::helper('form');
$fh = new FormHelper();
$yesno = array('No','Yes');
Loader::helper('concrete/interface');
$ih = new ConcreteInterfaceHelper();
?>
<h1><span>Affiliate Settings</span></h1>
<div class="ccm-dashboard-inner">
  <form id="save-config" method="post" action="<?php  echo $this->action('save'); ?>">
  <h2>Cookies</h2>
  <b>Use Cookies?</b>
  <?php  echo $fh->select('SCOTTC_AFFILIATE_USE_COOKIES', $yesno, $SCOTTC_AFFILIATE_USE_COOKIES); ?>
  <br />
  <p>How many days to attempt to persist a cookie for an affiliate entrance</p>
  <b>a user Session is always tracked, cookies can be unreliable.</b>
  <input type="text" name="SCOTTC_AFFILIATE_COOKIE_DAYS" size="10" value="<?php  echo $SCOTTC_AFFILIATE_COOKIE_DAYS ?>" /><br />
  <br />
  <h2>Timestamp Format</h2>
  <p>The format used for timestamps in tables, etc</p>
  <input type="text" name="SCOTTC_AFFILIATES_TIMESTAMP_FORMAT" size="10" value="<?php  echo $SCOTTC_AFFILIATES_TIMESTAMP_FORMAT ?>" /><br />
  <h2>Tracking Url Prefix</h2>
  <p>This is what is used to associate actions in the shopping cart with an affiliate</p>
  <p>Currently, any url that ends in /?<?php  echo $SCOTTC_AFFILIATES_GET_VAR_PREFIX; ?>=yourCodeHere will be tracked</p>
  <p>These are known as request parameters or $_GET vars.</p>
  <p><b>Do not change if you have any existing affiliates/tracking campaigns set up!</b></p>
  <input type="text" name="SCOTTC_AFFILIATES_GET_VAR_PREFIX" value="<?php  echo $SCOTTC_AFFILIATES_GET_VAR_PREFIX; ?>" />
  <h2>Ignore Super User from Stats?</h2>
  <?php  echo $fh->select("SCOTTC_AFFILIATE_IGNORE_SUPER_USER", $yesno, $SCOTTC_AFFILIATE_IGNORE_SUPER_USER); ?><br /><br />
  <?php  echo $ih->submit("Save",'','left') ?> <br />
  </form>
 
  
</div>

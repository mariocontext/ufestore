<h1><span>Help</span></h1>
<div class="ccm-dashboard-inner">
  <h3>General Help Topics</h3>
  <h2>Using your affiliate or tracking codes</h2>
  <p>Using your tracking code is easy.  Right now, you are currently set up to use the following tracking code:</p>
  <?php  $co = new Config();
  $co->setPackageObject(Package::getByHandle('scottc_affiliates'));
  $key = $co->get('SCOTTC_AFFILIATES_GET_VAR_PREFIX');
  ?>
  <h2><?php  echo $key; ?></h2>
  <p> Which can be used appended to any url linking to a page on your site using by appending</p>
  <p><b>http://yoursite/?<?php echo $key; ?>=TrackingCodeHere</b></p>
  <p>To Use this code, simply link anywhere on your site, usually the homepage, with this code, followed by a key.</p>
  <p>For example, to track google, simply link to your homepage as:
  <ol>
    <li><input type="text" value="<?php  echo BASE_URL.DIR_REL.'/?'.$key.'=GOOG'; ?>" size="70" style="font-size:1.3em;" /></li>
  </ol>
  <p><b>Another example is to link to a particular product</b></p>
        <ol>
          <li><?php  echo BASE_URL.DIR_REL.'/store/widget-name?'.$key.'=GOOG'; ?>
        </ol>
<p><b>Purchases that are initiated with this key, as long as the session is valid, will be registered and available
  <a href="<?php echo $this->url('dashboard/affiliates/detail'); ?>">here</a>, if available.</b></p>
  <h2>Associating Revenue with Tracking Codes and Calculating Commission</h2>
  <p>This package allows you to keep track of commission and or revenue generated per tracking code. If you create a registered
  tracking code <a href="<?php echo $this->url('dashboard/affiliates/manage'); ?>">here</a>, you can keep a running tab on either flat rate or percentage based commissions.</p>
  <p>This is great for booking tours and things of that nature.  While we haven't provided you with a fully featured booking system, we have provided a way
  to enter in payments to help keep track of payments you have made to affiliates(if you decide to use the commission functionality).
  </p>



</div>

<?php 

?>

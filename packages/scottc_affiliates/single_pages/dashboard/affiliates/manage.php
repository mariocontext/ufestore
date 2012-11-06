<?php 
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Loader::helper('form');
Loader::helper('form/user_selector');
Loader::helper('concrete/interface');
$us = new FormUserSelectorHelper();
$fh = new FormHelper();
$ih = new ConcreteInterfaceHelper();
?>
<script type="text/javascript">
var delete_affiliate = function(affID){
  confirmed =  confirm('Are You Sure You Want to Delete this affiliate?');
  if(confirmed) window.location = '<?php  echo $this->action('delete'); ?>' + affID;
}
</script>
<h1><span>Add/Edit Affiliate</span></h1>
<div class="ccm-dashboard-inner">
  <h2>Affiliate</h2>
  <form id="add-affiliate" method="post" action="<?php  echo $this->url('/dashboard/affiliates/manage','save'); ?>">
    <?php  if($affiliateID) echo $fh->hidden ('affiliateID',$affiliateID); ?>
    <?php  echo $fh->label('name','Affiliate Name').'<br />'.$fh->text('name',$name); ?><br />
    <h2>User</h2>
    <p>Optional</p>
    <div style="width:300px;">
    <?php  echo $us->selectUser('userID', $userID); ?></div><br />
    <?php  echo $fh->label('affiliateCode','Affiliate Code').'<br />'.$fh->text('affiliateCode',$affiliateCode); ?><br />
    <?php  echo $fh->label('earningType','Earning Affiliate Type').'<br />'.$fh->select('earningType',$types,$earningType); ?>
    <br /><br />
    <?php  echo $fh->label('amount','Earning Amount, ok to leave blank').'<br />'.$fh->text('amount',$amount); ?>
    <br /><br />
    <?php  echo $ih->submit(t('Save'),'','left'); ?>
  </form>

</div>
<?php  if($allAffiliates){ ?>
<h1><span>Existing Affiliates</span></h1>
<div class="ccm-dashboard-inner">
<table class="grid-list" cellspacing="1" border="0">
  <thead><td class="header">Name</td><td class="header">Code</td><td class="header">User ID</td><td class="header">Earning Type</td><td class="header">Amount</td><td class="header">Edit</td><td class="header">Delete</td></thead>
<tbody>
  <?php  foreach($allAffiliates as $af){ ?>
  <tr><td><?php  echo $af->name; ?></td>
    <td><?php  echo $af->affiliateCode; ?></td>
      <td><?php  echo ($af->userID) ? User::getByUserID($af->userID)->getUserName() : 'none'; ?></td>
      <td><?php  echo $types[$af->earningType]; ?></td>
      <td><?php  echo $af->amount; ?></td>
      <td><?php  echo $ih->button("edit", $this->action('edit',$af->affiliateID),'left'); ?></td>
      <td><?php  echo $ih->button_js('delete','delete_affiliate('.$af->affiliateID.')','left'); ?></td>
  </tr>
    <?php  } ?>
</tbody>
</table>
</div>
<?php  } ?>


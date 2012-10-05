<?php
  
?>

<div id="ccm-affiliated-signup">
  <h4>Affiliate Information</h4>
  
  <form method="post" action="<?php echo $this->action('newuser') ?>">
  <div class="user-information">
    <table>
    <tr>
      <td>Email Address</td>
      <td><input type="text" name="user[email]" /></td>
    </tr>
    <tr>
      <td>Password</td>
      <td><input type="password" name="user[password]" /></td>
    </tr>
    <tr>
      <td>First Name</td>
      <td><input type="text" name="user[first_name]" /></td>
    </tr>
    <tr>
      <td>Last Name</td>
      <td><input type="text" name="user[last_name]" /></td>
    </tr>
    <tr>
      <td>Company</td>
      <td><input type="text" name="user[company]" /></td>
    </tr>
    <tr>
      <td>Address</td>
      <td><input type="text" name="user[address]" /></td>
    </tr>
    <tr>
      <td>City</td>
      <td><input type="text" name="user[city]" /></td>
    </tr>
    <tr>
      <td>State</td>
      <td><input type="text" name="user[state]" /></td>
    </tr>
    <tr>
      <td>Zip/Postal Code</td>
      <td><input type="text" name="user[postal_code]" /></td>
    </tr>
    <tr>
      <td>Phone</td>
      <td><input type="text" name="user[phone]" /></td>
    </tr>
    <tr>
      <td>Fax</td>
      <td><input type="text" name="user[fax]" /></td>
    </tr>
    <tr>
      <td>Country</td>
      <td><input type="text" name="user[country]" /></td>
    </tr>
    </table>
  </div>
  <br />
  
  <input type="submit" value="Register" />
  </form>
</div>
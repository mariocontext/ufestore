<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

class AffiliateGatewayAffiliate {
 public static function findByEmail($email) {
   $db = Loader::db();
   $sql = "SELECT * FROM AffiliatedGatewayAffiliates WHERE email = ?";
   $rs = $db->Execute($sql, array($email));
   if($rs) {
     $rows = array();
     while ($row = $rs->FetchRow()) {
       $rows[] = $row;
     }
     return (count($rows) == 0 ? false : $rows[0]);
  }
  return false;
 }

 public static function signup($user=array()) {
   $db = Loader::db();
    $errors = array();
    
    $keys = array_keys($user);
    $fields = array();
    $values = array();
    $vals   = array();
    
    foreach($keys as $key) {
      $val = $user[$key];
      $fields[] = $key;
      $values[] = "?";
      $vals[]   = $val;
      
      if($key == "email" && self::findByEmail($val)) {
        $errors[] = "Email Address already exists";
      }
      self::validateField($key, $val, &$errors);
    }
    $fields = implode(", ", $fields);
    $values = implode(", ", $values);
    
    if(count($errors) == 0 && count($vals) > 0) {
      $sql = "INSERT INTO AffiliatedGatewayAffiliates (". $fields .") VALUES(". $values .")";
      $db->Execute($sql, $vals);
    }
    return $errors;
 }
 //
 public static function emptyField($field) {
   if(empty($field) || $field == " ") {
     return true;
   }
   return false;
 }
 
  public static function validateField($key, $value, $error) {       
    if($key == "email" && self::checkEmail($val) == false) {
      $errors[] = "Email is invalid format";
    }
    
    if($key == "password") {
      if(strlen($val) < 4) {
        $errors[] = "Password is too short, must be at least 4 characters";
      } else if(empty($val) || $val == "" || $val == " ") {
        $errors[] = "Password is blank";
      }
    }
    
    if($key == "first_name" && self::emptyField($val)) {
      $errors[] = "First Name is empty";
    }
    if($key == "last_name" && self::emptyField($val)) {
      $errors[] = "Last Name is empty";
    }
    if($key == "city" && self::emptyField($val)) {
      $errors[] = "City is empty";
    }
    if($key == "state" && self::emptyField($val)) {
      $errors[] = "State is empty";
    }
    if($key == "phone" && self::emptyField($val)) {
      $errors[] = "Phone number is empty";
    }
    if($key == "country" && self::emptyField($val)) {
      $errors[] = "Country is empty";
    }
  }
 //

 public static function checkEmail($email) {
   if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
     return true;
   }
   return false;
 } 
}

?>
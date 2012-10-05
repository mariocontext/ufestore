<?php   defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<?php  

Loader::model('product/model', 'core_commerce');
Loader::model('order/product', 'core_commerce');
Loader::model('cart', 'core_commerce');

$cart = CoreCommerceCart::get();
$errors = array();

if (!isset($_REQUEST['dialog'])) {
	$dialog = true;
} else {
	$dialog = $_REQUEST['dialog'];
}

?>

<?php  echo Loader::packageElement('cart_item_list', 'core_commerce', array('dialog' => $dialog,'errors' => $errors))?>

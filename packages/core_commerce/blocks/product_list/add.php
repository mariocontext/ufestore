<?php  
defined('C5_EXECUTE') or die(_("Access Denied.")); 

$controller->displayLimit=20;
$controller->font_size='inherit';
$controller->header_background_color = '#eeeeee';
$controller->header_font_color = '#333333';
$controller->even_bg_color = '#fafafa';
$controller->odd_bg_color = '#ffffff';
$controller->even_font_color = '#333333';
$controller->odd_font_color = '#333333';
$controller->alternate_rows=1;

$bt->inc('elements/form_setup_html.php', array( 'c'=>$c, 'b'=>$b, 'controller'=>$controller ) ); 

?>
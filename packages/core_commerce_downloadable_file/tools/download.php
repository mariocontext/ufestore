<?php  defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::model('downloadable_product','core_commerce_downloadable_file');


$u = new User();
$fh = Loader::helper('file');
$vh = Loader::helper('validation/identifier');
$form = Loader::helper('form');

if (isset($_REQUEST['hash'])) {
	$fID = DownloadableProduct::getFileByHash($_REQUEST['hash']);
	if($fID) {
		$f = File::getByID($fID);
		if(is_object($f)) {
			$f->trackDownload();
			$fh->forceDownload($f->getPath());
		}
	} 
	// redirect / display error
}

Controller::redirect(View::url('/page_forbidden'));
exit;

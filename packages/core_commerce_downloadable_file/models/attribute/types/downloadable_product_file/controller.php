<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

class DownloadableProductFileAttributeTypeController extends AttributeTypeController  {

	protected $searchIndexFieldDefinition = 'I DEFAULT 0 NULL';
	public $akLimitDownloadDays = 30;
	public $akLimitDownloadTime = 0;
	
	public function getValue() {
		$db = Loader::db();
		$value = $db->GetOne("select fID from atCoreCommerceProductDownloadableFile where avID = ?", array($this->getAttributeValueID()));
		if ($value > 0) {
			$f = File::getByID($value);
			return $f;
		}
	}
	
	public function getDisplayValue() {
		// no display value, but prevent the getValue function from beeing called.
		return "";
	}

	public function getFidValue() {
		$db = Loader::db();
		$value = $db->GetOne("select fID from atCoreCommerceProductDownloadableFile where avID = ?", array($this->getAttributeValueID()));
		return $value; 
	}
	
	public function getExpireValue() {
		$res = $this->load();
		if($this->akLimitDownloadTime) {
			$dh = Loader::helper('date');
			$now = $dh->getSystemDateTime();
			$now = strtotime($now);
			
			// 86400 = 1 day in seconds
			$expire = $now + ($this->akLimitDownloadDays * 86400);
			return date('Y-m-d H:i:s',$expire);
		} else {
			return false;
		}
	}
	
	
	public function searchForm($list) {
		$fileID = $this->request('value');
		$list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), $fileID);
		return $list;
	}
	
	public function search() {
		// search by file causes too many problems
		//$al = Loader::helper('concrete/asset_library');
		//print $al->file('ccm-file-akID-' . $this->attributeKey->getAttributeKeyID(), $this->field('value'), t('Choose File'), $bf);
	}
	
	public function form() {
		$bf = false;
		if ($this->getAttributeValueID() > 0) {
			$bf = $this->getValue();
		}
		$al = Loader::helper('concrete/asset_library');
		print $al->file('ccm-file-akID-' . $this->attributeKey->getAttributeKeyID(), $this->field('value'), t('Choose File'), $bf);
	}

	// run when we call setAttribute(), instead of saving through the UI
	public function saveValue($obj) {
		$db = Loader::db();
		$db->Replace('atCoreCommerceProductDownloadableFile', array('avID' => $this->getAttributeValueID(), 'fID' => $obj->getFileID()), 'avID', true);
	}
	
	public function deleteKey() {
		$db = Loader::db();
		$arr = $this->attributeKey->getAttributeValueIDList();
		foreach($arr as $id) {
			$db->Execute('delete from atCoreCommerceProductDownloadableFile where avID = ?', array($id));
		}
	}
	

	public function saveKey($data) {
		$ak = $this->getAttributeKey();
		$db = Loader::db();
		$akLimitDownloadTime = $data['akLimitDownloadTime'];
		$akLimitDownloadDays = $data['akLimitDownloadDays'];
		
		if ($data['akLimitDownloadTime'] != 1) {
			$akLimitDownloadDays = 0;
		}

		$db->Replace('atCoreCommerceProductDownloadableFileSettings', array(
			'akID' => $ak->getAttributeKeyID(), 
			'akLimitDownloadTime' => $akLimitDownloadTime,
			'akLimitDownloadDays' => $akLimitDownloadDays
		), array('akID'), true);
	}
	
	public function saveForm($data) {
		if ($data['value'] > 0) {
			$f = File::getByID($data['value']);
			$this->saveValue($f);
		} else {
			$db = Loader::db();
			$db->Replace('atCoreCommerceProductDownloadableFile', array('avID' => $this->getAttributeValueID(), 'fID' => 0), 'avID', true);	
		}
	}
	
	public function deleteValue() {
		$db = Loader::db();
		$db->Execute('delete from atCoreCommerceProductDownloadableFile where avID = ?', array($this->getAttributeValueID()));
	}
	
	public function load() {
		$ak = $this->getAttributeKey();
		if (!is_object($ak)) {
			return false;
		}
		
		$db = Loader::db();
		$row = $db->GetRow('select akLimitDownloadTime, akLimitDownloadDays from atCoreCommerceProductDownloadableFileSettings where akID = ?', $ak->getAttributeKeyID());
		
		$this->akLimitDownloadTime = $row['akLimitDownloadTime'];
		$this->akLimitDownloadDays = $row['akLimitDownloadDays'];
		
		$this->set('akLimitDownloadTime',$this->akLimitDownloadTime);
		$this->set('akLimitDownloadDays', $this->akLimitDownloadDays);
		return true;
	}
	
	public function type_form() {
		$form = Loader::helper('form');
		$this->load();
		?>
		<script language="javascript">
			$(document).ready(function() {
				$('#akLimitDownloadTime').change(function(){
					if($(this).attr('checked')) {
						$('#ak-file-download-days').show();
					} else {
						$('#ak-file-download-days').hide();
					}
				});
			}); 
		</script>
		<table class="entry-form" cellspacing="1" cellpadding="0">
		<tr>
			<td class="subheader"><?php echo t('Download Time Constraints')?></td>
		</tr>
		<tr>
			<td>
				<?php echo $form->checkbox('akLimitDownloadTime', 1, $this->akLimitDownloadTime)?>
				<?php echo t('Limit time download is available after purchase?')?>
				<div id="ak-file-download-days" style="margin: 2px 2px 2px 25px; <?php  echo ($this->akLimitDownloadTime?'':'display:none;')?>">
					<?php  echo $form->text('akLimitDownloadDays', $this->akLimitDownloadDays, array('size'=>4));	?> <?php  echo t('Days Available')?>
				</div>
			</td>	
		</tr>
		</table>
		<?php 		
	}
	
	
}
<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $root_directory, $current_user;
require_once 'Smarty_setup.php';
require_once 'include/Webservices/Create.php';

checkFileAccessForInclusion("modules/$currentModule/$currentModule.php");
require_once "modules/$currentModule/$currentModule.php";

if (isset($tool_buttons)==false) {
	$tool_buttons = Button_Check($currentModule);
}

$focus = new $currentModule();
$list_buttons=$focus->getListButtons($app_strings, $mod_strings);

$smarty = new vtigerCRM_Smarty();

$smarty->assign('CUSTOM_MODULE', $focus->IsCustomModule);

$smarty->assign('MOD', $mod_strings);
$smarty->assign('APP', $app_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', getTranslatedString('SINGLE_'.$currentModule));
$smarty->assign('BUTTONS', $list_buttons);
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");

$imgdir = $root_directory.vtlib_purify($_REQUEST['imgdir']);
if (!is_dir($imgdir)) {
	$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-error');
	$smarty->assign('ERROR_MESSAGE', getTranslatedString('Invalid Directory'));
	$smarty->display('applicationmessage.tpl');
} else {
	$smarty->display('Buttons_List.tpl');
	echo '<div class="slds-m-around_large">';
	$focus->column_fields['wcpitype'] = 'Image';
	$focus->column_fields['wcpistatus'] = 'Published';
	$focus->column_fields['wcpialt'] = '';
	$focus->column_fields['wcpicaption'] = '';
	$focus->column_fields['assigned_user_id'] = vtws_getEntityId('Users').'x'.vtlib_purify($_REQUEST['assignto']);
	$_REQUEST['assigntype'] = 'U';
	$pdocrmEntityTable = CRMEntity::getcrmEntityTableAlias('Products');
	$pdoQuery = 'select productid from vtiger_products inner join '.$pdocrmEntityTable.' on crmid=productid where deleted=0 and ref_fornitore=?';
	$wsPdo = vtws_getEntityId('Products').'x';

	echo '<br><H2>'.getTranslatedString('Procesando Ficheros', 'wcProductImage').'</H2><br>';
	$filegroup = '';
	$fileidx = 1;
	if (!is_dir($imgdir.'/processed')) {
		mkdir($imgdir.'/processed');
	}
	$files = scandir($imgdir);
	$numfiles = 0;
	foreach ($files as $filename) {
		if ($filename=='.' || $filename=='..' || !is_file($imgdir.'/'.$filename)) {
			continue;
		}
		//change DPI of image to 300x300
		// $absFIlename = $imgdir.'/'.$filename;
		// $image = new Imagick($absFIlename);
		// $getImageRes = $image->getImageResolution();
		// $x = $getImageRes['x'];
		// $y = $getImageRes['y'];
		// if ($x != 300 || $y != 300) {
		// 	$finfo = finfo_open(FILEINFO_MIME_TYPE);
		// 	$ext = finfo_file($finfo, $absFIlename);
		// 	$image->setImageUnits(2);
		// 	$image->setImageResolution(300, 300);
		// 	if ($ext == 'image/jpeg' || $ext == 'image/jpg') {
		// 		$image->setImageFormat('jpg');
		// 	} else {
		// 		$image->setImageFormat('png');
		// 	}
		// 	file_put_contents($absFIlename, $image); //put new images to folder for importing
		// 	finfo_close($finfo);
		// }
		$fname = pathinfo($filename, PATHINFO_FILENAME);
		$fext = pathinfo($filename, PATHINFO_EXTENSION);
		if (strpos($fname, '-')>0) {
			list($pdocode, $vname) = explode('-', $fname);
			$pdo = $adb->pquery($pdoQuery, array($pdocode));
		} else {
			$pdo = false;
		}
		if ($pdo && $adb->num_rows($pdo)>0) {
			$focus->column_fields['wcpirelated'] = $wsPdo.$adb->query_result($pdo, 0, 'productid');
			$focus->column_fields['wcpiname'] = $pdocode;
			$focus->column_fields['wcpimage'] = $pdocode.'.'.$fext;
			$focus->id = 0;
			$focus->mode = '';
			$model_filename=array(
				'name'=>$pdocode.'.'.$fext,  // no slash nor paths in the name
				'size'=>filesize($imgdir.'/'.$filename),
				'type'=> mime_content_type($imgdir.'/'.$filename),
				'content'=>base64_encode(file_get_contents($imgdir.'/'.$filename)),
			);
			$focus->column_fields['attachments'] = array(
				'wcpimage' => $model_filename,
			);
			vtws_create('wcProductImage', $focus->column_fields, $current_user);
			rename($imgdir.'/'.$filename, $imgdir.'/processed/'.$filename);
			echo '<span style="color:green;"><b>'.$imgdir.'/'.$filename.'</b> '.getTranslatedString('processed', 'wcProductImage').'.</span><br>';
		} else { // Not found, can't upload
			echo '<span style="color:red;"><b>'.$imgdir.'/'.$filename.'</b> '.getTranslatedString('notFound', 'wcProductImage').'</span><br>';
		}
		$numfiles++;
	}
	if ($numfiles==0) {
		echo '<br/><span style="color:red;">'.getTranslatedString('noneFound', 'wcProductImage').'</span><br/><br/>';
	}
	echo '<br/>'.getTranslatedString('Proceso terminado', 'wcProductImage').'.&nbsp;&nbsp;';
	echo '<a href="index.php?module=wcProductImage&action=index">'.getTranslatedString('LBL_GOBACK_BUTTON_LABEL', 'wcProductImage').'</a><br/><br/>';
	echo '</div>';
}
?>
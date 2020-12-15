<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $app_strings, $mod_strings, $current_language, $currentModule, $theme;
require_once 'Smarty_setup.php';

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
$smarty->assign('AUSERS', get_user_array(false, 'Active', $current_user->id));

$smarty->display('modules/wcProductImage/ImportDir.tpl');
?>
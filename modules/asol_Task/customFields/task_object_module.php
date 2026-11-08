<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

global $current_user, $mod_strings, $app_strings, $timedate, $app_list_strings, $db;

$focus = new asol_task();
$focus->retrieve($_REQUEST['record']);

$task_type = (isset($_REQUEST['task_type'])) ? $_REQUEST['task_type'] : $focus->task_type;


$triggerModule = $focus->getContextModule();
$tmpMod = json_decode(htmlspecialchars_decode($focus->task_implementation));

$mod = (!empty($tmpMod->module)) ? $tmpMod->module: "";
$objModule = (isset($_REQUEST['objectModule'])) ? $_REQUEST['objectModule'] : $mod;


$objectModule = ($task_type == "modify_object") ? $triggerModule : $objModule;

//$urlRedirect = "./index.php?module=".$_REQUEST['module']."&action=".$_REQUEST['action']."&return_module=".$_REQUEST['return_module']."&return_action=".$_REQUEST['return_action'];

//Obtener los m?dulo a los que tiene acceso el usuario activo
$acl_modules = ACLAction::getUserActions($current_user->id);

echo "<input type='hidden' id='triggerModule' value='".$triggerModule."'>";

if ($task_type == "create_object") 
	$selectModules = "<select id='objectModule' name='objectModule' onChange='this.form.module.value=\"".$_REQUEST['module']."\"; this.form.action.value=\"".$_REQUEST['action']."\"; this.form.submit();'>";
else if ($task_type == "modify_object")
	$selectModules = "<select id='objectModule' name='objectModule' disabled onChange='this.form.module.value=\"".$_REQUEST['module']."\"; this.form.action.value=\"".$_REQUEST['action']."\"; this.form.submit();'>";	

$selectModules .= "<option value=''></option>";

foreach($acl_modules as $key=>$mod){

	if($mod['module']['access']['aclaccess'] >= 0)
		$selectModules .= ($objectModule == $key) ? "<option value='".$key."' selected>".$app_list_strings['moduleList'][$key]."</option>" : "<option value='".$key."'>".$app_list_strings['moduleList'][$key]."</option>";

}

$selectModules .= "</select>";


echo $selectModules;

?>

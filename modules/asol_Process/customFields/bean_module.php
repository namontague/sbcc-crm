<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');


global $current_user, $mod_strings, $app_strings, $timedate, $app_list_strings, $db;

$focus = new asol_Process();
$focus->retrieve($_REQUEST['record']);

$triggerModuleTmp = $focus->bean_module;
$triggerModule = (isset($_REQUEST['triggerModule'])) ? $_REQUEST['triggerModule'] : $triggerModuleTmp;

//$urlRedirect = "./index.php?module=".$_REQUEST['module']."&action=".$_REQUEST['action']."&return_module=".$_REQUEST['return_module']."&return_action=".$_REQUEST['return_action'];

//Obtener los m?dulo a los que tiene acceso el usuario activo
$acl_modules = ACLAction::getUserActions($current_user->id);

$selectModules = "<select id='triggerModule' name='triggerModule' onChange='this.form.module.value=\"".$_REQUEST['module']."\"; this.form.action.value=\"".$_REQUEST['action']."\"; this.form.event_conditions.value=\"\"; this.form.submit();'>";
$selectModules .= "<option value=''></option>";

foreach($acl_modules as $key=>$mod){

	if($mod['module']['access']['aclaccess'] >= 0)
		$selectModules .= ($triggerModule == $key) ? "<option value='".$key."' selected>".$app_list_strings['moduleList'][$key]."</option>" : "<option value='".$key."'>".$app_list_strings['moduleList'][$key]."</option>";

}

$selectModules .= "</select>";

echo $selectModules;

?>

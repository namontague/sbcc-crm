<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

global $current_user, $mod_strings, $app_strings, $timedate, $app_list_strings, $db;

$focus = new asol_Events();
$focus->retrieve($_REQUEST['record']);


$triggerEventTmp = explode(" - ", $focus->trigger_event);

$triggerModule = (isset($_REQUEST['triggerModule'])) ? $_REQUEST['triggerModule'] : $triggerEventTmp[0];
$triggerEvent = (isset($_REQUEST['triggerEvent'])) ? $_REQUEST['triggerEvent'] : $triggerEventTmp[1];

//Obtenemos los posibles eventos sobre el modulo seleccionado
$selectEvents = "<select id='triggerEvent' name='triggerEvent'>";

//if ((!empty($triggerModule))) {

	$selectEvents .= ($triggerEvent == "on_create") ? "<option value='on_create' selected>".$mod_strings['LBL_ASOL_ON_CREATE']."</option>" : "<option value='on_create'>".$mod_strings['LBL_ASOL_ON_CREATE']."</option>";
	$selectEvents .= ($triggerEvent == "on_modify") ? "<option value='on_modify' selected>".$mod_strings['LBL_ASOL_ON_MODIFY']."</option>" : "<option value='on_modify'>".$mod_strings['LBL_ASOL_ON_MODIFY']."</option>";
	$selectEvents .= ($triggerEvent == "on_modify__before_save") ? "<option value='on_modify__before_save' selected>".$mod_strings['LBL_ASOL_ON_MODIFY_BEFORE_SAVE']."</option>" : "<option value='on_modify__before_save'>".$mod_strings['LBL_ASOL_ON_MODIFY_BEFORE_SAVE']."</option>";
	$selectEvents .= ($triggerEvent == "on_delete") ? "<option value='on_delete' selected>".$mod_strings['LBL_ASOL_ON_DELETE']."</option>" : "<option value='on_delete'>".$mod_strings['LBL_ASOL_ON_DELETE']."</option>";

//}

/*
if ($triggerModule == "Users") {
	
	$selectEvents .= ($triggerEvent == "before_login") ? "<option value='before_login' selected>Before Login</option>" : "<option value='before_login'>Before Login</option>";
	$selectEvents .= ($triggerEvent == "after_login") ? "<option value='after_login' selected>After Login</option>" : "<option value='after_login'>After Login</option>";
	$selectEvents .= ($triggerEvent == "before_logout") ? "<option value='before_logout' selected>Before Logout</option>" : "<option value='before_logout'>Before Logout</option>";
	$selectEvents .= ($triggerEvent == "after_logout") ? "<option value='after_logout' selected>After Logout</option>" : "<option value='after_logout'>After Logout</option>";
	
}
*/

$selectEvents .= "</select>";

echo $selectEvents;


?>

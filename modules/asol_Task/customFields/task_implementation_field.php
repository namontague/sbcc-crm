<?php
global $mod_strings;

if ($focus->task_type == "php_custom")
	$focus->task_implementation = htmlspecialchars($focus->task_implementation, ENT_QUOTES, "UTF-8");

	
$focus = new asol_task();
$focus->retrieve($_REQUEST['record']);

$task_type = (isset($_REQUEST['task_type'])) ? $_REQUEST['task_type'] : $focus->task_type;

$triggerModule = $focus->getContextModule();
$tmpMod = json_decode(htmlspecialchars_decode($focus->task_implementation));

$mod = (!empty($tmpMod->module)) ? $tmpMod->module: "";
	


if (($mod == $_REQUEST['objectModule']) || (empty($_REQUEST['objectModule'])))
	echo "<body onload=\"ASOLTask.selectTaskType('task_type', '".$focus->task_type."'); ASOLTask.loadImplementationField('".$focus->task_type."', '".$focus->task_implementation."');\"'>";
else
	echo "<body onload=\"ASOLTask.selectTaskType('task_type', '".$focus->task_type."');\">";

	
	if (($_REQUEST['task_type'] != "create_object") && ($_REQUEST['task_type'] != "modify_object")) 
		echo '<div style="display: block" id="task_implementation_field">  </div>';
	else
		echo '<div style="display: none" id="task_implementation_field">  </div>';
	
		
	if (($_REQUEST['task_type'] != "create_object") && ($_REQUEST['task_type'] != "modify_object")) 
		echo '<div style="display: none" id="task_implementation_field_create_object"><table class="edit view"><tr>';
	else 
		echo '<div style="display: block" id="task_implementation_field_create_object"><table class="edit view"><tr>';
	echo '<tr><td>'.$mod_strings['LBL_ASOL_OBJECT_MODULE'].': ';
	require_once ("modules/asol_Task/customFields/task_object_module.php");
	echo '</td><td></td></tr>';
	echo '<tr><td>';	
	require_once ("modules/asol_Task/customFields/module_fields.php");
	echo '</td><td>';
	require_once ("modules/asol_Task/customFields/module_object_values.php");
	echo '</td>';
	echo '</tr></table></div>';
	
echo "</body>";
	
?>

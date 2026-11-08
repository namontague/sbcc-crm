<?php


global $beanList, $beanFiles, $app_list_strings, $timedate, $db, $mod_strings;

/*Get the call status options*/
$calls_class_name = $beanList["Calls"];
require_once($beanFiles[$calls_class_name]);
$call = new $calls_class_name();
$calls_field_defs = $call->field_defs;
	
if ($calls_field_defs['status']['options'] != '') {

	if (count($app_list_strings[$calls_field_defs['status']['options']]) == 0)
	$app_list_strings[$calls_field_defs['status']['options']] = Array();

	$call_status_opts = implode('|', $app_list_strings[$calls_field_defs['status']['options']]);

} else if ($calls_field_defs['status']['function'] != '') {

	$call_status_opts = implode('|', $calls_field_defs['status']['function']());

}


/*Get the task status options*/
$tasks_class_name = $beanList["Tasks"];
require_once($beanFiles[$tasks_class_name]);
$task = new $tasks_class_name();
$tasks_field_defs = $task->field_defs;
	
if ($tasks_field_defs['status']['options'] != '') {

	if (count($app_list_strings[$tasks_field_defs['status']['options']]) == 0)
	$app_list_strings[$tasks_field_defs['status']['options']] = Array();

	$task_status_opts = implode('|', $app_list_strings[$tasks_field_defs['status']['options']]);

} else if ($tasks_field_defs['status']['function'] != '') {

	$task_status_opts = implode('|', $tasks_field_defs['status']['function']());

}

if ($tasks_field_defs['priority']['options'] != '') {

	if (count($app_list_strings[$tasks_field_defs['priority']['options']]) == 0)
	$app_list_strings[$tasks_field_defs['priority']['options']] = Array();

	$task_priority_opts = implode('|', $app_list_strings[$tasks_field_defs['priority']['options']]);

} else if ($tasks_field_defs['priority']['function'] != '') {

	$task_priority_opts = implode('|', $tasks_field_defs['priority']['function']());

}



$users_opts = array();
$users_query = $db->query("SELECT id, user_name FROM users WHERE deleted=0");
while ($users_row = $db->fetchByAssoc($users_query)) {
		$users_opts[$users_row['id']] = $users_row['user_name'];
}

$acl_roles_opts = array();
$acl_roles_query = $db->query("SELECT id, name FROM acl_roles WHERE deleted=0");
while ($acl_roles_row = $db->fetchByAssoc($acl_roles_query)) {
		$acl_roles_opts[$acl_roles_row['id']] = $acl_roles_row['name'];
}
while (!empty($acl_roles_row));
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


/*Get the task status options*/

$task_type = isset($_REQUEST['task_type']) ? $_REQUEST['task_type'] : $focus->task_type;

// peticion sql para saber si es send_email

$select_task_type_field = "<select name='task_type' id='task_type'>";

//$select_task_type_field .= "<option value=''></option>";
foreach ($app_list_strings['wfm_task_type_list'] as $key=>$task_type_field) {
	$select_task_type_field .= ($task_type == $key) ? "<option value='". $key ."' selected> ". $task_type_field ." </option>" : "<option value='". $key ."'> ". $task_type_field ." </option>";
}
$select_task_type_field .= "</select>";
echo $select_task_type_field;

echo "<script>
    $(document).ready(function() {
      ASOLTask.users=".json_encode($users_opts).";
      ASOLTask.aclRoles=".json_encode($acl_roles_opts).";
      $('#task_type').change(function(){
        var val = $(this).val();
        if (val != 'create_object' && val != 'modify_object') {
          ASOLTask.selectTaskType('task_type', null);
          if (val=='".$focus->task_type."') {
            rememberImplementationField('".$focus->task_type."', '".$focus->task_implementation."');
          }
          $('#task_implementation_field').show();
          $('#task_implementation_field_create_object').hide();
          document.getElementById('task_implementation_field').style.display='block';
          document.getElementById('task_implementation_field_create_object').style.display='none';
        } else {
          var form = $(this).closest('form');
          form.find('[name=\"action\"]').val('EditView');
          form.submit();
        }
      });
    });
    </script>
";


?>

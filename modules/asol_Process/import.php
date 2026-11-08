<?php

global $db;



$name = $_FILES['imported_process']['name'];
$tmpName = $_FILES['imported_process']['tmp_name'];
//$name = "imported_process";



//guardamos el archivo a la carpeta files
$target =  getcwd()."/modules/asol_Process/temp_Imported_Files/".$name."_".time().".txt";

copy($tmpName,$target);

$descriptor = fopen($target, "r");

$serialized_process = fread($descriptor, filesize($target));
$imported_process = unserialize($serialized_process);

fclose($descriptor);
unlink($target);


$GLOBALS['log']->debug("*********************** ASOL:  FINAL \$imported_process=[".print_r($imported_process,true)."]");

////////////////////////////////////////
/*
 global $db;

 $db->query ("
 INSERT INTO asol_Process (id 	name 	date_entered 	date_modified 	modified_user_id 	created_by 	description 	deleted 	assigned_user_id 	status )
 VALUES ('".create_guid()."', '".$imported_process['process']['name']."', '".$imported_process['process']['date_entered']."', '".$imported_process['process']['date_modified']."', '".$imported_process['process']['modified_user_id']."', '".$imported_process['process']['created_by']."', '".$imported_process['process']['description']."', '".$imported_process['process']['deleted']."', '".$imported_process['process']['assigned_user_id']."', '".$imported_process['process']['status']."')
 ");

 */

$old_ids__and__new_ids__process__array = Array();

require_once('modules/asol_Process/asol_Process.php');
foreach ($imported_process['processes'] as $key_process => $value_process) {
	$focus_process = new asol_Process();
	$focus_process->name 				= $value_process['name']."_copy";
	$focus_process->date_entered 		= $value_process['date_entered'];
	//$focus_process->date_modified 		= $value_process['date_modified'];//*************ni le hace caso, debe ponerlo en el save()
	$focus_process->modified_user_id 	= $value_process['modified_user_id'];
	$focus_process->created_by 			= $value_process['created_by'];
	$focus_process->description 		= $value_process['description'];
	$focus_process->deleted 			= $value_process['deleted'];
	$focus_process->assigned_user_id 	= $value_process['assigned_user_id'];
	$focus_process->status 				= $value_process['status'];
	$focus_process->bean_module			= $value_process['bean_module'];
	$new_process_id 					= $focus_process->save();

	$old_ids__and__new_ids__process__array[$value_process['id']] = $new_process_id;
}
//////////////////////////////////////////////////////////////////////


$old_ids__and__new_ids__event__array = Array();

require_once('modules/asol_Events/asol_Events.php');
foreach ($imported_process['events'] as $key_parent_process => $value_parent_process) {
	foreach ($value_parent_process as $key_event => $value_event) {
		// id 	name 	date_entered 	date_modified 	modified_user_id 	created_by 	description 	deleted 	assigned_user_id 	type 	trigger_type 	trigger_event 	condition_event
		$focus_event = new asol_Events();
		$focus_event->name 				= $value_event['name']."_copy";
		$focus_event->date_entered 		= $value_event['date_entered'];
		//$focus_event->date_modified	 	= $value_event['date_modified'];
		$focus_event->modified_user_id 	= $value_event['modified_user_id'];
		$focus_event->created_by 		= $value_event['created_by'];
		$focus_event->description 		= $value_event['description'];
		$focus_event->deleted 			= $value_event['deleted'];
		$focus_event->assigned_user_id 	= $value_event['assigned_user_id'];
		$focus_event->type 				= $value_event['type'];
		$focus_event->trigger_type 		= $value_event['trigger_type'];
		$focus_event->trigger_event 	= $value_event['trigger_event'];
		$focus_event->condition_event 	= $value_event['condition_event'];
		$focus_event->asol_process_id 	= $old_ids__and__new_ids__process__array[$key_parent_process];
		$new_event_id = $focus_event->save();

		$old_ids__and__new_ids__event__array[$value_event['id']] = $new_event_id;
	}
}
////////////////////////////////////////////////////////////////////


$old_ids__and__new_ids__activity__array = Array();

require_once('modules/asol_Activity/asol_Activity.php');
foreach ($imported_process['activities'] as $key_parent_event => $value_parent_event) {
	foreach ($value_parent_event as $key_activity => $value_activity) {

		$GLOBALS['log']->debug("*********************** ASOL: \$old_ids__and__new_ids__activity__array=[".print_r($old_ids__and__new_ids__activity__array,true)."]");
		if (!array_key_exists($value_activity['id'], $old_ids__and__new_ids__activity__array)) {	// Event duplicity.
			// id 	name 	date_entered 	date_modified 	modified_user_id 	created_by 	description 	deleted 	assigned_user_id 	condition_activity 	delay
			$focus_activity = new asol_Activity();
			$focus_activity->name 				= $value_activity['name']."_copy";
			$focus_activity->date_entered 		= $value_activity['date_entered'];
			//$focus_activity->date_modified 		= $value_activity['date_modified'];
			$focus_activity->modified_user_id 	= $value_activity['modified_user_id'];
			$focus_activity->created_by 		= $value_activity['created_by'];
			$focus_activity->description 		= $value_activity['description'];
			$focus_activity->deleted 			= $value_activity['deleted'];
			$focus_activity->assigned_user_id 	= $value_activity['assigned_user_id'];
			$focus_activity->condition_activity = $value_activity['condition_activity'];
			$focus_activity->delay 				= $value_activity['delay'];
			$new_activity_id 					= $focus_activity->save();

			$old_ids__and__new_ids__activity__array[$value_activity['id']] = $new_activity_id;
		}
		else {
			$GLOBALS['log']->debug("*********************** ASOL: Event duplicity");
		}
		////////////////
		//asol_events_asol_activity			id 	date_modified 	deleted 	asol_events_id 	asol_activity_id

		$db->query("
						INSERT INTO asol_events_asol_activity
						VALUES ('".create_guid()."', '".gmdate('Y-m-d H:i:s')."', 0, '".$old_ids__and__new_ids__event__array[$key_parent_event]."', '".$new_activity_id."')
					");


	}
}
////////////////////////////////////////////////////////////////////


//$old_ids__and__new_ids__next_activity__array = Array();        ----------> activities and next_activities in the same array

require_once('modules/asol_Activity/asol_Activity.php');
foreach ($imported_process['next_activities'] as $key_parent_activity => $value_parent_activity) {
	foreach ($value_parent_activity as $key_next_activity => $value_next_activity) {
		// id 	name 	date_entered 	date_modified 	modified_user_id 	created_by 	description 	deleted 	assigned_user_id 	condition_activity 	delay
		$focus_next_activity = new asol_Activity();
		$focus_next_activity->name 					= $value_next_activity['name']."_copy";
		$focus_next_activity->date_entered 			= $value_next_activity['date_entered'];
		//$focus_next_activity->date_modified 		= $value_next_activity['date_modified'];
		$focus_next_activity->modified_user_id 		= $value_next_activity['modified_user_id'];
		$focus_next_activity->created_by 			= $value_next_activity['created_by'];
		$focus_next_activity->description 			= $value_next_activity['description'];
		$focus_next_activity->deleted 				= $value_next_activity['deleted'];
		$focus_next_activity->assigned_user_id 		= $value_next_activity['assigned_user_id'];
		$focus_next_activity->condition_activity 	= $value_next_activity['condition_activity'];
		$focus_next_activity->delay 				= $value_next_activity['delay'];
		$focus_next_activity->predecessor_id	= $old_ids__and__new_ids__activity__array[$key_parent_activity];
		$new_next_activity_id 						= $focus_next_activity->save();

		$old_ids__and__new_ids__activity__array[$value_next_activity['id']] = $new_next_activity_id;
	}
}
////////////////////////////////////////////////////////////////////


//$old_ids__and__new_ids__activity__array = Array();        -------------> Tasks have no children

require_once('modules/asol_Task/asol_Task.php');
foreach ($imported_process['tasks'] as $key_parent_activity => $value_parent_activity) {
	foreach ($value_parent_activity as $key_task => $value_task) {
		// id 	name 	date_entered 	date_modified 	modified_user_id 	created_by 	description 	deleted 	assigned_user_id 	delay_type 	delay 	task_type 	order_task 	task_implementation
		$focus_task = new asol_Task();
		$focus_task->name 					= $value_task['name']."_copy";
		$focus_task->date_entered 			= $value_task['date_entered'];
		//$focus_task->date_modified 		= $value_task['date_modified'];
		$focus_task->modified_user_id 		= $value_task['modified_user_id'];
		$focus_task->created_by 			= $value_task['created_by'];
		$focus_task->description 			= $value_task['description'];
		$focus_task->deleted 				= $value_task['deleted'];
		$focus_task->assigned_user_id 		= $value_task['assigned_user_id'];
		$focus_task->delay_type 			= $value_task['delay_type'];
		$focus_task->delay 					= $value_task['delay'];
		$focus_task->task_type 				= $value_task['task_type'];
		$focus_task->order_task 			= $value_task['order_task'];
		$focus_task->task_implementation 	= $value_task['task_implementation'];
    $focus_task->asol_activity_id = $old_ids__and__new_ids__activity__array[$key_parent_activity];
		$new_task_id 						= $focus_task->save();

		//$old_ids__and__new_ids__activity__array[$value_activity['id']] = $new_activity_id;
	}
}
////////////////////////////////////////////////////////////////////


echo "The asol_Processes have been imported."
?>

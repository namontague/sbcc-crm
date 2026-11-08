<?php
global $db;

$GLOBALS['log']->debug("*********************** ASOL: ENTRY export.php");



$GLOBALS['log']->debug("*********************** ASOL: ENTRY POINT \$_REQUEST=[".print_r($_REQUEST,true)."]");




///////////////////////////////////////
//ASOL Custom ------------------> DANY
function getChildDomains($domainId, $childs=array()){

	$GLOBALS['log']->debug("ASOL*****************************************************Executing getChildDomains function");

	global $db;
	$child_query = $db->query("SELECT id, asol_domain_enabled FROM asol_domains WHERE asol_domains_id_c='".$domainId."' AND deleted=0");

	while($queryChildRow = $db->fetchByAssoc($child_query)) {
		$childs[count($childs)]['id'] = $queryChildRow['id'];
		$childs[count($childs)-1]['enabled'] = $queryChildRow['asol_domain_enabled'];
		$this->getChildDomains($queryChildRow['id'], & $childs);
	}

	return $childs;
}
/////////////////////////////////////




///////////////////////////////////////
///////////////////////////////////////
///////////////////////////////////////

$export_array = Array();

//$process_id = $_REQUEST['process_id'];//////////////*************para el export_button.php

// extract process_ids from $_REQUEST
$process_ids_array = explode(',', $_REQUEST['uid']);

foreach($process_ids_array as $key_process_id => $value_process_id) {
	$process_query = $db->query ("
									SELECT *
									FROM asol_process
									WHERE id = '".$value_process_id."'
								");
	$process_row = $db->fetchByAssoc($process_query);

	$export_array['processes'][] = $process_row;
}

$GLOBALS['log']->debug("*********************** ASOL: 1 FINAL \$export_array=[".print_r($export_array,true)."]");
///////////////////////////////


// search for events
foreach ($export_array['processes'] as $key_process => $value_process) {

	$event_ids_from_process__array = Array();
	$event_ids_from_process__query = $db->query("
													SELECT id AS event_id
													FROM asol_events
													WHERE asol_process_id= '".$value_process['id']."' AND deleted = 0
												");
	while ($event_ids_from_process__row = $db->fetchByAssoc($event_ids_from_process__query)) {
		$event_ids_from_process__array[] = $event_ids_from_process__row['event_id'];
	}
	//////////////
	foreach ($event_ids_from_process__array as $key_event_id => $value_event_id) {
		//$export_array['process']['event_ids'][] = $value_event_id;

		$event_query = $db->query ("
										SELECT *
										FROM asol_events
										WHERE id = '".$value_event_id."'
									");
		$event_row = $db->fetchByAssoc($event_query);

		$export_array['events'][$value_process['id']][] = $event_row;
	}
}
$GLOBALS['log']->debug("*********************** ASOL: 2 FINAL \$export_array=[".print_r($export_array,true)."]");
////////////////////////////////



// search for activities

foreach ($export_array['events'] as $key_parent_process => $value_parent_process) {
	foreach ($value_parent_process as $key_event => $value_event) {
		$activity_ids_from_event__array = Array();
		$activity_ids_from_event__query = $db->query("
														SELECT asol_activity_id AS activity_id
														FROM asol_events_asol_activity
														WHERE asol_events_id= '".$value_event['id']."' AND deleted = 0
													");

		while ($activity_ids_from_event__row = $db->fetchByAssoc($activity_ids_from_event__query)) {
			$activity_ids_from_event__array[] = $activity_ids_from_event__row['activity_id'];
		}

		foreach ($activity_ids_from_event__array as $key_activity_id => $value_activity_id) {
			//$export_array['events'][$key_event]['activity_ids'][] = $value_activity_id;

			$activity_query = $db->query ("
											SELECT *
											FROM asol_activity
											WHERE id = '".$value_activity_id."'
										");
			$activity_row = $db->fetchByAssoc($activity_query);

			$export_array['activities'][$value_event['id']][] = $activity_row;
			//$GLOBALS['log']->debug("*********************** ASOL: 3 part \$export_array=[".print_r($export_array,true)."]");
		}
	}
}
$GLOBALS['log']->debug("*********************** ASOL: 3 FINAL \$export_array=[".print_r($export_array,true)."]");
///////////////////


// search for next_activities from activities(from events)
function getNextActivities($activity_id, $next_activities=array()){ // recursive

	$GLOBALS['log']->debug("****************ASOL: Executing getNextActivities function");

	global $db;
	$next_activities_query = $db->query("
		                    SELECT id as next_activity_id
		                    FROM asol_activity
		                    WHERE predecessor_id='".$activity_id."' AND deleted = 0
										");

	while($next_activities_row = $db->fetchByAssoc($next_activities_query)) {
		$next_activities[] = $next_activities_row['next_activity_id'];

		getNextActivities($next_activities_row['next_activity_id'], & $next_activities);
	}

	return $next_activities;
}


$activity_ids = Array();

foreach ($export_array['activities'] as $key_parent_event => $value_parent_event) {
	foreach ($value_parent_event as $key_activity => $value_activity) {

		$GLOBALS['log']->debug("*********************** ASOL: \$activity_ids=[".print_r($activity_ids,true)."]");
		if (!in_array($value_activity['id'], $activity_ids)) {	// Event duplicity.

			$next_activity_ids_all_tree = getNextActivities($value_activity['id']);

			$GLOBALS['log']->debug("****************ASOL: \$next_activity_ids_all_tree".print_r($next_activity_ids_all_tree,true));

			foreach($next_activity_ids_all_tree as $key => $value) {
				//$export_array['activities'][$key_activity]['next_activity_ids'][] = $value;

				$next_activity_query = $db->query ("
													SELECT *
													FROM asol_activity
													WHERE id = '".$value."'
												");
				$next_activity_row = $db->fetchByAssoc($next_activity_query);

				$export_array['next_activities'][$next_activity_row['predecessor_id']][] = $next_activity_row;
			}
			
			$activity_ids[] = $value_activity['id'];
		}
		else {
			$GLOBALS['log']->debug("*********************** ASOL: Event duplicity");
		}
	}
}
$GLOBALS['log']->debug("*********************** ASOL: 4 FINAL \$export_array=[".print_r($export_array,true)."]");
////////////////////////////////////////////


// search for tasks from activities(from events)
foreach ($export_array['activities'] as $key_parent_event => $value_parent_event) {

	foreach($value_parent_event as $key_activity => $value_activity) {

		$task_ids_from_activity__array = Array();
		$task_ids_from_activity__query = $db->query("
													SELECT id AS task_id
													FROM asol_task
													WHERE asol_activity_id  = '".$value_activity['id']."' AND deleted = 0
												");
		while ($task_ids_from_activity__row = $db->fetchByAssoc($task_ids_from_activity__query)) {
			$task_ids_from_activity__array[] = $task_ids_from_activity__row['task_id'];
		}

		foreach ($task_ids_from_activity__array as $key_task_id => $value_task_id) {
			//$export_array['activities'][$key_activity]['task_ids'][] = $value_task_id;

			$task_query = $db->query ("
										SELECT *
										FROM asol_task
										WHERE id = '".$value_task_id."'
									");
			$task_row = $db->fetchByAssoc($task_query);

			$export_array['tasks'][$value_activity['id']][] = $task_row;
		}
	}
}


$GLOBALS['log']->debug("*********************** ASOL: 5 FINAL \$export_array=[".print_r($export_array,true)."]");
//////////////////////////////////////////////

// search for tasks from activities(from next_activities)
foreach ($export_array['next_activities'] as $key_parent_activity => $value_array_next_activities) {

	foreach($value_array_next_activities as $key_activity => $value_activity) {

		$task_ids_from_activity__array = Array();
		$task_ids_from_activity__query = $db->query("
													SELECT id AS task_id
													FROM asol_task
													WHERE asol_activity_id  = '".$value_activity['id']."' AND deleted = 0
												");
		while ($task_ids_from_activity__row = $db->fetchByAssoc($task_ids_from_activity__query)) {
			$task_ids_from_activity__array[] = $task_ids_from_activity__row['task_id'];
		}

		foreach ($task_ids_from_activity__array as $key_task_id => $value_task_id) {
			//$export_array['next_activities'][$key_activity]['task_ids'][] = $value_task_id;

			$task_query = $db->query ("
										SELECT *
										FROM asol_task
										WHERE id = '".$value_task_id."'
									");
			$task_row = $db->fetchByAssoc($task_query);

			$export_array['tasks'][$value_activity['id']][] = $task_row;
		}
	}
}

$GLOBALS['log']->debug("*********************** ASOL: 6 FINAL \$export_array=[".print_r($export_array,true)."]");
///////////////////////////////



$serialized_export_array = serialize($export_array);

$exported_process = 'exported_processes';

header("Cache-Control: private");
header("Content-Type: application/octet-stream");
header('Content-Disposition: attachment; filename="'.$exported_process.'.txt"');
header("Content-Description: File Transfer");
header("Content-Transfer-Encoding: binary");
header("Content-Length: ".mb_strlen($serialized_export_array, '8bit'));
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Pragma: public");

ob_clean();
flush();

echo $serialized_export_array;
$GLOBALS['log']->debug("*********************** ASOL: EXIT export.php");

exit;



?>

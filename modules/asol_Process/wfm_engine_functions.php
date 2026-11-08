<?php

$wfm_bean_cache = array();

function wfm_load_bean($module, $id) {
  global $wfm_bean_cache;
  if( !is_array($wfm_bean_cache[$module])) {
    $wfm_bean_cache[$module] = array();
  }
  if(!isset($wfm_bean_cache[$module][$id])) {
    //get and retrieve the current record of the main module
    global $beanList, $beanFiles;
    $class_name = $beanList[$module];
    require_once($beanFiles[$class_name]);
    $bean = new $class_name();
    $wfm_bean_cache[$module][$id] = $bean->retrieve($id);
  }
  return $wfm_bean_cache[$module][$id];
}

function wfm_load_linked($bean, $field) {
  if( isset($bean->wfm_linked[$field])) {
    $linked =$bean->wfm_linked[$field];
  } else {
    global $beanList;
    $bean->load_relationship($field);
    $class_name = $beanList[$bean->$field->getRelatedModuleName()];
    $linked = $bean->get_linked_beans($field,$class_name);
    if(!is_array($bean->wfm_linked)) {
       $bean->wfm_linked = array();
    }
    $bean->wfm_linked[$field] = $linked;
  }
  return $linked;
}

function wfm_cache_bean($bean) {
  global $wfm_bean_cache;
  if( isset($bean) && isset($bean->id)) {
    $wfm_bean_cache[$bean->module_dir][$bean->id] = $bean;
  }
}

function initialize_processes($bean_module, $wfm_action, $bean_id, $current_user_id, $bean_ungreedy_count , $bean=null) {

	global $db;
	//global $current_user;
  wfm_cache_bean($bean);
	//$current_user_id = (isset($_REQUEST['current_user_id'])) ? $_REQUEST['current_user_id'] : "problem";

	$GLOBALS['log']->debug("*********************** ASOL: ENTRY wfm_engine_functions.php function initialize_processes ". "\$bean_id=[" . $bean_id ."] \$bean_module=[".$bean_module."] \$bean_ungreedy_count=[".$bean_ungreedy_count."]******************************
	*************************************************************************************************************************************************************************");


	// Get events with the right trigger_event.

	$trigger_module = $bean_module;
	$trigger_event = $wfm_action;
	$trigger = $trigger_module . " - " . $trigger_event;

	$event_query = $db->query("
								SELECT id, condition_event  
								FROM asol_events
								WHERE (trigger_event = '" . $trigger .  "' AND trigger_type='logic_hook' AND deleted = 0)							  
							  ");

	$events_let = Array(); // This array contains just events that are compliant to the trigger_event. Two fields: id, condition_event.
	while ($event_row = $db->fetchByAssoc($event_query,-1,false)) {
		//$GLOBALS['log']->debug("*********************** ASOL: inside while-event-trigger print event_row " . print_r($event_row,true) . "*******************************************");
		$events_let[] = $event_row;
	}
	$GLOBALS['log']->debug("*********************** ASOL: after while-event-trigger print events_let " . print_r($events_let,true) . "*******************************************");


	// Check conditions for each event. ( The bean?s characteristics have to match with the event?s conditions ).

	$events_condition_let = Array(); // This array contains just events that applies condition_event ( and trigger_event, of course ).
	foreach ($events_let as $key => $value) {
		//$GLOBALS['log']->debug("*********************** ASOL: inside foreach-event-conditions print value " . print_r($value,true) . "*******************************************");
		if ( appliesConditions($bean_module, $bean_id, $value['condition_event']) ) { // This function is used to know if the event applies conditions.
			$events_condition_let[] = $value['id'];
			//$GLOBALS['log']->debug("*********************** ASOL: inside foreach-event-conditions-if print value" . print_r($value,true) . " applies" . "*******************************************");
		}
		else {
			//$GLOBALS['log']->debug("*********************** ASOL: inside foreach-event-conditions-else print value " . print_r($value,true) . " NOT applies" . "*******************************************");
		}
	}
	$GLOBALS['log']->debug("*********************** ASOL: after foreach-event-conditions print events_condition_let " . print_r($events_condition_let,true) . "*******************************************");


	// For each event, we get its activities(all from the event).

	$events_activities_let = Array(); // [0->[event_idX, activity_id0],..., ]
	foreach ($events_condition_let as $key => $value) {
		$activity_query = $db->query("
										SELECT asol_events_id AS event_id, asol_activity_id AS activity_id
										FROM asol_events_asol_activity
										WHERE (asol_events_id = '" . $value . "' AND deleted = 0)
									 ");
		while ($activity_row = $db->fetchByAssoc($activity_query,-1,false)) {
			$events_activities_let[] = $activity_row;
		}
		//$GLOBALS['log']->debug("*********************** ASOL: events_activities_let-PARTY " . print_r($events_activities_let, true) . "value=" .$value . "*******************************************");

	}
	$GLOBALS['log']->debug("*********************** ASOL: events_activities_let-TOTAL " . print_r($events_activities_let, true) . "*******************************************");

	// Now, we are going to filter the activities by their conditions
	$processes_events_activities__filter_by_conditions__let = Array(); // [0->[process_idZY, event_idX, activity_id0, condition_activity], ... , n->[process_idYX, event_idZX, activity_idn, condition_activity]] <- This activities are all that applies all conditions.(!!!!!ojo dos eventos que referencien una misma actividad)
	foreach ($events_activities_let as $key => $value) {

		$condition_activity_query = $db->query("
													SELECT asol_process.id  AS process_id , asol_events.id AS event_id , asol_events.type AS event_type, asol_events.condition_event AS condition_event, asol_activity.id AS activity_id , asol_activity.condition_activity AS condition_activity
													FROM asol_activity
													INNER JOIN asol_events_asol_activity ON (asol_events_asol_activity.asol_activity_id = asol_activity.id AND asol_events_asol_activity.deleted = 0)
													INNER JOIN asol_events ON (asol_events.id = asol_events_asol_activity.asol_events_id AND asol_events.deleted = 0)
													INNER JOIN asol_process ON (asol_process.id = asol_events.asol_process_id AND asol_process.deleted = 0 AND asol_process.status = 'active')
													WHERE asol_activity.id = '" . $value['activity_id'] . "'
													ORDER BY asol_events.id
											   ");
		$condition_activity_row = $db->fetchByAssoc($condition_activity_query,-1,false);

		if ( appliesConditions($bean_module, $bean_id, $condition_activity_row['condition_activity']) ) { // This function is used to know if the activity applies conditions.
			if ($condition_activity_row != null) {
				$processes_events_activities__filter_by_conditions__let[] = $condition_activity_row;
			}
		}
	}
	$GLOBALS['log']->debug("*********************** ASOL: processes_events_activities__filter_by_conditions__let " . print_r($processes_events_activities__filter_by_conditions__let, true) . "*******************************************");


	/*
	 * It is turn to introduce the information into the tables($db).
	 * wfm_process_instance row    1--------->n    asol_workingnodes
	 * Information is intended to show how many activities have applied the conditions.
	 * There are to tables.
	 * 		1)asol_workingnodes stores in its rows which activity belongs to which process_instance.
	 * 		2)wfm_process_instance just stores the process_instance?s id and how many activities there are by process, i.e., the tokens.
	 */

	$already_stored_activities__event_duplicity = Array(); // Contains the activities that already have been stored in wfm_working_node. <- Two or more events can link one activity; we don?t want to execute the same activity twice or more times, just once.

	$MAX_process_instances__from_this_process_id__at_the_moment = '10'; // Maximum number of instances by process, at this precise moment in time.

	foreach ($processes_events_activities__filter_by_conditions__let as $key => $value) {

		////////////////////////////////////////
		if (number_of_process_instances__from_this_process_id__at_the_moment($value['process_id']) >= $MAX_process_instances__from_this_process_id__at_the_moment) {
			$GLOBALS['log']->debug("*********************** ASOL: MAX_process_instances__from_this_process_id");
			continue;
		}
		////////////////////////////////////////


		if (!(in_array($value['activity_id'], $already_stored_activities__event_duplicity))) {
			$already_stored_activities__event_duplicity[] = $value['activity_id'];
		}
		else {
			continue;
		}

		if (isProcessInstanceAlive($value['process_id'], $bean_id)) {
			
			$process_instance_id__query = $db->query("
																	SELECT id
																	FROM asol_processinstances
																	WHERE (asol_process_id_c = '".$value['process_id']."' AND bean_id = '".$bean_id."')
															   ");
			$process_instance_id__row = $db->fetchByAssoc($process_instance_id__query);
			$id1 = $process_instance_id__row['id'];

		}
		else {

			switch($value['event_type']) {
				case 'start':

					$id1 = create_guid();
					$name1 = "p_i_".$id1;
					//$process_instance_id = create_guid();
					$process_id = $value['process_id'];
					$parent_process_instance_id = 'null';

						
					$db->query("
								INSERT INTO asol_processinstances (id, name, asol_process_id_c, bean_id, bean_module, asol_processinstances_id_c, bean_ungreedy_count)
								VALUES ('".$id1."', '".$name1."', '".$process_id."', '".$bean_id."', '".$bean_module."', ".$parent_process_instance_id.", ".$bean_ungreedy_count.")
						   ");

					break;
				case 'intermediate':
					break;
				case 'cancel':
					break;
			}

		}



		switch($value['event_type']) {
			case 'start':
				$priority = 0;
				break;
			case 'intermediate':
				$priority = 2;// priority = 1 -> subprocess
				break;
			case 'cancel':
				$priority = 3;
				break;
		}

		$id2 = create_guid();
		$name2 = "w_n_".$id2;
		$date_entered = gmdate('Y-m-d H:i:s');
		$date_modified = gmdate('Y-m-d H:i:s');
		$current_activity = $value['activity_id'];
		$current_task = 'null';
		$delay_wakeup_time = '0000-00-00 00:00:00';
		$created_by = $current_user_id;
		$status = 'not_started';
		
		$db->query("
								INSERT INTO asol_workingnodes (id, name, asol_processinstances_id_c, priority, date_entered, date_modified, asol_activity_id_c, asol_task_id_c, delay_wakeup_time, created_by, status)
								VALUES ('".$id2."', '".$name2."', '".$id1."', ".$priority.", '".$date_entered."', '".$date_modified."', '".$current_activity."', ".$current_task.", '".$delay_wakeup_time."', '".$created_by."', '".$status."')
						   ");

	}

	$GLOBALS['log']->debug("*********************** ASOL: EXIT wfm_engine_functions.php function initialize_processes ". "\$bean_id=[" . $bean_id ."] \$bean_module=[".$bean_module."] \$bean_ungreedy_count=[".$bean_ungreedy_count."]******************************
	*************************************************************************************************************************************************************************");
	execute_WFM();

}


function number_of_process_instances__from_this_process_id__at_the_moment($process_id) {

	global $db;

	
	$instanceAliveQuery = $db->query("
										SELECT COUNT(id) AS counter 
										FROM asol_processinstances 
										WHERE asol_process_id_c='".$process_id."'  
										LIMIT 1
									");
	$instanceAliveRow = $db->fetchByAssoc($instanceAliveQuery);

	return $instanceAliveRow['counter'];
}


function isProcessInstanceAlive($process_id, $bean_id) { //This function checks if a given process_id and bean_id applies for an active process_instance

	global $db;

	$isAlive = false;

	
	$instanceAliveQuery = $db->query("
										SELECT COUNT(id) AS counter 
										FROM asol_processinstances 
										WHERE asol_process_id_c='".$process_id."' AND bean_id='".$bean_id."'  
										LIMIT 1
									");
	$instanceAliveRow = $db->fetchByAssoc($instanceAliveQuery);

	if ($instanceAliveRow['counter'] >= '1') {
		$isAlive = true;
	}

	return $isAlive;
}



function execute_WFM() {

	global $db, $current_user;

	//require_once ("modules/asol_Process/wfm_engine_functions.php");
	require_once ("modules/asol_Task/functions.php");


	do {
		//id, process_instance_id, priority, date_entered, date_modified, current_activity, current_task, delay_wakeup_time, created_by, status
		$working_node_query = $db->query("
												SELECT id, asol_processinstances_id_c, priority, date_entered, date_modified, asol_activity_id_c, asol_task_id_c, delay_wakeup_time, created_by, status
												FROM asol_workingnodes
												WHERE (   status = 'not_started' OR status = 'in_progress' OR (status='delayed' AND delay_wakeup_time <= '".gmdate('Y-m-d H:i:s')."')  )
												ORDER BY priority DESC
												LIMIT 1
											");
		$working_node_row = $db->fetchByAssoc($working_node_query,-1,false);

		if ($working_node_row != null) {
			//id, process_instance_id, process_id, bean_id, bean_module, parent_process_instance_id, bean__ungreedy_count
			$process_instance_query = $db->query("
														SELECT id, name, asol_process_id_c, bean_id, bean_module, asol_processinstances_id_c, bean_ungreedy_count
														FROM asol_processinstances
														WHERE (id = '".$working_node_row['asol_processinstances_id_c']."')
												     ");//****************process_instance_id se cambi? id al cambiar las instancias a modulos sugar
			$process_instance_row = $db->fetchByAssoc($process_instance_query,-1,false) ;



			execute_process($process_instance_row, $working_node_row);
		}

	} while ($working_node_row != null);

}


function execute_process( $process_instance, $node) { //function that executes a certain working_node from process.
	global $db;

	$GLOBALS['log']->debug("*********************** ASOL: ENTRY execute_process \$process_instance=[".print_r($process_instance,true)."] \$node=[".print_r($node,true)."]");


	$activityDelay = getActivityDelay($node['asol_activity_id_c']);
	$GLOBALS['log']->debug("*********************** ASOL:\$activityDelay= ".print_r($activityDelay,true));

	if ((empty($node['asol_task_id_c'])) && ($activityDelay) && ($node['status'] != 'delayed')) {//////***** si la task es null AND si hay delay para la current_activity AND estado del nodo != delayed
		$GLOBALS['log']->debug("*********************** ASOL: inside if-si la task es null AND si hay delay para la current_activity AND estado del nodo != delayed");

		
		$db->query("
								UPDATE asol_workingnodes 
								SET status='delayed', delay_wakeup_time='".calculateWakeUpDelay($activityDelay)."' 
								WHERE id='".$node['id']."'
						   ");

	}
	else {///////******* si la task no es null OR si no hay delay para la current_activity OR estado del nodo == delayed
		$GLOBALS['log']->debug("*********************** ASOL: inside else-si la task no es null OR si no hay delay para la current_activity OR estado del nodo == delayed");
		//Initialize nodes at in_progress status

		if ($node['status'] == 'delayed') {
			$node_is_delayed = true;
			$GLOBALS['log']->debug("*********************** ASOL:node_is_delayed=true");
		}
		else {
			$node_is_delayed = false;
			$GLOBALS['log']->debug("*********************** ASOL: node_is_delayed=false");
		}

		$GLOBALS['log']->debug("*********************** ASOL: \$node_is_delayed=[".$node_is_delayed."]");

		
		$db->query("
								UPDATE asol_workingnodes 
								SET status='executing' 
								WHERE id='".$node['id']."'
						   ");


		//Get tasks to execute for current node activity
		$tasksQuery = $db->query("
											SELECT asol_task.* 
											FROM asol_task 
											WHERE asol_activity_id='".$node['asol_activity_id_c']."' AND deleted = 0
											ORDER BY order_task
										 ");
		$tasks = Array();
		while ($rowTask = $db->fetchByAssoc($tasksQuery,-1,false)) {
			$tasks[] = $rowTask;
		}
		
		if (empty($tasks)) {	// To allow that an activity can have no tasks. Otherwise, it will not clean the database correctly.
			$GLOBALS['log']->debug("*********************** ASOL: There is no task in this activity");
			$db->query("
							UPDATE asol_workingnodes 
							SET status='terminated' 
							WHERE id='".$node['id']."'
						");
		}
			
			
		//Supress tasks that has been already executed based on $node['current_task']. If current_task is 'null', the current task will be the first task of the current activity
		$GLOBALS['log']->debug("*********************** ASOL: array_before_slice \$tasks= ".print_r($tasks,true)." \$node['asol_task_id_c']=".$node['asol_task_id_c']);
		//if ($node['current_task'] != null) {//////////*****!!!!!!!!!!!!!!!!!!!!!!!!! el null puede dar problemas... porque comparo con algo que sale de la base de datos!!!!!!!!!
		if ( !(empty($node['asol_task_id_c'])) )	 {
			$i=0;
			while ($tasks[$i]['id'] != $node['asol_task_id_c']) {
				$i++;
			}
			$tasks = array_slice($tasks, $i);

		}

		$GLOBALS['log']->debug("*********************** ASOL: Execute obtained tasks=".print_r($tasks,true));
		//Execute obtained tasks
		foreach ($tasks as $keyT=>$task) {
			$GLOBALS['log']->debug("*********************** ASOL: current_task = ".print_r($task,true));
			if ($node_is_delayed) { //************** para no recalcular el wakeup_delay_time de la primera tarea del nodo que estaba delayed
				$GLOBALS['log']->debug("*********************** ASOL:node_is_delayed = true");
				$taskDelayStartTime = false;
				$node_is_delayed = false;///////////*****************???????????
			}
			else {
				$GLOBALS['log']->debug("*********************** ASOL:node_is_delayed = false");
				$taskDelayStartTime = getTaskStartDelayTime($task['delay_type'], $task['delay'], $process_instance['bean_module'], $process_instance['bean_id']);
			}
			$GLOBALS['log']->debug("*********************** ASOL: \$taskDelayStartTime=".$taskDelayStartTime);
			if (!$taskDelayStartTime) { //If current task has no delay, execute it
				$GLOBALS['log']->debug("*********************** ASOL: If current task has no delay, execute it");
				$GLOBALS['log']->debug("*********************** ASOL: current_task that is going to be executed = ".print_r($task,true));
				echo "\$task['task_implementation']=[".$task['task_implementation']."]";
				//$executeResult = Array();// If the task is a create_object or a modify object, it will return an array as this Array('object_id', 'object_module'). If the task is not succesful, it will return 'false';else it will return 'true'.
				if (     (($task['task_type'] != 'modify_object') && ($task['task_type'] != 'call_process') && ($process_instance['asol_processinstances_id_c'] != null))      || ($process_instance['asol_processinstances_id_c'] == null) ) {
					$executeResult = executeTask($task['id'], $task['task_type'], $task['task_implementation'], $process_instance['bean_module'], $process_instance['bean_id'], $node['asol_processinstances_id_c'], $node['id'], $process_instance['bean_ungreedy_count']);	//Every task execution will return true if is succesful (an id if is a create or a modify object)

					$GLOBALS['log']->debug("*********************** ASOL: \$executeResult=[".$executeResult."]");
					if ($task['task_type'] == "end") { // if task_type=end then break the foreach execution in order to not execute the following tasks (there should not be following tasks)
						$GLOBALS['log']->debug("*********************** ASOL: task_type=end ?s break");
						break;
					}
				}

				//If current activity has more than one next activity, add related entries to the database table working_nodes & increase the number of tokens for current process
				$nextTask = (!empty($tasks[$keyT+1]['id'])) ? $tasks[$keyT+1]['id'] : null;
				$nextActivity = getNextActivity($node['asol_activity_id_c'], $nextTask, $process_instance['bean_module'], $process_instance['bean_id'], $process_instance['asol_processinstances_id_c']); //function that returns an array with next activities for the current node, if next activity is the actual activity, return the actual activity.
					

				if (count($nextActivity) >= 2) { ////********** This branch ends and two or more branchs are borne from the branch that is going to die.
					$GLOBALS['log']->debug("*********************** ASOL: count(nextActivity) >=2");
					//End current node and add to DB the new bifurcated nodes
					
					$db->query("
											UPDATE asol_workingnodes 
											SET status='terminated' 
											WHERE id='".$node['id']."'
									   ");

					foreach ($nextActivity as $newNode) {

						$id3 = create_guid();
						$name3 = "w_n_".$id3;
						$db->query("
												INSERT INTO asol_workingnodes (id, name, asol_processinstances_id_c, priority, date_entered, date_modified, asol_activity_id_c, asol_task_id_c, delay_wakeup_time, created_by, status)
												VALUES ('".$id3."', '".$name3."', '".$process_instance['id']."', '".$node['priority']."', '".gmdate("Y-m-d H:i:s")."', '".gmdate("Y-m-d H:i:s")."', '".$newNode."', null, '0000-00-00 00:00:00', '".$current_user->id."', 'not_started')
										   ");
					}


				}
				elseif (count($nextActivity) == 1) {////**********sigue con la misma rama
					$GLOBALS['log']->debug("*********************** ASOL: count(nextActivity) ==1");
					//Update current_activity (if the task executed is the last one) and current_task
					if (count($tasks) == ($keyT+1)) {
						
						$db->query("
												UPDATE asol_workingnodes 
												SET asol_activity_id_c='".$nextActivity[0]."' 
												WHERE id='".$node['id']."' 
												LIMIT 1
										   ");
						$db->query("
								UPDATE asol_workingnodes 
								SET status='in_progress' 
								WHERE id='".$node['id']."'
						   ");
					}
					//Update current task for current node
					if ($nextTask != null) { //If not null
						
						$db->query("
												UPDATE asol_workingnodes 
												SET asol_task_id_c='".$nextTask."' 
												WHERE id='".$node['id']."'
										   ");
					}
					else { //If yes
						
						$db->query("
												UPDATE asol_workingnodes 
												SET asol_task_id_c=null 
												WHERE id='".$node['id']."'
										   ");
					}

					$on_hold_Result = checkUpdateOnHold($task['id'], $nextTask, $process_instance['id'], $node['priority'], $node['id'], $executeResult['object_id'], $executeResult['object_module']);////****para task ( task/call) de Sugar//*******en $executeResult vendr? el object_id(bean_id) si fue una task create_object o modify_object(modify_object NOO!!)
					$GLOBALS['log']->debug("*********************** ASOL: \$on_hold_Result=[".$on_hold_Result."]");
					if ($on_hold_Result) {
						$GLOBALS['log']->debug("*********************** ASOL: on_hold_Result = true");
						$db->query("
											UPDATE asol_workingnodes 
											SET status='held' 
											WHERE id='".$node['id']."'
								   ");

						break;
					}


				}
				else {/////////////*************There is no next_activity in this branch, the branch dies with no children
					$GLOBALS['log']->debug("*********************** ASOL: There is no next_activity in this branch, the branch dies with no children");
					//End current node
					$db->query("
											UPDATE asol_workingnodes 
											SET status='terminated' 
											WHERE id='".$node['id']."'
									   ");


				}

			}
			else { //If current task has delay, update the node in the database with status delayed and the expected wakeup_time_delay
				$GLOBALS['log']->debug("*********************** ASOL: If current task has delay, update the node in the database with status delayed and the expected wakeup_time_delay");
				$db->query("
										UPDATE asol_workingnodes 
										SET status='delayed', delay_wakeup_time='".calculateWakeUpDelay($task['delay'], $taskDelayStartTime)."' 
										WHERE id='".$node['id']."'
								   ");

					
				break;

			}

		}
			
	}



	$number_of_active_working_nodes__query_2 = $db->query("
															SELECT count(id) AS counter
															FROM asol_workingnodes
															WHERE (  status != 'terminated' AND asol_processinstances_id_c = '".$process_instance['id']."'  )
														");
	$number_of_active_working_nodes__row_2 = $db->fetchByAssoc($number_of_active_working_nodes__query_2);

	if ($number_of_active_working_nodes__row_2['counter'] == '0') {

		$db->query ("
						DELETE FROM asol_workingnodes
						WHERE status = 'terminated' AND asol_processinstances_id_c='".$process_instance['id']."'
				");

		$db->query("
							DELETE FROM asol_processinstances
							WHERE ( id='".$process_instance['id']."' )
					   ");
		$db->query("
							DELETE FROM asol_onhold
							WHERE (  asol_processinstances_id_c='".$process_instance['id']."' )
					   ");

	}

	$GLOBALS['log']->debug("*********************** ASOL: EXIT execute_process \$process_instance=[".print_r($process_instance,true)."] \$node=[".print_r($node,true)."]");

}



function getActivityDelay($activity_id) { //This function just returns the delay field for a given activity_id
	//IT WORKS

	global $db;

	$activityDelayQuery = $db->query("
										SELECT delay 
										FROM asol_activity 
										WHERE id='".$activity_id."' 
										LIMIT 1
									 ");
	$activityDelayRow = $db->fetchByAssoc($activityDelayQuery);

	$delay_array = explode(' - ',$activityDelayRow['delay']);
	if ($delay_array[1] == '0') {
		return false;
	}
	return $activityDelayRow['delay'];

}


function calculateWakeUpDelay($delay, $taskTime = false) {	//Calculates a wakeupDatetime based on current GMT Time and a given delay (e.g. 'hours - 5') //****** Either  it?s a activity delay or it?s a task delay. It?s all done in the same function because the working node can be awaken by either activity delay or task delay.
	//IT WORKS

	$wakeUpDate = false;
	$delayArray = explode(" - ", $delay);

	if (!$taskTime) { //is Activity Delay

		if ($delayArray[1] != '0') {

			switch ($delayArray[0]) {

				case "minutes":
					$wakeUpDate  = date("Y-m-d H:i:s", mktime(gmdate("H"), gmdate("i")+$delayArray[1], gmdate("s"), gmdate("m"), gmdate("d"), gmdate("Y")));
					break;

				case "hours":
					$wakeUpDate  = date("Y-m-d H:i:s", mktime(gmdate("H")+$delayArray[1], gmdate("i"), gmdate("s"), gmdate("m"), gmdate("d"), gmdate("Y")));
					break;

				case "days":
					$wakeUpDate  = date("Y-m-d H:i:s", mktime(gmdate("H"), gmdate("i"), gmdate("s"), gmdate("m"), gmdate("d")+$delayArray[1], gmdate("Y")));
					break;

				case "weeks":
					$wakeUpDate  = date("Y-m-d H:i:s", mktime(gmdate("H"), gmdate("i"), gmdate("s"), gmdate("m"), gmdate("d")+($delayArray[1]*7), gmdate("Y")));
					break;

				case "months":
					$wakeUpDate  = date("Y-m-d H:i:s", mktime(gmdate("H"), gmdate("i"), gmdate("s"), gmdate("m")+$delayArray[1], gmdate("d"), gmdate("Y")));
					break;

			}

		}

	}
	else { //is Task Delay

		if ($delayArray[1] != '0') {


			$dateTime = explode(" ", $taskTime);
			$date = explode("-", $dateTime[0]);
			$time = explode(":", $dateTime[1]);


			switch ($delayArray[0]) {

				case "minutes":
					$wakeUpDate  = date("Y-m-d H:i:s", mktime($time[0], $time[1]+$delayArray[1], $time[2], $date[1], $date[2], $date[0]));
					break;

				case "hours":
					$wakeUpDate  = date("Y-m-d H:i:s", mktime($time[0]+$delayArray[1], $time[1], $time[2], $date[1], $date[2], $date[0]));
					break;

				case "days":
					$wakeUpDate  = date("Y-m-d H:i:s", mktime($time[0], $time[1], $time[2], $date[1], $date[2]+$delayArray[1], $date[0]));
					break;

				case "weeks":
					$wakeUpDate  = date("Y-m-d H:i:s", mktime($time[0], $time[1], $time[2], $date[1], $date[2]+($delayArray[1]*7), $date[0]));
					break;

				case "months":
					$wakeUpDate  = date("Y-m-d H:i:s", mktime($time[0], $time[1], $time[2], $date[1]+$delayArray[1], $date[2], $date[0]));
					break;

			}

		}


	}

	return $wakeUpDate;

}


function getTaskStartDelayTime($delay_type, $delay, $bean_module, $bean_id) { //This function returns a datetime based on a delay type and a delay (e.g. 'minutes - 8')
	//IT WORKS

	global $beanList, $beanFiles, $db;

	$taskDelayTime = false;
	$delayArray = explode (" - ", $delay);

	if (($delay_type != "no_delay") && ($delayArray[1] != '0')) {

		if ($delay_type == "on_finish_previous_task") { //Standard Delay ////////////********** ?sto es para esperar por la anterior tarea //// si la anterior tarea es una sugar-task entonces habra que hacer lo de los logic hooks para saber cuando terminan las sugar-tasks

			$taskDelayTime = gmdate('Y-m-d H:i:s');

		}
		else if ($delay_type == "on_creation") {///////////////*********este type de delay_task es para en vez de tener de base de tiempos del delay a la tarea anterior... pues tener a cuando se creo el modulo que dispar? el evento ... event_type = on_create (Account, Opportunities, etc...)

      // TODO - Check if this should use wfm_bean_load method
			//get and retrieve the current record of the main module
			$class_name = $beanList[$bean_module];
			require_once($beanFiles[$class_name]);
			$bean = new $class_name();
			$bean_table = $bean->table_name;

			$onCreationDelayQuery = $db->query("
													SELECT date_entered 
													FROM ".$bean_table." 
													WHERE id='".$bean_id."' 
													LIMIT 1
											   ");
			$onCreationDelayRow = $db->fetchByAssoc($onCreationDelayQuery);

			$taskDelayTime = $onCreationDelayRow['date_entered'];

		}
		else if ($delay_type == "on_modification") {

      // TODO - Check if this should use wfm_bean_load method
			//get and retrieve the current record of the main module
			$class_name = $beanList[$bean_module];
			require_once($beanFiles[$class_name]);
			$bean = new $class_name();
			$bean_table = $bean->table_name;

			$onModificationDelayQuery = $db->query("
														SELECT date_modified 
														FROM ".$bean_table." 
														WHERE id='".$bean_id."' 
														LIMIT 1
												   ");
			$onModificationDelayRow = $db->fetchByAssoc($onModificationDelayQuery);

			$taskDelayTime = $onModificationDelayRow['date_modified'];

		}

	}

	return $taskDelayTime;

}


function getNextActivity($current_activity, $nextTask, $bean_module, $bean_id, $parent_process_instance_id) { //Return the next activities for a current activity and task. Checks if the returned activities applies the conditions for the context bean_id.
	//IT WORKS

	global $db;
	$nextActivities = array();

	if ($nextTask != null) {///*** Si la task es != null -> es que aun quedan tareas por ejecutar en esta actividad -> seguimos con la misma actividad
		return $nextActivities[] = $current_activity;
	}
	else {
		//Get from database the next activities for current_activity. Check if activities applies with the defined condition.
		$nextActivitiesQuery = $db->query("
		                    SELECT id as activity_id, condition_activity
		                    FROM asol_activity
		                    WHERE predecessor_id='".$current_activity." AND deleted = 0'
										  ");
		while ($nextActivityRow = $db->fetchByAssoc($nextActivitiesQuery,-1,false)) { //Check if next activities accept the start condition
			if ($parent_process_instance_id == null) { // process
				if (appliesConditions($bean_module, $bean_id, $nextActivityRow['condition_activity'])) {
					$nextActivities[] = $nextActivityRow['activity_id'];
				}
			}
			else {// subprocess -> no bean_id
				$nextActivities[] = $nextActivityRow['activity_id'];
			}
		}
	}
	return $nextActivities;

}


function checkUpdateOnHold($previous_task_id, $task_id, $process_instance_id, $priority, $working_node_id, $object_id, $object_module) { //Check if a activity must wait for the previous one to be manually closed and if it is, insert it into the on_hold DB table

	$GLOBALS['log']->debug("*********************** ASOL: ENTRY function checkUpdateOnHold");
	global $db;

	$isOnHold = false;

	if ($task_id != null) {

		$previousQuery = $db->query("
										SELECT task_type, task_implementation 
										FROM asol_task 
										WHERE id='".$previous_task_id."' 
										LIMIT 1
									");
		$previousRow = $db->fetchByAssoc($previousQuery,-1,false);
		$GLOBALS['log']->debug("*********************** ASOL: INSIDE function checkUpdateOnHold    \$previousRow['task_type']=".$previousRow['task_type']);
		if ($previousRow['task_type'] == "create_object") {

			$impl_array = explode("\${mod}", $previousRow['task_implementation']);
			$GLOBALS['log']->debug("*********************** ASOL: INSIDE function checkUpdateOnHold    \$impl_array=".print_r($impl_array,true));
			$impl_array_2= explode("\${pipe}", $impl_array[1]);
			$GLOBALS['log']->debug("*********************** ASOL: INSIDE function checkUpdateOnHold    \$impl_array_2=".print_r($impl_array_2,true));
			$impl_array_3= explode("\${dp}", $impl_array_2[3]);
			$GLOBALS['log']->debug("*********************** ASOL: INSIDE function checkUpdateOnHold    \$impl_array_3=".print_r($impl_array_3,true));
			if (  ( ($impl_array[0] == "Calls")&&($impl_array_3[1] != "Held") ) || ( ($impl_array[0] == "Tasks")&&($impl_array_3[1] != "Completed") )  ) {

				$currentQuery = $db->query("
												SELECT delay_type 
												FROM asol_task 
												WHERE id='".$task_id."' 
												LIMIT 1
										   ");
				$currentRow = $db->fetchByAssoc($currentQuery,-1,false);
				$GLOBALS['log']->debug("*********************** ASOL: INSIDE function checkUpdateOnHold    \$currentRow['delay_type']=".$currentRow['delay_type']);
				if ($currentRow['delay_type'] == "on_finish_previous_task") {
					$id4 = create_guid();
					$name4 = "o_h_".$id4;
					$db->query("
									INSERT INTO asol_onhold (id, name, bean_id, bean_module, asol_processinstances_id_c, asol_workingnodes_id_c)
									VALUES ('".$id4."', '".$name4."', '".$object_id."', '".$object_module."', '".$process_instance_id."', '".$working_node_id."')
							   ");
					$isOnHold = true;
				}
					
			}

		}

	}
	$GLOBALS['log']->debug("*********************** ASOL: EXIT function checkUpdateOnHold");
	return $isOnHold;
	//Checks if a task needs to be holded (only if the task is a create object task/call-sugar and the next task?s delay_type equals on_finish_previous_task)
	//If next task equals null --> nothing to be done

}




function appliesConditions($bean_module, $bean_id, $conditions) { //Returns true or false if the given conditions applies for the module bean_id
	//IT WORKS
	$GLOBALS['log']->debug("*********************** ASOL: ENTRY function appliesConditions \$bean_module=[".$bean_module."] \$bean_id=[".$bean_id."] \$condition=[".$conditions."]");

	global $current_user, $beanList, $beanFiles, $timedate, $db;
  if( is_string($conditions) ) {
      $conditions = json_decode($conditions);
  }

  $bean = wfm_load_bean($bean_module, $bean_id);
	$appliesConditions = true;

	foreach ($conditions as $key=>$value) {
		$operator = "";
		$hourOffset = $timedate->adjustmentForUserTimeZone();

		switch ($value->operator ){
			case "equals":
				if ($value->type  == "datetime"){
					$operator = "BETWEEN";

					$date1 = explode("-", $value->param1 );
					$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,$date1[1],$date1[2],$date1[0])+$hourOffset);
					$value->param2  = date("Y-m-d H:59:59", @mktime(0,59,59,$date1[1],$date1[2],$date1[0])+(24*3600)-(3600)+$hourOffset);
				} else {
					$operator = "EQUALS";
				}
				break;

			case "not equals":
				if ($value->type == "datetime"){
					$operator = "NOT BETWEEN";

					$date1 = explode("-", $value->param1 );
					$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,$date1[1],$date1[2],$date1[0])+$hourOffset);
					$value->param2  = date("Y-m-d H:59:59", @mktime(0,59,59,$date1[1],$date1[2],$date1[0])+(24*3600)-(3600)+$hourOffset);
				} else {
					$operator = "NOT EQUALS";
				}
				break;

			case "like":
				$operator = "LIKE";
				break;

			case "not like":
				$operator = "NOT LIKE";
				break;

			case "after date":
			case "more than":
				$operator = "GT";

				if ($value->type  == "datetime"){
					$date1 = explode("-", $value->param1 );
					$value->param1  = date("Y-m-d H:59:59", @mktime(0,59,59,$date1[1],$date1[2],$date1[0])+(24*3600)-(3600)+$hourOffset);
				}

				break;

			case "before date":
			case "less than":
				$operator = "LT";

				if ($value->type == "datetime"){
					$date1 = explode("-", $value->param1 );
					$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,$date1[1],$date1[2],$date1[0])+$hourOffset);
				}

				break;

			case "between":
				$operator = "BETWEEN";

				if ($value->type  == "datetime"){
					$date1 = explode("-", $value->param1 );
					$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,$date1[1],$date1[2],$date1[0])+$hourOffset);
					$date2 = explode("-", $value->param2 );
					$value->param2  = date("Y-m-d H:59:59", @mktime(0,59,59,$date2[1],$date2[2],$date2[0])+(24*3600)-(3600)+$hourOffset);
				}

				break;

			case "not between":
				$operator = "NOT BETWEEN";

				if ($value->type  == "datetime"){
					$date1 = explode("-", $value->param1 );
					$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,$date1[1],$date1[2],$date1[0])+$hourOffset);
					$date2 = explode("-", $value->param2 );
					$value->param2  = date("Y-m-d H:59:59", @mktime(0,59,59,$date2[1],$date2[2],$date2[0])+(24*3600)-(3600)+$hourOffset);
				}

				break;

			case "one of":
				$operator = "IN";
				break;

			case "not one of":
				$operator = "NOT IN";
				break;

			case "last":
				switch ($value->param1 ){

					case "day":
						if ($value->param2  == "")
						$value->param2  = 1;
						$value->param1  = date("Y-m-d H:00:00", time()-(($value->param2 )*24*3600)-(date("G")*3600)+$hourOffset);
						$value->param2  = date("Y-m-d H:59:59", time()-(24*3600)+((24*3600)-((date("G")+1)*3600))+$hourOffset);
						break;

					case "week":
						if ($value->param2  == "")
						$value->param2  = 1;
						$diaSemana = date("N", time()+$hourOffset)-1;

						$value->param1  = date("Y-m-d H:00:00", time()-(((($diaSemana-($week_start-1))+7)*24*3600) + (($value->param2 -1)*7*24*3600))-(date("G")*3600)+$hourOffset);
						$value->param2  = date("Y-m-d H:59:59", time()+(((6-($diaSemana-($week_start-1)))-7)*24*3600)+((24*3600)-((date("G")+1)*3600))+$hourOffset);
						break;

					case "month":
						if ($value->param2  == "")
						$value->param2  = 1;
						$diaMes = date("j", time()+$hourOffset)-1;
						$monthsDate = @mktime(0,0,0,date("m")-($value->param2 ),1,date("Y"));

						$value->param1  = date("Y-m-d H:00:00", $monthsDate+$hourOffset);
						$value->param2  = date("Y-m-d H:59:59", time()-($diaMes+1)*24*3600+((24*3600)-((date("G")+1)*3600))+$hourOffset);
						break;

					case "Fquarter":
						if ($value->param2  == "")
						$value->param2  = 1;
						$diaMes = date("j", time()+$hourOffset)-1;
						$numMes = date("n", time()+$hourOffset);


						$quarter_month = (($numMes-($quarter_month-1)) <= 0) ? $numMes+(12-($quarter_month-1)) : $numMes-($quarter_month-1);
						$thisQuarter = ceil($quarter_month/3);
						$monthInQuarter = $quarter_month-(3*($thisQuarter-1));

						$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,date("m")-($monthInQuarter-1)-(3+(3*($value->param2 -1))),date("d")-$diaMes,date("Y"))+$hourOffset);
						$mes = @mktime( 0, 0, 0, date("m")+(3-$monthInQuarter)-3, 1, date("Y"));
						$value->param2  = date("Y-m-d H:59:59", @mktime(0,0,0,date("m")+(3-$monthInQuarter)-3,date("t",$mes),date("Y"))+((24*3600)-(3600))+$hourOffset);
						break;

					case "Nquarter":
						if ($value->param2  == "")
						$value->param2  = 1;
						$diaMes = date("j", time()+$hourOffset)-1;
						$numMes = date("n", time()+$hourOffset);


						$thisQuarter = ceil($numMes/3);
						$monthInQuarter = $numMes-(3*($thisQuarter-1));

						$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,date("m")-($monthInQuarter-1)-(3+(3*($value->param2 -1))),date("d")-$diaMes,date("Y"))+$hourOffset);
						$mes = @mktime( 0, 0, 0, date("m")+(3-$monthInQuarter)-3, 1, date("Y"));
						$value->param2  = date("Y-m-d H:59:59", @mktime(0,0,0,date("m")+(3-$monthInQuarter)-3,date("t",$mes),date("Y"))+((24*3600)-(3600))+$hourOffset);
						break;

					case "Fyear":
						if ($value->param2  == "")
						$value->param2  = 1;
						$numMes = date("n", time()+$hourOffset);


						$year_month = $quarter_month;
						$quarter_month = (($numMes-($quarter_month-1)) <= 0) ? $numMes+(12-($quarter_month-1)) : $numMes-($quarter_month-1);
						$thisQuarter = ceil($quarter_month/3);
						$monthInQuarter = $quarter_month-(3*($thisQuarter-1));

						$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,$year_month,1,date("Y")-$value->param2 )+$hourOffset);
						$mes = @mktime( 0, 0, 0, date("m")+(((3-$monthInQuarter)-3) - (($thisQuarter-1)*3)), 1, date("Y"));
						$value->param2  = date("Y-m-d H:59:59", @mktime(0,0,0,date("m")+(((3-$monthInQuarter)-3) - (($thisQuarter-1)*3)),date("t",$mes),date("Y"))+((24*3600)-(3600))+$hourOffset);
						break;

					case "Nyear":
						if ($value->param2  == "")
						$value->param2  = 1;

						$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,1,1,date("Y")-($value->param2 ))+$hourOffset);
						$value->param2  = date("Y-m-d H:59:59", @mktime(0,0,0,12,31,date("Y")-1)+((24*3600)-(3600))+$hourOffset);
						break;

				}

				$operator = "BETWEEN";
				break;

			case "not last":
				switch ($value->param1 ){

					case "day":
						if ($value->param2  == "")
						$value->param2  = 1;
						$value->param1  = date("Y-m-d H:00:00", time()-(($value->param2 )*24*3600)-(date("G")*3600)+$hourOffset);
						$value->param2  = date("Y-m-d H:59:59", time()-(24*3600)+((24*3600)-((date("G")+1)*3600))+$hourOffset);
						break;

					case "week":
						if ($value->param2  == "")
						$value->param2  = 1;
						$diaSemana = date("N", time()+$hourOffset)-1;

						$value->param1  = date("Y-m-d H:00:00", time()-(((($diaSemana-($week_start-1))+7)*24*3600) + (($value->param2 -1)*7*24*3600))-(date("G")*3600)+$hourOffset);
						$value->param2  = date("Y-m-d H:59:59", time()+(((6-($diaSemana-($week_start-1)))-7)*24*3600)+((24*3600)-((date("G")+1)*3600))+$hourOffset);
						break;

					case "month":
						if ($value->param2  == "")
						$value->param2  = 1;
						$diaMes = date("j", time()+$hourOffset)-1;
						$monthsDate = @mktime(0,0,0,date("m")-($value->param2 ),1,date("Y"));

						$value->param1  = date("Y-m-d H:00:00", $monthsDate+$hourOffset);
						$value->param2  = date("Y-m-d H:59:59", time()-($diaMes+1)*24*3600+((24*3600)-((date("G")+1)*3600))+$hourOffset);
						break;

					case "Fquarter":
						if ($value->param2  == "")
						$value->param2  = 1;
						$diaMes = date("j", time()+$hourOffset)-1;
						$numMes = date("n", time()+$hourOffset);


						$quarter_month = (($numMes-($quarter_month-1)) <= 0) ? $numMes+(12-($quarter_month-1)) : $numMes-($quarter_month-1);
						$thisQuarter = ceil($quarter_month/3);
						$monthInQuarter = $quarter_month-(3*($thisQuarter-1));

						$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,date("m")-($monthInQuarter-1)-(3+(3*($value->param2 -1))),date("d")-$diaMes,date("Y"))+$hourOffset);
						$mes = @mktime( 0, 0, 0, date("m")+(3-$monthInQuarter)-3, 1, date("Y"));
						$value->param2  = date("Y-m-d H:59:59", @mktime(0,0,0,date("m")+(3-$monthInQuarter)-3,date("t",$mes),date("Y"))+((24*3600)-(3600))+$hourOffset);
						break;

					case "Nquarter":
						if ($value->param2  == "")
						$value->param2  = 1;
						$diaMes = date("j", time()+$hourOffset)-1;
						$numMes = date("n", time()+$hourOffset);


						$thisQuarter = ceil($numMes/3);
						$monthInQuarter = $numMes-(3*($thisQuarter-1));

						$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,date("m")-($monthInQuarter-1)-(3+(3*($value->param2 -1))),date("d")-$diaMes,date("Y"))+$hourOffset);
						$mes = @mktime( 0, 0, 0, date("m")+(3-$monthInQuarter)-3, 1, date("Y"));
						$value->param2  = date("Y-m-d H:59:59", @mktime(0,0,0,date("m")+(3-$monthInQuarter)-3,date("t",$mes),date("Y"))+((24*3600)-(3600))+$hourOffset);
						break;

					case "Fyear":
						if ($value->param2  == "")
						$value->param2  = 1;
						$numMes = date("n", time()+$hourOffset);


						$year_month = $quarter_month;
						$quarter_month = (($numMes-($quarter_month-1)) <= 0) ? $numMes+(12-($quarter_month-1)) : $numMes-($quarter_month-1);
						$thisQuarter = ceil($quarter_month/3);
						$monthInQuarter = $quarter_month-(3*($thisQuarter-1));

						$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,$year_month,1,date("Y")-$value->param2 )+$hourOffset);
						$mes = @mktime( 0, 0, 0, date("m")+(((3-$monthInQuarter)-3) - (($thisQuarter-1)*3)), 1, date("Y"));
						$value->param2  = date("Y-m-d H:59:59", @mktime(0,0,0,date("m")+(((3-$monthInQuarter)-3) - (($thisQuarter-1)*3)),date("t",$mes),date("Y"))+((24*3600)-(3600))+$hourOffset);
						break;

					case "Nyear":
						if ($value->param2  == "")
						$value->param2  = 1;

						$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,1,1,date("Y")-($value->param2 ))+$hourOffset);
						$value->param2  = date("Y-m-d H:59:59", @mktime(0,0,0,12,31,date("Y")-1)+((24*3600)-(3600))+$hourOffset);
						break;

				}

				$operator = "NOT BETWEEN";
				break;

			case "this":
				switch ($value->param1 ){

					case "day":
						$value->param1  = date("Y-m-d H:00:00", time()-(date("G")*3600)+$hourOffset);
						$value->param2  = date("Y-m-d H:59:59", time()+((24*3600)-((date("G")+1)*3600))+$hourOffset);
						break;

					case "week":
						$diaSemana = date("N", time()+$hourOffset)-1;
						$value->param1  = date("Y-m-d H:00:00", time()-(($diaSemana-($week_start-1))*24*3600)-(date("G")*3600)+$hourOffset);
						$value->param2  = date("Y-m-d H:59:59", time()+((6-($diaSemana-($week_start-1)))*24*3600)+((24*3600)-((date("G")+1)*3600))+$hourOffset);
						break;

					case "month":
						$diaMes = date("j", time()+$hourOffset)-1;
						$numDiasMes = date("t", time()+$hourOffset);
						$value->param1  = date("Y-m-d H:00:00", time()-($diaMes*24*3600)-(date("G")*3600)+$hourOffset);
						$value->param2  = date("Y-m-d H:59:59", time()+(($numDiasMes-$diaMes-1)*24*3600)+((24*3600)-((date("G")+1)*3600))+$hourOffset);
						break;

					case "Fquarter":
						$diaMes = date("j", time()+$hourOffset)-1;
						$numMes = date("n", time()+$hourOffset);



						$quarter_month = (($numMes-($quarter_month-1)) <= 0) ? $numMes+(12-($quarter_month-1)) : $numMes-($quarter_month-1);
						$thisQuarter = ceil($quarter_month/3);
						$monthInQuarter = $quarter_month-(3*($thisQuarter-1));

						$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,date("m")-($monthInQuarter-1),date("d")-$diaMes,date("Y"))+$hourOffset);
						$mes = @mktime( 0, 0, 0, date("m")+(3-$monthInQuarter), 1, date("Y"));
						$value->param2  = date("Y-m-d H:59:59", @mktime(0,0,0,date("m")+(3-$monthInQuarter),date("t",$mes),date("Y"))+(24*3600)-(3600)+$hourOffset);
						break;

					case "Nquarter":
						$diaMes = date("j", time()+$hourOffset)-1;
						$numMes = date("n", time()+$hourOffset);


						$thisQuarter = ceil($numMes/3);
						$monthInQuarter = $numMes-(3*($thisQuarter-1));

						$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,date("m")-($monthInQuarter-1),date("d")-$diaMes,date("Y"))+$hourOffset);
						$mes = @mktime( 0, 0, 0, date("m")+(3-$monthInQuarter), 1, date("Y"));
						$value->param2  = date("Y-m-d H:59:59", @mktime(0,0,0,date("m")+(3-$monthInQuarter),date("t",$mes),date("Y"))+(24*3600)-(3600)+$hourOffset);
						break;

					case "Fyear":
						$numMes = date("n", time()+$hourOffset);


						$year_month = $quarter_month;
						$quarter_month = (($numMes-($quarter_month-1)) <= 0) ? $numMes+(12-($quarter_month-1)) : $numMes-($quarter_month-1);
						$thisQuarter = ceil($quarter_month/3);
						$monthInQuarter = $quarter_month-(3*($thisQuarter-1));

						$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,$year_month,1,date("Y"))+$hourOffset);
						$mes = @mktime( 0, 0, 0, date("m")+(((3-$monthInQuarter)-3) - (($thisQuarter-1)*3)), 1, date("Y"));
						$value->param2  = date("Y-m-d H:59:59", @mktime(0,0,0,date("m")+(((3-$monthInQuarter)-3) - (($thisQuarter-1)*3)),date("t",$mes),date("Y")+1)+(24*3600)-(3600)+$hourOffset);
						break;

					case "Nyear":
						$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,1,1,date("Y"))+$hourOffset);
						$value->param2  = date("Y-m-d H:59:59", @mktime(0,0,0,12,31,date("Y"))+((24*3600)-(3600))+$hourOffset);
						break;

				}

				$operator = "BETWEEN";
				break;

			case "not this":
				switch ($value->param1 ){

					case "day":
						$value->param1  = date("Y-m-d H:00:00", time()-(date("G")*3600)+$hourOffset);
						$value->param2  = date("Y-m-d H:59:59", time()+((24*3600)-((date("G")+1)*3600))+$hourOffset);
						break;

					case "week":
						$diaSemana = date("N", time()+$hourOffset)-1;
						$value->param1  = date("Y-m-d H:00:00", time()-(($diaSemana-($week_start-1))*24*3600)-(date("G")*3600)+$hourOffset);
						$value->param2  = date("Y-m-d H:59:59", time()+((6-($diaSemana-($week_start-1)))*24*3600)+((24*3600)-((date("G")+1)*3600))+$hourOffset);
						break;

					case "month":
						$diaMes = date("j", time()+$hourOffset)-1;
						$numDiasMes = date("t", time()+$hourOffset);
						$value->param1  = date("Y-m-d H:00:00", time()-($diaMes*24*3600)-(date("G")*3600)+$hourOffset);
						$value->param2  = date("Y-m-d H:59:59", time()+(($numDiasMes-$diaMes-1)*24*3600)+((24*3600)-((date("G")+1)*3600))+$hourOffset);
						break;

					case "Fquarter":
						$diaMes = date("j", time()+$hourOffset)-1;
						$numMes = date("n", time()+$hourOffset);
						$quarter_month = (($numMes-($quarter_month-1)) <= 0) ? $numMes+(12-($quarter_month-1)) : $numMes-($quarter_month-1);
						$thisQuarter = ceil($quarter_month/3);
						$monthInQuarter = $quarter_month-(3*($thisQuarter-1));

						$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,date("m")-($monthInQuarter-1),date("d")-$diaMes,date("Y"))+$hourOffset);
						$mes = @mktime( 0, 0, 0, date("m")+(3-$monthInQuarter), 1, date("Y"));
						$value->param2  = date("Y-m-d H:59:59", @mktime(0,0,0,date("m")+(3-$monthInQuarter),date("t",$mes),date("Y"))+(24*3600)-(3600)+$hourOffset);
						break;

					case "Nquarter":
						$diaMes = date("j", time()+$hourOffset)-1;
						$numMes = date("n", time()+$hourOffset);
						$thisQuarter = ceil($numMes/3);
						$monthInQuarter = $numMes-(3*($thisQuarter-1));

						$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,date("m")-($monthInQuarter-1),date("d")-$diaMes,date("Y"))+$hourOffset);
						$mes = @mktime( 0, 0, 0, date("m")+(3-$monthInQuarter), 1, date("Y"));
						$value->param2  = date("Y-m-d H:59:59", @mktime(0,0,0,date("m")+(3-$monthInQuarter),date("t",$mes),date("Y"))+(24*3600)-(3600)+$hourOffset);
						break;

					case "Fyear":
						$numMes = date("n", time()+$hourOffset);
						$year_month = $quarter_month;
						$quarter_month = (($numMes-($quarter_month-1)) <= 0) ? $numMes+(12-($quarter_month-1)) : $numMes-($quarter_month-1);
						$thisQuarter = ceil($quarter_month/3);
						$monthInQuarter = $quarter_month-(3*($thisQuarter-1));

						$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,$year_month,1,date("Y"))+$hourOffset);
						$mes = @mktime( 0, 0, 0, date("m")+(((3-$monthInQuarter)-3) - (($thisQuarter-1)*3)), 1, date("Y"));
						$value->param2  = date("Y-m-d H:59:59", @mktime(0,0,0,date("m")+(((3-$monthInQuarter)-3) - (($thisQuarter-1)*3)),date("t",$mes),date("Y")+1)+(24*3600)-(3600)+$hourOffset);
						break;

					case "Nyear":
						$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,1,1,date("Y"))+$hourOffset);
						$value->param2  = date("Y-m-d H:59:59", @mktime(0,0,0,12,31,date("Y"))+((24*3600)-(3600))+$hourOffset);
						break;

				}

				$operator = "NOT BETWEEN";
				break;

			case "next":
				switch ($value->param1 ){

					case "day":
						if ($value->param2  == "")
						$value->param2  = 1;
						$value->param1  = date("Y-m-d H:00:00", time()+(1*24*3600)-((date("G"))*3600)+$hourOffset);
						$value->param2  = date("Y-m-d H:59:59", time()+(($value->param2 +1)*24*3600)-((date("G")+1)*3600)+$hourOffset);
						break;

					case "week":
						if ($value->param2  == "")
						$value->param2  = 1;
						$diaSemana = date("N", time()+$hourOffset)-1;
						$value->param1  = date("Y-m-d H:00:00", time()+((7-($diaSemana-($week_start-1)))*24*3600)-((date("G"))*3600)+$hourOffset);
						$value->param2  = date("Y-m-d H:59:59", time()+(((6-($diaSemana-($week_start-1)))+($value->param2 *7)+1)*24*3600)-((date("G")+1)*3600)+$hourOffset);
						break;

					case "month":
						if ($value->param2  == "")
						$value->param2  = 1;
						$mes = @mktime( 0, 0, 0, date("m")+$value->param2 , 1, date("Y"));

						$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,date("m")+1,1,date("Y"))+$hourOffset);
						$value->param2  = date("Y-m-d H:59:59", @mktime(0,0,0,date("m")+$value->param2 ,date("t", $mes),date("Y"))+(24*3600)-(3600)+$hourOffset);
						break;

					case "Fquarter":
						if ($value->param2  == "")
						$value->param2  = 1;
						$diaMes = date("j", time()+$hourOffset)-1;
						$numMes = date("n", time()+$hourOffset);


						$quarter_month = (($numMes-($quarter_month-1)) <= 0) ? $numMes+(12-($quarter_month-1)) : $numMes-($quarter_month-1);
						$thisQuarter = ceil($quarter_month/3);
						$monthInQuarter = $quarter_month-(3*($thisQuarter-1));

						$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,date("m")-($monthInQuarter-1)+3,date("d")-$diaMes,date("Y"))+$hourOffset);
						$mes = @mktime( 0, 0, 0, date("m")+(3-$monthInQuarter)+(3*$value->param2 ), 1, date("Y"));
						$value->param2  = date("Y-m-d H:59:59", @mktime(0,0,0,date("m")+(3-$monthInQuarter)+(3*$value->param2 ),date("t",$mes),date("Y"))+(24*3600)-(3600)+$hourOffset);

						break;

					case "Nquarter":
						if ($value->param2  == "")
						$value->param2  = 1;
						$diaMes = date("j", time()+$hourOffset)-1;
						$numMes = date("n", time()+$hourOffset);


						$thisQuarter = ceil($numMes/3);
						$monthInQuarter = $numMes-(3*($thisQuarter-1));

						$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,date("m")-($monthInQuarter-1)+3,date("d")-$diaMes,date("Y"))+$hourOffset);
						$mes = @mktime( 0, 0, 0, date("m")+(3-$monthInQuarter)+(3*$value->param2 ), 1, date("Y"));
						$value->param2  = date("Y-m-d H:59:59", @mktime(0,0,0,date("m")+(3-$monthInQuarter)+(3*$value->param2 ),date("t",$mes),date("Y"))+(24*3600)-(3600)+$hourOffset);
						break;

					case "Fyear":
						if ($value->param2  == "")
						$value->param2  = 1;
						$numMes = date("n", time()+$hourOffset);


						$year_month = $quarter_month;
						$quarter_month = (($numMes-($quarter_month-1)) <= 0) ? $numMes+(12-($quarter_month-1)) : $numMes-($quarter_month-1);
						$thisQuarter = ceil($quarter_month/3);
						$monthInQuarter = $quarter_month-(3*($thisQuarter-1));

						$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,$year_month,1,date("Y")+1)+$hourOffset);
						$mes = @mktime( 0, 0, 0, date("m")+(((3-$monthInQuarter)-3) - (($thisQuarter-1)*3)), 1, date("Y"));
						$value->param2  = date("Y-m-d H:59:59", @mktime(0,0,0,date("m")+(((3-$monthInQuarter)-3) - (($thisQuarter-1)*3)),date("t",$mes),date("Y")+$value->param2 +1)+(24*3600)-(3600)+$hourOffset);
						break;

					case "Nyear":
						if ($value->param2  == "")
						$value->param2  = 1;

						$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,1,1,date("Y")+1)+$hourOffset);
						$value->param2  = date("Y-m-d H:59:59", @mktime(0,0,0,12,31,date("Y")+$value->param2 )+(24*3600)-(3600)+$hourOffset);
						break;

				}


				$operator = "BETWEEN";
				break;

			case "not next":
				switch ($value->param1 ){

					case "day":
						if ($value->param2  == "")
						$value->param2  = 1;
						$value->param1  = date("Y-m-d H:00:00", time()+(1*24*3600)-((date("G"))*3600)+$hourOffset);
						$value->param2  = date("Y-m-d H:59:59", time()+(($value->param2 +1)*24*3600)-((date("G")+1)*3600)+$hourOffset);
						break;

					case "week":
						if ($value->param2  == "")
						$value->param2  = 1;
						$diaSemana = date("N", time()+$hourOffset)-1;
						$value->param1  = date("Y-m-d H:00:00", time()+((7-($diaSemana-($week_start-1)))*24*3600)-((date("G"))*3600)+$hourOffset);
						$value->param2  = date("Y-m-d H:59:59", time()+(((6-($diaSemana-($week_start-1)))+($value->param2 *7)+1)*24*3600)-((date("G")+1)*3600)+$hourOffset);
						break;

					case "month":
						if ($value->param2  == "")
						$value->param2  = 1;
						$mes = @mktime( 0, 0, 0, date("m")+$value->param2 , 1, date("Y"));

						$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,date("m")+1,1,date("Y"))+$hourOffset);
						$value->param2  = date("Y-m-d H:59:59", @mktime(0,0,0,date("m")+$value->param2 ,date("t", $mes),date("Y"))+(24*3600)-(3600)+$hourOffset);
						break;

					case "Fquarter":
						if ($value->param2  == "")
						$value->param2  = 1;
						$diaMes = date("j", time()+$hourOffset)-1;
						$numMes = date("n", time()+$hourOffset);


						$quarter_month = (($numMes-($quarter_month-1)) <= 0) ? $numMes+(12-($quarter_month-1)) : $numMes-($quarter_month-1);
						$thisQuarter = ceil($quarter_month/3);
						$monthInQuarter = $quarter_month-(3*($thisQuarter-1));

						$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,date("m")-($monthInQuarter-1)+3,date("d")-$diaMes,date("Y"))+$hourOffset);
						$mes = @mktime( 0, 0, 0, date("m")+(3-$monthInQuarter)+(3*$value->param2 ), 1, date("Y"));
						$value->param2  = date("Y-m-d H:59:59", @mktime(0,0,0,date("m")+(3-$monthInQuarter)+(3*$value->param2 ),date("t",$mes),date("Y"))+(24*3600)-(3600)+$hourOffset);

						break;

					case "Nquarter":
						if ($value->param2  == "")
						$value->param2  = 1;
						$diaMes = date("j", time()+$hourOffset)-1;
						$numMes = date("n", time()+$hourOffset);


						$thisQuarter = ceil($numMes/3);
						$monthInQuarter = $numMes-(3*($thisQuarter-1));

						$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,date("m")-($monthInQuarter-1)+3,date("d")-$diaMes,date("Y"))+$hourOffset);
						$mes = @mktime( 0, 0, 0, date("m")+(3-$monthInQuarter)+(3*$value->param2 ), 1, date("Y"));
						$value->param2  = date("Y-m-d H:59:59", @mktime(0,0,0,date("m")+(3-$monthInQuarter)+(3*$value->param2 ),date("t",$mes),date("Y"))+(24*3600)-(3600)+$hourOffset);
						break;

					case "Fyear":
						if ($value->param2  == "")
						$value->param2  = 1;
						$numMes = date("n", time()+$hourOffset);


						$year_month = $quarter_month;
						$quarter_month = (($numMes-($quarter_month-1)) <= 0) ? $numMes+(12-($quarter_month-1)) : $numMes-($quarter_month-1);
						$thisQuarter = ceil($quarter_month/3);
						$monthInQuarter = $quarter_month-(3*($thisQuarter-1));

						$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,$year_month,1,date("Y")+1)+$hourOffset);
						$mes = @mktime( 0, 0, 0, date("m")+(((3-$monthInQuarter)-3) - (($thisQuarter-1)*3)), 1, date("Y"));
						$value->param2  = date("Y-m-d H:59:59", @mktime(0,0,0,date("m")+(((3-$monthInQuarter)-3) - (($thisQuarter-1)*3)),date("t",$mes),date("Y")+$value->param2 +1)+(24*3600)-(3600)+$hourOffset);
						break;

					case "Nyear":
						if ($value->param2  == "")
						$value->param2  = 1;

						$value->param1  = date("Y-m-d H:00:00", @mktime(0,0,0,1,1,date("Y")+1)+$hourOffset);
						$value->param2  = date("Y-m-d H:59:59", @mktime(0,0,0,12,31,date("Y")+$value->param2 )+(24*3600)-(3600)+$hourOffset);
						break;

				}


				$operator = "NOT BETWEEN";
				break;
            case "changed":
                if( $bean && $bean->fetched_row) {
                    $operator = "NOT EQUALS";
                    $value->param1  = $bean->fetched_row[$field];
                }
                break;
            case "not changed":
                if( $bean && $bean->fetched_row) {
                    $operator = "EQUALS";
                    $value->param1  = $bean->fetched_row[$field];
                }
                break;
            case "not equals bean value":  
                $operator = "NOT EQUALS BEAN";
                break;
		}

		//make check on filter value
    $fieldArray = explode(".", $value->name );
    if(count($fieldArray) == 2) {
      $relatedModule =  $fieldArray[0] ;
      $field = $fieldArray[1];
    } else {
      $relatedModule =  null;
      $field =$value->name ;
    }

		$currentFieldValue = "";
    $currentFieldDef = null;

		if (empty($relatedModule)) {
			$currentFieldValue = $bean->$field;
      $currentFieldDef = $bean->field_defs[$field];
		} else {
      $key = $value->relatedKey;
      $related_bean = wfm_load_bean($relatedModule, $bean->$key);

			$currentFieldValue = $related_bean->$field;
      $currentFieldDef = $related_bean->field_defs[$field];
		}

    $multiFieldValue = array($currentFieldValue);
    if( $currentFieldDef['type'] == 'multienum') {
      if( is_array($currentFieldValue) ) {
        $multiFieldValue = $currentFieldValue;
      }else {
        $multiFieldValue = unencodeMultienum($currentFieldValue);
      }
    }

    foreach( $multiFieldValue as $currentFieldValue) {
      //check if current condition applies
      switch ($operator) {
        case "NOT EQUALS BEAN":
          $appliesConditions = ($appliesConditions && ($currentFieldValue != $bean->{$value->param1})) ? true : false;
          break;  
          
        case "EQUALS":
          $appliesConditions = ($appliesConditions && ($currentFieldValue == $value->param1 )) ? true : false;
          break;

        case "NOT EQUALS":
          $appliesConditions = ($appliesConditions && ($currentFieldValue != $value->param1 )) ? true : false;
          break;

        case "LIKE":
          $appliesConditions = ($appliesConditions && (strpos($value->param1 , $currentFieldValue))) ? true : false;
          break;

        case "NOT LIKE":
          $appliesConditions = ($appliesConditions && (!strpos($value->param1 , $currentFieldValue))) ? true : false;
          break;

        case "BETWEEN":
          $appliesConditions = ($appliesConditions && (($currentFieldValue >= $value->param1 ) && ($currentFieldValue <= $value->param2 ))) ? true : false;
          break;

        case "NOT BETWEEN":
          $appliesConditions = ($appliesConditions && (($currentFieldValue < $value->param1 ) || ($currentFieldValue > $value->param2 ))) ? true : false;
          break;

        case "GT":
          $appliesConditions = ($appliesConditions && ($currentFieldValue > $value->param1 )) ? true : false;
          break;

        case "LT":
          $appliesConditions = ($appliesConditions && ($currentFieldValue < $value->param1 )) ? true : false;
          break;

        case "IN":
          $appliesConditions = ($appliesConditions && (in_array($currentFieldValue, $value->param1 ))) ? true : false;
          break;

        case "NOT IN":
          $appliesConditions = ($appliesConditions && (!in_array($currentFieldValue, $value->param1 ))) ? true : false;
          break;

      }
    }

	}
	$GLOBALS['log']->debug("*********************** ASOL: EXIT function appliesConditions");
	return $appliesConditions;

}


?>

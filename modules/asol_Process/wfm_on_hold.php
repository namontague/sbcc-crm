<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class wfm_hook_on_hold {

	function wakeup_on_hold(&$bean, $event, $arguments) {
		//$GLOBALS['log']->debug("*********************** ASOL: ENTRY class wfm_hook_on_hold function wakeup_on_hold \$bean->module_dir=[".$bean->module_dir."] \$bean->name=[".$bean->name."] \$bean->id=[".$bean->id."] \$bean->status=[".$bean->status."] \$bean->fetched_row=[".print_r($bean->fetched_row,true)."] \$event=[".$event."]****************");

		global $db;

		$wfm_module = $bean->module_dir; //Calls or Tasks modules
		$wfm_bean_id = $bean->id;
		$working_node_id = "";

		$sendCurlRequest = false;

		if (($event == "after_save") && (($wfm_module == "Calls") || ($wfm_module == "Tasks"))) {  ////////////*************** cuando le des a close a la call/task-sugar

			//if (!empty($bean->fetched_row)) {

				$wfm_action = "on_modify"; //Only on modify these kind of object/////////************ no es un on_delete  porque lo que haces es cambiar el estado con close, no estas borrando, la entrada permanece

				switch ($wfm_module) {


					case "Calls":
						//Check for Calls if there is any entry at the wfm_on_hold table
						if ($bean->status == "Held") {

							/*$callOnHoldQuery = $db->query("
															SELECT working_node_id 
															FROM wfm_on_hold 
															WHERE (bean_id='".$wfm_bean_id."') 
															LIMIT 1
														  ");//************solo se ejecuta uno por el wfm_bean_id...*/
							$callOnHoldQuery = $db->query("
															SELECT asol_workingnodes_id_c 
															FROM asol_onhold 
															WHERE (bean_id='".$wfm_bean_id."') 
															LIMIT 1
														  ");//************solo se ejecuta uno por el wfm_bean_id...
							$callOnHoldRow = $db->fetchByAssoc($callOnHoldQuery);

							$working_node_id = $callOnHoldRow['asol_workingnodes_id_c'];

							if (!empty($working_node_id))
							$sendCurlRequest = true;

						}
						break;
							
							
					case "Tasks":
						//Check for Tasks if there is any entry at the wfm_on_hold table
						if ($bean->status == "Completed") {

							/*$taskOnHoldQuery = $db->query("
															SELECT working_node_id 
															FROM wfm_on_hold 
															WHERE ( bean_id='".$wfm_bean_id."') 
															LIMIT 1
														  ");*/
							$taskOnHoldQuery = $db->query("
															SELECT asol_workingnodes_id_c 
															FROM asol_onhold 
															WHERE ( bean_id='".$wfm_bean_id."') 
															LIMIT 1
														  ");
							$taskOnHoldRow = $db->fetchByAssoc($taskOnHoldQuery);

							$working_node_id = $taskOnHoldRow['asol_workingnodes_id_c'];

							if (!empty($working_node_id))
							$sendCurlRequest = true;

						}
						break;


				}
				/////////////////////////////////////////////???????????????????????????????????????????????
				if ($sendCurlRequest) {
					/*$db->query("
									UPDATE wfm_working_nodes
									SET status = 'in_progress'
									WHERE (status = 'held' AND id = '".$working_node_id."')
							  ");*/
					$db->query("
									UPDATE asol_workingnodes
									SET status = 'in_progress'
									WHERE (status = 'held' AND id = '".$working_node_id."')
							  ");

					/*$db->query("
									DELETE FROM wfm_on_hold
									WHERE working_node_id = '".$working_node_id."'
							   ");*/
					$db->query("
									DELETE FROM asol_onhold
									WHERE asol_workingnodes_id_c = '".$working_node_id."'
							   ");

				}
				//////////////////////////////////////////////////????????????????????????????????????????

				//				if ($sendCurlRequest) {
				//
				//					$GLOBALS['log']->debug("ASOL **********************************************ENTRY cURL REQUEST (class wfm_hook_on_hold function wakeup_on_hold)***************************************");
				//					$ch = curl_init();
				//
				//					// set URL and other appropriate options
				//					curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1/sugarcrm/index.php?entryPoint=wfm_engine&wfm_module=".$wfm_module."&wfm_action=".$wfm_action."&wfm_bean_id=".$wfm_bean_id."&working_node_id=".$working_node_id."&execution_type=on_hold");
				//					$GLOBALS['log']->debug("ASOL ************cURL=http://127.0.0.1/sugarcrm/index.php?entryPoint=wfm_engine&wfm_module=".$wfm_module."&wfm_action=".$wfm_action."&wfm_bean_id=".$wfm_bean_id."&working_node_id=".$working_node_id."&execution_type=on_hold********************");
				//					curl_setopt($ch, CURLOPT_HEADER, 0);
				//					curl_setopt($ch, CURLOPT_TIMEOUT, 1);
				//					// grab URL and pass it to the browser
				//					curl_exec($ch);
				//
				//					// close cURL resource, and free up system resources
				//					curl_close($ch);
				//					$GLOBALS['log']->debug("ASOL ***********************************************EXIT cURL REQUEST (class wfm_hook_on_hold function wakeup_on_hold)*****************************************");
				//
				//				}

			//}

		}
		if (($event == "after_delete") && (($wfm_module == "Calls") || ($wfm_module == "Tasks"))) {  ////////////*************** cuando le des a delete a la call/task-sugar
			$wfm_action = "on_delete";
				
			/*$taskOnHoldQuery = $db->query("
															SELECT working_node_id 
															FROM wfm_on_hold 
															WHERE ( bean_id='".$wfm_bean_id."') 
															LIMIT 1
														  ");*/
			$taskOnHoldQuery = $db->query("
															SELECT asol_workingnodes_id_c 
															FROM asol_onhold 
															WHERE ( bean_id='".$wfm_bean_id."') 
															LIMIT 1
														  ");
			$taskOnHoldRow = $db->fetchByAssoc($taskOnHoldQuery);
			$working_node_id = $taskOnHoldRow['asol_workingnodes_id_c'];
				
			
			/*$db->query("
								UPDATE wfm_working_nodes 
								SET status='terminated' 
								WHERE id='".$working_node_id."'
						");*/
			$db->query("
								UPDATE asol_workingnodes 
								SET status='terminated' 
								WHERE id='".$working_node_id."'
						");
			
			/*$db->query("
							DELETE FROM wfm_on_hold
							WHERE (bean_id='".$wfm_bean_id."')
						");*/
			$db->query("
							DELETE FROM asol_onhold
							WHERE (bean_id='".$wfm_bean_id."')
						");
				
		}
		$GLOBALS['log']->debug("*********************** ASOL: EXIT class wfm_hook_on_hold function wakeup_on_hold \$bean->module_dir=[".$bean->module_dir."] \$bean->name=[".$bean->name."] \$bean->id=[".$bean->id."] \$event=[".$event."]****************");
	
	}

	
}

?>

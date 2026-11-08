<?php
//require_once("include/SugarPHPMailer.php");

//global $beanList, $beanFiles, $app_list_strings;

function executeTask($task_id, $task_type, $task_implementation, $bean_module, $bean_id, $process_instance_id, $working_node_id, $bean__ungreedy_count) {
	$GLOBALS['log']->debug("*********************** ASOL: ENTRY function executeTask");
  $task_implementation = json_decode($task_implementation);
	switch ($task_type) {
		case 'send_email':
			$success = task_send_email($task_implementation, $bean_module, $bean_id);
			break;
		case 'create_object':
			$success = task_create_object($task_implementation , $bean_module, $bean_id, $bean__ungreedy_count); // if succesful -> return Array('object_id' => $object_id,'object_module' =>$module ) else return false
			break;
		case 'modify_object':
			$success = task_modify_object($task_implementation,$bean_module,$bean_id, $bean__ungreedy_count); // if succesful -> return Array('object_id' => $object_id,'object_module' =>$module ) else return false
			break;
		case 'php_custom':
			$success = task_php_custom($task_id/*,$task_implementation*/);
			break;
		case 'end':
			$success = task_end($task_implementation, $process_instance_id, $working_node_id);
			break;
		case 'call_process':
			$success = task_call_process($task_implementation, $bean_module, $bean_id, $process_instance_id, $bean__ungreedy_count);
			break;

	}

	$GLOBALS['log']->debug("*********************** ASOL: EXIT function executeTask");
	return $success;
}

function task_send_email($implementationField, $bean_module, $bean_id) {

	global $beanList, $beanFiles, $app_list_strings, $db;
	require_once("include/SugarPHPMailer.php");

	$GLOBALS['log']->debug("*********************** ASOL: Inside function task_send_email");

	//Get the email template bean
	require_once("modules/EmailTemplates/EmailTemplate.php");
	$templateBean = wfm_load_bean('EmailTemplates',$implementationField->template_id);
	//Get the email template bean


  $bodyHtml = html_entity_decode($templateBean->body_html,ENT_COMPAT,'UTF-8');
	$bodyHtml = replace_wfm_vars($bodyHtml, $bean_module, $bean_id);


	$mail = new SugarPHPMailer();

	$mail->setMailerForSystem();

	$mail->From = $implementationField->from;
	$mail->FromName = $implementationField->from;
	$mail->Timeout = 30;

  $to_address = array();
  $list = replace_wfm_vars($implementationField->list, $bean_module, $bean_id);
	$emails = preg_split('/[,\n\r]+/', $list,-1, PREG_SPLIT_NO_EMPTY); // It could be more than one email. Emails have to be separated by commas(with no blanks).
	if (!empty($emails[0])) {
		foreach($emails as $key => $value) {
			$mail->AddAddress($value);
      $to_address[] = $value;
		}
	}
	////////////////////////////////////////********************************************
	// Get emails from selected Users & Roles in the send email task.
	$users = $implementationField->users;
	if (!empty($users[0])) {
		$GLOBALS['log']->debug("*********************** ASOL: \$users=[".print_r($users,true)."]");
		$acl_roles = $implementationField->roles;
		$GLOBALS['log']->debug("*********************** ASOL: \$acl_roles=[".print_r($acl_roles,true)."]");

		// For each user, we get his email by means of the function getUsersNameAndEmail() that is defined in User.php
		foreach ($users as $key => $value) {
			$GLOBALS['log']->debug("*********************** ASOL:user");
			$theUser = wfm_load_bean('Users', $value);
			$name_and_email__array = $theUser->getUsersNameAndEmail();
			$mail->AddAddress($name_and_email__array['email']);
      $to_address[] =$name_and_email__array['email'];

		}

		// For each role, we get his users(it can be more than one). And once we have the role?s users, we get the user?s email.
		if (!empty($acl_roles[0])) {
			foreach($acl_roles as $key => $value) {

				$GLOBALS['log']->debug("*********************** ASOL:asol_role");
				$users_from__acl_role = Array();
				$user_from__acl_role__query = $db->query("
													SELECT user_id
													FROM acl_roles_users
													WHERE role_id = '".$value."'
												");
				while($user_from__acl_role__row = $db->fetchByAssoc($user_from__acl_role__query)) {
					$users_from__acl_role[] = $user_from__acl_role__row['user_id'];
				}
				$GLOBALS['log']->debug("*********************** ASOL: \$users_from__acl_role=[".print_r($users_from__acl_role,true)."]");
				if (!empty($users_from__acl_role[0])) {
					foreach($users_from__acl_role as $key => $value) {
						$GLOBALS['log']->debug("*********************** ASOL:user de asol_role");
            $theUser = wfm_load_bean('Users', $value);
						$name_and_email__array = $theUser->getUsersNameAndEmail();
						$mail->AddAddress($name_and_email__array['email']);
            $to_address[] =$name_and_email__array['email'];
					}
				}
			}
		}
	}
    if(empty($to_address)){
        return true;
    }
    
	////////////////////////////////////////*************************************

	//$GLOBALS['log']->debug("*********************** ASOL: \$fields=".print_r($fields[5],true));
	$mail->IsHTML(true);

	//	$mail->Subject = "mola_subject";
	//	$mail->Body = "mola_body";
  $subject =replace_wfm_vars(from_html($templateBean->subject), $bean_module, $bean_id);
	$mail->Subject = $subject;
	//$GLOBALS['log']->debug("*********************** ASOL: subject=".$mail->Subject);
	$mail->Body = $bodyHtml;
	//$GLOBALS['log']->debug("*********************** ASOL: body=".$mail->Body);
	$mail->AltBody = from_html($templateBean->body);
	//$GLOBALS['log']->debug("*********************** ASOL: altbody=".$mail->AltBody);

  $file_attachments = array();
	$attachmentsQuery = $db->query("SELECT * FROM notes WHERE parent_type='Emails' AND parent_id='".$templateBean->id."' AND deleted = 0");

	while($row = $db->fetchByAssoc($attachmentsQuery)) {
		$GLOBALS['log']->debug("*********************** ASOL: row = ".print_r($row,true));
    $file_location = getcwd()."/cache/upload/".$row['id'];
    $file_attachments[] = array(
      'location' => $file_location,
      'name' => $row['filename'],
      'mimeType' => $row['file_mime_type'],
    );
		$mail->AddAttachment($file_location, $row['filename']);

	}

  $string_attachments = array();
  $attachmentsQuery = $db->query("SELECT * FROM reportviewer_beans WHERE bean_module='EmailTemplates' AND bean_id='".$templateBean->id."' AND deleted = 0");

  $bean = wfm_load_bean($bean_module, $bean_id);
  while($row = $db->fetchByAssoc($attachmentsQuery,-1,false)) {
    $GLOBALS['log']->debug("*********************** ASOL: row = ".print_r($row,true));
    $report = wfm_load_bean('ReportViewer',$row['report_id']);
    $reportBinary = $report->execute($bean);
    if($reportBinary) {
      $filename = $report->buildFileNameFrom($row['filename']);
      $filename = replace_wfm_vars($filename, $bean_module, $bean_id);
      $mail->AddStringAttachment($reportBinary, $filename,'base64', $report->getMimeType());
      $string_attachments[] = array(
        'string' => $reportBinary,
        'name' =>  $filename,
        'mimeType' => $report->getMimeType(),
      );

    }
  }





	//$GLOBALS['log']->debug("*********************** ASOL: \$mail=".print_r($mail,true));
	$success = $mail->Send();

	$tries=1;
	while (!($success) && ($tries < 5)) {
		sleep(5);
		$success = $mail->Send();
		$tries++;
	}

  if($success &&  $implementationField->archive ) {
    require_once ('modules/Emails/Email.php');
    $email = new Email();
    $email->id = create_guid();
    $email->new_with_id = true;
    $email->name = $subject;
    $email->type = 'out';
    $email->status = 'sent';
    $email->intent='pick';
    global $timedate,$sugar_config;
    $email->date_sent =date($timedate->get_date_time_format());
    $email->to_addrs =implode(',',$to_address);
    $email->to_addrs_names = $email->to_addrs;
    $email->from_addr = $implementationField->from;
    $email->from_addr_name = $implementationField->from;

    $email->description_html = $bodyHtml;
    $email->description = $templateBean->body;

    $email->parent_type=$bean_module;
    $email->parent_id=$bean_id;

    // Needed to stop automatic linking of note to record that triggered workflow
    $email->not_use_rel_in_req = true;
    $email->in_workflow = true;
    $email->new_rel_id=false;
    $email->new_rel_relname=false;

    $email->save();

    if($bean->load_relationship('emails')) {
      $bean->emails->add($email->id);
    } // if



    foreach($file_attachments as $f) {
      $email->saveTempNoteAttachments($f['name'], $f['location'], $f['mimeType']);
    }
    foreach($string_attachments as $f) {
      $tmpNote = new Note();
      $tmpNote->id = create_guid();
      $tmpNote->new_with_id = true;
      $tmpNote->parent_id = $email->id;
      $tmpNote->parent_type = 'Emails';
      $tmpNote->name = $f['name'];
      $tmpNote->filename = $f['name'];
      $tmpNote->file_mime_type = $f['mimeType'];

      $noteFile = "{$sugar_config['upload_dir']}{$tmpNote->id}";
      if($fp = fopen($noteFile, 'wb')) {
        fwrite($fp, $f['string']);
        fclose($fp);
      }

      // Needed to stop automatic linking of note to record that triggered workflow
      $tmpNote->not_use_rel_in_req = true;
      $tmpNote->in_workflow = true;
      $tmpNote->new_rel_id=false;
      $tmpNote->new_rel_relname=false;

      $tmpNote->save();
    }
  }

	$GLOBALS['log']->debug("*********************** ASOL: END- Inside function task_send_email");
	return $success;
}


function task_php_custom($task_id/*,$script*/) {

	//$script_to_disk_file = urldecode($script);

	///////////////////////////////////////////////////
	$myFile = $task_id.".php";
	/*$fh = fopen("modules/asol_Task/temp_php_custom_Files/".$myFile, 'w') or die("can't open file");

	$stringData = "<?php ".$script_to_disk_file."?>";
	fwrite($fh, $stringData);

	fclose($fh);// *********************flush????*/
	/////////////////////////////////////////////////

	//sleep(5);
	require_once ("modules/asol_Task/temp_php_custom_Files/{$myFile}");

	return true;
}

function task_end($task_implementation, $process_instance_id, $working_node_id) {

	global $db;


	$db->query("
					UPDATE asol_workingnodes 
					SET status='terminated' 
					WHERE id='".$working_node_id."'
			");/////////****************** esta query ya se hace tambien despues, cuando muere la rama. Se supone que la task_type=end tiene que ser la ultima tarea del working_node...

	if ($task_implementation->terminate_process) { // terminate process
		echo "terminate_process";
		$db->query("
						UPDATE asol_workingnodes 
						SET status='terminated' 
						WHERE asol_processinstances_id_c='".$process_instance_id."'
				");
	}

	return 'task_end';

}

function task_continue() {
}

function task_create_object($implementationField, $bean_module, $bean_id, $bean__ungreedy_count ) {

	$GLOBALS['log']->debug("*********************** ASOL: ENTRY function task_create_object");
	global $beanList, $beanFiles, $app_list_strings;
	//	echo "beanList<BR><BR><BR>";
	//	print_r($beanList);
	//	echo "<BR><BR>beanFiles<BR><BR><BR>";
	//	print_r($beanFiles);
	//	echo "<BR><BR>app_list_strings<BR><BR><BR>";
	//	print_r($app_list_strings);

	$module = $implementationField->module;
	//echo "module=[" . $module . "]";
	
	if ($module == "") {
		$GLOBALS['log']->debug("*********************** ASOL: no module selected");
		return false;
	}
	
	$class_name = $beanList[$module];
	//echo "class_name=[" . $class_name ."]";
	require_once($beanFiles[$class_name]);
	$bean_create_object = new $class_name();

	$fields = $implementationField->fields;

	foreach ($fields as $field) {

		$value_field = replace_wfm_vars(urldecode($field->value), $bean_module, $bean_id);

    $fieldname=$field->name;
		$bean_create_object->$fieldname = $value_field;
		//echo "||field[0]=[" . $field[0] . "]";
		//echo "||field[1]=[" . $field[1] . "]";
	}

	$bean_create_object->ungreedy_count = $bean__ungreedy_count + 1;

	//$GLOBALS['log']->debug("*********************** ASOL: before_save() \$bean_create_object=[".print_r($bean_create_object,true)."]");
	$object_id = $bean_create_object->save();

	//echo "<BR><BR>bean<BR><BR><BR>";
	//print_r($bean_create_object);
	//$GLOBALS['log']->debug("*********************** ASOL: after_save() \$bean_create_object=[".print_r($bean_create_object,true)."]");
	$GLOBALS['log']->debug("*********************** ASOL: after_save() \$bean_create_object->id=[".$bean_create_object->id."]");
	//echo "<BR>><BR>END bean<BR>><BR>";

	$GLOBALS['log']->debug("*********************** ASOL: EXIT function task_create_object");
	if ($object_id != false) {
		return Array('object_id' => $object_id,'object_module' =>$module );
	}
	return $object_id; //It has to be false if the flow comes this away.
}

function task_modify_object($implementationField, $bean_module, $bean_id, $bean__ungreedy_count ) {

	global $beanList, $beanFiles, $app_list_strings;

	$module = $implementationField->module;

	$bean = wfm_load_bean($bean_module, $bean_id);
	$fields = $implementationField->fields;

	foreach ($fields as $field) {
    $value_field = replace_wfm_vars($field->value, $bean_module, $bean_id, $bean);
    $fieldname=$field->name;
		$bean->$fieldname = $value_field;
	}

	$bean->ungreedy_count = $bean__ungreedy_count + 1;

	$object_id = $bean->save();

	if ($object_id != false) {
		return Array('object_id' => $object_id, 'object_module' => $module );
	}
	return $object_id; //It has to be false if the flow comes this away.
}

function task_call_process( $task_implementation, $parent_bean_module, $parent_bean_id, $parent_process_instance_id, $bean__ungreedy_count) {
	global $db;

	$GLOBALS['log']->debug("*********************** ASOL: ENTRY function task_call_process");


	$process_id = $task_implementation->process_id;
	$process_name = $task_implementation->process_name;
	$event_id = $task_implementation->event_id;
	$event_name = $task_implementation->event_name;



	////////////************ 

	$process_id__from_parent_process_instance_id__query = $db->query("
																		SELECT asol_process_id_c
																		FROM asol_processinstances
																		WHERE id = '".$parent_process_instance_id."'
																	");
	$process_id__from_parent_process_instance_id__row = $db->fetchByAssoc($process_id__from_parent_process_instance_id__query);

	if ($process_id == $process_id__from_parent_process_instance_id__row['asol_process_id_c']) {	// To avoid that a process can call_process itselft at run-time.
		$GLOBALS['log']->debug("*********************** ASOL: process_id == subprocess_id -> do NOT execute.");
		return false;
	}
	
	//////////
	
	$subprocess_query = $db->query("
										SELECT status, bean_module
										FROM asol_process
										WHERE id = '".$process_id."'
									");
	$subprocess_row = $db->fetchByAssoc($subprocess_query);
	
	if ($subprocess_row['status'] == 'inactive') {	// To avoid that a process can call_process a subprocess that is inactive.
		$GLOBALS['log']->debug("*********************** ASOL: Subprocess is inactive -> do NOT execute.");
		return false;
	}
	
	//////////
	
	$parent_process_query = $db->query("
										SELECT status, bean_module
										FROM asol_process
										WHERE id = '".$process_id__from_parent_process_instance_id__row['asol_process_id_c']."'
									");
	$parent_process_row = $db->fetchByAssoc($parent_process_query);
	
	if ($subprocess_row['bean_module'] != $parent_process_row['bean_module']) {	// To avoid that a process can call_process a subprocess that has a different Module.
		$GLOBALS['log']->debug("*********************** ASOL: \$subprocess_row['bean_module'] != \$parent_process_row['bean_module'] -> do NOT execute.");
		return false;
	}
	

	/////////////***********


	/////////////////////////////////////////////////
	//initialize_subprocess($process_id, $process_name, $event_id, $event_name, $parent_bean_module, $parent_bean_id, $parent_process_instance_id);
	//$GLOBALS['log']->debug("*********************** ASOL: ENTRY wfm_engine_functions.php function initialize_subprocess \$process_id=[".$process_id."] \$process_name=[".$process_name."] \$event_id=[".$event_id."] \$event_name=[".$event_name."] \$parent_bean_module=[".$parent_bean_module."] \$parent_bean_id=[".$parent_bean_id."] \$parent_process_instance_id=[".$parent_process_instance_id."]*******************************
	//*************************************************************************************************************************************************************************");



	$id1 = create_guid();
	$process_instance_id = create_guid();
	//$process_id = $value['process_id'];
	//$parent_process_instance_id = 'null';

	$bean__ungreedy_count++;
	$id1 = create_guid();
	$name1 = "p_i_".$id1;

	

	$activity_query = $db->query("
										SELECT asol_events_id AS event_id, asol_activity_id AS activity_id
										FROM asol_events_asol_activity
										WHERE (asol_events_id = '" . $event_id . "' AND deleted = 0)
								");
	while ($activity_row = $db->fetchByAssoc($activity_query)) {
		$events_activities_let[] = $activity_row;
	}
	
	if (!isset($events_activities_let)) {	// To allow that an event can have no activities. Otherwise, it will not clean the database correctly.
		return false;
	}
	
	
	$db->query("
					INSERT INTO asol_processinstances (id, name, asol_process_id_c, bean_id, bean_module, asol_processinstances_id_c, bean_ungreedy_count)
					VALUES ('".$id1."', '".$name1."', '".$process_id."', '".$parent_bean_id."', '".$parent_bean_module."', '".$parent_process_instance_id."', ".$bean__ungreedy_count.")
				");

	foreach($events_activities_let as $key => $value) {

		$priority = 1; //subprocess?s priority

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
								INSERT asol_workingnodes (id, name, asol_processinstances_id_c, priority, date_entered, date_modified, asol_activity_id_c, asol_task_id_c, delay_wakeup_time, created_by, status)
								VALUES ('".$id2."', '".$name2."', '".$id1."', ".$priority.", '".$date_entered."', '".$date_modified."', '".$current_activity."', ".$current_task.", '".$delay_wakeup_time."', '".$created_by."', '".$status."')
						   ");

	}



	/////////////////////////////////////////////////


	$GLOBALS['log']->debug("*********************** ASOL: events_activities_let-TOTAL " . print_r($events_activities_let, true) . "*******************************************");


	$GLOBALS['log']->debug("*********************** ASOL: EXIT function task_call_process");
	
	return true;
}

function replace_wfm_vars($text_with_vars, $bean_module, $bean_id) {
	//Format and replace variables to bodyHtml
	//$text = html_entity_decode($text_with_vars);
	$GLOBALS['log']->debug("*********************** ASOL: ENTRY function replace_wfm_vars \$text_with_vars=[".$text_with_vars."], \$bean_module=[".$bean_module."], \$bean_id=[".$bean_id."]");
	global $beanList, $beanFiles;

  $bean = wfm_load_bean($bean_module, $bean_id);

	$tmpText = $text_with_vars;

	$pos = strpos($tmpText, '${');

	//$GLOBALS['log']->debug("*********************** ASOL:\$pos=[".print_r($pos,true)."]");
	$beanItems = Array();
	while ($pos !== false) {

		//$GLOBALS['log']->debug("*********************** ASOL:while");
		$tmp_last = substr($tmpText, $pos);
		$posEnd = strpos($tmp_last, '}');


		$first = ($pos === 0) ? "" : substr($tmpText, 0, $pos-1);
		//$GLOBALS['log']->debug("*********************** ASOL:\$first=[".print_r($first,true)."]");
		$item = substr($tmp_last, 0, $posEnd+1);
		//$GLOBALS['log']->debug("*********************** ASOL:\$item=[".print_r($item,true)."]");
		$last = substr($tmp_last, $posEnd+2);
		//$GLOBALS['log']->debug("*********************** ASOL:\$last=[".print_r($last,true)."]");

		$tmpText = $first." ASOL ".$last;
		//$GLOBALS['log']->debug("*********************** ASOL:\$tmpText=[".print_r($tmpText,true)."]");
		$beanItems[] = $item;



		$pos = strpos($tmpText, '${');
		//$GLOBALS['log']->debug("*********************** ASOL:\$pos=[".print_r($pos,true)."]");
	}

	foreach($beanItems as $beanItem) {
		//$GLOBALS['log']->debug("*********************** ASOL:foreach");
		$beanField = "";
		$tmpBeanItem = substr($beanItem, 2);
		$tmpBeanItem = substr($tmpBeanItem, 0, -1);

		$beanValues = explode("->", $tmpBeanItem);

    if(count($beanValues) > 1 && $beanValues[0]='bean') {
       $beanField = evaluateBeanVariable($beanValues,$bean);
    } else if (count($beanValues) == 2) {
			//$GLOBALS['log']->debug("*********************** ASOL:count(beanValues)=2");
			if ($beanValues[0] == "current_datetime") {
				$delta = $beanValues[1];
				$delta__array = explode(' ',$delta);
				$delta_date = $delta__array[0];
				$delta_time = $delta__array[1];

				$delta_date__array = explode('-',$delta_date);
				$years = $delta_date__array[0];
				$months = $delta_date__array[1];
				$days = $delta_date__array[2];

				$delta_time__array = explode(':',$delta_time);
				$hours = $delta_time__array[0];
				$minutes = $delta_time__array[1];

				$current_datetime__plus__delta  = date("Y-m-d H:i:s", mktime(gmdate("H") + $hours, gmdate("i") + $minutes, gmdate("s") , gmdate("m") + $months, gmdate("d") + $days, gmdate("Y") + $years));
				$GLOBALS['log']->debug("*********************** ASOL: \$current_datetime__plus__delta=[",$current_datetime__plus__delta,"]");
				$beanField = $current_datetime__plus__delta;
			}
			elseif ($beanValues[0] == "current_date") {
				$delta = $beanValues[1];
				//$delta__array = explode(' ',$delta);
				$delta_date = $delta;
				//$delta_time = $delta__array[1];

				$delta_date__array = explode('-',$delta_date);
				$years = $delta_date__array[0];
				$months = $delta_date__array[1];
				$days = $delta_date__array[2];

				/*$delta_time__array = explode(':',$delta_time);
				 $hours = $delta_time__array[0];
				 $minutes = $delta_time__array[1];*/

				$current_date__plus__delta  = date("Y-m-d", mktime(gmdate("H") , gmdate("i") , gmdate("s") , gmdate("m") + $months, gmdate("d") + $days, gmdate("Y") + $years));
				$GLOBALS['log']->debug("*********************** ASOL: \$current_date__plus__delta=[",$current_date__plus__delta,"]");
				$beanField = $current_date__plus__delta;
			}
			elseif ($beanValues[0] == "bean")  {
				$beanField = $bean->$beanValues[1];
			}
			elseif ($beanValues[0] == "old_bean")  {
        if( $bean->fetched_row ) {
				    $beanField = $bean->fetched_row[$beanValues[1]];
        }
			}

		} else if (count($beanValues) == 3) {
			//$GLOBALS['log']->debug("*********************** ASOL:count(beanValues)=3");
      $related_bean = wfm_load_bean($beanValues[0],$bean->$beanValues[1] );
			$beanField = $related_bean->$beanValues[2];

		}

		$text_with_vars = str_replace($beanItem, $beanField, $text_with_vars);

	}
	//Format and replace variables to bodyHtml

	$GLOBALS['log']->debug("*********************** ASOL: EXIT function replace_wfm_vars ");
	return $text_with_vars;
}

function evaluateBeanVariable($beanValues, $bean) {
  global $beanList;
  for($i =1; $i < count($beanValues); $i++) {
    $value = $beanValues[$i];
    if(strpos($value,'(')> 0) {
      $openParen = strpos($value,'(');
      $closeParen = strpos($value,')');
      $method=substr($value, 0,$openParen);
      $args = substr($value,$openParen+1, $closeParen - $openParen-1);
      $args = explode(',',$args);
      $bean = call_user_func_array(array($bean, $method), $args);
    } else if($i < count($beanValues)-1) {
      $openBracket = strpos($value,'[');
      $index = 0;
      $field = $value;
      if($openBracket> 0 ) {
        $closeBracket = strpos($value,']');
        $field=substr($value, 0,$openBracket);
        $index += substr($value,$openBracket+1, $closeBracket- $openBracket-1);
      }
      $bean = wfm_load_linked($bean, $field);
      $bean = $bean[$index];
    } else {
      $bean = $bean->$value;
    }
  }
  return $bean;
}

?>

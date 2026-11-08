<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 ********************************************************************************/


// Task is used to store customer information.
class Task extends SugarBean {
        var $field_name_map;

	// Stored fields
	var $id;
	var $date_entered;
	var $date_modified;
	var $assigned_user_id;
	var $modified_user_id;
	var $created_by;
	var $created_by_name;
	var $modified_by_name;
	var $description;
	var $name;
	var $status;
	var $date_due_flag;
	var $date_due;
	var $time_due;
	var $date_start_flag;
	var $date_start;
	var $time_start;
	var $priority;
	var $parent_type;
	var $parent_id;
	var $contact_id;
    var $activity_schedule;
    var $activity_schedule_id;

	var $parent_name;
	var $contact_name;
	var $contact_phone;
	var $contact_email;
	var $assigned_user_name;

    var $onlinedocument_id;

	var $duration_hours;
	var $duration_minutes;
	var $minutes_value_default = 15;
	var $minutes_values = array('0'=>'00','15'=>'15','30'=>'30','45'=>'45');
	var $update_vcal = true;
	var $contacts_arr;
	var $users_arr;
	var $leads_arr;
	var $accept_status;
	var $rel_users_table = "tasks_users";
	var $rel_contacts_table = "tasks_contacts";
	var $rel_leads_table = "tasks_leads";

//bug 28138 todo
//	var $default_task_name_values = array('Assemble catalogs', 'Make travel arrangements', 'Send a letter', 'Send contract', 'Send fax', 'Send a follow-up letter', 'Send literature', 'Send proposal', 'Send quote', 'Call to schedule meeting', 'Setup evaluation', 'Get demo feedback', 'Arrange introduction', 'Escalate support request', 'Close out support request', 'Ship product', 'Arrange reference call', 'Schedule training', 'Send local user group information', 'Add to mailing list');

	var $table_name = "tasks";

	var $object_name = "Task";
	var $module_dir = 'Tasks';

	var $importable = true;
	// This is used to retrieve related fields from form posts.
	var $additional_column_fields = Array('assigned_user_name', 'assigned_user_id', 'contact_name', 'contact_phone', 'contact_email', 'parent_name', 'onlinedocument_id');

    var $relationship_fields = Array(
        'onlinedocument_id'=>'onlinedocuments',
    );


	function Task() {
		parent::SugarBean();
	}

	var $new_schema = true;

    function pre_save() {
        parent::pre_save();
        if( empty($this->activity_schedule)) {
            $this->activity_schedule = new ActivitySchedule();
            $this->activity_schedule->addFieldsTo($this);
        }
    }
    
    // save date_end by calculating user input
    // this is for calendar
    function save($check_notify = FALSE)
    {

        global $timedate, $current_user;

        if (isset($this->date_start) && isset($this->duration_hours) && isset($this->duration_minutes))
        {
            $td = $timedate->fromDb($this->date_start);
            if ($td)
            {
                $this->date_end = $td->modify("+{$this->duration_hours} hours {$this->duration_minutes} mins")->asDb();
            }
        }

        if (!empty($_REQUEST['send_invites']) && $_REQUEST['send_invites'] == '1')
        {
            $check_notify = true;
            $this->special_notification = true;
        }
        else
        {
            $check_notify = false;
        }
        if (empty($_REQUEST['send_invites']))
        {
            if (!empty($this->id))
            {
                $old_record = new Task();
                $old_record->retrieve($this->id);
                $old_assigned_user_id = $old_record->assigned_user_id;
            }
            if ((empty($this->id) && isset($_REQUEST['assigned_user_id']) && !empty($_REQUEST['assigned_user_id']) && $GLOBALS['current_user']->id != $_REQUEST['assigned_user_id']) || (isset($old_assigned_user_id) && !empty($old_assigned_user_id) && isset($_REQUEST['assigned_user_id']) && !empty($_REQUEST['assigned_user_id']) && $old_assigned_user_id != $_REQUEST['assigned_user_id']))
            {
                $check_notify = true;
                if (isset($_REQUEST['assigned_user_name']))
                {
                    $this->new_assigned_user_name = $_REQUEST['assigned_user_name'];
                }
            }
        }

        if (empty($this->status) ) {
            $this->status = $this->getDefaultStatus();
        }

        $resaveSchedule = false;
        if(!empty ($this->activity_schedule) && isset($this->activity_schedule_type)) {
            $this->activity_schedule->setCompletionOffsetFromEndDate($this->date_due);
            $resaveSchedule = $this->activity_schedule->saveScheduleFor($this,$check_notify);
        }

        $result_id = parent::save($check_notify);

        if($resaveSchedule) {
            $resaveSchedule = $this->activity_schedule->saveScheduleFor($this,$check_notify);
        }

        global $current_user;
        if($this->update_vcal) {
            vCal::cache_sugar_vcal($current_user);
        }
	return $result_id;
    }

	function get_summary_text()
	{
		return "$this->name";
	}

    function create_export_query(&$order_by, &$where, $relate_link_join='')
    {
        $custom_join = $this->getCustomJoin(true, true, $where);
        $custom_join['join'] .= $relate_link_join;
                $contact_required = stristr($where,"contacts");
                if($contact_required)
                {
                        $query = "SELECT tasks.*, contacts.first_name, contacts.last_name, users.user_name as assigned_user_name ";
                        $query .= $custom_join['select'];
                        $query .= " FROM contacts, tasks ";
                        $where_auto = "tasks.contact_id = contacts.id AND tasks.deleted=0 AND contacts.deleted=0";
                }
                else
                {
                        $query = 'SELECT tasks.*, users.user_name as assigned_user_name ';
                        $query .= $custom_join['select'];
                        $query .= ' FROM tasks ';
                        $where_auto = "tasks.deleted=0";
                }


        $query .= $custom_join['join'];
		$query .= "  LEFT JOIN users ON tasks.assigned_user_id=users.id ";

                if($where != "")
                        $query .= "where $where AND ".$where_auto;
                else
                        $query .= "where ".$where_auto;

        $order_by = $this->process_order_by($order_by);
        if (empty($order_by)) {
            $order_by = 'tasks.name';
        }
        $query .= ' ORDER BY ' . $order_by;

                return $query;

        }



  function fill_in_additional_list_fields() {
    parent::fill_in_additional_list_fields();
    // EINTEL Schedule loading
    //ActivitySchedule::loadScheduleFor($this);
	}


	function fill_in_additional_detail_fields()
	{
        parent::fill_in_additional_detail_fields();
		global $app_strings;

		if (isset($this->contact_id)) {

			$contact = new Contact();
			$contact->retrieve($this->contact_id);

			if($contact->id != "") {
				$this->contact_name = $contact->full_name;
				$this->contact_name_owner = $contact->assigned_user_id;
				$this->contact_name_mod = 'Contacts';
				$this->contact_phone = $contact->phone_work;
				$this->contact_email = $contact->emailAddress->getPrimaryAddress($contact);
			} else {
				$this->contact_name_mod = '';
				$this->contact_name_owner = '';
				$this->contact_name='';
				$this->contact_email = '';
				$this->contact_id='';
			}

		}

		$this->fill_in_additional_parent_fields();

    // EINTEL Schedule loading
    ActivitySchedule::loadScheduleFor($this);
	}

	function fill_in_additional_parent_fields()
	{

		$this->parent_name = '';
		global $app_strings, $beanFiles, $beanList, $locale;
		if ( ! isset($beanList[$this->parent_type]))
		{
			$this->parent_name = '';
			return;
		}

	    $beanType = $beanList[$this->parent_type];
		require_once($beanFiles[$beanType]);
		$parent = new $beanType();

		if (is_subclass_of($parent, 'Person')) {
			$query = "SELECT first_name, last_name, assigned_user_id parent_name_owner from $parent->table_name where id = '$this->parent_id'";
		}
		else if (is_subclass_of($parent, 'File')) {
			$query = "SELECT document_name, assigned_user_id parent_name_owner from $parent->table_name where id = '$this->parent_id'";
		}
		else {

			$query = "SELECT name ";
			if(isset($parent->field_defs['assigned_user_id'])){
				$query .= " , assigned_user_id parent_name_owner ";
			}else{
				$query .= " , created_by parent_name_owner ";
			}
			$query .= " from $parent->table_name where id = '$this->parent_id'";
		}
		$result = $this->db->query($query,true," Error filling in additional detail fields: ");

		// Get the id and the name.
		$row = $this->db->fetchByAssoc($result);

		if ($row && !empty($row['parent_name_owner'])){
			$this->parent_name_owner = $row['parent_name_owner'];
			$this->parent_name_mod = $this->parent_type;
		}
		if (is_subclass_of($parent, 'Person') and $row != null)
		{
			$this->parent_name = $locale->getLocaleFormattedName(stripslashes($row['first_name']), stripslashes($row['last_name']));
		}
		else if (is_subclass_of($parent, 'File') && $row != null) {
			$this->parent_name = $row['document_name'];
		}
		elseif($row != null)
		{
			$this->parent_name = stripslashes($row['name']);
		}
		else {
			$this->parent_name = '';
		}
	}


    protected function formatStartAndDueDates(&$task_fields, $dbtime, $override_date_for_subpanel)
    {
        global $timedate;

        if(empty($dbtime)) return;

        $today = $timedate->nowDbDate();

        $task_fields['TIME_DUE'] = $timedate->to_display_time($dbtime);
        $task_fields['DATE_DUE'] = $timedate->to_display_date($dbtime);

        $date_due = $task_fields['DATE_DUE'];

        $dd = $timedate->to_db_date($date_due, false);
        $taskClass = 'futureTask';
		if ($dd < $today){
            $taskClass = 'overdueTask';
		}else if( $dd	== $today ){
            $taskClass = 'todaysTask';
		}
        $task_fields['DATE_DUE']= "<font class='$taskClass'>$date_due</font>";
        if($override_date_for_subpanel){
            $task_fields['DATE_START']= "<font class='$taskClass'>$date_due</font>";
        }
    }

	function get_list_view_data(){
		global $action, $currentModule, $focus, $current_module_strings, $app_list_strings, $timedate;

		$override_date_for_subpanel = false;
		if(!empty($_REQUEST['module']) && $_REQUEST['module'] !='Calendar' && $_REQUEST['module'] !='Tasks' && $_REQUEST['module'] !='Home'){
			//this is a subpanel list view, so override the due date with start date so that collections subpanel works as expected
			$override_date_for_subpanel = true;
		}

		$today = $timedate->nowDb();
		$task_fields = $this->get_list_view_array();
		$dbtime = $timedate->to_db($task_fields['DATE_DUE']);
		if($override_date_for_subpanel){
			$dbtime = $timedate->to_db($task_fields['DATE_START']);
		}

        if(!empty($dbtime))
        {
            $task_fields['TIME_DUE'] = $timedate->to_display_time($dbtime);
            $task_fields['DATE_DUE'] = $timedate->to_display_date($dbtime);
            $this->formatStartAndDueDates($task_fields, $dbtime, $override_date_for_subpanel);
        }

		if (!empty($this->priority))
			$task_fields['PRIORITY'] = $app_list_strings['task_priority_dom'][$this->priority];
		if (isset($this->parent_type))
			$task_fields['PARENT_MODULE'] = $this->parent_type;
		if ($this->status != "Completed" && $this->status != "Deferred" )
		{
			$setCompleteUrl = "<a id='{$this->id}' onclick='SUGAR.util.closeActivityPanel.show(\"{$this->module_dir}\",\"{$this->id}\",\"Completed\",\"listview\",\"1\");'>";
		    $task_fields['SET_COMPLETE'] = $setCompleteUrl . SugarThemeRegistry::current()->getImage("close_inline","title=".translate('LBL_LIST_CLOSE','Tasks')." border='0'",null,null,'.gif',translate('LBL_LIST_CLOSE','Tasks'))."</a>";
		}

        // make sure we grab the localized version of the contact name, if a contact is provided
        if (!empty($this->contact_id))
        {
            $contact_temp = BeanFactory::getBean("Contacts", $this->contact_id);
            if (!empty($contact_temp))
            {
                // Make first name, last name, salutation and title of Contacts respect field level ACLs
                $contact_temp->_create_proper_name_field();
                $this->contact_name = $contact_temp->full_name;
                $this->contact_phone = $contact_temp->phone_work;
            }
        }

		$task_fields['CONTACT_NAME']= $this->contact_name;
		$task_fields['CONTACT_PHONE']= $this->contact_phone;
		$task_fields['TITLE'] = '';
		if (!empty($task_fields['CONTACT_NAME'])) {
			$task_fields['TITLE'] .= $current_module_strings['LBL_LIST_CONTACT'].": ".$task_fields['CONTACT_NAME'];
		}
		if (!empty($this->parent_name)) {
			$task_fields['TITLE'] .= "\n".$app_list_strings['parent_type_display'][$this->parent_type].": ".$this->parent_name;
			$task_fields['PARENT_NAME']=$this->parent_name;
		}
                
                if(isset($task_fields['ACTIVITY_SCHEDULE'])){
                    //ActivitySchedule::loadScheduleFor($this);
                    //loadScheduleFor does not support list view and the array keys being uppercase
                    //manually setting the values
                    //Load up bean with all fields, this calls loadschedulefor, then map fields across
                    $focus_tasks = BeanFactory::getBean('Tasks',$this->id);  
                    $task_fields['ACTIVITY_SCHEDULE_ID'] = $focus_tasks->activity_schedule_id;
                    $task_fields['ACTIVITY_SCHEDULE_TYPE'] = $focus_tasks->activity_schedule_type;	
                    $task_fields['ACTIVITY_SCHEDULE_REPEAT_INTERVAL'] = $focus_tasks->activity_schedule_repeat_interval;
                    $task_fields['ACTIVITY_SCHEDULE_STARTDATE'] = $focus_tasks->activity_schedule_startdate;
                    $task_fields['ACTIVITY_SCHEDULE_NEXTDATE'] = $focus_tasks->activity_schedule_nextdate;
                    $task_fields['ACTIVITY_SCHEDULE_ENDDATE'] = $focus_tasks->activity_schedule_enddate;
                    $task_fields['ACTIVITY_SCHEDULE_NUMWITHINMONTH'] = $focus_tasks->activity_schedule_numwithinmonth;
                    $task_fields['ACTIVITY_SCHEDULE_DAYSOFWEEK'] = $focus_tasks->activity_schedule_daysofweek;
                }
                    
                
                
                
		return $task_fields;
	}

	function set_notification_body($xtpl, $task)
	{
		global $app_list_strings;
        global $timedate;
//      $notifyUser = $task->current_notify_user;
//      $prefDate = $notifyUser->getUserDateTimePreferences();
        global $current_user;

        // rrs: bug 42684 - passing a contact breaks this call
        $notifyUser =($task->current_notify_user->object_name == 'User') ? $task->current_notify_user : $current_user;

        // Send Invites template
        if(!empty($_REQUEST['send_invites']) && $_REQUEST['send_invites'] == '1') {
            global $sugar_config;

            // Assumes $task dates are in user format
            $taskdate = $timedate->fromDb($task->date_start);
            $xOffset = $timedate->asUser($taskdate, $notifyUser).' '.$timedate->userTimezoneSuffix($taskdate, $notifyUser);

            if ( strtolower(get_class($task->current_notify_user)) == 'contact' ) {
                $xtpl->assign("ACCEPT_URL", $sugar_config['site_url'].
                '/index.php?entryPoint=acceptDecline&module=Tasks&contact_id='.$task->current_notify_user->id.'&record='.$task->id);
            } elseif ( strtolower(get_class($task->current_notify_user)) == 'lead' ) {
                $xtpl->assign("ACCEPT_URL", $sugar_config['site_url'].
                '/index.php?entryPoint=acceptDecline&module=Tasks&lead_id='.$task->current_notify_user->id.'&record='.$task->id);
            } else {
                $xtpl->assign("ACCEPT_URL", $sugar_config['site_url'].
                '/index.php?entryPoint=acceptDecline&module=Tasks&user_id='.$task->current_notify_user->id.'&record='.$task->id);
            }

            $xtpl->assign("TASK_STARTDATE", $xOffset);
        }

		$xtpl->assign("TASK_SUBJECT", $task->name);
		//MFH #13507
		$xtpl->assign("TASK_PRIORITY", (isset($task->priority)?$app_list_strings['task_priority_dom'][$task->priority]:""));

        if(!empty($task->date_due))
        {
		    $duedate = $timedate->fromDb($task->date_due);
            $xtpl->assign("TASK_DUEDATE", $timedate->asUser($duedate, $notifyUser)." ".TimeDate::userTimezoneSuffix($duedate, $notifyUser));
        } else {
            $xtpl->assign("TASK_DUEDATE", '');
        }

		$xtpl->assign("TASK_STATUS", (isset($task->status)?$app_list_strings['task_status_dom'][$task->status]:""));
		$xtpl->assign("TASK_DESCRIPTION", $task->description);

		return $xtpl;
	}

	function bean_implements($interface){
		switch($interface){
			case 'ACL':return true;
		}
		return false;
	}
	function listviewACLHelper(){
		$array_assign = parent::listviewACLHelper();
		$is_owner = false;
		if(!empty($this->parent_name)){
			if(!empty($this->parent_name_owner)){
				global $current_user;
				$is_owner = $current_user->id == $this->parent_name_owner;
			}
		}

			if(!ACLController::moduleSupportsACL($this->parent_type) || ACLController::checkAccess($this->parent_type, 'view', $is_owner)){
				$array_assign['PARENT'] = 'a';
			}else{
				$array_assign['PARENT'] = 'span';
			}
		$is_owner = false;
		if(!empty($this->contact_name)){
			if(!empty($this->contact_name_owner)){
				global $current_user;
				$is_owner = $current_user->id == $this->contact_name_owner;
			}
		}

		if( ACLController::checkAccess('Contacts', 'view', $is_owner)){
				$array_assign['CONTACT'] = 'a';
		}else{
				$array_assign['CONTACT'] = 'span';
		}

		return $array_assign;
	}



	public function getDefaultStatus()
    {
         $def = $this->field_defs['status'];
         if (isset($def['default'])) {
             return $def['default'];
         } else {
            $app = return_app_list_strings_language($GLOBALS['current_language']);
            if (isset($def['options']) && isset($app[$def['options']])) {
                $keys = array_keys($app[$def['options']]);
                return $keys[0];
            }
        }
        return '';
    }

    function check_status_change($old_status){
       if($this->status == 'Completed'){
           //Status has changed from something to completed
           if($old_status != $this->status){
                //send email
                //old status has changed to completed.
                $this->send_status_notification();              
            }
            if($old_status == $this->status){
                //Status has not changed from Complete
                //dont send email.
                return;
            }
        }else {
            //status not complete, move on.
            return;
        }
    }


    //What will the subject of the email be in reference to?
    function check_task_alter($task_id = null, $old_status = null, $old_assigned_user_id = null, $old_contact = null){
        if(isset($this->contact_id)){
            //we have a contact so continue with email setup
            if(!isset($task_id)){
                //New task
                //send this contact an email about task creation
                $emailtype = 'Task Creation';
                $message = "Task: $this->name, has been created.";
                if(isset($this->assigned_user_id)){
                     $message .= " $this->assigned_user_name has been assigned to this task";
                }
            } else{
                //Existing task
                //What has changed with the task
                if(isset($this->assigned_user_id)){
                    if($this->assigned_user_id != $old_assigned_user_id && $this->contact_id != $old_contact){
                        //assigned user has changed and contact has also changed
                        //send email about assignment and contact change
                        $emailtype = 'Assigned user and Contact change';
                        $message = "The Assigned user has been changed to $this->assigned_user_name and you have been listed as a contact for this Task($this->name)";
                    } elseif(!isset($emailtype)){
                        if($this->assigned_user_id != $old_assigned_user_id)  {
                            //assigned user has changed
                            //send email about assigended suer change
                            $emailtype = 'Assigned user change';
                            $message = "The Assigned user has been changed to $this->assigned_user_name.";
                        }
                        if($this->contact_id != $old_contact){
                            //Contact has changed
                            //send email about contact change
                            $emailtype = 'Contact change';
                            $message = 'You have been listed as a contact for this task.';
                        }
                    }
                }
                if(!isset($emailtype) && $this->contact_id != $old_contact){
                    //Contact has been changed
                    //send email about contact being related to task
                    $emailtype = 'Contact change';
                    $message = 'You have been listed as a contact for this task.';
                }
            }
            if(isset($emailtype)){
                $this->notify_contact($emailtype, $message);
            }
        }else{
            //no contact so we cant send an email
            return;
        }
    }


    //Notify contact About Task
    function notify_contact($subject, $message){
        global $current_user;
        global $sugar_config;

        require_once('include/eintel/notifications/notifications.php');
        require_once("XTemplate/xtpl.php");

        //Template items
        
        $template_file='include/eintel/notifications/templates/change.php';
        $template_name = 'Task_Change';
       
        $contactuser = new Contact();
        $contactuser->retrieve($this->contact_id);
        
        $creator = new User();
        $creator->retrieve($this->created_by);

        $xtpl = new XTemplate($template_file);
        //Complete items for the template
        $xtpl->assign('MOD_BY',$this->modified_by_name);
        $xtpl->assign('STATUS',$this->status);
        $xtpl->assign('CONTACT_NAME',$contactuser->name);
        $xtpl->assign('ASSIGNED_USER',$this->assigned_user_name);
        $xtpl->assign('CREATOR_USER',$creator->full_name);
        $xtpl->assign('EMAIL_SUBJECT', $subject);
        $xtpl->assign('TASK_SUBJECT', $this->name);
        $xtpl->assign('DATE_DUE',$this->date_due);
        $xtpl->assign('DESCRIPTION',$this->description);
        $xtpl->assign('PRIORITY',$this->priority);
        $xtpl->assign('HEADER',$message);
        $xtpl->assign('URL', $sugar_config["site_url"]. "/index.php?module=Tasks&action=DetailView&record=". $this->id);
        $xtpl->parse($template_name.'.TASK_ROWS');
        //Send Notification
        send_notification($contactuser->email1, $contactuser->name, $template_name, $xtpl);

    }

    //send email to the creator and Contact when a task
    //has been marked as completed
    function send_status_notification(){
        global $current_user;
        global $sugar_config;

        require_once('include/eintel/notifications/notifications.php');
        require_once("XTemplate/xtpl.php");


        $creator = new User();
        $creator->retrieve($this->created_by);

        $assigned_user = new User();
        $assigned_user->retrieve($this->assigned_user_id);

        $template_file='include/eintel/notifications/templates/complete_admin.php';
        $template_name = 'Task_Complete';
        $xtpl = new XTemplate($template_file);



        if(isset($assigned_user->full_name)){
            $message = "$assigned_user->full_name has completed the following task";
        }else {
            $message = "The following task has been completed.";
        }



        $xtpl->assign('HEADER',$message);
        $xtpl->assign('ASSIGNED_USER',$this->assigned_user_name);
        $xtpl->assign('USER',$creator->full_name);
        $xtpl->assign('DATE_DUE',$this->date_due);
        $xtpl->assign('DESCRIPTION',$this->description);
        $xtpl->assign('SUBJECT',$this->name);
        $xtpl->assign('PRIORITY',$this->priority);
        $xtpl->assign('URL', $sugar_config["site_url"]. "/index.php?module=Tasks&action=DetailView&record=". $this->id);
        $xtpl->parse($template_name.'.TASK_ROWS');

        send_notification($creator->email1, $creator->name, $template_name, $xtpl);
        //send_notification to contact as well if there is one
        if(isset($this->contact_id)){
            $template_contact_file='include/eintel/notifications/templates/complete_contact.php';
            $template_contact = 'Task_Contact';
            $xtpl_contact = new XTemplate($template_contact_file);
            //get contact user object
            $contact = new Contact();
            $contact->retrieve($this->contact_id);

            $xtpl_contact->assign('HEADER',$message);
            $xtpl_contact->assign('ASSIGNED_USER',$this->assigned_user_name);
            $xtpl_contact->assign('USER',$contact->full_name);
            $xtpl_contact->assign('DESCRIPTION',$this->description);
            $xtpl_contact->assign('SUBJECT',$this->name);
            $xtpl_contact->parse($template_name.'.TASK_ROWS');
            send_notification($contact->email1, $contact->full_name, $template_contact, $xtpl_contact);
        }
    }

    function get_task_users()
    {
        $template = new User();
        // First, get the list of IDs.
        $query = "SELECT tasks_users.required, tasks_users.accept_status, tasks_users.user_id from tasks_users where tasks_users.task_id='$this->id' AND tasks_users.deleted=0";
        $GLOBALS['log']->debug("Finding linked records $this->object_name: " . $query);
        $result = $this->db->query($query, true);
        $list = Array();

        while ($row = $this->db->fetchByAssoc($result))
        {
            $template = new User(); // PHP 5 will retrieve by reference, always over-writing the "old" one
            $record = $template->retrieve($row['user_id']);
            $template->required = $row['required'];
            $template->accept_status = $row['accept_status'];

            if ($record != null)
            {
                // this copies the object into the array
                $list[] = $template;
            }
        }
        return $list;
    }

    // EINTEL Addition - Sending Invites from Scheduler
    function get_notification_recipients()
    {
        if (!$this->special_notification)
        {
            return parent::get_notification_recipients();
        }

//		$GLOBALS['log']->debug('Call.php->get_notification_recipients():'.print_r($this,true));
        $list = array();
        if (!is_array($this->contacts_arr))
        {
            $this->contacts_arr = array();
        }

        if (!is_array($this->users_arr))
        {
            $this->users_arr = array();
        }

        if (!is_array($this->leads_arr))
        {
            $this->leads_arr = array();
        }

        foreach ($this->users_arr as $user_id)
        {
            $notify_user = new User();
            $notify_user->retrieve($user_id);
            $notify_user->new_assigned_user_name = $notify_user->full_name;
            $GLOBALS['log']->info("Notifications: recipient is $notify_user->new_assigned_user_name");
            $list[$notify_user->id] = $notify_user;
        }

        foreach ($this->contacts_arr as $contact_id)
        {
            $notify_user = new Contact();
            $notify_user->retrieve($contact_id);
            $notify_user->new_assigned_user_name = $notify_user->full_name;
            $GLOBALS['log']->info("Notifications: recipient is $notify_user->new_assigned_user_name");
            $list[$notify_user->id] = $notify_user;
        }

        foreach ($this->leads_arr as $lead_id)
        {
            $notify_user = new Lead();
            $notify_user->retrieve($lead_id);
            $notify_user->new_assigned_user_name = $notify_user->full_name;
            $GLOBALS['log']->info("Notifications: recipient is $notify_user->new_assigned_user_name");
            $list[$notify_user->id] = $notify_user;
        }
//		$GLOBALS['log']->debug('Call.php->get_notification_recipients():'.print_r($list,true));
        return $list;
    }

    // EINTEL ADDITION : for Scheduling
    function set_accept_status(&$user, $status)
    {
        if ($user->object_name == 'User')
        {
            $relate_values = array('user_id' => $user->id, 'task_id' => $this->id);
            $data_values = array('accept_status' => $status);
            $this->set_relationship($this->rel_users_table, $relate_values, true, true, $data_values);
        }
        else if ($user->object_name == 'Contact')
        {
            $relate_values = array('contact_id' => $user->id, 'task_id' => $this->id);
            $data_values = array('accept_status' => $status);
            $this->set_relationship($this->rel_contacts_table, $relate_values, true, true, $data_values);
        }
        else if ($user->object_name == 'Lead')
        {
            $relate_values = array('lead_id' => $user->id, 'task_id' => $this->id);
            $data_values = array('accept_status' => $status);
            $this->set_relationship($this->rel_leads_table, $relate_values, true, true, $data_values);
        }

        // EINTEL Addition - #EISCRM-69
        // update vCal data for contacts, leads, users object types for scheduler
        if ($this->update_vcal)
        {
            vCal::cache_sugar_vcal($user);
        }
    }

}

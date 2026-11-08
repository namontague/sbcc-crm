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


class Project extends SugarBean {
	// database table columns
	var $id;
	var $date_entered;
	var $date_modified;
	var $assigned_user_id;
	var $modified_user_id;
	var $created_by;
	var $name;
	var $description;
	var $deleted;


	// related information
	var $assigned_user_name;
	var $modified_by_name;
	var $created_by_name;

	var $account_id;
	var $contact_id;
	var $opportunity_id;
	var $email_id;
    var $estimated_start_date;

	// calculated information
	var $total_estimated_effort;
	var $total_actual_effort;

	var $object_name = 'Project';
	var $module_dir = 'Project';
	var $new_schema = true;
	var $table_name = 'project';

        // EINTEL Addition
        var $onlinedocument_id;
	var $estimated_effort;
        var $actual_effort;
        var $billable_effort;

	// This is used to retrieve related fields from form posts.
	var $additional_column_fields = array(
		'account_id',
		'contact_id',
		'opportunity_id',
        'onlinedocument_id',
	);
	var $relationship_fields = array(
		'account_id' => 'accounts',
		'contact_id'=>'contacts',
		'opportunity_id'=>'opportunities',
		'email_id' => 'emails',
        'onlinedocument_id'=>'onlinedocuments',
	);

	//////////////////////////////////////////////////////////////////
	// METHODS
	//////////////////////////////////////////////////////////////////

	/**
	 *
	 */
	function Project()
	{
		parent::SugarBean();
	}

	/**
	 * overriding the base class function to do a join with users table
	 */

	/**
	 *
	 */
	function fill_in_additional_detail_fields()
	{
	    parent::fill_in_additional_detail_fields();

		$this->assigned_user_name = get_assigned_user_name($this->assigned_user_id);
		$this->estimated_effort = $this->_get_total_estimated_effort($this->id);
		//$this->total_actual_effort = $this->_get_total_actual_effort($this->id);
	}

	/**
	 *
	 */
	function fill_in_additional_list_fields()
	{
	    parent::fill_in_additional_list_fields();
		$this->assigned_user_name = get_assigned_user_name($this->assigned_user_id);
		$this->estimated_effort = $this->_get_total_estimated_effort($this->id);
		//$this->total_actual_effort = $this->_get_total_actual_effort($this->id);

		// EINTEL addition #EISCRM-66
		$accounts = $this->get_accounts($this->id);
		foreach ($accounts as $account)
		{
			if (!empty($this->accounts_names))
				$this->accounts_names .= ", ";

			$this->accounts_names .= "<a href='index.php?module=Accounts&action=DetailView&record={$account['id']}'>{$account['name']}</a>";
		}
	}

    /**
    * Save changes that have been made to a relationship.
    *
    * @param $is_update true if this save is an update.
    */
    function save_relationship_changes($is_update, $exclude=array())
    {
        parent::save_relationship_changes($is_update, $exclude);
        $new_rel_id = false;
        $new_rel_link = false;
        //this allows us to dynamically relate modules without adding it to the relationship_fields array
        if(!empty($_REQUEST['relate_id']) && !in_array($_REQUEST['relate_to'], $exclude) && $_REQUEST['relate_id'] != $this->id){
            $new_rel_id = $_REQUEST['relate_id'];
            $new_rel_relname = $_REQUEST['relate_to'];
            if(!empty($this->in_workflow) && !empty($this->not_use_rel_in_req)) {
                $new_rel_id = $this->new_rel_id;
                $new_rel_relname = $this->new_rel_relname;
            }
            $new_rel_link = $new_rel_relname;
            //Try to find the link in this bean based on the relationship
            foreach ( $this->field_defs as $key => $def ) {
                if (isset($def['type']) && $def['type'] == 'link'
                && isset($def['relationship']) && $def['relationship'] == $new_rel_relname) {
                    $new_rel_link = $key;
                }
            }
            if ($new_rel_link == 'contacts') {
                $accountId = $this->db->getOne('SELECT account_id FROM accounts_contacts WHERE contact_id=' . $this->db->quoted($new_rel_id));
                if ($accountId !== false) {
                    if($this->load_relationship('accounts')){
                        $this->accounts->add($accountId);
                    }
                }
            }
        }
    }
	/**
	 *
	 */
	function _get_total_estimated_effort($project_id)
	{
		$return_value = '';
        if( $project_id) {
		$query = 'SELECT SUM('.$this->db->convert('estimated_effort', "IFNULL", array(0)).') total_estimated_effort';
            $query.= ' FROM project_task';
            $query.= " WHERE project_id='{$project_id}' AND deleted=0";

		$result = $this->db->query($query,true," Error filling in additional detail fields: ");
		$row = $this->db->fetchByAssoc($result);
		if($row != null)
		{
			$return_value = $row['total_estimated_effort'];
            }
		}

		return $return_value;
	}

	/**
	 *
	 */
	function _get_total_actual_effort($project_id)
	{
		$return_value = '';

		$query = 'SELECT SUM('.$this->db->convert('actual_effort', "IFNULL", array(0)).') total_actual_effort';
		$query.=  ' FROM project_task';
		$query.=  " WHERE parent_id='{$project_id}' AND deleted=0";

		$result = $this->db->query($query,true," Error filling in additional detail fields: ");
		$row = $this->db->fetchByAssoc($result);
		if($row != null)
		{
			$return_value = $row['total_actual_effort'];
		}

		return $return_value;
	}

	/**
	 *
	 */
	function get_summary_text()
	{
		return $this->name;
	}

	/**
	 *
	 */
	function build_generic_where_clause ($the_query_string)
	{
		$where_clauses = array();
		$the_query_string = $GLOBALS['db']->quote($the_query_string);
		array_push($where_clauses, "project.name LIKE '%$the_query_string%'");

		$the_where = '';
		foreach($where_clauses as $clause)
		{
			if($the_where != '') $the_where .= " OR ";
			$the_where .= $clause;
		}

		return $the_where;
	}

	function get_list_view_data()
	{
		$field_list = $this->get_list_view_array();
		$field_list['USER_NAME'] = empty($this->user_name) ? '' : $this->user_name;
		$field_list['ASSIGNED_USER_NAME'] = $this->assigned_user_name;
		// EINTEL addition - #EISCRM-66
		$field_list['ACCOUNTS_NAMES'] = $this->accounts_names;
		return $field_list;
	}
	  function bean_implements($interface){
		switch($interface){
			case 'ACL':return true;
		}
		return false;
	}

    function create_export_query(&$order_by, &$where, $relate_link_join='')
    {
        $custom_join = $this->getCustomJoin(true, true, $where);
        $custom_join['join'] .= $relate_link_join;
		$query = "SELECT
				project.*,
                users.user_name as assigned_user_name ";
        $query .=  $custom_join['select'];
        $query .= " FROM project ";

        $query .=  $custom_join['join'];
        $query .= " LEFT JOIN users
                   	ON project.assigned_user_id=users.id ";

        $where_auto = " project.deleted=0 ";

        if($where != "")
        	$query .= "where ($where) AND ".$where_auto;
        else
            $query .= "where ".$where_auto;

        if(!empty($order_by)){
           	//check to see if order by variable already has table name by looking for dot "."
           	$table_defined_already = strpos($order_by, ".");

	        if($table_defined_already === false){
	        	//table not defined yet, define accounts to avoid "ambigous column" SQL error
	        	$query .= " ORDER BY $order_by";
	        }else{
	        	//table already defined, just add it to end of query
	            $query .= " ORDER BY $order_by";
	        }
        }
        return $query;
    }

	function getAllProjectTasks(){
		$projectTasks = array();

		$query = "SELECT * FROM project_task WHERE project_id = '" . $this->id. "' AND deleted = 0 ORDER BY project_task_id";
		$result = $this->db->query($query,true,"Error retrieving project tasks");
		$row = $this->db->fetchByAssoc($result);

		while ($row != null){
			$projectTaskBean = new ProjectTask();
			$projectTaskBean->id = $row['id'];
			$projectTaskBean->retrieve();
			array_push($projectTasks, $projectTaskBean);

			$row = $this->db->fetchByAssoc($result);
		}

		return $projectTasks;
	}


    function populatefromtemplate(){
        $ProjectTemplate = new Project();
        $ProjectTemplate->retrieve($this->template_id);

        $relationship_list = array(
            'Account' =>    'projects_accounts',
            'Contact' =>    'projects_contacts',
            'Opportunity' =>'projects_opportunities',
            'aCase' =>      'projects_cases',
            'Note' =>       'projects_notes',
            'Task' =>       'projects_tasks',
            'Meeting' =>    'projects_meetings',
            'Call' =>       'projects_calls',
            'Email' =>      'emails_projects_rel',
            'ProjectTask' => 'projects_project_tasks',
            'Bug' =>        'projects_bugs');

        $linked_beans = array(
            'Account' =>    'accounts',
            'Contact' =>    'contacts',
            'Opportunity' =>'opportunities',
            'aCase' =>      'cases',
            'Note' =>       'notes',
            'Task' =>       'tasks',
            'Meeting' =>    'meetings',
            'Call' =>       'calls',
            'Email' =>      'emails',
            'ProjectTask' => 'projecttask',
            'Bug' =>        'bugs');

        foreach($linked_beans as $bean_name => $relationship){
            $related_beans[$bean_name] = $ProjectTemplate->get_linked_beans($relationship,$bean_name);
            if(empty($related_beans[$bean_name])){
                unset($related_beans[$bean_name]);
            }
        }

        if(!empty($related_beans)){
            foreach($related_beans as $rel_bean){
                foreach($rel_bean as $bean){
                    if(empty($bean)){
                        continue;
                    }

                    $relationship_field = $linked_beans[$bean->object_name];
                    if($bean->object_name == 'Case'){
                        $bean->object_name = 'aCase';
                        $bean->case_number = '';
                    }
                    if($bean->object_name == 'Bug'){
                        $bean->bug_number =  '';
                    }

                    if($bean->object_name == 'Account' || $bean->object_name == 'Contact'){
                        $this->load_relationship($relationship_field);
                        $this->$relationship_field->add($bean->id);
                        continue;
                    }else{
                        if($bean->object_name == 'ProjectTask'){
                            //$processesTasks[$bean->id] = $this->processestask_relationships($bean);
                            continue;
                        }else{
                            $this->create_all_existing_relationships($bean, $linked_beans, $this);
                        }
                    }

                }
            }
            $projectTasks[$bean->id] = $this->projecttask_relationships($related_beans['ProjectTask']);
        }
    }

    //special cases for project tasks.....
    function projecttask_relationships($sbean){
        $bean_name = 'ProjectTask';
        $linked_tasks = array('0'=>'predecessor', '1'=>'subtasks');
        $beans = array();
        foreach($sbean as $bean){
            $beans[$bean->id] = $bean;

        }unset($bean);

        foreach($linked_tasks as $field_name){
            foreach($beans as $bean){
                $related_beans[$bean->id][$field_name] = $bean->get_linked_beans($field_name,$bean_name);
                if(empty($related_beans[$bean->id][$field_name])){
                    unset($related_beans[$bean->id][$field_name]);
                }
            }
        }unset($bean);

        $linked_beans = array('Note' => 'notes', 'Task' => 'tasks', 'Meeting' => 'meetings', 'Call' => 'calls', 'Email' => 'emails');

        foreach($beans as $bean){
            $this->create_all_existing_relationships($bean, $linked_beans, $this);
            $this->load_relationship('projecttask');
            $this->projecttask->add($bean->id);
        }unset($bean);

        foreach($related_beans as $beans_id => $relationship){
            foreach($relationship as $rel => $projecttasks){
                foreach($projecttasks as $bean){
                        $beans[$beans_id]->load_relationship($rel);
                        $beans[$beans_id]->$rel->add($beans[$bean->id]->id);
                }
            }
        }
    }

    //create a relationships
    function create_all_existing_relationships($sbean, $linked_beans, $parent_bean, $parentbean_id = null){
        global $current_user;
        foreach($linked_beans as $bean_name => $field_name){
            if($sbean->object_name == 'Case'){
                $sbean->object_name = 'aCase';
            }
            if($sbean->object_name == 'Bug'){
                $sbean->bug_number =  '';
            }
            if($bean_name != 'Account' && $bean_name != 'Contact'){
                if($sbean->load_relationship($field_name)){
                    $related_beans[$bean_name] = $sbean->get_linked_beans($field_name,$bean_name);
                    if(empty($related_beans[$bean_name])){
                        unset($related_beans[$bean_name]);
                    }
                }
            }
        }
        $bean_id_before_save = $sbean->id;
        $relationship_field = $linked_beans[$sbean->object_name];
        if($sbean->object_name != 'Account' && $sbean->object_name != 'Contact'){
            if($this->template_dates == false){
                $this->create_new_dates($sbean);
            }
            $sbean->date_entered = '';
            $sbean->id = '';
            $sbean->assigned_user_id = $current_user->id;
            $sbean->save();
        }

        if($sbean->object_name == 'ProjectTask'){
            //send email assignment of task
            $projectTask_asarray = $sbean;
            $projectTask_asarray = array();
            $sbean->check_for_changes($projectTask_asarray);
        }

        if($parent_bean->load_relationship($relationship_field)){
            $parent_bean->$relationship_field->add($sbean->id);
        }

        if(!empty($related_beans)){
            foreach($related_beans as $rel_bean){
                foreach($rel_bean as $related){
                    if($related->id != $parentbean_id){
                        $parentbean_id = $bean_id_before_save;
                        $this->create_all_existing_relationships($related, $linked_beans, $sbean, $parentbean_id);
                    }
                }
            }
        }
    }

    //create new dates relevate to the time the project was created
    function create_new_dates($bean){
        /*  email =       date_sent??
        opportunities = date_closed */
        global $timedate;
        global $current_user;
        $date_format = $timedate->get_date_format();
        $date_time_format = $timedate->get_date_time_format();
        $cal_format = $timedate->get_cal_date_format();


        //$bean->date_entered = date($date_time_format, time());
        if(isset($bean->date_start)){
            if($bean->object_name == 'ProjectTask'){
                $date_start = $timedate->fromUserType($bean->date_start, 'date', $current_user);
                $date_time_format = $date_format;
            } else {
                $date_start = $timedate->fromUserType($bean->date_start, 'datetime', $current_user);
            }

            $date_start->add($this->time_difference);
            $bean->date_start =  $date_start->format($date_time_format);
        }
        if(isset($bean->date_end)){
            if ($bean->object_name == 'ProcessesTask') {
                $date_end = $timedate->fromUserType($bean->date_end, 'date', $current_user);
                $date_time_format = $date_format;
            } else {
                $date_end = $timedate->fromUserType($bean->date_end, 'datetime', $current_user);
            }

            $date_end->add($this->time_difference);
            $bean->date_end =  $date_end->format($date_time_format);
        }
        if(isset($bean->date_finish)){
            $date_finish = $timedate->fromUserDate($bean->date_finish, false, $current_user);
            $date_finish->add($this->time_difference);
            $bean->date_finish =  $date_finish->format($date_format);
        }

    }

    // EINTEL Addition -
	function save($check_notify = FALSE){
        return parent::save($check_notify);
	}

	/**
	 * EINTEL Addition - #EISCRM-66
	 * Static helper function for getting releated account info.
	 */
	function get_accounts($proj_id) {
		$ret_array = array();
		$db = DBManagerFactory::getInstance();
		$query = "SELECT acc.id, acc.name "
			. "FROM accounts acc, projects_accounts p_a "
			. "WHERE acc.id=p_a.account_id"
			. " AND p_a.project_id='$proj_id'"
			. " AND p_a.deleted=0"
			. " AND acc.deleted=0";
		$result = $db->query($query, true,"Error filling in project account details: ");
		while($row = $db->fetchByAssoc($result)) {
			$list = array();
			$list['name'] = $row['name'];
			$list['id'] = $row['id'];
			$ret_array[] = $list;
		}
		return $ret_array;
	}

}
?>

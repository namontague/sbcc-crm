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













class ProjectTask extends SugarBean {
	// database table columns
	var $id;
	var $date_entered;
	var $date_modified;
	//var $assigned_user_id;
	//var $modified_user_id;
	//var $created_by;
	var $name;
    var $description;
    var $project_id;
    var $project_task_id;
    var $date_start;
    var $date_finish;
    var $duration;
    var $duration_unit;
    var $percent_complete;
    var $parent_task_id;
    var $predecessors;
    var $priority;

	// related information
	var $assigned_user_name;
	var $parent_name;
	var $depends_on_name;
	var $email_id;

	var $table_name = 'project_task';
	var $object_name = 'ProjectTask';
	var $module_dir = 'ProjectTask';

	var $field_name_map;
	var $new_schema = true;

    var $onlinedocument_id;
    // This is used to retrieve related fields from form posts.
	var $additional_column_fields = Array(
        'onlinedocument_id',
	);

	var $relationship_fields = array(
		'email_id' => 'emails',
        'onlinedocument_id'=>'onlinedocuments',

	);
    /**
     * @var bool skip updating parent percent complete
     */
    protected $_skipParentUpdate = false;

    var $project_task_id_prefix;
    var $task_number;
    var $project_name;
    var $parent_name_owner;
    var $depends_on_name_owner;
    var $depends_on_name_mod;
    var $parent_name_mod;


    //////////////////////////////////////////////////////////////////
	// METHODS
	//////////////////////////////////////////////////////////////////

	/*
	 *
	 */
	function ProjectTask($init=true)
	{
		parent::SugarBean();
		if ($init) {
			// default value for a clean instantiation
			$this->utilization = 100;

			global $current_user;
			if(empty($current_user))
			{
				$this->assigned_user_id = 1;
				$admin_user = new User();
				$admin_user->retrieve($this->assigned_user_id);
				$this->assigned_user_name = $admin_user->user_name;
			}
			else
			{
				$this->assigned_user_id = $current_user->id;
				$this->assigned_user_name = $current_user->user_name;
			}

		}
	}
    /**
     * @param bool $skip updating parent percent complete
     */
    public function skipParentUpdate($skip = true)
    {
        $this->_skipParentUpdate = $skip;
    }
	function save($check_notify = FALSE)
	{
		//Bug 46012.  When saving new Project Tasks instance in a workflow, make sure we set a project_task_id value
		//associated with the Project if there is no project_task_id specified.
        if ($this->in_workflow && empty($this->id) && empty($this->project_task_id) && !empty($this->project_id))
        {
//            $this->project_task_id = $this->getNumberOfTasksInProject($this->project_id) + 1;
        }
        $old_task_id = '';
        $full_rebuild = false;
        if ( !isset($this->id)) {  // New record
            $this->project_task_id ='';
            if( $this->parent_task_id) {
                // Get the parent task id and set our project task id appropriately
                $r = $this->db->fetchOne("SELECT project_task_id as parent FROM project_task WHERE id ='{$this->parent_task_id}'");
                $this->project_task_id =$r['parent'].'.';
            }
            $taskNumberInfo = $this->taskNumberInfo();
            $this->makeTaskNumberUnique($taskNumberInfo);
            $this->project_task_id = $this->project_task_id.$this->formatTaskNumber($taskNumberInfo);
            $full_rebuild = $this->projectTaskIdsRequireRebuild($taskNumberInfo);
        } else if( $this->fetched_row) {
            if($this->fetched_row['parent_task_id'] != $this->parent_task_id ) {
                // Parent task has changed, create new task id
                $old_task_id = $this->fetched_row['project_task_id'];
                $taskNumberInfo = $this->taskNumberInfo();
                $this->makeTaskNumberUnique($taskNumberInfo);
                if( $this->parent_task_id ) {
                    $r = $this->db->fetchOne("SELECT project_task_id as parent FROM project_task WHERE id ='{$this->parent_task_id}'");
                    $new_task_id = $r['parent'].'.'.$this->formatTaskNumber($taskNumberInfo);
                } else {
                    $new_task_id = $this->task_number;
                }
                $this->project_task_id = $new_task_id;
                $full_rebuild = $this->projectTaskIdsRequireRebuild($taskNumberInfo);
            } else if($this->fetched_row['task_number'] != $this->task_number) {
                // Check if we need to bump sibling tasks to ensure uniqueness
                $taskNumberInfo = $this->taskNumberInfo();
                if($taskNumberInfo['alreadyExists']) {
                    // We do.. easier to do full rebuild
                    $full_rebuild = true;
                    $siblingTaskFilter = $this->siblingTasksFilter();
                    // Update the sibling task numbers
                    if( $this->fetched_row['task_number'] > $this->task_number ) {
                        // Task number has been made smaller, need to bump up siblings
                        $q="UPDATE project_task SET task_number = task_number+1
                            WHERE $siblingTaskFilter
                            AND task_number >= $this->task_number AND task_number < {$this->fetched_row['task_number']} ";
                    } else {
                        // Task number has been made larger, need to bump down siblings
                        $q="UPDATE project_task SET task_number = task_number-1
                            WHERE $siblingTaskFilter
                            AND task_number <= $this->task_number AND task_number > {$this->fetched_row['task_number']} ";
                    }
                    $this->db->query($q);
                } else {
                    // Task number has changed, create new task id
                    $old_task_id = $this->fetched_row['project_task_id'];
                    $new_task_id = $this->extractParentTaskPrefix($old_task_id).$this->formatTaskNumber($taskNumberInfo);
                    $this->project_task_id = $new_task_id;
                    $full_rebuild = $this->projectTaskIdsRequireRebuild($taskNumberInfo);
                }
            }
        }
        $id = parent::save($check_notify);
        if($this->_skipParentUpdate == false)
        {
            $this->updateStatistic();
        }
	
        if( $full_rebuild) {
            $parent = new ProjectTask();
            if( $this->parent_task_id ) {
                $parent->retrieve($this->parent_task_id);
            } else {
                $parent->project_id = $this->project_id;
            }
            $parent->rebuildChildProjectTaskIds();
        }
        else if( $old_task_id ) {
            // Update all descendants with new task ids
            $old_length = strlen($old_task_id)+1;
            $q = "UPDATE project_task SET project_task_id = concat('{$new_task_id}', substr(project_task_id, $old_length)) WHERE project_id = '$this->project_id' AND project_task_id LIKE '{$old_task_id}.%'";
            $result = $this->db->query($q);
        }
        return $id;
	}

    function validate(&$errors) {
        $valid = parent::validate($errors);
        if(!empty($this->parent_task_id)){
            $query = "select date_start, date_finish from project_task where id = '{$this->parent_task_id}'";
            $result = $this->db->query($query);
            $row = $this->db->fetchByAssoc($result);
            
            $errors['date_start'] = $this->validateProjectTaskDates($row['date_start'],$row['date_finish'], true);
            $errors['date_finish'] = $this->validateProjectTaskDates($row['date_start'],$row['date_finish'], false);

            if(!empty($errors['date_start']) || !empty($errors['date_finish']) ){
                $valid = false;
            }else{
                $valid = true;
            }
        }
        return $valid;
    }

    function validateProjectTaskDates($parent_startdate, $parent_finishdate, $date_start){
        global $timedate;
        $error = '';
        $date_format = $timedate->get_date_format();            

        $child_sd_array = date_parse($timedate->swap_formats($this->date_start, $date_format, 'Y-m-d'));
        $child_sd_ts =  mktime(0,0,0,$child_sd_array['month'],$child_sd_array['day'],$child_sd_array['year']);
        $child_fd_array = date_parse($timedate->swap_formats($this->date_finish, $date_format, 'Y-m-d'));
        $child_fd_ts = mktime(0,0,0,$child_fd_array['month'],$child_fd_array['day'],$child_fd_array['year']);

        $parent_sd_array = date_parse($timedate->swap_formats($parent_startdate, $timedate->dbDayFormat, 'Y-m-d'));
        $parent_sd_ts =  mktime(0,0,0,$parent_sd_array['month'],$parent_sd_array['day'],$parent_sd_array['year']);
        $parent_fd_array = date_parse($timedate->swap_formats($parent_finishdate, $timedate->dbDayFormat, 'Y-m-d'));
        $parent_fd_ts =  mktime(0,0,0,$parent_fd_array['month'],$parent_fd_array['day'],$parent_fd_array['year']);


        if($date_start){
            if($child_sd_ts < $parent_sd_ts){
                $error .= 'Childs start date is before parent tasks start date.';
            }
            if($child_sd_ts > $parent_fd_ts){
                $error .= 'Childs start date is after parent tasks finish date. ';
            }
        }else{
            if($child_fd_ts < $parent_sd_ts){
                $error .= 'Childs finish date is before parents tasks start date. ';
            }
            if($child_fd_ts > $parent_fd_ts){
                $error .= 'Childs finish date is after parents tasks finish date. ';
            }
        }
 
        return $error;
    }




    function makeTaskNumberUnique($taskNumberInfo = null ) {
        if(!$taskNumberInfo ) {
            $taskNumberInfo = $this->taskNumberInfo();
        }
        // Check if another task has the specified task number
        if($taskNumberInfo['alreadyExists']) {
            // Change task number to unique
            $this->task_number = $taskNumberInfo['next'];
        }
    }
    function taskNumberInfo($childInfo = false) {
        $taskNum = $this->task_number;
        if(!$taskNum) {
            $taskNum = -1;
        }
        $q = "SELECT MAX(task_number) + 1 as next,
              LENGTH(MAX(task_number)) as maxNumberLen,
              MIN(LENGTH(project_task_id)) as minTaskIdLen,
              MAX(LENGTH(project_task_id)) as maxTaskIdLen,
              MAX( task_number = $taskNum) as alreadyExists";
        $q .= " FROM project_task WHERE ";
        if( $childInfo ) {
            $q .= $this->childTasksFilter();
        } else {
            $q .= $this->siblingTasksFilter();
        }
        return $this->db->fetchOne($q);
    }

    function formatTaskNumber( $taskNumberInfo) {
        $length = $taskNumberInfo['maxNumberLen'];
        return str_pad($this->task_number, $length,'0',STR_PAD_LEFT);
    }
    function projectTaskIdsRequireRebuild( $taskNumberInfo  ) {
        if( strlen($this->task_number) > $taskNumberInfo['maxNumberLen']) {
            return true;
        }
        if( $taskNumberInfo['minTaskIdLen'] != $taskNumberInfo['maxTaskIdLen']) {
            return true;
        }
        $expectedLen = strlen($this->extractParentTaskPrefix($this->project_task_id)) + 1 + $taskNumberInfo['maxNumberLen'];
        if($expectedLen != $taskNumberInfo['maxTaskIdLen']  ) {
            return true;
        }
        return false;
    }

    function extractParentTaskPrefix($child_task_id) {
        $separator_pos =strrpos($child_task_id,'.');
        if( $separator_pos ) {
            return substr($child_task_id, 0, strrpos($child_task_id,'.')+1);
        } else {
            return '';
        }
    }

    function rebuildChildProjectTaskIds() {
        $parent_task_prefix = '';
        if($this->project_task_id) {
            $parent_task_prefix = $this->project_task_id.'.';
        }

        $childFilter = $this->childTasksFilter();
        $taskNumberInfo = $this->taskNumberInfo(true);
        if( $taskNumberInfo['maxNumberLen']) {
            $q = "UPDATE project_task SET project_task_id = concat('$parent_task_prefix', LPAD(task_number,{$taskNumberInfo['maxNumberLen']},'0')) WHERE $childFilter";
            $this->db->query($q);
        }

        $q = "SELECT * FROM project_task WHERE $childFilter";
        $child = new ProjectTask();
        $result = $child->process_list_query($q,0,999999999,999999999);
        foreach($result['list'] as $t) {
            $t->rebuildChildProjectTaskIds();
        }
    }

    function siblingTasksFilter() {
        return $this->buildChildTasksFilterFor($this->parent_task_id);
    }

    function childTasksFilter() {
        return $this->buildChildTasksFilterFor($this->id);
    }
    function buildChildTasksFilterFor($parentId) {
        if( $this->project_id || $parentId ) {
            if( $parentId ) {
                $filters[] = "parent_task_id = '$parentId'";
            } else {
                $filters[] = "parent_task_id is null";
                $filters[] = "project_id = '$this->project_id'";  // project_id must exists
            }

        } else  {
           $filters[] = "id = '$this->id'";
        }
        $filters[] = "deleted = 0";
        return implode(" AND ", $filters);
    }


	/**
	 * overriding the base class function to do a join with users table
	 */

	/*
	 *
	 */
   function fill_in_additional_detail_fields()
   {
      $this->assigned_user_name = get_assigned_user_name($this->assigned_user_id);
      $this->project_name = $this->_get_project_name($this->project_id);
		/*
        $this->depends_on_name = $this->_get_depends_on_name($this->depends_on_id);
		if(empty($this->depends_on_name))
		{
			$this->depends_on_id = '';
		}
		$this->parent_name = $this->_get_parent_name($this->parent_id);
		if(empty($this->parent_name))
		{
			$this->parent_id = '';
		}
        */

      $this->project_task_id_prefix= $this->extractParentTaskPrefix($this->project_task_id);
   }

	/*
	 *
	 */
   function fill_in_additional_list_fields()
   {
      $this->assigned_user_name = get_assigned_user_name($this->assigned_user_id);
      //$this->parent_name = $this->_get_parent_name($this->parent_id);
      $this->project_name = $this->_get_project_name($this->project_id);
   }

	/*
	 *
	 */
	function get_summary_text()
	{
		return $this->name;
	}

	/*
	 *
	 */
	function _get_depends_on_name($depends_on_id)
	{
		$return_value = '';

		$query  = "SELECT name, assigned_user_id FROM {$this->table_name} WHERE id='{$depends_on_id}'";
		$result = $this->db->query($query,true," Error filling in additional detail fields: ");
		$row = $this->db->fetchByAssoc($result);
		if($row != null)
		{
			$this->depends_on_name_owner = $row['assigned_user_id'];
			$this->depends_on_name_mod = 'ProjectTask';
			$return_value = $row['name'];
		}

		return $return_value;
	}

    function _get_project_name($project_id)
    {
        $return_value = '';

        $query  = "SELECT name, assigned_user_id FROM project WHERE id='{$project_id}'";
        $result = $this->db->query($query,true," Error filling in additional detail fields: ");
        $row = $this->db->fetchByAssoc($result);
        if($row != null)
        {
            //$this->parent_name_owner = $row['assigned_user_id'];
            //$this->parent_name_mod = 'Project';
            $return_value = $row['name'];
        }

        return $return_value;
    }
	/*
	 *
	 */
	function _get_parent_name($parent_id)
	{
		$return_value = '';

		$query  = "SELECT name, assigned_user_id FROM project WHERE id='{$parent_id}'";
		$result = $this->db->query($query,true," Error filling in additional detail fields: ");
		$row = $this->db->fetchByAssoc($result);
		if($row != null)
		{
			$this->parent_name_owner = $row['assigned_user_id'];
			$this->parent_name_mod = 'Project';
			$return_value = $row['name'];
		}

		return $return_value;
	}

	/*
	 *
	 */
	function build_generic_where_clause ($the_query_string)
	{
		$where_clauses = array();
		$the_query_string = $GLOBALS['db']->quote($the_query_string);
		array_push($where_clauses, "project_task.name like '$the_query_string%'");

		$the_where = "";
		foreach($where_clauses as $clause)
		{
			if($the_where != "") $the_where .= " or ";
			$the_where .= $clause;
		}

		return $the_where;
	}

	function get_list_view_data(){
		global $action, $currentModule, $focus, $current_module_strings, $app_list_strings, $timedate, $locale;
		$today = $timedate->handle_offset(date($GLOBALS['timedate']->get_db_date_time_format(), time()), $timedate->dbDayFormat, true);
		$task_fields =$this->get_list_view_array();
		//$date_due = $timedate->to_db_date($task_fields['DATE_DUE'],false);
        if (isset($this->parent_type))
			$task_fields['PARENT_MODULE'] = $this->parent_type;

		/*
        if ($this->status != "Completed" && $this->status != "Deferred" ) {
			$task_fields['SET_COMPLETE'] = "<a href='index.php?return_module=$currentModule&return_action=$action&return_id=" . ((!empty($focus->id)) ? $focus->id : "") . "&module=ProjectTask&action=EditView&record={$this->id}&status=Completed'>".SugarThemeRegistry::current()->getImage("close_inline","alt='Close' border='0'")."</a>";
		}

		if( $date_due	< $today){
			$task_fields['DATE_DUE']= "<font class='overdueTask'>".$task_fields['DATE_DUE']."</font>";
		}else if( $date_due	== $today ){
			$task_fields['DATE_DUE'] = "<font class='todaysTask'>".$task_fields['DATE_DUE']."</font>";
		}else{
			$task_fields['DATE_DUE'] = "<font class='futureTask'>".$task_fields['DATE_DUE']."</font>";
		}
        */

        if ( !isset($task_fields["FIRST_NAME"]) )
            $task_fields["FIRST_NAME"] = '';
        if ( !isset($task_fields["LAST_NAME"]) )
            $task_fields["LAST_NAME"] = '';
		$task_fields['CONTACT_NAME']= $locale->getLocaleFormattedName($task_fields["FIRST_NAME"],$task_fields["LAST_NAME"]);
		$task_fields['TITLE'] = '';
		if (!empty($task_fields['CONTACT_NAME'])) {
			$task_fields['TITLE'] .= $current_module_strings['LBL_LIST_CONTACT'].": ".$task_fields['CONTACT_NAME'];
		}

		return $task_fields;
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
			if(ACLController::checkAccess('Project', 'view', $is_owner)){
				$array_assign['PARENT'] = 'a';
			}else{
				$array_assign['PARENT'] = 'span';
			}
		$is_owner = false;
		if(!empty($this->depends_on_name)){

			if(!empty($this->depends_on_name_owner)){
				global $current_user;
				$is_owner = $current_user->id == $this->depends_on_name_owner;
			}
		}
			if( ACLController::checkAccess('ProjectTask', 'view', $is_owner)){
				$array_assign['PARENT_TASK'] = 'a';
			}else{
				$array_assign['PARENT_TASK'] = 'span';
			}

		return $array_assign;
	}

    function create_export_query(&$order_by, &$where, $relate_link_join='')
    {
        $custom_join = $this->getCustomJoin(true, true, $where);
        $custom_join['join'] .= $relate_link_join;
		$query = "SELECT
				project_task.*,
                users.user_name as assigned_user_name ";
        $query .=  $custom_join['select'];

        $query .= " FROM project_task LEFT JOIN project ON project_task.project_id=project.id AND project.deleted=0 ";

        $query .=  $custom_join['join'];
        $query .= " LEFT JOIN users
                   	ON project_task.assigned_user_id=users.id ";

        $where_auto = " project_task.deleted=0 ";

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


    /**
    * This method recalculates the percent complete of a parent task
    */
    public function updateParentProjectTaskPercentage()
	{

		if (empty($this->parent_task_id))
		{
			return;
		}

		if (!empty($this->project_id))
		{
            //determine parent task
            $parentProjectTask = $this->getProjectTaskParent();

            //get task children
            if ($parentProjectTask)
            {
                $subProjectTasks = $parentProjectTask->getAllSubProjectTasks();
                $tasks = array();
                foreach($subProjectTasks as &$task)
                {
                    array_push($tasks, $task->toArray(true));
                }
                $parentProjectTask->percent_complete = $this->_calculateCompletePercent($tasks);
                unset($tasks);
                $parentProjectTask->save(isset($GLOBALS['check_notify']) ? $GLOBALS['check_notify'] : '');
            }
		}
	}

    /**
     * Calculate percent complete for parent task based on it's children tasks
     * @param $subProjectTasks mixed Array of children tasks
     * @return int percent complete
     */
    private function _calculateCompletePercent(&$subProjectTasks)
    {
        $totalHours = 0;
        $cumulativeDone = 0;
        //update cumulative calculation - mimics gantt calculation
        foreach ($subProjectTasks as $key => &$value)
        {
            if ($value['duration'] == "")
            {
                $value['duration'] = 0;
            }

            if ($value['percent_complete'] == "")
            {
                $value['percent_complete'] = 0;
            }

            if ($value['duration_unit'] == "Hours")
            {
                $totalHours += $value['duration'];
                $cumulativeDone += $value['duration'] * ($value['percent_complete'] / 100);
            }
            else
            {
                $totalHours += ($value['duration'] * 8);
                $cumulativeDone += ($value['duration'] * 8) * ($value['percent_complete'] / 100);
            }
        }

        $cumulativePercentage = 0;
        if ($totalHours != 0)
        {
            $cumulativePercentage = round(($cumulativeDone/$totalHours) * 100);
        }
        return $cumulativePercentage;
    }

    /**
    * Retrieves the parent project task of a project task
    * returns project task bean
    */
    function getProjectTaskParent()
    {

        $projectTaskParent=false;

        if (!empty($this->parent_task_id) && !empty($this->project_id))
        {
            $query = "SELECT id FROM project_task WHERE project_id = '{$this->project_id}' AND project_task_id = '{$this->parent_task_id}' AND deleted = 0 ORDER BY date_modified DESC";
            $project_task_id = $this->db->getOne($query, true, "Error retrieving parent project task");

            if (!empty($project_task_id))
            {
                $projectTaskParent = BeanFactory::getBean('ProjectTask', $project_task_id);
            }
        }

        return $projectTaskParent;
    }

    /**
    * Retrieves all the child project tasks of a project task
    * returns project task bean array
    */
    function getAllSubProjectTasks()
    {
		$projectTasksBeans = array();

        if (!empty($this->project_task_id) && !empty($this->project_id))
		{
            //select all tasks from a project
            $query = "SELECT id, project_task_id, parent_task_id FROM project_task WHERE project_id = '{$this->project_id}' AND deleted = 0 ORDER BY project_task_id";

            $result = $this->db->query($query, true, "Error retrieving child project tasks");

            $projectTasks=array();
            while($row = $this->db->fetchByAssoc($result))
            {
                $projectTasks[$row['id']]['project_task_id'] = $row['project_task_id'];
                $projectTasks[$row['id']]['parent_task_id'] = $row['parent_task_id'];
            }

            $potentialParentTaskIds[$this->project_task_id] = $this->project_task_id;
            $actualParentTaskIds=array();
            $subProjectTasks=array();

            $startProjectTasksCount=0;
            $endProjectTasksCount=0;

            //get all child tasks
            $run = true;
            while ($run)
            {
                $count=0;

                foreach ($projectTasks as $id=>$values)
                {
                    if (in_array($values['parent_task_id'], $potentialParentTaskIds))
                    {
                        $potentialParentTaskIds[$values['project_task_id']] = $values['project_task_id'];
                        $actualParentTaskIds[$values['parent_task_id']] = $values['parent_task_id'];

                        $subProjectTasks[$id]=$values;
                        $count=$count+1;
                    }
                }

                $endProjectTasksCount = count($subProjectTasks);

                if ($startProjectTasksCount == $endProjectTasksCount)
                {
                    $run = false;
                }
                else
                {
                    $startProjectTasksCount = $endProjectTasksCount;
                }
            }

            foreach($subProjectTasks as $id=>$values)
            {
                //ignore tasks that are parents
                if(!in_array($values['project_task_id'], $actualParentTaskIds))
                {
                    $projectTaskBean = BeanFactory::getBean('ProjectTask', $id);
                    array_push($projectTasksBeans, $projectTaskBean);
                }
            }
		}

		return $projectTasksBeans;
	}
	
	
	/**
	 * getNumberOfTasksInProject
	 * 
	 * Returns the count of project_tasks for the given project_id
	 * 
	 * This is a private helper function to get the number of project tasks for a given project_id.
	 * 
	 * @param $project_id integer value of the project_id associated with this ProjectTask instance
	 * @return total integer value of the count of project tasks, 0 if none found
	 */
    private function getNumberOfTasksInProject($project_id='')
    {
    	if(!empty($project_id))
    	{
	        $query = "SELECT count(project_task_id) AS total FROM project_task WHERE project_id = '{$project_id}'";
	        $result = $this->db->query($query, true);
	        if($result)
	        {
		        $row = $this->db->fetchByAssoc($result);
		        if(!empty($row['total']))
		        {
		           return $row['total'];
		        }
	        }
    	}
        return 0;
    }	
    /**
     * Update percent complete for project tasks with children tasks based on children's values
     */
    public function updateStatistic()
    {
        /**
         * @var array Array of tasks for current project
         */
        $list = array();
        /**
         * @var array Key-value array of project_task_id => parent_task_id
         */
        $tree = array();
        /**
         * @var array Array with nodes which have childrens
         */
        $nodes = array();
        /**
         * @var array Array with IDs of list which have been changed
         */
        $changed = array();

        $db = DBManagerFactory::getInstance();
        $this->disable_row_level_security = true;
        $query = $this->create_new_list_query('', "project_id = {$db->quoted($this->project_id)}");
        $this->disable_row_level_security = false;
        $res = $db->query($query);
        while($row = $db->fetchByAssoc($res))
        {
            array_push($list, $row);
        }
        // fill in $tree
        foreach($list as $k => &$v)
        {
            if(isset($v['project_task_id']) && $v['project_task_id'] != '')
            {
                $tree[$v['project_task_id']] = $v['parent_task_id'];
                if(isset($v['parent_task_id']) && $v['parent_task_id'])
                {
                    if(!isset($nodes[$v['parent_task_id']]))
                    {
                        $nodes[$v['parent_task_id']] = 1;
                    }
                }
            }
        }
        unset($v);
        // fill in $nodes array
        foreach($nodes as $k => &$v)
        {
            $run = true;
            $i = $k;
            while($run)
            {
                if(isset($tree[$i]) &&  $tree[$i]!= '')
                {
                    $i = $tree[$i];
                    $v++;
                }
                else
                {
                    $run = false;
                }
            }
        }
        arsort($nodes);
        unset($v);
        // calculating of percentages and comparing calculated value with database one
        foreach($nodes as $k => &$v)
        {
            $currRow = null;
            $currChildren = array();
            $run = true;
            $tmp = array();
            $i = $k;
            while($run)
            {
                foreach($list as $id => &$taskRow)
                {
                    if($taskRow['project_task_id'] == $i && $currRow === null)
                    {
                        $currRow = $id;
                    }
                    if($taskRow['parent_task_id'] == $i)
                    {
                        if(!in_array($taskRow['project_task_id'], array_keys($nodes)))
                        {
                            array_push($currChildren, $taskRow);
                        }
                        else
                        {
                            array_push($tmp, $taskRow['project_task_id']);
                        }
                    }
                }
                unset($taskRow);
                if(count($tmp) == 0)
                {
                    $run = false;
                }
                else
                {
                    $i = array_shift($tmp);
                }
            }
            $subres = $this->_calculateCompletePercent($currChildren);
            if($subres != $list[$currRow]['percent_complete'])
            {
                $list[$currRow]['percent_complete'] = $subres;
                array_push($changed, $currRow);
            }
        }
        unset($v);
        // updating data in database for changed tasks
        foreach($changed as $k => &$v)
        {
            $task = BeanFactory::getBean('ProjectTask');
            $task->populateFromRow($list[$v]);
            $task->skipParentUpdate();
            $task->save(false);
        }
    }

    //Check for changes and construct the email
    function check_for_changes($projectTask){
        $mailTo = array();
        $message = '';
        $subject = '';
        $templateName = 'ProjectTask';
        $templateFile = 'include/eintel/notifications/templates/ProjectTask/projecttask.php';

        $creator = new User();
        $creator->retrieve($this->created_by);
        
        $assigned = new User();
        $assigned->retrieve($this->assigned_user_id);


        if(isset($projectTask['id'])){
            if($projectTask['assigned_user_id'] != $this->assigned_user_id){
                //assigned user has changed
                $message = "The assigned user for Project Task ".$this->name." has been changed to ".$assigned->full_name.".";
                $mailTo = array('assigned' => array( 'address' => $assigned->email1,
                                                      'name' => $assigned->full_name),
                                //'creator' => array( 'creatot' => $creator->email1,
                                //                    'name' => $creator->full_name),
                              );
                $this->send_notification($message, $subject = "Assigned user change", $mailTo, $templateName, $templateFile, $creator, $assigned);
            }
            if(isset($projectTask['status']) && $projectTask['status'] ==  "Completed" && $this->status != "Completed"){
                 //status changed from completed to something else.
                 $message = "Project Task ".$this->name." has been reopend.";
                 $mailTo = array('assigned' => array( 'address' => $assigned->email1,
                                                  'name' => $assigned->full_name),
                                 //'creator' => array( 'address' => $creator->email1,
                                 //                   'name' => $creator->full_name),
                                  );
                $this->send_notification($message, $subject = "Reopend", $mailTo,  $templateName, $templateFile, $creator, $assigned);
            }

            if(isset($projectTask['status']) && $this->status == "Completed"){
                if($this->status != $projectTask['status']){
                    //status has been change to complete
                    $message = "Project Task ".$this->name." has been completed by ".$assigned->full_name.".";
                    $mailTo = array(//'creator' => array( 'address' => $creator->email1,
                                    //              'name' => $creator->full_name),
                                    'assigned' => array( 'address' => $assigned->email1,
                                                  'name' => $assigned->full_name),
                                  );
                    $this->send_notification($message, $subject = "Completed", $mailTo,  $templateName, $templateFile, $creator, $assigned);
                }               
            }
        }else{
        //New task, email assigned user
            $subject = "Created";
            $message = "A new Project Task has been created and assigned to ".$assigned->full_name.".";
            if($this->status == "Completed"){
                $message .= " The Project Task has also been Completed.";
                $subject = "Created and Completed";
                //$mailTo = array('creator' => array( 'address' => $creator->email1,
                //                                  'name' => $creator->full_name),
                //                'assigned' => array( 'address' => $assigned->email1,
                //              'name' => $assigned->full_name),
                //            );
            }
           
            $mailTo = array('assigned' => array( 'address' => $assigned->email1,
                              'name' => $assigned->full_name),
                            );
            $this->send_notification($message, $subject, $mailTo,  $templateName, $templateFile, $creator, $assigned);
        }
    }

    //send notification
    function send_notification($message = null, $subject= null, $mailTo = null, $templateName, $templateFile, $creator = null, $assigned = null){
        global $current_user;
        global $sugar_config;

        require_once('include/eintel/notifications/notifications.php');
        require_once("XTemplate/xtpl.php");

        if(empty($creator->full_name)){
            $creator = new User();
            $creator->retrieve($this->created_by);
        }
        if(empty($assigned->full_name)){
            $assigned = new User();
            $assigned->retrieve($this->assigned_user_id);
        }

        if(isset($mailTo)){
            foreach($mailTo as $to){
                $xtpl = new XTemplate($templateFile);
                
                $xtpl->assign('SUBJECT', $subject);
                $xtpl->assign('MESSAGE', $message);
                $xtpl->assign('ASSIGNED_USER',$assigned->full_name);
                $xtpl->assign('USER', $to['name']);
                $xtpl->assign('STATUS', $this->status);
                $xtpl->assign('CREATOR', $creator->full_name);
                $xtpl->assign('DATE_DUE',$this->date_finish);
                $xtpl->assign('DESCRIPTION',$this->description);
                $xtpl->assign('TASK',$this->name);
                $xtpl->assign('PRIORITY',$this->priority);
                $xtpl->assign('URL', $sugar_config["site_url"]. "/index.php?module=ProjectTask&action=DetailView&record=". $this->id);
                $xtpl->parse($templateName.'.PROJECTTASK_ROWS');
                              
                send_notification($to['address'], $to['name'], $templateName, $xtpl);
            }
        }

    }
//END


	
}

function getUtilizationDropdown($focus, $field, $value, $view) {
	global $app_list_strings;

	if($view == 'EditView') {
		global $app_list_strings;
        $html = '<select name="'.$field.'">';
        $html .= get_select_options_with_id($app_list_strings['project_task_utilization_options'], $value);
        $html .= '</select>';
        return $html;
    }

    return translate('project_task_utilization_options', '', $focus->$field);
}
?>
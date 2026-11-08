<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
class Timesheet extends Basic {
	var $field_name_map = array();
	// Stored fields
  var $id;
  var $name;
  var $date_entered;
  var $date_modified;
  var $modified_user_id;
  var $modified_by_name;
  var $created_by;
  var $created_by_name;
  var $description;
  var $deleted;
  var $created_by_link;
  var $modified_user_link;
  var $assigned_user_id;
  var $assigned_user_name;
  var $assigned_user_link;



	/*
  var $id;
	var $date_entered;
  var $date_modified;
	var $assigned_user_id;
	var $modified_user_id;
	var $created_by;
	var $created_by_name;
	var $modified_by_name;
  var $assigned_user_name;
  var $description;
  */

  var $parent_type;
	var $parent_id;
  var $parent_name;

  // used in Reports module only
  var $account_id;
  var $project_id;
  //


  var $text_for_bill;
  var $actual;
  var $date_booked;
  var $status;
  var $billable;

  var $invoice_name;
  var $invoice_id;

	var $module_dir = "Timesheet";
	var $table_name = "timesheet";
	var $new_schema = true;
	var $object_name = "Timesheet";

	// This is used to retrieve related fields from form posts.
	var $additional_column_fields = Array('assigned_user_name', 'assigned_user_id', 'parent_name');



	function Timesheet() {
		parent::Basic();

            //$this->team_id = 1; // make the item globally accessible
            @$this->disable_row_level_security = true;

            if (isset($_REQUEST['action']) &&
                    ( strtolower($_REQUEST['action']) == 'savematrix' || strtolower($_REQUEST['action']) == 'savedailymatrix')) {
              @$this->process_save_dates = false;
            }

        /*if (empty($this->date_booked)) {
          global $timedate;
          $this->date_booked = $timedate->to_display_date(date("Y-m-d", time()));
        }*/
	}

  // this function is used in track viewer
  function get_summary_text() {
    return $this->date_booked . ": ".$this->actual."h";
  }

  function create_export_query(&$order_by, &$where) {
    $modules = array('Project', 'ProjectTask', 'Cases');
    $sql = array();
    $custom_join = $this->custom_fields->getJOIN();
    foreach ($modules as $mod) {
      switch ($mod) {
        case 'Project':
          $select = "project.name project, '' as project_task, '' as `case`";
          $join = " INNER JOIN project ON project.id = timesheet.parent_id AND timesheet.parent_type='Project' LEFT JOIN users ON $this->table_name.assigned_user_id=users.id ";
        break;

        case 'ProjectTask':
          $select = "project.name project, project_task.name project_task, '' as `case`";
          $join = " INNER JOIN project_task ON project_task.id = timesheet.parent_id AND timesheet.parent_type='ProjectTask' LEFT JOIN project ON project.id = project_task.project_id LEFT JOIN users ON $this->table_name.assigned_user_id=users.id ";
        break;

        case 'Cases':
          $select = "'' as project, '' as project_task, cases.name `case`";
          $join = " INNER JOIN cases ON cases.id = timesheet.parent_id AND timesheet.parent_type='Cases' LEFT JOIN users ON $this->table_name.assigned_user_id=users.id ";
        break;
      }

      $query = "SELECT ";
      $query .= "users.user_name as employee, ".$select.", timesheet.actual, timesheet.billable, timesheet.date_booked, timesheet.status, timesheet.text_for_bill, timesheet.description";
      if ($custom_join) {
        $query .= $custom_join['select'];
      }
      $query .= " FROM $this->table_name ";
      $query .= $join;
      if ($custom_join) {
        $query .= $custom_join['join'];
      }
      $where_auto = '1=1';
      if($show_deleted == 0){
        $where_auto = " $this->table_name.deleted=0 ";
      } else if($show_deleted == 1) {
        $where_auto = "$this->table_name.deleted=1";
      }
      if ($where != "")
        $query .= "where $where AND ".$where_auto;
      else
        $query .= "where ".$where_auto;

      if ($order_by != "")
        $query .=  " ORDER BY ". $this->process_order_by($order_by, null);
      else
        $query .= " ORDER BY $this->table_name.date_entered DESC";
      $sql[] = $query;
    }
    $query = "(".join(") UNION ALL (", $sql).")";
		return $query;
	}

	function fill_in_additional_list_fields() {
    $this->fill_in_additional_detail_fields();
	}

	function fill_in_additional_detail_fields() {
    // Fill in the assigned_user_name
		$this->assigned_user_name = get_assigned_user_name($this->assigned_user_id);

    $this->created_by_name = get_assigned_user_name($this->created_by);
		$this->modified_by_name = get_assigned_user_name($this->modified_user_id);

    $this->fill_in_additional_parent_fields();
  }

  function fill_in_additional_parent_fields()
	{
		global $app_strings;
		$this->parent_name = '';
        $query = false;

		if ($this->parent_type == "Project") {
            $query = "SELECT name FROM project WHERE id='$this->parent_id'";
        }
        else if ($this->parent_type == 'Cases') {
          $query = "SELECT name FROM cases WHERE id='$this->parent_id'";
        }
        else if ($this->parent_type == 'ProjectTask') {
          $query = "SELECT name FROM project_task WHERE id='$this->parent_id'";
        }
        else if ($this->parent_type == 'Tasks') {
          $query = "SELECT name FROM tasks WHERE id='$this->parent_id'";
        }

    if ($query) {
      $result = $this->db->query($query,true, $app_strings['ERR_CREATING_FIELDS']);

      // Get the id and the name.
      $row = $this->db->fetchByAssoc($result);
      if ($row && !empty($row['name'])){
        $this->parent_name = $row['name'];
      }
    }
  }

  function get_list_view_data() {
    $fields = $this->get_list_view_array();
    if (!empty($this->parent_name)) {
      $fields['PARENT_NAME'] = $this->parent_name;
		}
    return $fields;
  }

  // defines whether we have project/case leader
  function hasPermission($user) {
    if ($this->parent_type == 'ProjectTask') {
      $query = "SELECT p.assigned_user_id FROM project_task pt INNER JOIN project p ON pt.project_id = p.id  WHERE pt.id = '$this->parent_id'";
    }
    else if ($this->parent_type == 'Cases') {
      $query = "SELECT a.assigned_user_id FROM cases c INNER JOIN accounts a ON c.account_id = a.id WHERE c.id = '$this->parent_id'";
    }
    else {
      $query = "SELECT assigned_user_id FROM ".strtolower($this->parent_type)." WHERE id = '$this->parent_id'";
    }
    $result = $this->db->query($query, true);
    $row = $this->db->fetchByAssoc($result);
    if ($row && ($row['assigned_user_id'] == $user->id || is_admin($user))) {
      return true;
    }
    return false;
  }

  function create_new_list_query($order_by, $where, $filter = array(), $params = array(), $show_deleted = 0, $join_type='', $return_array = false, $parentbean, $singleSelect = false) {

    $ret_arr = parent::create_new_list_query($order_by, $where, $filter, $params, $show_deleted, $join_type, $return_array, $parentbean, $singleSelect);

    // $main_query = $ret_array['select'] . $params['custom_select'] . $ret_array['from'] . $params['custom_from'] . $ret_array['where'] . $params['custom_where'] . $ret_array['order_by'] . $params['custom_order_by'];
    if ($parentbean->object_name == 'Timesheet') {
      preg_match_all("/parent.([^ ]+)/i", $where, $regs);
      $orig_where = $where;
      if (!empty($regs[0])) {
        for ($i = 0; $i < count($regs[0]); $i++) {
          $where = preg_replace("|".$regs[0][$i]."|", $regs[1][$i].'.name', $where, 1);

          $ret_arr['select'] .= ", ".$regs[1][$i].".name ".$regs[1][$i]."_name ";
          $ret_arr['from'] .= " LEFT JOIN ".$regs[1][$i]." ON ".$regs[1][$i].".id = timesheet.parent_id ";
        }
        $ret_arr['where'] = str_replace($orig_where, $where, $ret_arr['where']);
      }
    }
    return $ret_arr;
  }

  function bean_implements($interface) {
    switch($interface) {
         case 'ACL':return true;
    }
    return false;
  }

  //re#100 EINTEL task(re#85) moved into new function
  function fetch_related_accounts_ofprojects($jobs){
    //get related accounts for a project even if the project is not assigned to user
    foreach($jobs as $id => &$item){
      //foreach($data as &$item){
        if($item['type'] == 'Project'){
          $accounts_query = "SELECT a.id as account_id, a.name as account_name, a.deleted as account_del, pa.deleted as pa_account_del
                                FROM accounts a
                                LEFT JOIN projects_accounts pa ON a.id = pa.account_id AND pa.deleted is false AND a.deleted is false
                                LEFT JOIN project p ON pa.project_id = p.id
                                WHERE pa.project_id = '{$item['id']}'";
          $result = $this->db->query($accounts_query);
          //$GLOBALS['log']->debug("TIMESHEET: $result");
          if($result){
            while ($row = $this->db->fetchByAssoc($result)) {
              $item['accounts'][] = $row;
            }
          }
        }

      }
    return $jobs;
  }
  //END


  //sortJobs() not in use anymore
  function sortJobs($jobs) {
  $aJobs = array();
  foreach ($jobs as $i => $job) {
    if ($job['type'] == 'ProjectTask') {
      if(isset($aJobs[$job['parent_id']])) {#if the project tasks parent is in the the $ajobs array add it as a child
        $aJobs[$job['parent_id']][] = $job;
      }
      else {
        $aJobs[$job['parent_id']][] = array(
          'id' => $job['parent_id'],
          'name' => $job['parent_name'],
          'type' => 'Project'
        );
        $aJobs[$job['parent_id']][] = $job;
      }
    }
    else {
        $aJobs[$job['id']][] = $job;

    }
  }
  return $aJobs;
}


//RE#96 EINTEL new sort function
function build_Parents($jobs = null){
    $parents = array();
    foreach($jobs as $job){
        if(!isset($job['parent_id'])){
           $parents[$job['id']] = $job;
        }
    }
    foreach ($jobs as $j){
        if(isset($j['parent_id']) && $j['type'] != 'Project'){
            if(array_key_exists($j['parent_id'], $parents)){
            }else{
                $isgrandchild = false;
                foreach($jobs as $jid){
                    if($jid['id'] == $j['parent_id']){
                        $isgrandchild = true;
                    }
                }
                    if(!$isgrandchild){
                        $parents[$j['parent_id']] = array(
                            'id' => $j['parent_id'],
                            'name' => $j['parent_name'],
                            'type' => $j['parent_type']
                        );
                    }
            }
        }
    }


    foreach($parents as &$parent){
        $parent['children'] = $this->getChildOf($parent['id'], $jobs);

    }
    return $parents;
}

function getChildOf($parent = null, $jobs = null){
    $children = array();
    foreach($jobs as $child){
        if(isset($child['parent_id']) && $child['parent_id'] == $parent){
            $children[$child['id']]= $child;
            $children[$child['id']]['children'] = $this->getChildOf($child['id'], $jobs);
        }
    }
    if(!empty($children)){
        return $children;
    }
}
//END

  function getAssignedParents($userid) {
    $ret = array();
    // select not completed Projects
    $q[] = "SELECT id, status, name, 'Project' as type FROM project WHERE assigned_user_id = '$userid' AND deleted = 0 and (status != 'Completed' OR status IS NULL)";

    // select not completed Project Tasks w/ related Projects
    //$q[] = "SELECT pt.id, concat(pt.project_task_id, ' : ', pt.name) as name, 'ProjectTask' as type, p.id as parent_id, p.name as parent_name, 'Project' as parent_type FROM project_task pt LEFT JOIN project p ON pt.project_id = p.id WHERE pt.assigned_user_id = '$userid' AND pt.deleted = 0 AND (pt.status != 'Completed' OR pt.status IS NULL) AND (pt.percent_complete != '100' OR pt.percent_complete IS NULL) order by project_task_id";

    $q[] = "SELECT pt.id, pt.status,
            concat(pt.project_task_id, ' : ', pt.name) as name,
            'ProjectTask' as type,
            COALESCE(pt.parent_task_id, p.id) as parent_id,
            COALESCE(ptp.name, p.name) as parent_name,
            IF (ptp.id is not null, 'ProjectTask', 'Project') as parent_type
            FROM project_task pt
            LEFT JOIN project p ON pt.project_id = p.id
            LEFT JOIN project_task ptp ON pt.parent_task_id = ptp.id
            WHERE pt.assigned_user_id = '$userid'
            AND pt.deleted = 0
            AND (pt.status != 'Completed' OR pt.status IS NULL)
            AND (pt.percent_complete != '100' OR pt.percent_complete IS NULL)
            order by pt.project_task_id";

    // select not closed Cases w/ related Accounts
    $q[] = "SELECT c.id, c.status c.name, 'Cases' as type, a.id as account_id, a.name as account_name, 'Accounts' as parent_type FROM cases c LEFT JOIN accounts a ON c.account_id = a.id WHERE c.assigned_user_id = '$userid' AND c.deleted = 0 AND (c.status != 'Closed' OR c.status IS NULL)";

    /** EINTEL part of RE# 96
     * Adding tasks to timesheets.
     * Select not completed Tasks w/ related Accounts
     */
    $q[] = "SELECT
                t.id, t.status, t.name, 'Tasks' as type, a.id as account_id, a.name as account_name,
             CASE t.parent_type
                WHEN 'Cases' THEN c.name
                WHEN 'ProjectTask' THEN pt.name
                WHEN 'Project' THEN p.name
                WHEN 'Tasks' THEN t1.name
             END as parent_name,
             CASE t.parent_type
                WHEN 'Accounts' THEN t.parent_type = NULL
                WHEN 'ProjectTask' THEN t.parent_type
                WHEN 'Project' THEN t.parent_type
                WHEN 'Cases' THEN t.parent_type
                WHEN 'Tasks' THEN t.parent_type
             END as parent_type,
             CASE t.parent_type
                WHEN 'Cases' THEN t.parent_id
                WHEN 'ProjectTask' THEN t.parent_id
                WHEN 'Project' THEN t.parent_id
                WHEN 'Tasks' THEN t.parent_id
             ELSE
                t.parent_id = NULL
             END as parent_id
            FROM
              tasks t
              LEFT JOIN project p ON t.parent_id = p.id AND p.deleted = 0
              LEFT JOIN project_task pt ON t.parent_id = pt.id AND pt.deleted = 0
              LEFT JOIN cases c on t.parent_id = c.id AND c.deleted = 0
              LEFT JOIN accounts a ON t.parent_id = a.id AND a.deleted = 0
              LEFT JOIN tasks t1 ON t.parent_id = t1.id AND t1.deleted = 0
            WHERE
              (t.parent_type IS NOT NULL AND t.parent_type != '')
            AND
              (t.status != 'Completed' OR t.status IS NULL)
            AND
               t.assigned_user_id = '$userid'
            AND
              (t.deleted = 0 or t.deleted is false)";
        //END
    foreach ($q as $query) {
      $result = $this->db->query($query);
      while ($row = $this->db->fetchByAssoc($result)) {
        $ret[$row['id']] = $row;
      }
    }

     $ret = $this->getMissingParents($ret, $userid);

    return $ret;
  }

  function getMissingParents(&$ret, $userid){
        $missingParents = array();
        foreach($ret as $r){
            unset($q);
           
            if(isset($r['parent_id']) && !array_key_exists($r['parent_id'], $ret)){
                $missingParents[] = array('id' => $r['parent_id'], 'type' => $r['parent_type']);
                switch ($r['parent_type']) {
                    case 'Project':
                        $select = "p.id, p.status,
                                 p.name,
                                 'Project' as type";
                        $from = "project p";
                        $where = "p.id = '{$r['parent_id']}'";
                        $display = true;
                    break;
                    case 'ProjectTask':
                        $select = "pt.id,
                                concat(pt.project_task_id, ' : ', pt.name) as name, pt.status,
                                'ProjectTask' as type,
                                COALESCE(pt.parent_task_id, p.id) as parent_id,
                                COALESCE(ptp.name, p.name) as parent_name,
                                IF (ptp.id is not null, 'ProjectTask', 'Project') as parent_type";
                        $from = "project_task pt
                                LEFT JOIN project p ON pt.project_id = p.id
                                LEFT JOIN project_task ptp ON pt.parent_task_id = ptp.id";
                        $where = "pt.id = '{$r['parent_id']}'";
                        $display = true;
                    break;
                    case 'Tasks':
                        $select = "t.id, t.name, t.status, 'Tasks' as type, a.id as account_id, a.name as account_name,
                                 CASE t.parent_type
                                    WHEN 'Cases' THEN c.name
                                    WHEN 'ProjectTask' THEN pt.name
                                    WHEN 'Project' THEN p.name
                                    WHEN 'Tasks' THEN t1.name
                                 END as parent_name,
                                 CASE t.parent_type
                                    WHEN 'Accounts' THEN t.parent_type = NULL
                                    WHEN 'ProjectTask' THEN t.parent_type
                                    WHEN 'Project' THEN t.parent_type
                                    WHEN 'Cases' THEN t.parent_type
                                    WHEN 'Tasks' THEN t.parent_type
                                 END as parent_type,
                                 CASE t.parent_type
                                    WHEN 'Cases' THEN t.parent_id
                                    WHEN 'ProjectTask' THEN t.parent_id
                                    WHEN 'Project' THEN t.parent_id
                                    WHEN 'Tasks' THEN t.parent_id
                                 ELSE
                                    t.parent_id = NULL
                                 END as parent_id";
                         $from = "tasks t
                                  LEFT JOIN project p ON t.parent_id = p.id AND p.deleted = 0
                                  LEFT JOIN project_task pt ON t.parent_id = pt.id AND pt.deleted = 0
                                  LEFT JOIN cases c on t.parent_id = c.id AND c.deleted = 0
                                  LEFT JOIN accounts a ON t.parent_id = a.id AND a.deleted = 0
                                  LEFT JOIN tasks t1 ON t.parent_id = t1.id AND t1.deleted = 0";
                         $where = "t.id = '{$r['parent_id']}'";
                         $display = true;
                    break;
                    case 'Cases':
                        $select = "c.id, c.name,c.status, 'Cases' as type, a.id as account_id, a.name as account_name, 'Accounts' as parent_type";
                        $from = "cases c LEFT JOIN accounts a ON c.account_id = a.id";
                        $where = "c.id = '{$r['parent_id']}'";
                        $display = true;
                    break;
                }
            if($display){
                $q[] = "SELECT {$select} FROM {$from} where {$where}";
                unset($display);
            }

        }
        if(isset($q)){
            foreach ($q as $query) {
                $result = $this->db->query($query);
                while ($row = $this->db->fetchByAssoc($result)) {
                    $ret[$row['id']] = $row;
                }
            }
        }
    }
    if(!empty($missingParents)){
        $this->getMissingParents($ret, $userid);
    }
    return $ret;

}



  function getUserTimesheets($userid, $date) {
    $ret = array();
    $q = "SELECT id, parent_id, actual, description, date_booked FROM {$this->table_name} WHERE deleted = 0 AND assigned_user_id = '$userid' AND date_booked >= '".date("Y-m-d", $date[0])."' AND date_booked <= '".date("Y-m-d", $date[1])."'";
    $result = $this->db->query($q);
    while ($row = $this->db->fetchByAssoc($result)) {
      $ret[$row['parent_id']][$row['date_booked']] = array('id' => $row['id'], 'actual' => $row['actual'], 'description' => $row['description']);
    }
    return $ret;
  }


  /** EINTEL re#103 NM
   * The getUserTimesheets($userid, $date) returns an array of timesheets but the only the lastest timesheet for each parent or child is returned.
   * This new functions getUserTimesheetsMatrix($userid, $date) returns  all the timesheets for a project/ptask/case/task
   */
    function getUserTimesheetsMatrix($userid, $date) {
    $ret = array();
    $q = "SELECT id, parent_id, actual, description, date_booked FROM {$this->table_name} WHERE deleted = 0 AND assigned_user_id = '$userid' AND date_booked >= '".date("Y-m-d", $date[0])."' AND date_booked <= '".date("Y-m-d", $date[1])."'";
    $result = $this->db->query($q);
    while ($row = $this->db->fetchByAssoc($result)) {
        $ret[$row['parent_id']][][$row['date_booked']] = array('id' => $row['id'], 'actual' => $row['actual'], 'description' => $row['description']);
    }
    return $ret;
  }
  //END


  function get_projects_array() {
    global $current_user;
    $ret = array();

    if( is_admin($current_user) ) {
        $q = "SELECT p.id, CONCAT(COALESCE(a.name,'****'), ' - ', p.name) as name FROM project p
                LEFT JOIN projects_accounts pa ON p.id = pa.project_id
                LEFT JOIN accounts a ON pa.account_id = a.id
                WHERE p.deleted = 0 and (p.status != 'Completed1' OR p.status IS NULL)
				ORDER BY name";
    } else {
        $q = "SELECT id, name FROM project WHERE deleted = 0  AND assigned_user_id = '$current_user->id'";
    }
    $result = $GLOBALS['db']->query($q);
    while ($row = $GLOBALS['db']->fetchByAssoc($result)) {
      $ret[$row['id']] = $row['name'];
    }
    return $ret;
  }

  /*
   * NM, Getting the invoice numbers array
   */
  function get_invoices_array() {
    global $current_user;
    $invoice = array();


    //$q = "SELECT id, name FROM invoice WHERE deleted = 0  AND assigned_user_id = '$current_user->id'";
    $q = "SELECT id, name FROM invoice WHERE deleted = 0";

    $result = $GLOBALS['db']->query($q);

    $invoice['0'] = '--All--';

    while ($row = $GLOBALS['db']->fetchByAssoc($result)) {
      $invoice[$row['id']] = $row['name'];
    }
    return $invoice;
  }
 /*
  * End
  */

  /*
   * NM, Added in invoice number to querys
   */
  function getTotals($args) {
    global $current_user, $timedate;
    $ret = array('actual' => 0, 'billable' => 0, 'entries' => array());

    $args['date_start'] = $timedate->to_db_date($args['date_start'], false);
    $args['date_end'] = $timedate->to_db_date($args['date_end'], false);

	if( is_admin($current_user) ) {
		$q = "SELECT id FROM project WHERE id = '".$args['project_id']."' AND deleted = 0";
	} else {
		$q = "SELECT id FROM project WHERE assigned_user_id = '$current_user->id' AND id = '".$args['project_id']."' AND deleted = 0";
	}
    if ($GLOBALS['db']->getOne($q)) {
      if(empty($args['invoice_id'])){
        $q = "SELECT invoice_name, SUM(t.actual) actual, SUM(t.billable) billable FROM timesheet t WHERE t.deleted = 0 AND(t.parent_id = '".$args['project_id']."' OR t.parent_id IN (SELECT pt.id FROM project_task pt INNER JOIN project p ON pt.project_id = p.id WHERE p.id = '".$args['project_id']."' AND pt.deleted = 0))";
      }else {
        $q = "SELECT invoice_name, SUM(t.actual) actual, SUM(t.billable) billable FROM timesheet t WHERE t.deleted = 0 AND invoice_id = '".$args['invoice_id']."' AND(t.parent_id = '".$args['project_id']."' OR t.parent_id IN (SELECT pt.id FROM project_task pt INNER JOIN project p ON pt.project_id = p.id WHERE p.id = '".$args['project_id']."' AND pt.deleted = 0))";
      }

      $date_where = '';
      if (!empty($args['date_start']) && !empty($args['date_end'])) {
        $date_where .= "t.date_booked BETWEEN CAST('".$args['date_start']."' AS DATE) AND CAST('".$args['date_end']."' AS DATE)";
      }
      else if (!empty($args['date_start'])) {
        $date_where .= "CAST('".$args['date_start']."' AS DATE) BETWEEN CAST('".$args['date_start']."' AS DATE) AND t.date_booked";
      }
      else if (!empty($args['date_end'])) {
        $date_where .= "t.date_booked BETWEEN t.date_booked AND CAST ('".$args['date_end']."' AS DATE)";
      }

      if ($date_where != '') {
        $q .= " AND ($date_where) ";
      }

      if (!empty($args['user_id'])) {
        $q .= " AND t.assigned_user_id = '".$args['user_id']."'";
      }
      $q .= " GROUP BY t.parent_id";

      $result = $GLOBALS['db']->query($q);
      while ($row = $GLOBALS['db']->fetchByAssoc($result)) {
        if (isset($row['actual'])) {
          $ret['actual'] += $row['actual'];
        }
        if (isset($row['billable'])) {
          $ret['billable'] += $row['billable'];
        }
      }

      $assigned_user_id = "";
      if (!empty($args['user_id'])) {
        $assigned_user_id = "t.assigned_user_id = '".$args['user_id']."' AND ";
      }

      $entries = array();
      if(empty($args['invoice_id'])){
        $q = "SELECT t.invoice_name, p.name parent_name, t.parent_type, t.date_booked, t.parent_id, t.actual, IFNULL(t.billable, 0) billable, u.user_name, u.id user_id FROM timesheet t INNER JOIN project p ON t.parent_id = p.id LEFT JOIN users u ON t.assigned_user_id = u.id WHERE $assigned_user_id t.deleted = 0 AND t.parent_id = '".$args['project_id']."'";
      } else{
        $q = "SELECT t.invoice_name, p.name parent_name, t.parent_type, t.date_booked, t.parent_id, t.actual, IFNULL(t.billable, 0) billable, u.user_name, u.id user_id FROM timesheet t INNER JOIN project p ON t.parent_id = p.id LEFT JOIN users u ON t.assigned_user_id = u.id WHERE $assigned_user_id t.deleted = 0 AND t.parent_id = '".$args['project_id']."' AND invoice_id = '".$args['invoice_id']."'";
      }

      if ($date_where != '') {
        $q .= " AND ($date_where) ";
      }
      $res = $GLOBALS['db']->query($q);
      while ($row = $GLOBALS['db']->fetchByAssoc($res)) {
        $entries[] = $row;
      }
      if(empty($args['invoice_id'])){
        $q = "SELECT t.invoice_name, pt.name parent_name, pt.project_id, t.parent_type, t.date_booked, t.parent_id, t.actual, IFNULL(t.billable, 0) billable, u.user_name, u.id user_id FROM timesheet t INNER JOIN project_task pt ON t.parent_id = pt.id LEFT JOIN users u ON t.assigned_user_id = u.id WHERE $assigned_user_id t.deleted = 0 AND t.parent_id IN (SELECT pt.id FROM project_task pt INNER JOIN project p ON pt.project_id = p.id WHERE p.id = '".$args['project_id']."' AND pt.deleted = 0)";
      } else{
        $q = "SELECT t.invoice_name, pt.name parent_name, pt.project_id, t.parent_type, t.date_booked, t.parent_id, t.actual, IFNULL(t.billable, 0) billable, u.user_name, u.id user_id FROM timesheet t INNER JOIN project_task pt ON t.parent_id = pt.id LEFT JOIN users u ON t.assigned_user_id = u.id WHERE $assigned_user_id t.deleted = 0 AND invoice_id = '".$args['invoice_id']."' AND t.parent_id IN (SELECT pt.id FROM project_task pt INNER JOIN project p ON pt.project_id = p.id WHERE p.id = '".$args['project_id']."' AND pt.deleted = 0)";
      }

      if ($date_where != '') {
        $q .= " AND ($date_where) ";
      }
      $res = $GLOBALS['db']->query($q);
      while ($row = $GLOBALS['db']->fetchByAssoc($res)) {
        $entries[] = $row;
      }
      $ret['entries'] = $entries;
    }
    return $ret;
  }

    // EINTEL Addition - Timesheet before save hook
	function save($check_notify = FALSE){

        $this->setAccountAndProject();

        // check whether it is TimesheetTimer object
        if ($this->object_name != 'TimesheetTimer') {
            $this->name = $this->parent_type . ": " . $this->actual;
        }

        //EINTEL ADDITION re#76 & re#88
        if(!isset($this->id)){
            //new record
             if(isset($this->actual) && empty($this->billable)){
                 //default billable to actual
                 $this->billable = $this->actual;
             }
        }
        //END

        $r = parent::save($check_notify);
        $this->updateParentTotals();
        return $r;
	}

    // EINTEL Addition - Timesheet before delete hook
    function mark_deleted($id){
        $r = parent::mark_deleted($id);
		if(!isset($_SESSION['show_deleted'])) {
            $this->updateParentTotals();
        }
        return $r;
    }


    function setAccountAndProject() {
        // check whether it is TimesheetTimer object
        if ($this->object_name == 'TimesheetTimer')
          return;

        // timesheet table name
        $tbl = $this->table_name;

        // timesheet parent id
        $id = $this->parent_id;

        switch ($this->parent_type) {
          // timesheet is created for Tasks:
          case 'Tasks':
          // set parent_id in timesheet table for compliance w/ Reports module
            $q = "SELECT parent_id as 'account_id' FROM tasks WHERE id = '$id' AND deleted = '0' AND parent_type = 'Accounts'";
            if ($account_id = $this->db->getOne($q)) {
              $this->account_id = $account_id;
            }
          break;
          // timesheet is created for Case:
          case 'Cases':
            // set account_id in timesheet table for compliance w/ Reports module
            $q = "SELECT account_id FROM cases WHERE id = '$id' AND deleted = '0'";
            if ($account_id = $this->db->getOne($q)) {
              $this->account_id = $account_id;
            }
          break;

          // timesheet is created for ProjectTask
          case 'ProjectTask':
            // count all timesheet related to ProjectTasks' Project
            // check whether ProjectTask has parent Project
            $q = "SELECT p.id FROM project p INNER JOIN project_task pt ON p.id = pt.project_id WHERE pt.id = '$id'";
            if ($p_id = $this->db->getOne($q)) {
              // set project_id in timesheet table for compliance w/ Reports module
              $this->project_id = $p_id;

              // set account_id if applicable
              $q = "SELECT a.id FROM project p LEFT JOIN projects_accounts pr ON p.id = pr.project_id LEFT JOIN accounts a ON pr.account_id = a.id AND pr.deleted = 0 AND a.deleted = 0 WHERE p.id = '$p_id'";
              if ($account_id = $this->db->getOne($q)) {
                $this->account_id = $account_id;
              }
            }
          break;

          // timesheet is created for Project
          case 'Project':
            // set project_id in timesheet table for compliance w/ Reports module
            $this->project_id = $id;

            // set account_id if applicable
            $q = "SELECT a.id FROM project p LEFT JOIN projects_accounts pr ON p.id = pr.project_id LEFT JOIN accounts a ON pr.account_id = a.id AND pr.deleted = 0 AND a.deleted = 0 WHERE p.id = '$id'";
            if ($account_id = $this->db->getOne($q)) {
              $this->account_id = $account_id;
            }
          break;
        }
    }
  // doCount is used to count total actual, total billable afford
  // when Timesheet record is being saved
  function updateParentTotals()
  {
    // check whether it is TimesheetTimer object
    if ($this->object_name == 'TimesheetTimer')
      return;

    // timesheet table name
    $tbl = $this->table_name;

    // timesheet parent id
    $id = $this->parent_id;

    switch ($this->parent_type) {
      // timesheet is created for Tasks:
      // count all timesheet records for that Task
////      case 'Tasks': these fields are no in the tasks module yet.
////        $q = "SELECT IFNULL(SUM(actual), 0) as actual_sum, IFNULL(SUM(billable), 0) as billable_sum FROM $tbl WHERE parent_id = '$id' AND deleted = 0 GROUP BY parent_id";
////        $result = $this->db->query($q);
////        $row = $this->db->fetchByAssoc($result);
////        if ($row & is_array($row)) {
////          $q = "UPDATE Tasks SET actual_effort = '".$row['actual_sum']."', billable_effort = '".$row['billable_sum']."' WHERE id = '$id'";
////          $this->db->query($q);
////        }
////      break;

      // timesheet is created for Case:
      // count all timesheet records for that Case
      case 'Cases':
        $q = "SELECT IFNULL(SUM(actual), 0) as actual_sum, IFNULL(SUM(billable), 0) as billable_sum FROM $tbl WHERE parent_id = '$id' AND deleted = 0 GROUP BY parent_id";
        $result = $this->db->query($q);
        $row = $this->db->fetchByAssoc($result);
        if ($row & is_array($row)) {
          $q = "UPDATE cases SET actual_effort = '".$row['actual_sum']."', billable_effort = '".$row['billable_sum']."' WHERE id = '$id'";
          $this->db->query($q);
        }

      break;

      // timesheet is created for ProjectTask
      // count all timesheet records for that ProjectTask plus
      // count all timesheet records for parent Project
      // (projects' timesheets + projects' project tasks timesheets)
      case 'ProjectTask':
        // count all timesheet related to ProjectTask
        $q = "SELECT IFNULL(SUM(actual), 0) as actual_sum, IFNULL(SUM(billable), 0) as billable_sum FROM $tbl WHERE parent_id = '$id' AND deleted = 0 GROUP BY parent_id";
        $result = $this->db->query($q);
        $row = $this->db->fetchByAssoc($result);
        if ($row && is_array($row)) {
          $q = "UPDATE project_task SET actual_effort = '".$row['actual_sum']."', billable_effort = '".$row['billable_sum']."' WHERE id = '$id'";
          $this->db->query($q);
        }

        // count all timesheet related to ProjectTasks' Project
        // check whether ProjectTask has parent Project
        $q = "SELECT p.id FROM project p INNER JOIN project_task pt ON p.id = pt.project_id WHERE pt.id = '$id'";
        if ($p_id = $this->db->getOne($q)) {
          // count totals from project tasks and project
          $q = "SELECT IFNULL(SUM(actual), 0) as actual_sum, IFNULL(SUM(billable), 0) as billable_sum FROM $tbl WHERE parent_id IN (SELECT pt.id FROM project_task pt WHERE pt.project_id = '$p_id' AND pt.deleted = 0 UNION SELECT '$p_id') AND deleted = 0";
          $result = $this->db->query($q);
          $row = $this->db->fetchByAssoc($result);
          if ($row && is_array($row)) {
             $q = "UPDATE project SET actual_effort = '".$row['actual_sum']."', billable_effort = '".$row['billable_sum']."' WHERE id = '$p_id'";
             $this->db->query($q);
          }
        }
      break;

      // timesheet is created for Project
      // count all totals from all timesheet records related to Project plus
      // count all totals from all timesheet records related to Projects' Project Tasks
      case 'Project':
        // count all timesheet records related to Project and project tasks
        $q = "SELECT IFNULL(SUM(actual), 0) as actual_sum, IFNULL(SUM(billable), 0) as billable_sum FROM $tbl WHERE parent_id IN (SELECT pt.id FROM project_task pt WHERE pt.project_id = '$id' AND pt.deleted = 0 UNION SELECT '$id') AND deleted = 0";
        $result = $this->db->query($q);
        $row = $this->db->fetchByAssoc($result);
        if ($row && is_array($row)) {
             $q = "UPDATE project SET actual_effort = '".$row['actual_sum']."', billable_effort = '".$row['billable_sum']."' WHERE id = '$id'";
             $this->db->query($q);
        }
      break;
    }
  }


  function completeTaskhasTimesheets($job, $cols, $timesheet){   
      foreach($cols as $date){
        $weekdates_array[] = date('Y-m-d',$date);
      }

      $has_timesheetforweek = false;
      foreach($timesheet[$job['id']] as $timesheets){
          foreach($weekdates_array as $date){
              if(array_key_exists($date, $timesheets)){
                  $has_timesheetforweek = true;
              }
          }
      }
      return $has_timesheetforweek;
  }


function buildWeeklyMatrix($aJobs, &$k, &$horizSum, &$vertSum, $xtpl, $cols, $timesheet, $depth = 0, $mod_strings){
    foreach ($aJobs as $job) {
        $display = true;
        $assignee = array();
        $assignee['rowid'] = $k;
        if($job['status'] == 'Completed'){                    
            if(!isset($job['children']) && !$this->completeTaskhasTimesheets($job, $cols, $timesheet)){
                continue;
            }

        }

      
            foreach ($cols as $loopDay => $difftime) {
                $assignee['id'] = $job['id'];
                $assignee['type'] = $job['type'];
                $assignee['date'] = date("Y-m-d", $difftime);
                $assignee['actual'] = "";
                $assignee['disabled'] = "";
    //            if($job['status'] == 'Completed'){
    //                $assignee['disabled'] = "disabled";
    //            }
                $assignee['timesheet_id'] = "";
                //EINTEL NM re#103
                /*
                 * When a project has 2 or more timesheets the total of all the timesheets are added and the field is disabled.
                 * This why you can see the total time you have spend on this project and not just the lastest time.
                 * You are not able to add more time to this project in the matrixs.
                 */
                if (!empty($timesheet[$assignee['id']])) {
                    $count = 0;
                    $actual = 0;
                    foreach($timesheet[$assignee['id']] as $timesheet_item){
                        if(!empty($timesheet_item[$assignee['date']]['actual'])){
                            $count++;
                            $actual = $actual + $timesheet_item[$assignee['date']]['actual'];
                        }
                    }
                    switch ($count) {
                        case 0:
                        break;
                        case 1:
                            foreach($timesheet[$assignee['id']] as $single_timesheet){
                               if(!empty($single_timesheet[$assignee['date']]['actual'])){
                                   $assignee['actual'] = $single_timesheet[$assignee['date']]['actual'];
                                   $assignee['timesheet_id'] = $single_timesheet[$assignee['date']]['id'];
                               }
                            }
                            $horizSum[$k] = 0 + @$horizSum[$k] + $assignee['actual'];
                            $vertSum[$loopDay] = 0 + @$vertSum[$loopDay] + $assignee['actual'];
                            $assignee['actual'] = format_number($assignee['actual']);
                            break;
                        default:
                            $assignee['actual'] = $actual;
                            $assignee['disabled'] = "disabled";
                            $horizSum[$k] = 0 + @$horizSum[$k] + $assignee['actual'];
                            $vertSum[$loopDay] = 0 + @$vertSum[$loopDay] + $assignee['actual'];
                            $assignee['actual'] = format_number($assignee['actual']);
                            break;
                    }
                }
                //END
                $assignee['colid'] = $loopDay;
                $xtpl->assign("MATRIX", $assignee);
                $xtpl->parse("main.matrix_row.matrix_subrow");
            }
        
        $assignee = $job;
        $assignee['type_lbl'] = $mod_strings['LBL_TYPE_'.strtoupper($assignee['type'])];
        $assignee['rowid'] = $k;
        $assignee['font'] = 'font-style:normal;';
        if($assignee['status'] == 'Completed'){
          $assignee['font'] = 'font-style:italic;';
        }else{
            //$assignee['status'] == $assignee['status'];
        }
        $xtpl->assign("MATRIX", $assignee);

        if (!empty($assignee['parent_id'])) {
           $assignee['depth'] = 10 * $depth;
           $assignee['depth'] = $assignee['depth'].'px';
           $xtpl->assign("MATRIX", $assignee);
           $xtpl->parse("main.matrix_row.matrix_parent");
        }

        //re#85 EINTEL assign the accounts field to the template
        if (!empty($assignee['accounts'])) {
          foreach($assignee['accounts'] as $account){
            $xtpl->assign("MATRIX_accounts", $account);
            if (!empty($account['account_id'])) {
              $xtpl->parse("main.matrix_row.matrix_account_project");
            }
          }
        }
        //END

        if (!empty($assignee['account_id'])) {
         $xtpl->assign("MATRIX_accounts", $account);
         $xtpl->parse("main.matrix_row.matrix_account");
        }
        $xtpl->parse("main.matrix_row");
        $k++;


        


        if(isset($job['children'])){
            $this->buildWeeklyMatrix($job['children'], $k, $horizSum, $vertSum, $xtpl, $cols, $timesheet, $depth + 1, $mod_strings);
        }
    }
}
//END
function buildDailyMatrix($aJobs, &$k, &$total, $xtpl, $timesheet, $depth = 0, $mod_strings, $dayUnixTime){
    foreach ($aJobs as $job) {
        $assignee = array();
        $assignee['rowid'] = $k;
        $assignee['id'] = $job['id'];
        $assignee['type'] = $job['type'];
        $assignee['date'] = gmdate("Y-m-d", $dayUnixTime);
        $assignee['actual'] = "";
        $assignee['disabled'] = "disabled";
        $assignee['timesheet_id'] = "";
        $assignee['description'] = "";
        $job['font'] = 'font-style:normal;';
        /** EINTEL re#103 NM
         * Changed to return all the timesheets for a project/ptask/case/task along with
         * their description and then displayed under each other.
         */
        if($job['status'] == 'Completed'){
            $job['font'] = 'font-style:italic;';
            $date[] = $dayUnixTime;
            if(!isset($job['children']) && !$this->completeTaskhasTimesheets($job, $date, $timesheet)){
                continue;
            }

        }




        if(isset($timesheet[$assignee['id']])){
            foreach($timesheet[$assignee['id']] as $timesheet_list){
                if (!empty($timesheet_list[$assignee['date']]['actual'])) {
                  $assignee['actual'] = $timesheet_list[$assignee['date']]['actual'];
                  $assignee['disabled'] = "";
                  $assignee['timesheet_id'] = $timesheet_list[$assignee['date']]['id'];
                  $assignee['description'] = $timesheet_list[$assignee['date']]['description'];

                  $total += $assignee['actual'];
                  // format number
                  $assignee['actual'] = format_number($assignee['actual']);
                }
                $xtpl->assign("MATRIX", $assignee);
                $xtpl->parse("main.matrix_row.matrix_subrow");
            }
        }
        //END
        if(!isset($timesheet[$assignee['id']]) || empty($timesheet[$assignee['id']])){
            $xtpl->assign("MATRIX", $assignee);
            $xtpl->parse("main.matrix_row.matrix_subrow");
        }
        $assignee = $job;
        $assignee['type_lbl'] = $mod_strings['LBL_TYPE_'.strtoupper($assignee['type'])];
        $xtpl->assign("MATRIX", $assignee);
        if (!empty($assignee['parent_id'])) {
            $assignee['depth'] = 10 * $depth;
            $assignee['depth'] = $assignee['depth'].'px';
            
            $xtpl->assign("MATRIX", $assignee);
            $xtpl->parse("main.matrix_row.matrix_parent");
        }else{
            $xtpl->assign("MATRIX", $assignee);
        }

        /** re#85 EINTEL
         * Assigns the new accounts field to the template to appear after the parents name like so:
         * parent_type : name (acount_name)(acount_name)
         */
        if (!empty($assignee['accounts'])) {
          foreach($assignee['accounts'] as $account){
            $xtpl->assign("MATRIX_accounts", $account);
            if (!empty($account['account_id'])) {
              $xtpl->parse("main.matrix_row.matrix_account_project");
            }
          }
        }
        //END

        if (!empty($assignee['account_id'])) {
         $xtpl->assign("MATRIX_accounts");
         $xtpl->parse("main.matrix_row.matrix_account");
        }


        $xtpl->parse("main.matrix_row");
        $k++;
        if(isset($job['children'])){
            $this->buildDailyMatrix($job['children'], $k, $total, $xtpl, $timesheet, $depth + 1, $mod_strings, $dayUnixTime);
        }
    }
}
//END
}
?>

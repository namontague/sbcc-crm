<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class TimesheetTimer extends SugarBean {
  var $id;
  var $assigned_user_id;
  var $parent_type;
  var $parent_id;
  var $parent_name;
  var $started;

  var $table_name = 'timesheet_timer';
	var $module_dir = 'Timesheet';
	var $object_name ='TimesheetTimer';
	var $disable_custom_fields = true;

 
  // get Timer object by assigned user
  function loadTimerByUser() {
    global $current_user;
    $this->retrieveBy(array('assigned_user_id' => $current_user->id));
  }

  // remove record from db
  function removeMe() {
    $q = "DELETE FROM {$this->table_name} WHERE id = '{$this->id}'";
    $this->db->query($q);
  }

  function getCurrentTimerLink($do) {
    $link = "index.php?module=Timesheet&action=Timer&do=$do";
    if (isset($this->parent_type)) {
      $link .= "&return_module=".$this->parent_type;
    }
    if (isset($_REQUEST['return_action'])) {
      $link .= "&return_action=".$_REQUEST['return_action'];
    }
    else {
      $link .= "&return_action=DetailView";
    }
    if (isset($this->parent_id)) {
      $link .= "&return_id=".$this->parent_id;
    }
    return $link;
  }

  function resolveInterval($interval) {
    preg_match("/([0-9,\.]+)([a-zA-Z]{0,})$/is", $interval, $m);
    if (!isset($m[2]))
      $m[2] = 'sec';

    switch (@$m[2]) {
      case 'min':
        $interval = $m[1] * 60;
      break;

      case 'hr':
        $interval = $m[1] * 3600;
      break;

      /*case 'day':
        $interval = $m[1] * 24 * 3600;
      break;

      case 'wk':
        $interval = $m[1] * 7 * 24 * 3600;
      break;

      case 'mo':
        $interval = $m[1] * 28 * 24 * 3600;
      break;*/
    }
    return array('value' => floatval($interval), 'number' => $m[1], 'literal' => $m[2]);
  }

  static function user_db_today() {
    global $timedate;
     // First get gmt time
    $gmt_time = gmdate($timedate->get_db_date_time_format(), time());
    // Convert to user timezone, the formate as db date (drop time part in format)
    return $timedate->handle_offset($gmt_time, $timedate->dbDayFormat, true);
  }

    
}
?>
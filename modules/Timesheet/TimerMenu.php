<?php
global $mod_strings, $app_strings, $current_user, $current_language;
$timesheet_strings = return_module_language($current_language, 'Timesheet');

require_once 'modules/Timesheet/TimesheetTimer.php';
$timer = new TimesheetTimer;
if (!empty($_REQUEST['record'])) {
  $fields = array();
  if (isset($_REQUEST['module'])) {
    $fields['parent_type'] = $_REQUEST['module'];
  }
  $fields['assigned_user_id'] = $current_user->id;
  $fields['parent_id'] = $_REQUEST['record'];
  $fields['date_entered'] = TimesheetTimer::user_db_today();
  $timer->retrieveBy($fields);

  if (!empty($timer->id)) {
    // timer already started for this object, show stop link
    $module_menu[] = Array("index.php?module=Timesheet&action=Timer&do=stop&return_module=".$_REQUEST['module']."&return_action=".@$_REQUEST['action']."&return_id=".$_REQUEST['record'], $timesheet_strings['LBL_STOP_TIMER'],"StopTimer");
  }
  else {
    // timer not started for that object 
    $module_menu[] = Array("index.php?module=Timesheet&action=Timer&do=start&return_module=".$_REQUEST['module']."&return_action=".@$_REQUEST['action']."&return_id=".$_REQUEST['record'], $timesheet_strings['LBL_START_TIMER'],"StartTimer");    
  }  
}

if (empty($timer->id)) {
  // find any timer for current user and show stop link
  $fields = array();
  $fields['assigned_user_id'] = $current_user->id;
  $fields['date_entered'] = TimesheetTimer::user_db_today();
  $timer->retrieveBy($fields);
  if (!empty($timer->id)) {
    $module_menu[] = Array("index.php?module=Timesheet&action=Timer&do=stop&return_module=".$timer->parent_type."&return_action=DetailView&return_id=".$timer->parent_id, $timesheet_strings['LBL_STOP_TIMER'].' "'.$timer->parent_name.'"',"StopTimer");
  }
}
?>

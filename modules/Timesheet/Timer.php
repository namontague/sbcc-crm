<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
require_once('modules/Timesheet/Timesheet.php');
require_once('modules/Timesheet/TimesheetTimer.php');
require_once('include/formbase.php');

global $current_user, $beanFiles, $beanList, $timedate, $locale;

$do = null;
switch (@$_REQUEST['do']) {
  case 'start':
  case 'stop':
    $do = $_REQUEST['do'];
}
if (is_null($do)) {
  handleRedirect();
}

// check whether current user is assigned to project task
$bean_name = isset($beanList[$_REQUEST['return_module']]) ? $beanList[$_REQUEST['return_module']] : $_REQUEST['return_module'];
require_once($beanFiles[$bean_name]);
$focus = new $bean_name();
$focus->retrieve($_REQUEST['return_id']);

if (!empty($focus->assigned_user_id) && $current_user->id != $focus->assigned_user_id) {
  // current user is not assigned to this project task
  echo "You are not assigned to ".$_REQUEST['return_module']." \"<a href=\"index.php?module=".$_REQUEST['return_module']."&action=DetailView&record=".$focus->id."\">{$focus->name}</a>\".";
}
else {
  $timesheet = new Timesheet;
  $timer = new TimesheetTimer;

  $today=TimesheetTimer::user_db_today();

  // check whether timer has already started for that object
  // load timer object, initializing its properties
  $fields_array = array(
    'parent_type' => $_REQUEST['return_module'],
    'parent_id' => $_REQUEST['return_id'],
    'assigned_user_id' => $current_user->id,
    'date_entered' => $today,
  );
  $timer->retrieveBy($fields_array);
  if (!empty($timer->id)) {
    // timer has been already started for requested object by current user
    if ($do == 'start') {
      echo "Timer has been already started for ".$_REQUEST['return_module']." \"<a href=\"index.php?module=".$_REQUEST['return_module']."&action=DetailView&record=".$focus->id."\">{$focus->name}</a>\". Would you like to <a href=\"".$timer->getCurrentTimerLink('stop')."\">stop</a> it?";
    }
    else if ($do == 'stop') {
      // stopping timer

      $deltaTime = microtime(true) - $timer->started;
      // count effort
      $effort = round(ceil($deltaTime) / 3600, 2);

      if ($effort > 0) {
        // save to timesheet record
        $date_booked = $today;
        $fields_array = array(
          'parent_type' => $timer->parent_type,
          'parent_id' => $timer->parent_id,
          'date_booked' => $date_booked,
          'assigned_user_id' => $timer->assigned_user_id
        );
        if( $timesheet->retrieveBy($fields_array) ) {
            if (isset($timesheet->actual) && $timesheet->actual != '') {
              $timesheet->actual += $effort;
            }
            else {
              $timesheet->actual = $effort;
            }
            if (isset($timesheet->billable) && !empty($timesheet->billable)) {
              $timesheet->billable += $effort;
              $timesheet->billable = format_number($timesheet->billable);
            }
        } else {
            $timesheet->parent_type = $timer->parent_type;
            $timesheet->parent_id = $timer->parent_id;
            $timesheet->assigned_user_id = $timer->assigned_user_id;
            $timesheet->actual = $effort;
        }
        $timesheet->actual = format_number($timesheet->actual);
        $timesheet->save();
      }

      // now remove timer
      $timer->removeMe();

      echo "Timer for ".$_REQUEST['return_module']." \"<a href=\"index.php?module=".$_REQUEST['return_module']."&action=DetailView&record=".$focus->id."\">{$focus->name}</a>\" stopped. Calculated effort: ".format_number($effort)."h. Would you like to re-<a href=\"".$timer->getCurrentTimerLink('start')."\">start</a> timer?";
    }
  }
  else {
    // timer has not been started for requested object by current user
    if ($do == 'start') {
      // user tries to start timer

      // check whether another timer has been started for current user
      $timer->loadTimerByUser();
      if (!empty($timer->id)) {
        // another timer exists for current user

        // retrieve information about object
        $timer_bean_name = isset($beanList[$timer->parent_type]) ? $beanList[$timer->parent_type] : $timer->parent_type;
        require_once($beanFiles[$timer_bean_name]);
        $timer_focus = new $timer_bean_name();
        $timer_focus->retrieve($timer->parent_id);

        echo "You have already started timer for {$timer->parent_type} \"<a href=\"index.php?module={$timer->parent_type}&action=DetailView&record={$timer->parent_id}\">{$timer_focus->name}</a>\". Would you like to <a href=\"".$timer->getCurrentTimerLink('stop')."\">stop</a> it?";
      }
      else {
        // fire timer
        $timer->started = microtime(true);
        $timer->fromArray($fields_array);
        $timer->save();
        echo "Timer has been successfully started for ".$_REQUEST['return_module']." \"<a href=\"index.php?module=".$_REQUEST['return_module']."&action=DetailView&record=".$focus->id."\">{$focus->name}</a>\".";
        //handleRedirect();
      }
    }
    else {
      echo "You haven't started timer for ".$_REQUEST['return_module']." \"<a href=\"index.php?module=".$_REQUEST['return_module']."&action=DetailView&record=".$focus->id."\">{$focus->name}</a>\".";
    }
  }
}

?>
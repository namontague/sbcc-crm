<?php
require_once('data/SugarBean.php');
require_once('modules/Timesheet/TimesheetTimer.php');

class TimesheetLogicHook {

   // called on after_ui_frame
  function closeOpenTimers($event = null, $arguments =null) {
    global $db, $current_user, $timedate;
    if (empty($current_user->user_name))
      return;

    $localDB =DBManagerFactory::getInstance();

    $today=TimesheetTimer::user_db_today();

    // find all timers where date_entered != today grouped by user by date entered
    $q = "SELECT *, DATE_FORMAT(date_entered, '%Y-%m-%d') date_formatted FROM timesheet_timer WHERE deleted = 0 AND date_entered != '".$today."'";
    $result = $localDB->query($q);

    require_once 'modules/Timesheet/Timesheet.php';
    $timesheet = new Timesheet;

    $timers = array();
    $effort = array();
    $removeClosedTimers = false;
    while($row = $localDB->fetchByAssoc($result)) {
      $removeClosedTimers = true;
      $timers[$row['assigned_user_id'].$row['date_formatted']] = $row;
      // find timesheets where timer_date_entered = timesheet_date_booked
      $q = "SELECT id, actual, date_booked, parent_type, parent_id FROM timesheet WHERE deleted = 0 AND assigned_user_id = '".$row['assigned_user_id']."' AND date_booked = CAST('".$row['date_formatted']."' AS DATE)";

      // go through all timesheet records
      $res = $db->query($q);
      if ($db->getRowCount($res)) {
        while ($r = $db->fetchByAssoc($res)) {
          if (!isset($effort[$row['assigned_user_id'].$row['date_formatted']])) {
            $effort[$row['assigned_user_id'].$row['date_formatted']] = $r['actual'];
          }
          else {
            $effort[$row['assigned_user_id'].$row['date_formatted']] += $r['actual'];
          }
          // this will be used to know which timesheet record should be updated
          $timesheets[$row['assigned_user_id'].$r['parent_type'].$r['parent_id'].$row['date_formatted']] = $r['id'];
        }
      }
    }
    foreach ($timers as $key => $timerRow) {
      if (isset($effort[$key])) {
        if ($effort[$key] < 8) {
          // for this date the total effort is less than 8 hr, while timer was not stopped
          // therefore last fired task should be adjusted in timesheet

          $timesheet_id = $timesheets[$timerRow['assigned_user_id'].$timerRow['parent_type'].$timerRow['parent_id'].$timerRow['date_formatted']];
          $timesheet->retrieve($timesheet_id);
          $timesheet->actual = $timesheet->actual + (8 - $effort[$key]);
          $timesheet->save();
        }
      }
      else {
        // timer exists but not timesheet record for that user in that date
        // create timesheet record
        $timesheet = new Timesheet;
        $timesheet->parent_type = $timerRow['parent_type'];
        $timesheet->parent_id = $timerRow['parent_id'];
        $timesheet->date_booked = $timedate->to_display_date($timerRow['date_formatted'], false);
        $timesheet->assigned_user_id = $timerRow['assigned_user_id'];
        $timesheet->actual = 8;
        $timesheet->save();
      }
    }

    // now remove timers where date_entered != today
    if ($removeClosedTimers) {
      $q = "DELETE FROM timesheet_timer WHERE date_entered != '".$today."'";
      $localDB->query($q);
    }
  }

  function reminder($event = null, $arguments = null) {
    global $current_user, $beanFiles, $beanList;
    if (empty($current_user->user_name) ||
        !empty($_REQUEST['to_pdf']) ||
        !empty($_REQUEST['sugar_body_only']))
      return;

    if (is_null($current_user->getPreference('timerReminder'))) {
      $current_user->setPreference('timerReminder', 'on');
    }
    if ($current_user->getPreference('timerReminder') == 'on') {
      // if user switched on reminder

      $today=TimesheetTimer::user_db_today();
      $q = "SELECT * FROM timesheet_timer WHERE deleted = 0 AND assigned_user_id = '{$current_user->id}' AND date_entered = '".$today."'";
      $result = $GLOBALS['db']->query($q);
      if ($GLOBALS['db']->getRowCount($result)) {
        $timerRow = $GLOBALS['db']->fetchByAssoc($result);
        if (is_null($current_user->getPreference('timerReminder_interval'))) {
          $interval = '30min';
          $current_user->setPreference('timerReminder_interval', $interval);
        }
        $interval = TimesheetTimer::resolveInterval($current_user->getPreference('timerReminder_interval'));
        $interval = $interval['value'];

        // get information about timer object
        $bean_name = isset($beanList[$timerRow['parent_type']]) ? $beanList[$timerRow['parent_type']] : $timerRow['parent_type'];
        require_once($beanFiles[$bean_name]);
        $focus = new $bean_name();
        $focus->retrieve($timerRow['parent_id']);
        echo '
        <script language="javascript">
        <!--
        var timerInterval = '.$interval.';
        var timerConfirmStr = "Recently, you have started timer for '.$timerRow['parent_type'].' \"'.$focus->name.'\".\nWould you like to stop it?";
        var timerStopUrl = "index.php?module=Timesheet&action=Timer&do=stop&return_id='.$focus->id.'&return_module='.$timerRow['parent_type'].'&return_action=DetailView";
        var timerReminderFired = false;

        function timerReminder() {
          if (!timerReminderFired) {
            timerReminderFired = true;
            if (confirm(timerConfirmStr)) {
              window.location.href = timerStopUrl;
            } else {
              timerReminderFired = false;
            }
          }
          return true;
        }
        //window.setTimeout("timerReminder()", '.($interval - (microtime(true) - $timerRow['started'])).' * 1000);
        window.setInterval("timerReminder()", timerInterval * 1000);
        //-->
        </script>';
      }
    }
  }
}
?>

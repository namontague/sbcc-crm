<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

require_once('XTemplate/xtpl.php');
require_once('data/Tracker.php');
require_once('modules/Timesheet/Timesheet.php');
require_once('modules/Timesheet/Forms.php');

if(module_enabled('Timesheet')) {
global $timedate;
global $current_user;
global $app_strings;
global $app_list_strings;
global $mod_strings;
global $sugar_version, $sugar_config;

$focus = new Timesheet();


echo "\n<p>\n";
echo get_module_title($mod_strings['LBL_MODULE_NAME'], $mod_strings['LBL_MODULE_NAME'].": ".$mod_strings['LBL_FILL_MATRIX'], true);
echo "\n</p>\n";
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once('include/utils/layout_utils.php');

$GLOBALS['log']->info("Timesheet Edit view");

$xtpl=new XTemplate('modules/Timesheet/EditMatrix.html');
$xtpl->assign("MOD", $mod_strings);
$xtpl->assign("APP", $app_strings);

// Set the number grouping and decimal separators
$seps = get_number_seperators();
$dec_sep = $seps[1];
$num_grp_sep = $seps[0];
$xtpl->assign('NUM_GRP_SEP', $num_grp_sep);
$xtpl->assign('DEC_SEP', $dec_sep);

$xtpl->assign("CALENDAR_LANG", "en");


if (isset($_REQUEST['return_module'])) $xtpl->assign("RETURN_MODULE", $_REQUEST['return_module']);
if (isset($_REQUEST['return_action'])) $xtpl->assign("RETURN_ACTION", $_REQUEST['return_action']);
if (isset($_REQUEST['return_id'])) $xtpl->assign("RETURN_ID", $_REQUEST['return_id']);
// handle Create $module then Cancel
if (empty($_REQUEST['return_id'])) {
	$xtpl->assign("RETURN_ACTION", 'index');
}
$xtpl->assign("THEME", $theme);
$xtpl->assign("IMAGE_PATH", $image_path);$xtpl->assign("PRINT_URL", "index.php?".$GLOBALS['request_string']);
$xtpl->assign("ID", $focus->id);

$aDays = array(
$mod_strings['LBL_SUNDAY'],
$mod_strings['LBL_MONDAY'],
$mod_strings['LBL_TUESDAY'],
$mod_strings['LBL_WEDNESDAY'],
$mod_strings['LBL_THURSDAY'],
$mod_strings['LBL_FRIDAY'],
$mod_strings['LBL_SATURDAY']
);

// consider user offset
$now = time() + ($timedate->adjustmentForUserTimeZone($timedate->getUserTimeZone()) * 60);//mktime(1,1,1,12,29,2008);

/** new
 * 1. Entry point is current date
 * 2. Determine day of week for current date
 * 3. Determine year for current date
 * 4. Determine end date of previous week and start date of next week
 * 5. Set flag isCurrentWeek to determine whether it is current week
 */

// determine the date
$curDate = date('Y-n-j', $now);
if (!isset($_REQUEST['date'])) {
  $defaultDate = $curDate; // YYYYmd
}
else {
  $defaultDate = $_REQUEST['date'];
}
// determine date info
$dateInfo = explode('-', $defaultDate);

$defaultDay = $dateInfo[2];
$defaultMonth = $dateInfo[1];
$defaultYear = $dateInfo[0];
$defaultTimestamp = strtotime($defaultDate);
$defaultWeekDay = date('w', $defaultTimestamp);

// determine days number to next monday
$daysToNextMo = ($defaultWeekDay == 0) ? 1 : (8 - $defaultWeekDay);

// determine days number to prev sunday
$daysToPrevSu = ($defaultWeekDay == 0) ? 7 : $defaultWeekDay;

// determine start date of next week
// next monday day is out of days in defaultMonth
if (($defaultDay + $daysToNextMo) > date('t', $defaultTimestamp)) {
  $nextDay = $daysToNextMo - (date('t', $defaultTimestamp) - $defaultDay);
  if ($defaultMonth == 12) {
    $nextMonth = 1;
    $nextYear = $defaultYear + 1;
  }
  else {
    $nextMonth = $defaultMonth + 1;
    $nextYear = $defaultYear;
  }
}
else {
  $nextDay = $defaultDay + $daysToNextMo;
  $nextMonth = $defaultMonth;
  $nextYear = $defaultYear;
}
$nextDate = $nextYear.'-'.$nextMonth.'-'.$nextDay;

// determine end date of prev week
// prev monday day is negative
if (($defaultDay - $daysToPrevSu) < 0) {
  if ($defaultMonth == 1) {
    $prevMonth = 12;
    $prevYear = $defaultYear - 1;
  }
  else {
    $prevMonth = $defaultMonth - 1;
    $prevYear = $defaultYear;
  }
  $prevDay = date('t', strtotime($prevYear.'-'.$prevMonth.'-1')) + ($defaultDay - $daysToPrevSu);
}
else {
  $prevYear = $defaultYear;
  $prevMonth = $defaultMonth;
  $prevDay = $defaultDay - $daysToPrevSu;
}
$prevDate = $prevYear.'-'.$prevMonth.'-'.$prevDay;

// determine week number
$weekNumber = intval(date('W', $defaultTimestamp));
if ($weekNumber == 0) {
  $weekNumber = 1;
}
$xtpl->assign("WEEK", $weekNumber);

// timestamp for the end of chosen week in chosen year
$weektime = strtotime($nextDate) - 24 * 3600;

$xtpl->assign("NEXTDATE", $nextDate);
$xtpl->assign("PREVDATE", $prevDate);

// Set numeric representation of the day of the end of the chosen week
$idxDay = date("w", $weektime);

$idxCurDay = date("w", $now);


if ($idxDay == 0) {
  $date[0] = $weektime - 6 * 24 * 3600;
  $date[1] = $weektime;
  $idxDay = 7;
}
else {
  $date[0] = $weektime - ($idxDay - 1) * 24 * 3600;
  $date[1] = $weektime + (7 - $idxDay) * 24 * 3600;
}

/*EINTEL re#103 NM
 * Call new function to return all the timesheets for project/ptask/case/task and not just the lastest ones.
 */
 $timesheet = $focus->getUserTimesheetsMatrix($current_user->id, $date);
//END
$cols = array();
for ($i = 1; $i < 8; $i++) {
  if ($i < ($idxDay + 1)) {
    $difftime = $weektime - ($idxDay - $i) * 24 * 3600;
  }
  else if ($i > $idxDay) {
    $difftime = $weektime + ($i - $idxDay) * 24 * 3600;
  }
  $cols[] = $difftime;

  $idxLoopDay = date("w", $difftime);
  if ($idxLoopDay == 0) {
    $idxLoopDay = 7;
  }
  $style = "";
  if (date('Y-n-j', $difftime) == $curDate) {
    $style = "font-weight: bold";
  }

  $assignee = array();
  $assignee['day_of_week'] = "<span style='$style'>".$aDays[($idxLoopDay == 7 ? 0 : $idxLoopDay)]."</span>";
  $assignee['day'] = "<span style='$style'>".date("d.m", $difftime)."</span>";
  $xtpl->assign("MATRIX", $assignee);
  $xtpl->parse("main.matrix_header");
}

$jobs = $focus->getAssignedParents($current_user->id, date("Y-m-d", $cols[0]), date("Y-m-d", $cols[6]));

//re#100 EINTEL fetch related accounts to the current projects we have
$jobs = $focus->fetch_related_accounts_ofprojects($jobs);
//$aJobs = sortJobs($jobs);

$horizSum = $vertSum = array();
$k = 0; // row
//EINTEL Re:96 adding Tasks to matrix, NM
$focus->buildWeeklyMatrix($focus->build_Parents($jobs), $k, $horizSum, $vertSum, $xtpl, $cols, $timesheet, $depth = 0, $mod_strings);
/**
function buildMatrix($aJobs, &$k, &$horizSum, &$vertSum, $xtpl, $cols, $timesheet, $depth = 0, $mod_strings){
    foreach ($aJobs as $job) {
        $assignee = array();
        $assignee['rowid'] = $k;
        foreach ($cols as $loopDay => $difftime) {
            $assignee['id'] = $job['id'];
            $assignee['type'] = $job['type'];
            $assignee['date'] = date("Y-m-d", $difftime);
            $assignee['actual'] = "";
            $assignee['disabled'] = "";
            $assignee['timesheet_id'] = "";
            //EINTEL NM re#103
            /*
             * When a project has 2 or more timesheets the total of all the timesheets are added and the field is disabled.
             * This why you can see the total time you have spend on this project and not just the lastest time.
             * You are not able to add more time to this project in the matrixs.
             
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
            buildMatrix($job['children'], $k, $horizSum, $vertSum, $xtpl, $cols, $timesheet, $depth + 1, $mod_strings);
        }
    }   
}
*///END

$total = 0;
foreach ($horizSum as $idx => $val) {
  $xtpl->assign("ROW_IDX", $idx);
  $xtpl->assign("ROW_VAL", format_number($val));
  $xtpl->parse("main.total_row");
  $total += $val;
}
$xtpl->assign("TOTAL", format_number($total));

foreach ($vertSum as $idx => $val) {
  $xtpl->assign("COL_IDX", $idx);
  $xtpl->assign("COL_VAL", format_number($val));
  $xtpl->parse("main.total_col");
}

if(is_admin($current_user) && $_REQUEST['module'] != 'DynamicLayout' && !empty($_SESSION['editinplace'])){
	$record = '';
	if(!empty($_REQUEST['record'])){
		$record = 	$_REQUEST['record'];
	}
	$xtpl->assign("ADMIN_EDIT","<a href='index.php?action=index&module=DynamicLayout&from_action=".$_REQUEST['action'] ."&from_module=".$_REQUEST['module'] ."&record=".$record. "'>".get_image($image_path."EditLayout","border='0' alt='Edit Layout' align='bottom'")."</a>");
}
//Add Custom Fields
require_once('modules/DynamicFields/templates/Files/EditView.php');

$xtpl->parse("main");
$xtpl->out("main");
}else{
    echo '<script>function set_focus(){}</script><p class="error">' . translate('LBL_NO_ACCESS', 'ACL') . '</p>';
}
?>

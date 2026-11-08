<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
global $mod_strings, $app_strings, $current_user;

if(ACLController::checkAccess('Timesheet', 'list', true)) 
  $module_menu[] = array("index.php?module=Timesheet&action=ListView&return_module=Timesheet&return_action=DetailView", $mod_strings['LNK_TIMESHEET_LIST'],"TimesheetList");

if(ACLController::checkAccess('Timesheet', 'edit', true)) 
  $module_menu[]=Array("index.php?module=Timesheet&action=EditView&return_module=Timesheet&return_action=DetailView", $mod_strings['LNK_NEW_TIMESHEET'],"CreateTimesheet");

if(ACLController::checkAccess('Timesheet', 'edit', true)) 
  $module_menu[]=Array("index.php?module=Timesheet&action=EditMatrix&return_module=Timesheet&return_action=ListView", $mod_strings['LNK_NEW_MATRIX_TIMESHEET'],"CreateMatrixTimesheet");

if(ACLController::checkAccess('Timesheet', 'edit', true)) 
  $module_menu[]=Array("index.php?module=Timesheet&action=EditDailyMatrix&return_module=Timesheet&return_action=ListView", $mod_strings['LNK_NEW_DAILY_MATRIX_TIMESHEET'],"CreateDailyMatrixTimesheet");

if(ACLController::checkAccess('Timesheet', 'edit', true)) 
  $module_menu[]=Array("index.php?module=Timesheet&action=Report&return_module=Timesheet&return_action=ListView", $mod_strings['LNK_REPORT'],"ReportTimesheet");

if(ACLController::checkAccess('Timesheet', 'edit', true)) 
  $module_menu[]=Array("index.php?module=Timesheet&action=TimerConfig&return_module=Timesheet&return_action=ListView", $mod_strings['LNK_TIMER_CONFIG'],"TimerConfig");

?>

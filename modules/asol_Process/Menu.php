<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

global $mod_strings, $app_strings;
//$module_menu[]=Array("index.php?module=Reports&action=index", "Reports","Reports");
//$module_menu[]=Array("index.php?module=Reports&action=EditView", "Create","Reports");
//$module_menu[]=Array("index.php?module=Administration&action=asolConfig&return_module=Reports&return_action=index", "Configuration","Reports");

$module_menu[]=Array("index.php?module=asol_Process&action=index", $mod_strings["LBL_ASOL_VIEW_ASOL_PROCESSES"],"asol_Process");
$module_menu[]=Array("index.php?module=asol_Process&action=EditView", $mod_strings["LBL_ASOL_CREATE_ASOL_PROCESS"],"Createasol_Process");
$module_menu[]=Array("index.php?module=asol_Events&action=index", $mod_strings["LBL_ASOL_VIEW_ASOL_EVENTS"],"asol_Events");
$module_menu[]=Array("index.php?module=asol_Events&action=EditView", $mod_strings["LBL_ASOL_CREATE_ASOL_EVENT"],"Createasol_Events");
$module_menu[]=Array("index.php?module=asol_Activity&action=index", $mod_strings["LBL_ASOL_VIEW_ASOL_ACTIVITIES"],"asol_Activity");
$module_menu[]=Array("index.php?module=asol_Activity&action=EditView", $mod_strings["LBL_ASOL_CREATE_ASOL_ACTIVITY"],"Createasol_Activity");
$module_menu[]=Array("index.php?module=asol_Task&action=index", $mod_strings["LBL_ASOL_VIEW_ASOL_TASKS"],"asol_Task");
$module_menu[]=Array("index.php?module=asol_Task&action=EditView", $mod_strings["LBL_ASOL_CREATE_ASOL_TASK"],"Createasol_Task");

$module_menu[]=Array("index.php?module=asol_WorkingNodes&action=index", $mod_strings["LBL_ASOL_ALINEASOL_WFM_MONITOR"],"asol_WorkingNodes");

$module_menu[]=Array("index.php?module=asol_Process&action=import_button", $mod_strings["LBL_ASOL_ASOL_IMPORT_ASOL_PROCESSES"],"asol_Process");

//$module_menu[]=Array("index.php?module=asol_WorkingNodes&action=index","mod_strings".print_r($mod_strings,true),"asol_WorkingNodes")

//$module_menu[]=Array("index.php?module=Administration&action=asolConfig&return_module=Reports&return_action=index", "Configuration","Reports");














//global $mod_strings, $app_strings, $sugar_config;
//
//		
//if(ACLController::checkAccess('Accounts', 'edit', true))$module_menu[]=Array("index.php?module=Accounts&action=EditView&return_module=Accounts&return_action=index", $mod_strings['LNK_NEW_ACCOUNT'],"CreateAccounts", 'Accounts');
//
//if(ACLController::checkAccess('Accounts', 'list', true))$module_menu[]=Array("index.php?module=Accounts&action=index&return_module=Accounts&return_action=DetailView", $mod_strings['LNK_ACCOUNT_LIST'],"Accounts", 'Accounts');
//if(ACLController::checkAccess('Accounts', 'import', true))$module_menu[]=Array("index.php?module=Import&action=Step1&import_module=Accounts&return_module=Accounts&return_action=index", $mod_strings['LNK_IMPORT_ACCOUNTS'],"Import", 'Accounts');

?>

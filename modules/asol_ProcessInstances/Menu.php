<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

global $mod_strings,$app_strings;
//$module_menu[]=Array("index.php?module=Reports&action=index", "Reports","Reports");
//$module_menu[]=Array("index.php?module=Reports&action=EditView", "Create","Reports");
//$module_menu[]=Array("index.php?module=Administration&action=asolConfig&return_module=Reports&return_action=index", "Configuration","Reports");

$module_menu[]=Array("index.php?module=asol_ProcessInstances&action=index", $mod_strings["LBL_ASOL_VIEW_ASOL_PROCESSINSTANCES"],"asol_ProcessInstances");
//$module_menu[]=Array("index.php?module=asol_ProcessInstances&action=EditView", "Create asol_ProcessInstances","asol_ProcessInstances");
$module_menu[]=Array("index.php?module=asol_WorkingNodes&action=index", $mod_strings["LBL_ASOL_VIEW_ASOL_WORKINGNODES"],"asol_WorkingNodes");
//$module_menu[]=Array("index.php?module=asol_WorkingNodes&action=EditView", "Create asol_WorkingNodes","asol_WorkingNodes");
$module_menu[]=Array("index.php?module=asol_OnHold&action=index", $mod_strings["LBL_ASOL_VIEW_ASOL_ONHOLD"],"asol_OnHold");
//$module_menu[]=Array("index.php?module=asol_OnHold&action=EditView", "Create asol_OnHold","asol_OnHold");

$module_menu[]=Array("index.php?module=asol_Process&action=index", $mod_strings["LBL_ASOL_ALINEASOL_WFM"],"asol_Process");

//$module_menu[]=Array("index.php?module=Administration&action=asolConfig&return_module=Reports&return_action=index", "Configuration","Reports");














//global $mod_strings, $app_strings, $sugar_config;
//
//		
//if(ACLController::checkAccess('Accounts', 'edit', true))$module_menu[]=Array("index.php?module=Accounts&action=EditView&return_module=Accounts&return_action=index", $mod_strings['LNK_NEW_ACCOUNT'],"CreateAccounts", 'Accounts');
//
//if(ACLController::checkAccess('Accounts', 'list', true))$module_menu[]=Array("index.php?module=Accounts&action=index&return_module=Accounts&return_action=DetailView", $mod_strings['LNK_ACCOUNT_LIST'],"Accounts", 'Accounts');
//if(ACLController::checkAccess('Accounts', 'import', true))$module_menu[]=Array("index.php?module=Import&action=Step1&import_module=Accounts&return_module=Accounts&return_action=index", $mod_strings['LNK_IMPORT_ACCOUNTS'],"Import", 'Accounts');

?>

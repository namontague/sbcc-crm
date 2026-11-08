<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

global $mod_strings, $app_strings, $sugar_config;

$module_name = 'SMS_Replies';

if(ACLController::checkAccess($module_name, 'list', true)) $module_menu[]=Array("index.php?module={$module_name}&action=index", $mod_strings['LNK_LIST'], "{$module_name}", "{$module_name}");

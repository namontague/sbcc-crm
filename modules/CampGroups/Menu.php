<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/* * *******************************************************************************
 * (C) 2013 Enterprise Intelligence Systems Pty Ltd
 * No part of this file should be used without the express permission
 * of the authors.
 *
 * Contact: info@eintel.com.au for more information.
 *
 *
 * ****************************************************************************** */

global $mod_strings, $app_strings, $sugar_config;
 
$module_name = 'CampGroups';
$module_singular_name = 'CampGroup';

if(ACLController::checkAccess($module_name, 'edit', true))$module_menu[]=Array("index.php?module={$module_name}&action=EditView&return_module={$module_name}&return_action=DetailView", $mod_strings['LNK_NEW_RECORD'],"Create{$module_singular_name}", "{$module_name}");
if(ACLController::checkAccess($module_name, 'list', true))$module_menu[]=Array("index.php?module={$module_name}&action=index&return_module={$module_name}&return_action=DetailView", $mod_strings['LNK_LIST'],"{$module_name}", "{$module_name}");
if(ACLController::checkAccess($module_name, 'import', true))$module_menu[]=Array("index.php?module=Import&action=Step1&import_module={$module_name}&return_module={$module_name}&return_action=index", $app_strings['LBL_IMPORT'],"Import", "{$module_name}");
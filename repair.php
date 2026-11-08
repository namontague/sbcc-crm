<?php

/*
 * Copyright 2013
 * Jeff Bickart
 * @bickart
 * jeff @ neposystems.com
 */

if (!defined('sugarEntry')) define('sugarEntry', true);

require_once('include/entryPoint.php');
require_once('modules/Administration/QuickRepairAndRebuild.php');

$sapi_type = php_sapi_name();
if (substr($sapi_type, 0, 3) != 'cli') {
    sugar_die("repair.php is CLI only.");
}

global $sugar_config;
if (empty($current_language)) {
    $current_language = $sugar_config['default_language'];
}

$app_list_strings = return_app_list_strings_language($current_language);
$app_strings = return_application_language($current_language);

global $current_user;
$current_user = new User();
$current_user->getSystemUser();

$GLOBALS['log']->fatal('--------------------------------------------> at repair.php <--------------------------------------------');
$repair = new RepairAndClear();
$repair->repairAndClearAll(array('clearAll'), array(translate('LBL_ALL_MODULES')), true, false);

if (class_exists('DBManagerFactory')) {
    $db = DBManagerFactory::getInstance();

    $GLOBALS['log']->fatal('Updating database version');
    $db->query("update config set value='{$sugar_db_version}' where name='sugar_version'");
}

// Repair config
$GLOBALS['log']->fatal('Check config file');
if( $sugar_config['sugar_version'] != $sugar_version ){

    // retrieve configuration from file so that contents of config_override.php
    // is not merged (bug #54403)
    $clean_config = loadCleanConfig();
    if ( rebuildConfigFile($clean_config, $sugar_version) ) {
        $GLOBALS['log']->fatal('Config File was rebuilt successfully');
    }
    else {
        $GLOBALS['log']->fatal('ERROR - Config File failed rebuild');
    }
}


$GLOBALS['log']->fatal('--------------------------------------------> finished repair <--------------------------------------------');

$exit_on_cleanup = true;

sugar_cleanup(false);

// some jobs have annoying habit of calling sugar_cleanup(), and it can be called only once
// but job results can be written to DB after job is finished, so we have to disconnect here again
// just in case we couldn't call cleanup
if (class_exists('DBManagerFactory')) {
    $db = DBManagerFactory::getInstance();
    $db->disconnect();
}

if ($exit_on_cleanup) exit;
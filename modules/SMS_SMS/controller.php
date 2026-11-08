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

require_once('include/MVC/Controller/SugarController.php');

class SMS_SMSController extends SugarController
{
    function action_configSave(){
        require_once('modules/Configurator/Configurator.php');

        $configurator = new Configurator();
        global $sugar_config;
        global $current_user;
        if ( !is_admin($current_user)
                && !is_admin_for_module($GLOBALS['current_user'],'SMS_SMS')){
            sugar_die("Unauthorized access to administration.");
        }

        $configurator->config['SMS_ACTIVE'] = ($_REQUEST['SMS_ACTIVE']=="false")?false:true;
        $configurator->config['SMS_API_KEY'] = $_REQUEST['SMS_API_KEY'];
        $configurator->config['SMS_API_SECRET'] = $_REQUEST['SMS_API_SECRET'];

        ksort($sugar_config);

        $configurator->handleOverride();
        
        header("Location: index.php?action={$_POST['return_action']}&module={$_POST['return_module']}");
        exit;
  
    }

}
?>

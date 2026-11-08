<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*
 * EINTEL
 */
    require_once('include/formbase.php');

    global $beanFiles, $beanList;

    $bean_name = $beanList[$_REQUEST['module']];
    require_once($beanFiles[$bean_name]);

    if(!empty($_REQUEST['record'])){
        if(!$this->bean->ACLAccess('Delete')){
            ACLController::displayNoAccess(true);
            sugar_cleanup(true);
        }
        $this->bean->mark_deleted($_REQUEST['record']);

        if(!empty($_REQUEST['return_url'])){
            $_REQUEST['return_url'] =urldecode($_REQUEST['return_url']);
        }
        $GLOBALS['log']->debug("$bean_name deleted bean id: $related_id" );
        if(empty($_REQUEST['refresh_page'])){
            handleRedirect();
        }

    }else{
        sugar_die("A record number must be specified to delete");
    }

    exit;

?>

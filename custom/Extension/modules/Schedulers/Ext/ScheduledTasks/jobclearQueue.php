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

$job_strings[] = 'jobclearQueue';


function jobclearQueue(){
    //kick off our job here. Best practice is to not do the actual logic in this file
    $GLOBALS['log']->info('Running: jobclearQueue');
    return BeanFactory::getBean('Camps')->schedulejob_clearQueue();

}


?>

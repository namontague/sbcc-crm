<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*
 * (C) 2010 Enterprise Intelligence Systems Pty Ltd
 *
 * Script to review activity schedules and create next instance of scheduled activities.
 *
 */
require_once('modules/ActivitySchedule/ActivitySchedule.php');
require_once('include/TimeDate.php');
require_once('include/utils.php');

global $timedate;
global $beanFiles;

$now = $timedate->get_gmt_db_datetime();

$bean = new ActivitySchedule();

$bean->correctSchedules();
$schedList = $bean->getSchedulesBefore($now);

foreach($schedList as $sched) {
    $sched->createNextInstance(null);
}



<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*
 * (C) 2009 Enterprise Intelligence Systems Pty Ltd
 * TaskAssigned.php
 * Script to review tasks assigned to users and notify them
 *
 */
global $sugar_config;
require_once('modules/Tasks/Task.php');
require_once('modules/Users/User.php');
require_once('include/eintel/notifications/notifications.php');
require_once("XTemplate/xtpl.php");

// The template file
$template_file='include/eintel/notifications/templates/tasks.php';
$template_name='Task_List';
// Get users lists
$user = new User();
$userList = $user->get_full_list();

// For each user in the list iterate through tasks and send email

foreach ($userList as $taskUser) {
    if($taskUser->status != 'Active'){
        continue;
    }
    $hastasks = false;
    $hasprojecttasks = false;

    // The template to be used
    $xtpl = new XTemplate($template_file);

    // Check to see if they have an email
    $userEmail = $taskUser->getPreferredEmail();
    // Only complete the next step if the user has an email address
    if(!empty($userEmail['email'])) {
    // Get list of tasks for that user
        $where = "tasks.assigned_user_id='" .$taskUser->id. "' and tasks.status !='Completed'";
        // Search for tasks assgined to user.
        $task = new Task();
        // Get the list of tasks
        $taskList = $task->get_full_list("", $where);
        //$rowsize = count($taskList);

        if(isset($taskList) && !empty($taskList)){
            if(count($taskList)>0) {
                $hastasks = true;
                $xtpl->parse('Task_List.TASK_HEADER');
                foreach($taskList as $taskobject){
                  //  $xtpl->assign('DATA', $taskList[$i]);
                  //  $xtpl->parse('TASK_ROWS');
                  $xtpl->assign('NAME',$taskobject->name);
                  $xtpl->assign('URL', $sugar_config["site_url"]. "/index.php?module=Tasks&action=DetailView&record=". $taskobject->id);
                  $xtpl->assign('DATE_DUE',$taskobject->date_due);
                  $xtpl->assign('PRIORITY',$taskobject->priority);
                  $xtpl->assign('STATUS',$taskobject->status);
                  $xtpl->parse('Task_List.TASK_ROWS');
                }
                // $xtpl->insert_loop('TASK_ROWS', $taskList); priority
                // $xtpl->parse('TASK_ROWS');
                // $xtpl->assign('TEST','HELLO');
                // Now that the message has been constructed send the email
            }
        }
  
        /*
         * Adding Project Tasks to the Task nightly Email
         */

        $projecttaskwhere = "project_task.assigned_user_id='" .$taskUser->id. "' and project_task.status !='Completed'";
        // Search for tasks assgined to user.
        $projecttask = new ProjectTask();
        // Get the list of tasks
        $projecttaskList = $projecttask->get_full_list("", $projecttaskwhere);
        //$projecttaskrowsize = count($projecttaskList);

        if(isset($projecttaskList) && !empty($projecttaskList)){          
            if(count($projecttaskList)>0) {
                $hasprojecttasks = true;
                $xtpl->parse('Task_List.PROJECT_TASK_HEADER');
                foreach($projecttaskList as $ptask){
                  $xtpl->assign('HAS_PROJECTTASKS',true);
                  $xtpl->assign('PROJECTTASK_NAME',$ptask->name);
                  $xtpl->assign('PROJECTTASK_URL', $sugar_config["site_url"]. "/index.php?module=ProjectTask&action=DetailView&record=". $ptask->id);
                  $xtpl->assign('PROJECTTASK_DATE_DUE',$ptask->date_finish);
                  $xtpl->assign('PROJECTTASK_PRIORITY',$ptask->priority);
                  $xtpl->assign('PROJECTTASK_STATUS',$ptask->status);
                  $xtpl->parse('Task_List.PROJECT_TASK_ROWS');
                }
            }
        }

        /*
         * End Project Task addions 
         */
        if($hasprojecttasks || $hastasks){
            send_notification($userEmail['email'], $userEmail['name'], $template_name, $xtpl);
        }
    }
    
}

// Loop through and email users about tasks

?>

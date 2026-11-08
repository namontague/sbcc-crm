<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
if(!is_admin($current_user)) sugar_die("Unauthorized access to administration.");


$tasks = new ProjectTask();

// Remove all project_task_ids;
$tasks->db->query("UPDATE project_task SET project_task_id='REINDEX' where deleted =0");

// Update task numbers

$tasks->db->query("UPDATE project_task SET task_number=1 where task_number is NULL and deleted =0 ");


$q = "SELECT * FROM project_task WHERE (project_id IS NOT NULL OR parent_task_id IS NOT NULL) and deleted =0
        ORDER BY project_id, parent_task_id, task_number, date_start";

$result = $tasks->process_list_query($q,0,999999999,999999999);
$project_id = null;
$parent_task_id = null;
$task_number = -1;
foreach($result['list'] as $t) {
    if($t->project_id == $project_id && $t->parent_task_id == $parent_task_id) {
        if( isset($t->task_number ) && $t->task_number > $task_number ) {
            $task_number = $t->task_number;
        } else {
            $task_number = $task_number + 1;
            $t->task_number = $task_number;
            $t->save();
        }
    } else {
        $project_id = $t->project_id;
        $parent_task_id =$t->parent_task_id;
        $task_number = $t->task_number;
    }
}


// Recreate project_task_id s

$tasks = new ProjectTask();
$q = "SELECT * FROM project_task WHERE parent_task_id is null and deleted =0 order by project_id";
$result = $tasks->process_list_query($q,0,999999999,999999999);
foreach($result['list'] as $t) {
    $task_number = $t->task_number;
    $taskNumberInfo = $t->taskNumberInfo();
    $task_number = $t->formatTaskNumber($taskNumberInfo);
    $t->project_task_id = $task_number;
    $t->save();
    $t->rebuildChildProjectTaskIds();
}

$tasks = new ProjectTask();

$q = "update project_task set date_start = (select estimated_start_date from project where id = project_task.project_id) where date_start is null";
$tasks->db->query($q);
$q = "update project_task set date_finish = (select estimated_end_date from project where id = project_task.project_id) where date_finish is null";
$tasks->db->query($q);


echo $mod_strings['LBL_DIAGNOSTIC_DONE'];


<?php

class GanttChart {

  
    //Return an array of tasks with numerical ids
    function taskArrayValues($tasks){
        $count = 1;
        $task_key_array = array();
        if(isset($tasks)){
            foreach($tasks as $task){                                    
                $task_key_array[$task->id] = array(
                        'id' => $count,
                        'parent' => (empty($task->parent_task_id)? 1 : 0),  // Root node (project) has id 1
                        'taskObject' => $task,
                        'child' => array());
            }

            // Process parent /child settings
            foreach($task_key_array as $sugarId => $task){
                if($task['taskObject']->parent_task_id != null){
                    $parentSugarId = $task['taskObject']->parent_task_id;
                    if(array_key_exists($parentSugarId, $task_key_array)) {
                        $task_key_array[$parentSugarId]['child'][] = $sugarId;
                    }
                }
            }

        }
        return $task_key_array;
    }

    //Return predecessors of a task as numerical ids
    function predecessors($task_key_array, $task){
        $task_predecessors = null;
        if(isset($task_key_array)){
            if(isset($task->predecessors) && $task->predecessors != null){
                foreach($task->predecessors as $predecessor){
                    if(array_key_exists($predecessor->id, $task_key_array)) {
                        $task_predecessors .= $predecessor->id.",";
                    }
                }
                $task_predecessors = substr($task_predecessors, 0, -1);
            }
            return $task_predecessors;
        }
    }

    //Replace special characters
    function xmlentities($string) {
       return str_replace ( array ( '&', '"', "'", '<', '>' ), array ( '&amp;' , '&quot;', '&apos;' , '&lt;' , '&gt;' ), $string );
    }

    //Create/Display XML for Project
    function ganttDataXML($project, $tasks){        
        $task_key_array = $this->taskArrayValues($tasks);

        //echo projects xml       
        echo"<?xml version='1.0' encoding='UTF-8'?>
                <projects>
                    <project id = '{$project->id}' name = '{$this->xmlentities($project->name)}' startdate = '{$this->checkDateFormat($project->estimated_start_date)}'>";

        //Display each project task of the project
        foreach($task_key_array as $id => $t){
            if( $t['parent'] == 1 ) {  
                $this->displayXML($id, $task_key_array);
            }
        }
      
        echo "</project></projects>";
    }

    //Create/Display the XML for Task
    function displayXML($id, $task_key_array){
        $task = $task_key_array[$id]['taskObject'];
        $task_predecessors = $this->predecessors($task_key_array, $task);        
        $percentcomplete = $task->percent_complete;
        if( $percentcomplete == '' ) {
           $percentcomplete = 0;
        }

        echo "
            <task id='{$task->id}'>
                <name>({$task->task_number}) {$this->xmlentities($task->name)}</name>
                <est>{$this->checkDateFormat($task->date_start)}</est>
                <duration>{$this->setDurcation($task)}</duration>
                <percentcompleted>{$percentcomplete}</percentcompleted>
                <predecessortasks>{$task_predecessors}</predecessortasks>
                <childtasks>";
              
                //check for childern
                if(!empty($task_key_array[$id]['child'])){
                    foreach($task_key_array[$id]['child'] as $childId){
                        $this->displayXML($childId, $task_key_array);
                    }
                }

                echo"</childtasks></task>";

    }


    function setDurcation($bean){
        global $timedate;
        $start_date = date_parse($timedate->swap_formats($bean->date_start, $timedate->get_date_format(), 'Y-m-d'));
        $end_date = date_parse($timedate->swap_formats($bean->date_finish, $timedate->get_date_format(), 'Y-m-d'));
        $time_difference = mktime(0,0,0,$end_date['month'],$end_date['day'],$end_date['year']) - mktime(0,0,0,$start_date['month'],$start_date['day'],$start_date['year']);

        return (($time_difference/3600)/24)*8+8;     

    }


    function checkDateFormat($date = null){
        if(isset($date)){
            global $timedate;
            return $timedate->swap_formats($date, $timedate->get_date_format(), 'Y,m,d');
        }
    }
  




}

?>

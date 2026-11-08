<?php
/*********************************************************************************
 *
 * (C) 2009 Enterprise Intelligence Pty Ltd
 * Not to be used or redistributed without permission.
 *
 ********************************************************************************/



require_once('include/MVC/View/SugarView.php');
require_once('modules/Project/gantt.php');

class ProjectViewGanttXml extends SugarView {

 	function ProjectViewGanttXml(){
 	  	parent::SugarView();
 	}

        function process(){
            // This will bypass the sugar header and footer so only the required text is sent
            $this->display();

        }

        function display() {
            $projectid = $_GET['id'];
            $project = new Project();
            $project->retrieve($projectid);

            //order by task start date.
            $tasks = new ProjectTask();
            $tasks = $project->get_related_list($tasks,"projecttask", $order_by = "task_number", $where = "",$row_offset = 0, $limit=-99, $max=-99, $show_deleted = 0);


                foreach($tasks['list'] as $t){
                    $task_predecessors = $t->get_linked_beans('predecessor','ProjectTask');
                    $task_successors = $t->get_linked_beans('successors','ProjectTask');
                    $t->predecessors = $task_predecessors;
                    $t->successors = $task_successors;
                }

                ob_clean();
                header('Content-Type: application/xml');
                $ganttChart = new GanttChart();
                print($ganttChart->ganttDataXML($project, $tasks['list']));
                sugar_cleanup(true);
            
        }

}
?>
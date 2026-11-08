<?php
/*********************************************************************************
 *
 * (C) 2009 Enterprise Intelligence Pty Ltd
 * Not to be used or redistributed without permission.
 *
 ********************************************************************************/



require_once('include/MVC/View/SugarView.php');

class ProjectViewGanttChart extends SugarView {

 	function ProjectViewGanttChart(){
 	  	parent::SugarView();
 	}

        function process(){
            // This will bypass the sugar header and footer so only the required text is sent
            $this->displayJavascript();
            $this->display();

        }

        function display() {
            $projectid = $_GET['id'];
            $project = new Project();
            $project->retrieve($projectid);

            $tasks = $project->get_linked_beans('projecttask','ProjectTask');

            //EINTEL re#84 adding close button
            require_once('include/eintel/dhtmlx_util.php');
            echo dhtmlxJavascriptFor(array('gantt'));
            echo "<input type ='button' value='Close' name='close' onclick='parent.closeGantt();'/>";
                //END

                if(empty($tasks)){
                    echo "<p>Project: <b>".$project->name."</b>. There are no Project Tasks for this project. There for no Gantt Chart can be displayed.</p>";
                } else{
                    echo "<h1>$project->name</h1>";
                    $this->displayChartParseXml($project);
                }
            
        }

        function displayChartParseXml($project){
               echo "

                <script type='text/javascript' >
                
                    $(document).ready(function() {
                        createChartControl('GanttDiv');
                    });
                 
                </script>
                <script type='text/javascript' language='JavaScript'>
                        function createChartControl(htmlDiv1)
                        {

                           // Create Gantt control
                            var ganttChartControl = new GanttChart();
                            // Setup paths and behavior
                            ganttChartControl.setImagePath(dhtmlxImagePaths['gantt']);
                            // Build control on the page
                            ganttChartControl.create(htmlDiv1);
                            // Load data structure

                            ganttChartControl.setLoadPath('index.php?module=Project&id={$project->id}&action=GanttXml');
                            ganttChartControl.loadData('index.php?module=Project&id={$project->id}&action=GanttXml',true,false);
                        }
                </script>
                <div style='width:100%;height:100%;position:absolute' id='GanttDiv'>";
                            

        }

}
?>
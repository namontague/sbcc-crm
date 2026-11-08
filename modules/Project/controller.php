<?php

require_once('include/MVC/Controller/SugarController.php');

class ProjectController extends SugarController
{

    function action_GanttChart()
    {
		$this->view = 'ganttchart';
    }

    function action_GanttXml()
    {
		$this->view = 'ganttxml';
    }
    function action_CreateTemplate()
    {
		$this->view = 'createtemplate';
    }


}
?>
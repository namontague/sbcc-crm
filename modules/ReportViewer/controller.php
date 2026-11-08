<?php

require_once('include/MVC/Controller/SugarController.php');

class ReportViewerController extends SugarController
{

    function action_ReportList()
    {
		$this->view = 'reportlist';
    }
    function action_ReportRun()
    {
		$this->view = 'reportrun';
    }
    function action_ReportParams()
    {
		$this->view = 'reportparams';
    }
    function action_GroupList()
    {
		$this->view = 'grouplist';
    }
    function action_PopupList()
    {
		$this->view = 'popuplist';
    }

}
?>
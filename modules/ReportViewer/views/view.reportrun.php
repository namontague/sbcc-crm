<?php
/*********************************************************************************
 *
 * (C) 2009 Enterprise Intelligence Pty Ltd
 * Not to be used or redistributed without permission.
 *
 ********************************************************************************/



    require_once('include/MVC/View/SugarView.php');
    require_once('modules/ReportServer/ReportRestClient.php');
    
    class ReportViewerViewReportRun extends SugarView {

        function ReportViewerViewReportRun(){
            parent::SugarView();
        }

        function process(){
            // This will bypass the sugar header and footer so only the required text is sent
            $this->executeReport();
        }

        function executeReport(){
            //run the report
            $excluded_params=array('module','action','report','format','execute_button');

            $ws = new ReportRestClient();
            $ws->format($_POST['format']);
            $ws->reportUnit = $_POST['report'];
            $ws->report_name = $_POST['report_name'];
            $ws->getParams();

            $ws->runRestReport();

        }
    }

?>

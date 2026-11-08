<?php

class ZohoReportsViewReportParams extends SugarView {
	
	function process() {
		$report = new ZohoReport();
		$report = $report->retrieve($_REQUEST['id']);

        $filterCriteria='';
        $relatedModule = $report->related_module;

        if( $relatedModule && $_REQUEST['record_id']) {
            $bean = new $relatedModule;
            $bean = $bean->retrieve($_REQUEST['record_id']);
		
		    $param_name = $report->parameter;
		    $param_val = $bean->$param_name;
            $filterCriteria="&ZOHO_CRITERIA=$param_name='$param_val'";
        }
		
		//add parameters to url
		echo trim($report->url).$filterCriteria;
		//include('modules/ZohoReports/views/reportparams.html.php');
	}
}

?>




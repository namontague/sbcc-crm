<?php

$viewdefs['ZohoReports']['DetailView'] = array(
	'templateMeta' => array(
     	'javascript' => '<script type="text/javascript" src="modules/ZohoReports/javascript/detailview.js"></script>',
     ),
       'panels' => array(
            'default' => array(
                array('name'),
                array('description'),
                array('name' => 'url','label' => 'LBL_URL'),
                array('status'),
                array('parameter'),
                array(
              		array(
                	// 'displayIf' => 'empty($fields.related_module.value)',
              		 'customCode' => '<input class="reportid" type="button" value="Execute Report" name="report_run"  onClick=zohoReportExecute("{$fields.id.value}"); />'
					//'customCode' => '<input class="reportid" type="button" value="Execute Report" name="report_run" onClick=onExecute("{$fields.id.value}"); />'
					)
            	),
            ),
        ),
);
?>
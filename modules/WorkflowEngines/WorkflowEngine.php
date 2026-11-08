<?php
/*********************************************************************************
 * (C) 2009 Enterprise Intelligence Systems Pty Ltd
 * No part of this file should be used without the express permission
 * of the authors.
 *
 * Contact: info@eintel.com.au for more information.
 *
 *
 ********************************************************************************/

require_once('modules/WorkflowEvents/WorkflowEvent.php');
require_once('modules/WorkflowEngines/Bonita/BonitaClient.php');

class WorkflowEngine extends SugarBean {
    var $module_dir = 'WorkflowEngine';
    var $object_name = 'WorkflowEngine';

    function processEvents() {
        //get the Bonita REST API connection
        $bonitaClient = new BonitaClient();

        // get the list of workflow events whose process id is NULL
        $workflowevents = new WorkflowEvent();
        $workflowevenrecords = $workflowevents->get_full_list('','process_id is NULL');

        if(!empty($allSchedulers)){
            error_log("No Workflows to run");
        }
        else {
        // for each records retrieved, store the initiated bonita process id into the 'process_id' field
        foreach($workflowevenrecords as $workflowevenrecord) {

            // start the bonita process using the REST API
            $process_id = $bonitaClient->startBonitaProcess($workflowevenrecord->module, $workflowevenrecord->record_id, $workflowevenrecord->type);

            $workflowevenrecord->process_id = $process_id;
            $workflowevenrecord->save();
        }

      }
        
    }
}
?>

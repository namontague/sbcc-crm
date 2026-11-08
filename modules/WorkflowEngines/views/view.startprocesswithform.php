<?php
/*********************************************************************************
 * (C) 2011 Enterprise Intelligence Systems Pty Ltd
 * No part of this file should be used without the express permission
 * of the authors.
 *
 * Contact: info@eintel.com.au for more information.
 *
 *
 ********************************************************************************/

require_once('include/MVC/View/SugarView.php');
require_once('modules/WorkflowEngines/Bonita/BonitaClient.php');


/**
 * Start a process
 * Params
 *  - sourceId = associated record id
 *  - sourceModule = Module of the associated record
 */
class WorkflowEnginesViewStartprocesswithform extends SugarView {

    function WorkflowEnginesViewStartprocesswithform(){
        parent::SugarView();
    }

    function process(){
        // This will bypass the sugar header and footer so only the required text is sent
        $this->startProcessWithForm();
    }

    function startProcessWithForm(){
        $bonitaClient = new BonitaClient();

        $sourceModule = $_GET['sourceModule'];
        $sourceId = $_GET['sourceId'];
        $processName = $_GET['processName'];
        $processId = $bonitaClient->startBonitaProcess($sourceModule, $sourceId, 'manual', $processName);
        $taskIds= $bonitaClient->getTaskIdsFor($processName, $processId);
        if( count($taskIds) > 0 ) {
            $formurl = $bonitaClient->formUrl($taskIds[0]);
            $bonitaClient->setVariable($taskIds[0],'formurl', $formurl);

			@ob_end_clean(); // clear output buffer
			session_write_close();
			header( 'HTTP/1.1 302 Form' );
			header( "Location: ". $formurl );

        }

    }
}

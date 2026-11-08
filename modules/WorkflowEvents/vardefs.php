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
$dictionary['WorkflowEvent'] = array(
        'table'=>'workflowevents',
        'fields'=>array(
                'module' => array(
                        'name' => 'module',
                        'vname' => 'LBL_MODULE',
                        'required' => true,
                        'dbType' => 'varchar',
                        'type' => 'varchar',
                        'len' => 50,
                        'comment' => 'Module name',
                ),
                'type' => array(
                        'name' => 'type',
                        'vname' => 'LBL_TYPE',
                        'required' => true,
                        'dbType' => 'varchar',
                        'type' => 'varchar',
                        'len' => 50,
                        'comment' => 'Event type',
                ),
                'record_id' => array(
                        'name' => 'record_id',
                        'vname' => 'LBL_RECORD_ID',
                        'required' => true,
                        'dbType' => 'varchar',
                        'type' => 'varchar',
                        'len' => 50,
                        'comment' => 'Record id of the module',
                ),
                'process_id' => array(
                        'name' => 'process_id',
                        'vname' => 'LBL_PROCESS_ID',
                        'required' => false,
                        'dbType' => 'varchar',
                        'type' => 'varchar',
                        'len' => 50,
                        'comment' => 'process id',
                ),
        ),
        'relationships'=>array(),
        'optimistic_lock'=>true,
);

require_once('include/SugarObjects/VardefManager.php');

VardefManager::createVardef('WorkflowEvents','WorkflowEvent', array('basic'));
?>
<?php

/* * *******************************************************************************
 * (C) 2009 Enterprise Intelligence Systems Pty Ltd
 * No part of this file should be used without the express permission
 * of the authors.
 *
 * Contact: info@eintel.com.au for more information.
 *
 *
 * ****************************************************************************** */
$dictionary['ZohoReport'] = array(
    'table' => 'zoho_reports',
    'audited' => true,
    'fields' => array(
        'url' => array(
            'name' => 'url',
            'vname' => 'LBL_URL',
            'required' => true,
            'type' => 'varchar',
            'reportable' => true,
            'comment' => 'Zoho report url'
        ),
        'status' => array(
                'name' => 'status',
                'vname' => 'LBL_STATUS',
                'required' => false,
                'type' => 'enum',
                'options' => 'report_status',
                'comment' => 'Select status',
        ),
        'parameter' => array(
                'name' => 'parameter',
                'vname' => 'LBL_PARAMETER',
                'required' => false,
                'type' => 'varchar',
                'comment' => 'Report Parameter'
        ),
        'related_module' => array(
                'name' => 'related_module',
                'vname' => 'LBL_RELATED_MODULE',
                'required' => false,
                'type' => 'enum',
                'function' => 'zoho_reports_related_modules',
                'comment' => 'Select related module',
        ),
    ),
    'relationships' => array(),
    'optimistic_lock' => true,
);
require_once('include/SugarObjects/VardefManager.php');
VardefManager::createVardef('ZohoReport', 'ZohoReport', array('basic'));
?>
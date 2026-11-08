<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/* * *******************************************************************************
 * (C) 2013 Enterprise Intelligence Systems Pty Ltd
 * No part of this file should be used without the express permission
 * of the authors.
 *
 * Contact: info@eintel.com.au for more information.
 *
 *
 * ****************************************************************************** */

$module_name = 'Camps';
$searchdefs[$module_name] = array(
	'templateMeta' => array(
		'maxColumns' => '3',
		'widths' => array('label' => '5', 'field' => '25'),
	),
	'layout' => array(
		'basic_search' => array(
			'name',
            'start_date',
            'venue',
            'type',
			//array('name' => 'current_user_only', 'label' => 'LBL_CURRENT_USER_FILTER', 'type' => 'bool'),
		),
		'advanced_search' => array(
			'name',
            'start_date',
            'end_date',
            'venue',
            'type',
			array('name' => 'assigned_user_id', 'type' => 'enum', 'label' => 'LBL_ASSIGNED_TO', 'function' => array('name' => 'get_user_array', 'params' => array(false))),
	     //'age_groups' =>
            // array(
            //        'name' => 'age_groups',
            //        'vname' => 'age_groups',
            //        'label' => 'LBL_AGE_GROUPS',
            //        'type' => 'enum',
            //        'options' => 'participants_age_groups',
            //        'required' => false,
            //        'do_report' => false,
            //        'reportable' => false,
            //        'comment' => 'Currency in use for the campaign',
            //        'source' => 'non-db'
            //  ),	
	    ),
	),
);
?>

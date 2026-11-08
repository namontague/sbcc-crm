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
$_module_name = strtolower($module_name);
$bean_name = 'Camp';

$popupMeta = array('moduleMain' => $bean_name,
	'varName' => $bean_name,
	'orderBy' => 'name',
	'whereClauses' => array(
		'name' => $_module_name . '.name',
	),
	'searchInputs' => array('name', 'type', 'status'),
	'listview' => 'modules/' . $module_name . '/metadata/listviewdefs.php',
        'searchdefs'  => array(
            'name',
            'start_date',
            'end_date',
            'venue',
	     'type',
            array('name' => 'assigned_user_id', 'type' => 'enum', 'label' => 'LBL_ASSIGNED_TO', 'function' => array('name' => 'get_user_array', 'params' => array(false))),		
            'age_groups' =>
            array(
                'name' => 'age_groups',
                'vname' => 'age_groups',
                'label' => 'LBL_AGE_GROUPS',
                'type' => 'enum',
                'options' => 'participants_age_groups',
                'required' => false,
                'do_report' => false,
                'reportable' => false,
                'comment' => 'Currency in use for the campaign',
                'source' => 'non-db'
            ),
					'participant_gender' =>
						array(
							'name' => 'participant_gender',
							'vname' => 'participant_gender',
							'label' => 'LBL_GENDER',
							'type' => 'enum',
							'options' => 'participants_gender_dom',
							'required' => false,
							'do_report' => false,
							'reportable' => false,
							'comment' => 'Participant Gender',
							'source' => 'non-db'
						),
				),
);
?>
 


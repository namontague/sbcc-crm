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

$module_name = 'CampParticipants';
$searchdefs[$module_name] = array(
	'templateMeta' => array(
		'maxColumns' => '3',
		'widths' => array('label' => '10', 'field' => '30'),
	),
	'layout' => array(
		'basic_search' => array(
			'name',
            'type',
			array('name' => 'current_user_only', 'label' => 'LBL_CURRENT_USER_FILTER', 'type' => 'bool'),
		),
		'advanced_search' => array(
			'name',
            'camp_name',
			array('name' => 'assigned_user_id', 'type' => 'enum', 'label' => 'LBL_ASSIGNED_TO', 'function' => array('name' => 'get_user_array', 'params' => array(false))),
		),
	),
);
?>

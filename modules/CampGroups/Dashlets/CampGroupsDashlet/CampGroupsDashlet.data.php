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

global $current_user;

$dashlet_name = 'CampGroupsDashlet';

$dashletData[$dashlet_name]['searchFields'] = array(
	'date_entered' => array('default' => ''),
	'date_modified' => array('default' => ''),
	'assigned_user_id' => array('type' => 'assigned_user_name',
		'default' => $current_user->name));

$dashletData[$dashlet_name]['columns'] = array('name' => array('width' => '40',
		'label' => 'LBL_LIST_NAME',
		'link' => true,
		'default' => true),
	'date_entered' => array('width' => '15',
		'label' => 'LBL_DATE_ENTERED',
		'default' => true),
	'date_modified' => array('width' => '15',
		'label' => 'LBL_DATE_MODIFIED'),
	'created_by' => array('width' => '8',
		'label' => 'LBL_CREATED'),
	'assigned_user_name' => array('width' => '8',
		'label' => 'LBL_LIST_ASSIGNED_USER'),
);

?>

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
$subpanel_layout = array(
	'top_buttons' => array(
		array('widget_class' => 'SubPanelTopCreateButton'),
		array('widget_class' => 'SubPanelTopSelectButton', 'popup_module' => $module_name),
	),
	'where' => '',
	'list_fields' => array(
		'name' => array(
			'vname' => 'LBL_NAME',
			'widget_class' => 'SubPanelDetailViewLink',
			'width' => '25%',
		),
		'start_date' => array(
			'vname' => 'LBL_START_DATE',
			'width' => '12%',
		),
		'end_date' => array(
			'vname' => 'LBL_END_DATE',
			'width' => '12%',
		),
		'type' => array(
			'vname' => 'LBL_TYPE',
			'width' => '12%',
		),
        'venue' => array(
			'vname' => 'LBL_VENUE',
			'width' => '12%',
		),
        'contact_name' => array(
			'vname' => 'LBL_CONTACT_NAME',
			'width' => '12%',
		),
//		'assigned_user_name' => array(
//			'name' => 'assigned_user_name',
//			'vname' => 'LBL_ASSIGNED_TO_NAME',
//            'width' => '15%',
//		),
		'edit_button' => array(
			'widget_class' => 'SubPanelEditButton',
			'module' => $module_name,
			'width' => '4%',
		),
		'remove_button' => array(
			'widget_class' => 'SubPanelRemoveButton',
			'module' => $module_name,
			'width' => '5%',
		),
	),
);
?>

<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$module_name = 'SMS_Replies';
$subpanel_layout = array(
	'top_buttons' => array(),

	'where' => '',
	'list_fields' => array(
		'received_at' => array(
			'vname' => 'LBL_RECEIVED_AT',
			'widget_class' => 'SubPanelDetailViewLink',
			'width' => '15%',
			'default' => true,
		),
		'msisdn' => array(
			'vname' => 'LBL_MSISDN',
			'width' => '15%',
			'default' => true,
		),
		'name' => array(
			'vname' => 'LBL_NAME',
			'width' => '15%',
			'default' => true,
		),
		'response' => array(
			'vname' => 'LBL_RESPONSE',
			'width' => '40%',
			'default' => true,
		),
		'remove_button' => array(
			'widget_class' => 'SubPanelRemoveButton',
			'module' => $module_name,
			'width' => '5%',
		),
	),
);

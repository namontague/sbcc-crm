<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$module_name = 'SMS_Replies';

$viewdefs[$module_name]['DetailView'] = array(
	'templateMeta' => array(
		'form' => array(
			'buttons' => array('EDIT', 'DELETE'),
		),
		'maxColumns' => '2',
		'widths' => array(
			array('label' => '10', 'field' => '30'),
			array('label' => '10', 'field' => '30'),
		),
	),
	'panels' => array(
		'lbl_panel_details' => array(
			array(
				'sms_name',
				'received_at',
			),
			array(
				'msisdn',
				array('name' => 'name', 'label' => 'LBL_NAME'),
			),
			array(
				'first_name',
				'last_name',
			),
			array(
				'response',
			),
			array(
				'longcode',
				'message_id',
			),
			array(
				'transmit_id',
			),
		),
	),
);

<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$module_name = 'SMS_Replies';

$viewdefs[$module_name]['EditView'] = array(
	'templateMeta' => array(
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
				'name',
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

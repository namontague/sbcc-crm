<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$module_name = 'SMS_Replies';
$searchdefs[$module_name] = array(
	'templateMeta' => array(
		'maxColumns' => '3',
		'widths' => array('label' => '10', 'field' => '30'),
	),
	'layout' => array(
		'basic_search' => array(
			'msisdn',
			'response',
		),
		'advanced_search' => array(
			'msisdn',
			'response',
			'sms_name',
		),
	),
);

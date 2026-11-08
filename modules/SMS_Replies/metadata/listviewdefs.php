<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$module_name = 'SMS_Replies';
$listViewDefs[$module_name] = array(
	'NAME' => array(
		'width' => '15%',
		'label' => 'LBL_NAME',
		'default' => true,
		'link' => true,
	),
	'MSISDN' => array(
		'width' => '12%',
		'label' => 'LBL_MSISDN',
		'default' => true,
	),
	'RESPONSE' => array(
		'width' => '28%',
		'label' => 'LBL_RESPONSE',
		'default' => true,
	),
	'SMS_NAME' => array(
		'width' => '20%',
		'label' => 'LBL_SMS',
		'link' => true,
		'id' => 'SMS_ID',
		'module' => 'SMS_SMS',
		'default' => true,
		'related_fields' => array('sms_id'),
	),
	'RECEIVED_AT' => array(
		'width' => '15%',
		'label' => 'LBL_RECEIVED_AT',
		'default' => true,
	),
	'DATE_ENTERED' => array(
		'width' => '10%',
		'label' => 'LBL_DATE_ENTERED',
		'default' => false,
	),
);

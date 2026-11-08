<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$module_name = 'SMS_Replies';

$searchFields[$module_name] = array(
	'msisdn' => array('query_type' => 'default'),
	'response' => array('query_type' => 'default'),
	'sms_name' => array('query_type' => 'default'),

	'range_received_at' => array('query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true),
	'start_range_received_at' => array('query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true),
	'end_range_received_at' => array('query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true),
);

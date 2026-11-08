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
$_object_name = 'camp';

$searchFields[$module_name] =
		array(
			'name' => array('query_type' => 'default'),
			'type' => array('query_type' => 'default', 'options' => $_object_name . '_type_dom'),
			'current_user_only' => array('query_type' => 'default', 'db_field' => array('assigned_user_id'), 'my_items' => true),
			'assigned_user_id' => array('query_type' => 'default'),

                        'venue' => array('query_type' => 'default'),
			// Range Search Support
			'range_start_date' => array ('query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true),
			'start_range_start_date' => array ('query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true),
			'end_range_start_date' => array ('query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true),
            
            'range_end_date' => array ('query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true),
			'start_range_end_date' => array ('query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true),
			'end_range_end_date' => array ('query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true),
			// Range Search Support
);
?>

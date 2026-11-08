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


$module_name = 'CampGroups';
$bean_name = 'CampGroup';
$_bean_name = 'campgroup';
$table_name = 'campgroups';

$dictionary[$bean_name] = array(
	'table' => $table_name,
	'audited' => true,
	'fields' => array(
		
		        
		// linked camp
		'camp_id' =>
		array(
                    'name' => 'camp_id',
                    'type' => 'id',
                    'group' => 'camp_name',
                    'reportable' => false,
                    'vname' => 'LBL_CAMP_ID',
		),
		'camp_name' =>
		array(
                    'name' => 'camp_name',
                    'rname' => 'name',
                    'group' => 'camp_name',
                    'vname' => 'LBL_CAMP',
                    'reportable' => false,
                    'id_name' => 'camp_id',
                    'type' => 'relate',
                    'module' => 'Camps',
                    'table' => 'camps',
		),

//		//linked contact
//		'contact_id' =>
//		array(
//			'name' => 'contact_id',
//			'type' => 'id',
//			'group' => 'contact_name',
//			'reportable' => false,
//			'vname' => 'LBL_CONTACT_ID',
//		),
//		'contact_name' =>
//		array(
//			'name' => 'contact_name',
//			'group' => 'contact_name',
//			'vname' => 'LBL_CONTACT_NAME',
//			'reportable' => false,
//			'id_name' => 'contact_id',
//			'type' => 'relate',
//			'module' => 'Contacts',
//			'table' => 'contacts',
//		),

            
                'participants' =>
		array(
			'name' => 'participants',
			'type' => 'link',
			'relationship' => 'participants_campgroups',
			'module' => 'CampParticipants',
			'bean_name' => 'CampParticipant',
			'source' => 'non-db',
			'vname' => 'LBL_GROUPS',
		),
            
		'SecurityGroups' => array(
			'name' => 'SecurityGroups',
			'type' => 'link',
			'relationship' => 'securitygroups_' . $_bean_name,
			'module' => 'SecurityGroups',
			'bean_name' => 'SecurityGroup',
			'source' => 'non-db',
			'vname' => 'LBL_SECURITYGROUPS',
		),
	),
	'relationships' => array(
		'securitygroups_' . $_bean_name => array(
			'lhs_module' => 'SecurityGroups', 'lhs_table' => 'securitygroups', 'lhs_key' => 'id',
			'rhs_module' => $module_name, 'rhs_table' => $table_name, 'rhs_key' => 'id',
			'relationship_type' => 'many-to-many',
			'join_table' => 'securitygroups_records', 'join_key_lhs' => 'securitygroup_id', 'join_key_rhs' => 'record_id',
			'relationship_role_column' => 'module', 'relationship_role_column_value' => $module_name),
            
            
                'participants_campgroups' => array(
                    'lhs_module' => 'CampGroups', 
                    'lhs_table' => 'campgroups', 
                    'lhs_key' => 'id',

                    'rhs_module' => 'CampParticipants', 
                    'rhs_table' => 'campparticipants', 
                    'rhs_key' => 'campgroup_id',
                    'relationship_type' => 'one-to-many'),
            
            
	),
	'optimistic_lock' => true,
	'optimistic_locking' => true,
);
VardefManager::createVardef($module_name, $bean_name, array('default', 'assignable'));

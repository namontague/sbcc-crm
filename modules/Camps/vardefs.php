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
$bean_name = 'Camp';
$_bean_name = 'camp';
$table_name = 'camps';

$dictionary[$bean_name] = array(
	'table' => $table_name,
	'audited' => true,
    'unified_search' => true,
	'fields' => array(
		
		'start_date' =>
		array(
			'name' => 'start_date',
			'vname' => 'LBL_START_DATE',
			'type' => 'date',
			'required' => true,
			'comment' => 'Date in which camp is schedule to (or did) start',
			'importable' => 'required',
			'display_default' => 'now',
			'validation' => array('type' => 'isbefore', 'compareto' => 'end_date', 'blank' => false),
			'enable_range_search' => true,
			'options' => 'date_range_search_dom',
		),
		'end_date' =>
		array(
			'name' => 'end_date',
			'vname' => 'LBL_END_DATE',
			'type' => 'date',
			'display_default' => '+1 day',
			'required' => true,
			'massupdate' => false,
			'comment' => 'Date is which camp is scheduled to (or did) end',
			'validation' => array('type' => 'isafter', 'compareto' => 'start_date', 'blank' => false),
            'enable_range_search' => true,
			'options' => 'date_range_search_dom',
		),
		'venue_address_building_type' =>
        array(
            'name' => 'venue_address_building_type',
            'vname' => 'LBL_VENUE_ADDRESS_BUILDING_TYPE',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'The building type used for venue',
            'group' => 'venue_address',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),
        'venue_address_number' =>
        array(
            'name' => 'venue_address_number',
            'vname' => 'LBL_VENUE_ADDRESS_NUMBER',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'The number used for venue',
            'group' => 'venue_address',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),
        'venue_address_street' =>
        array(
            'name' => 'venue_address_street',
            'vname' => 'LBL_VENUE_ADDRESS_STREET',
            'type' => 'varchar',
            'len' => '150',
            'comment' => 'The street address used for venue',
            'group' => 'venue_address',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),

        'venue_address_city' =>
        array(
            'name' => 'venue_address_city',
            'vname' => 'LBL_VENUE_ADDRESS_CITY',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'The city used for venue',
            'group' => 'venue_address',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),

        'venue_address_state' =>
        array(
            'name' => 'venue_address_state',
            'vname' => 'LBL_VENUE_ADDRESS_STATE',
            'type' => 'varchar',
            'len' => '100',
            'group' => 'venue_address',
            'comment' => 'The state used for venue',
            'merge_filter' => 'enabled',
            'massupdate' => false,
          ),

        'venue_address_postalcode' =>
        array(
            'name' => 'venue_address_postalcode',
            'vname' => 'LBL_VENUE_ADDRESS_POSTALCODE',
            'type' => 'varchar',
            'len' => '20',
            'group' => 'venue_address',
            'comment' => 'The postal code used for venue',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),

        'venue_address_country' =>
        array(
            'name' => 'venue_address_country',
            'vname' => 'LBL_VENUE_ADDRESS_COUNTRY',
            'type' => 'varchar',
            'group' => 'venue_address',
            'comment' => 'The country used for the venue address',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),
        
        'venue' =>
        array(
            'name' => 'venue',
            'vname' => 'LBL_VENUE',
            'type' => 'varchar',
            'comment' => 'The name of the venue',
            'merge_filter' => 'enabled',
            'massupdate' => false,
            'unified_search' => true,
        ),
        
		//linked contact
        'contact_id' =>
        array (
            'name' => 'contact_id',
            'rname' => 'id',
            'id_name' => 'contact_id',
            'vname' => 'LBL_CONTACT_NAME',
            'type' => 'contact_name',
            'table' => 'contacts',
            'group'=>'contact_name',
            'dbType' => 'id',
            'reportable'=>true,
            'massupdate' => false,
        ),
        'contact_name' => 
        array (
            'name' => 'contact_name',
            'vname' => 'LBL_CONTACT_NAME',
            'type' => 'relate',
            'reportable'=>false,
            'source'=>'non-db',
            'rname'=>'full_name',
            'table' => 'contacts',
            'id_name' => 'contact_id',
            'module'=>'Contacts',
            'link'=>'coordinator_link',
            'duplicate_merge'=>'disabled',
            'massupdate' => false,
            'metadata' => 'popupdefs_staff',
        ),  
        'coordinator_link' =>
        array (
            'name' => 'coordinator_link',
            'type' => 'link',
            'relationship' => 'camps_coordinator',
            'vname' => 'LBL_CONTACT_NAME',
            'link_type' => 'one',
            'module'=>'Contacts',
            'bean_name'=>'Contact',
            'source'=>'non-db',
        ),
        
		'type' =>
		array(
            'required' => false, 
			'name' => 'type',
			'vname' => 'LBL_TYPE',
			'type' => 'enum',
			'options' => $_bean_name . '_type_dom',
		),
		
        'sms_list_id' =>
		array(
            'required' => false, 
			'name' => 'sms_list_id',
			'vname' => 'LBL_SMS_LIST_ID',
			'type' => 'varchar',
		),
		

		
		'staff' =>
		array(
			'name' => 'staff',
			'type' => 'link',
			'relationship' => $_bean_name . '_staff',
			'source' => 'non-db',
			'vname' => 'LBL_STAFF',
		),
		
		'tasks' =>
		array(
			'name' => 'tasks',
			'type' => 'link',
			'relationship' => $_bean_name . '_tasks',
			'module' => 'Tasks',
			'bean_name' => 'Task',
			'source' => 'non-db',
			'vname' => 'LBL_TASKS',
		),
		'meetings' =>
		array(
			'name' => 'meetings',
			'type' => 'link',
			'relationship' => $_bean_name . '_meetings',
			'module' => 'Meetings',
			'bean_name' => 'Meeting',
			'source' => 'non-db',
			'vname' => 'LBL_MEETINGS',
		),
		'calls' =>
		array(
			'name' => 'calls',
			'type' => 'link',
			'relationship' => $_bean_name . '_calls',
			'module' => 'Calls',
			'bean_name' => 'Call',
			'source' => 'non-db',
			'vname' => 'LBL_CALLS',
		),
		'notes' =>
		array(
			'name' => 'notes',
			'type' => 'link',
			'relationship' => $_bean_name . '_notes',
			'module' => 'Notes',
			'bean_name' => 'Note',
			'source' => 'non-db',
			'vname' => 'LBL_NOTES',
		),
        'campparticipants' =>
		array(
			'name' => 'campparticipants',
			'type' => 'link',
			'relationship' => 'camp_campparticipants',
			'module' => 'CampParticipants',
			'bean_name' => 'CampParticipant',
			'source' => 'non-db',
			'vname' => 'LBL_PARTICIPANTS',
		),
        'campgroups' =>
		array(
			'name' => 'campgroups',
			'type' => 'link',
			'relationship' => 'camp_campgroups',
			'module' => 'CampGroups',
			'bean_name' => 'CampGroup',
			'source' => 'non-db',
			'vname' => 'LBL_GROUPS',
		),
        'campgroupbookings' =>
		array(
			'name' => 'campgroupbookings',
			'type' => 'link',
			'relationship' => 'camp_campgroupbookings',
			'module' => 'CampGroupBookings',
			'bean_name' => 'CampGroupBooking',
			'source' => 'non-db',
			'vname' => 'LBL_GROUPBOOKINGS',
		),
        'prospect_lists' =>
        array (
          'name' => 'prospect_lists',
          'type' => 'link',
          'relationship' => 'prospect_list_camps',
          'module'=>'ProspectLists',
          'source'=>'non-db',
          'vname'=>'LBL_PROSPECT_LIST',
        ), 
        'emails' =>
        array(
            'name' => 'emails',
            'type' => 'link',
            'relationship' => 'camp_emails', /* reldef in emails */
            'module' => 'Emails',
            'bean_name' => 'Email',
            'source' => 'non-db',
            'vname' => 'LBL_EMAILS',
        ),    
        'sms' =>
		array(
			'name' => 'sms',
			'type' => 'link',
			'module' => 'SMS_SMS',
            'relationship' => 'camp_sms',
			'bean_name' => 'SMS_SMS',
			'source' => 'non-db',
			'vname' => 'LBL_SMS_SMS',
		),
       
        'age_groups' =>
         array(
                'name' => 'age_groups',
                'vname' => 'age_groups',
                'label' => 'LBL_AGE_GROUPS',
                'type' => 'enum',
                'options' => 'participants_age_groups',                
                'required' => false,
                'do_report' => false,
                'reportable' => false,
                'source' => 'non-db'
        ),
        'participant_gender' =>
          array(
            'name' => 'participant_gender',
            'vname' => 'participant_gender',
            'label' => 'LBL_GENDER',
            'type' => 'enum',
            'options' => 'participants_gender_dom',
            'required' => false,
            'do_report' => false,
            'reportable' => false,
            'source' => 'non-db'
          ),

    'total_participants' => array(
          'name' => 'total_participants',
          'vname' => 'LBL_TOTAL_PARTICIPANTS',
          'type' => 'function',
          'source'=>'function',
          'function_name' => 'Camp::getTotalParticipants',
          'function_params' => array('id'),
          'function_params_source' => 'this',
    ),

    'total_confirmed_participants' => array(
         'name' => 'total_confirmed_participants',
         'vname' => 'LBL_TOTAL_CONFIRMED_PARTICIPANTS',
         'type' => 'function',
         'source'=>'function',
         'function_name' => 'Camp::getTotalConfirmedParticipants',
         'function_params' => array('id'),
         'function_params_source' => 'this',
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
		$_bean_name . '_tasks' => array('lhs_module' => $module_name, 'lhs_table' => $table_name, 'lhs_key' => 'id',
			'rhs_module' => 'Tasks', 'rhs_table' => 'tasks', 'rhs_key' => 'parent_id',
			'relationship_type' => 'one-to-many', 'relationship_role_column' => 'parent_type',
			'relationship_role_column_value' => $module_name),
		$_bean_name . '_meetings' => array('lhs_module' => $module_name, 'lhs_table' => $table_name, 'lhs_key' => 'id',
			'rhs_module' => 'Meetings', 'rhs_table' => 'meetings', 'rhs_key' => 'parent_id',
			'relationship_type' => 'one-to-many', 'relationship_role_column' => 'parent_type',
			'relationship_role_column_value' => $module_name),
		$_bean_name . '_calls' => array('lhs_module' => $module_name, 'lhs_table' => $table_name, 'lhs_key' => 'id',
			'rhs_module' => 'Calls', 'rhs_table' => 'calls', 'rhs_key' => 'parent_id',
			'relationship_type' => 'one-to-many', 'relationship_role_column' => 'parent_type',
			'relationship_role_column_value' => $module_name),
		$_bean_name . '_notes' => array('lhs_module' => $module_name, 'lhs_table' => $table_name, 'lhs_key' => 'id',
			'rhs_module' => 'Notes', 'rhs_table' => 'notes', 'rhs_key' => 'parent_id',
			'relationship_type' => 'one-to-many', 'relationship_role_column' => 'parent_type',
			'relationship_role_column_value' => $module_name),
        
        'camps_coordinator' => array('lhs_module'=> 'Contacts', 'lhs_table'=> 'contacts', 'lhs_key' => 'id',
            'rhs_module'=> 'Camps', 'rhs_table'=> 'camps', 'rhs_key' => 'contact_id',
            'relationship_type'=>'one-to-many'),
        
        
        'camp_campparticipants' => array(
            'lhs_module' => 'Camps', 
            'lhs_table' => 'camps', 
            'lhs_key' => 'id',

            'rhs_module' => 'CampParticipants', 
            'rhs_table' => 'campparticipants', 
            'rhs_key' => 'camp_id',
            'relationship_type' => 'one-to-many'),
            
        'camp_campgroups' => array(
            'lhs_module' => 'Camps', 
            'lhs_table' => 'camps', 
            'lhs_key' => 'id',

            'rhs_module' => 'CampGroups', 
            'rhs_table' => 'campgroups', 
            'rhs_key' => 'camp_id',
            'relationship_type' => 'one-to-many'),
            
        'camp_sms' => array(
            'lhs_module'=> 'Camps', 'lhs_table'=> 'camps', 'lhs_key' => 'id',
            'rhs_module'=> 'SMS_SMS', 'rhs_table'=> 'sms_sms', 'rhs_key' => 'parent_id',
            'relationship_type'=>'one-to-many', 'relationship_role_column'=>'parent_type',
            'relationship_role_column_value'=>'Camps'
        ),    
            
            
            
        
        'camp_campgroupbookings' => array(
            'lhs_module' => 'Camps', 
            'lhs_table' => 'camps', 
            'lhs_key' => 'id',

            'rhs_module' => 'CampGroupBookings', 
            'rhs_table' => 'campgroupbookings', 
            'rhs_key' => 'camp_id',
            'relationship_type' => 'one-to-many'),
        
        'camp_emails' => array('lhs_module'=> 'Camps', 'lhs_table'=> 'camps', 'lhs_key' => 'id',
                              'rhs_module'=> 'Emails', 'rhs_table'=> 'emails', 'rhs_key' => 'parent_id',
                              'relationship_type'=>'one-to-many', 'relationship_role_column'=>'parent_type',
                              'relationship_role_column_value'=>'Camps'),
        
//		$_bean_name . '_account' => array('lhs_module' => $module_name, 'lhs_table' => $table_name, 'lhs_key' => 'account_id',
//			'rhs_module' => 'Accounts', 'rhs_table' => 'accounts', 'rhs_key' => 'id',
//			'relationship_type' => 'one-to-many'),
        
        
		'securitygroups_' . $_bean_name => array(
			'lhs_module' => 'SecurityGroups', 'lhs_table' => 'securitygroups', 'lhs_key' => 'id',
			'rhs_module' => $module_name, 'rhs_table' => $table_name, 'rhs_key' => 'id',
			'relationship_type' => 'many-to-many',
			'join_table' => 'securitygroups_records', 'join_key_lhs' => 'securitygroup_id', 'join_key_rhs' => 'record_id',
			'relationship_role_column' => 'module', 'relationship_role_column_value' => $module_name),
	),
	'optimistic_lock' => true,
	'optimistic_locking' => true,
);
VardefManager::createVardef($module_name, $bean_name, array('default', 'assignable'));

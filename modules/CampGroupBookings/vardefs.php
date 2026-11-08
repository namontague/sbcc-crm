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


$module_name = 'CampGroupBookings';
$bean_name = 'CampGroupBooking';
$_bean_name = 'CampGroupBooking';
$table_name = 'campgroupbookings';

$dictionary[$bean_name] = array(
	'table' => $table_name,
	'audited' => true,
	'fields' => array(
		
		'salutation' =>
		array (
			'name' => 'salutation',
			'vname' => 'LBL_SALUTATION',
			'type' => 'enum',
			'options' => 'salutation_dom',
			'massupdate' => false,
			'len' => '255',
			'comment' => 'Contact salutation (e.g., Mr, Ms)'            
		),
        'first_name' =>
		array (
			'name' => 'first_name',
			'vname' => 'LBL_FIRST_NAME',
			'type' => 'varchar',
			'len' => '100',
			'unified_search' => true,
			'full_text_search' => array('boost' => 3),
			'comment' => 'First name of the contact',
            'merge_filter' => 'selected',     
            
		),
        'last_name' =>
		array (
			'name' => 'last_name',
			'vname' => 'LBL_LAST_NAME',
			'type' => 'varchar',
			'len' => '100',
			'unified_search' => true, 
			'full_text_search' => array('boost' => 3),
			'comment' => 'Last name of the participant',
            'merge_filter' => 'selected',
            'required'=>true,
            'importable' => 'required',
		),
        'name' =>
		array (
			'name' => 'name',
			'rname' => 'name',
			'vname' => 'LBL_NAME',
			'type' => 'name',
			'link' => true, // bug 39288 
			'fields' => array('first_name', 'last_name'),
			//'sort_on' => 'last_name',
			'source' => 'non-db',
			'group'=>'last_name',
			'len' => '255',
			'db_concat_fields'=> array(0=>'first_name', 1=>'last_name'),
            'importable' => 'false',
		),
        
        
        'primary_address_building_type' =>
        array(
            'name' => 'primary_address_building_type',
            'vname' => 'LBL_PRIMARY_ADDRESS_BUILDING_TYPE',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'The building type used for primary',
            'group' => 'primary_address',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),
        'primary_address_number' =>
        array(
            'name' => 'primary_address_number',
            'vname' => 'LBL_PRIMARY_ADDRESS_NUMBER',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'The number used for primary',
            'group' => 'primary_address',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),
        'primary_address_street' =>
        array(
            'name' => 'primary_address_street',
            'vname' => 'LBL_PRIMARY_ADDRESS_STREET',
            'type' => 'varchar',
            'len' => '150',
            'comment' => 'The street address used for primary',
            'group' => 'primary_address',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),

        'primary_address_city' =>
        array(
            'name' => 'primary_address_city',
            'vname' => 'LBL_PRIMARY_ADDRESS_CITY',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'The city used for primary',
            'group' => 'primary_address',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),

        'primary_address_state' =>
        array(
            'name' => 'primary_address_state',
            'vname' => 'LBL_PRIMARY_ADDRESS_STATE',
            'type' => 'varchar',
            'len' => '100',
            'group' => 'primary_address',
            'comment' => 'The state used for primary',
            'merge_filter' => 'enabled',
            'massupdate' => false,
          ),

        'primary_address_postalcode' =>
        array(
            'name' => 'primary_address_postalcode',
            'vname' => 'LBL_PRIMARY_ADDRESS_POSTALCODE',
            'type' => 'varchar',
            'len' => '20',
            'group' => 'primary_address',
            'comment' => 'The postal code used for primary',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),

        'primary_address_country' =>
        array(
            'name' => 'primary_address_country',
            'vname' => 'LBL_PRIMARY_ADDRESS_COUNTRY',
            'type' => 'varchar',
            'group' => 'primary_address',
            'comment' => 'The country used for the primary address',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),
        
        'phone_mobile' =>
        array(
            'name' => 'phone_mobile',
            'vname' => 'LBL_PHONE_MOBILE',
            'type' => 'varchar',
            'comment' => 'Mobile phone number',
            'merge_filter' => 'enabled',
            'massupdate' => false,
            'required' => true,
        ),
        
        'phone_home' =>
         array(
            'name' => 'phone_home',
            'vname' => 'LBL_PHONE_HOME',
            'type' => 'varchar',
            'comment' => 'Home phone number',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),
        'phone_work' =>
         array(
            'name' => 'phone_work',
            'vname' => 'LBL_PHONE_WORK',
            'type' => 'varchar',
            'comment' => 'Work phone number',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),

        'email_address' =>
         array(
            'name' => 'email_address',
            'vname' => 'LBL_EMAIL_ADDRESS',
            'type' => 'varchar', 
            'comment' => 'Email address',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),
        
        'booked_date' =>
		array(
			'name' => 'booked_date',
			'vname' => 'LBL_BOOKED_DATE',
			'type' => 'datetimecombo',
            'dbType' => 'datetime',
			'required' => true,
			'comment' => 'Date in which participant book camp',
			'importable' => 'required',
			'display_default' => 'now',
			'enable_range_search' => true,
			'options' => 'date_range_search_dom',
		),
        
        'medical' =>
         array(
            'name' => 'medical',
            'vname' => 'LBL_MEDICAL',
            'type' => 'varchar', 
            'comment' => 'Field to hold medical information about participants',
            'type'=>'text',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),
        
        'allergy' =>
         array(
            'name' => 'allergy',
            'vname' => 'LBL_ALLERGY',
            'type' => 'varchar', 
            'comment' => 'Field to hold allergy information about participants',
            'type'=>'text',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),
        'booked_date' =>
		array(
			'name' => 'booked_date',
			'vname' => 'LBL_BOOKED_DATE',
			'type' => 'datetimecombo',
            'dbType' => 'datetime',
			'required' => true,
			'comment' => 'Date in which group was booked',
			'importable' => 'required',
			'display_default' => 'now',
			'enable_range_search' => true,
			'options' => 'date_range_search_dom',
		),
        'club' =>
         array(
            'name' => 'club',
            'vname' => 'LBL_CLUB',
            'type' => 'varchar', 
            'comment' => 'Name of club or group',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),
        'camp_clinic' =>
         array(
            'name' => 'camp_clinic',
            'vname' => 'LBL_CAMP_CLINIC',
            'type' => 'varchar', 
            'comment' => 'Camp/Clinic to attend',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),
        //linked camp
        'camp_id' =>
        array (
            'name' => 'camp_id',
            'rname' => 'id',
            'id_name' => 'camp_id',
            'vname' => 'LBL_CAMP',
            'type' => 'camp_name',
            'table' => 'camps',
            'group'=>'camp_name',
            'dbType' => 'id',
            'reportable'=>true,
            'massupdate' => false,
        ),
        'camp_name' => 
        array (
            'name' => 'camp_name',
            'vname' => 'LBL_CAMP',
            'type' => 'relate',
            'reportable'=>false,
            'source'=>'non-db',
            'rname'=>'name',
            'table' => 'camps',
            'id_name' => 'camp_id',
            'module'=>'Camps',
            'link'=>'camp_link',
            'duplicate_merge'=>'disabled',
            'massupdate' => false,
        ),  
        'camp_link' =>
        array (
            'name' => 'camp_link',
            'type' => 'link',
            'relationship' => 'booking_camp',
            'vname' => 'LBL_CAMP',
            'link_type' => 'one',
            'module'=>'Camps',
            'bean_name'=>'Camp',
            'source'=>'non-db',
        ),    
        
		//linked contact
		'contact_id' =>
		array(
			'name' => 'contact_id',
			'type' => 'id',
			'group' => 'contact_name',
			'reportable' => false,
			'vname' => 'LBL_CONTACT_ID',
		),
		'contact_name' =>
		array(
			'name' => 'contact_name',
			'rname' => 'name',
            'source' => 'non-db',
            'group' => 'contact_name',
			'vname' => 'LBL_CONTACT_NAME',
			'reportable' => false,
			'id_name' => 'contact_id',
			'type' => 'relate',
			'module' => 'Contacts',
            'link' => 'related_contact',
			'table' => 'contacts',
		),
		'related_contact' => array(
			'name' => 'related_contact',
			'type' => 'link',
			'relationship' => 'booking_contact',
			'source' => 'non-db',
			'vname' => 'LBL_RELATED_CONTACT',
		),    
        
        'participant_lines' => array (
            'required' => false,
            'name' => 'participant_lines',
            'vname' => 'LBL_PARTICIPANT_LINES',
            'type' => 'function',
            'source' => 'non-db',
            'massupdate' => 0,
            'importable' => 'false',
            'duplicate_merge' => 'disabled',
            'duplicate_merge_dom_value' => 0,
            'audited' => false,
            'reportable' => false,
            'function' => array (
                'name' => 'display_participant_lines',
                'returns' => 'html',
                'include' => 'modules/CampGroupBookings/participantLines.php'
            ),
        ),
        
        'participants' =>
		array(
			'name' => 'participants',
			'type' => 'link',
			'relationship' => 'participants_campgroupbooking',
			'module' => 'CampParticipants',
			'bean_name' => 'CampParticipant',
			'source' => 'non-db',
			'vname' => 'LBL_CAMPGROUPBOOKINGS',
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
        
        
        'bookings_camp' => array(
            'lhs_module'=> 'Camps', 'lhs_table'=> 'camps', 'lhs_key' => 'id',
            'rhs_module'=> 'CampGroupBookings', 'rhs_table'=> 'campgroupbookings', 'rhs_key' => 'camp_id',
            'relationship_type'=>'one-to-many'),
        
        
        
	),
	'optimistic_lock' => true,
	'optimistic_locking' => true,
);
VardefManager::createVardef($module_name, $bean_name, array('default', 'assignable'));

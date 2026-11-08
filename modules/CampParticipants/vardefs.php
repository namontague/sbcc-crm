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


$module_name = 'CampParticipants';
$bean_name = 'CampParticipant';
$_bean_name = 'campparticipant';
$table_name = 'campparticipants';

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
        'phone_other' =>
         array(
            'name' => 'phone_other',
            'vname' => 'LBL_PHONE_OTHER',
            'type' => 'varchar',
            'comment' => 'Other phone number',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),
        'participant_age' =>
         array(
            'required' => true,
            'name' => 'participant_age',
            'vname' => 'LBL_AGE',
            'type' => 'int', 
            'comment' => 'Age of participant',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),
        
//        'email_address' =>
//         array(
//            'name' => 'email_address',
//            'vname' => 'LBL_EMAIL_ADDRESS',
//            'type' => 'varchar', 
//            'comment' => 'Email address',
//            'merge_filter' => 'enabled',
//            'massupdate' => false,
//        ),
        
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
        
        'how_booked' =>
         array(
            'name' => 'how_booked',
            'vname' => 'LBL_HOW_BOOKED',
            'type' => 'enum',
            'options' => 'camp_how_booked_dom', 
            'comment' => 'How the participant heard of sbcc',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),
        
        'medical_condition' =>
         array(
            'name' => 'medical_condition',
            'vname' => 'LBL_MEDICAL_CONDITION',
            'type' => 'enum',
            'options' => 'camp_medical_condition_dom', 
            'comment' => 'No/Yes options used for group bookings',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),
        
        'allergy' =>
         array(
            'name' => 'allergy',
            'vname' => 'LBL_ALLERGY',
            'type' => 'enum',
            'options' => 'camp_allergy_dom', 
            'comment' => 'SBCC Allergy Policy',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),
        
        'medical' =>
         array(
            'name' => 'medical',
            'vname' => 'LBL_MEDICAL',
            'type' => 'varchar', 
            'comment' => 'Field to hold medical information about participant',
            'type'=>'text',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),
        
        'comments' =>
         array(
            'name' => 'comments',
            'vname' => 'LBL_COMMENTS',
            'type' => 'varchar', 
            'comment' => 'Field to hold any comments entered by participant',
            'type'=>'text',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),
        'payment_type' =>
         array(
            'name' => 'payment_type',
            'vname' => 'LBL_PAYMENT_TYPE',
            'type' => 'enum',
            'options' => 'camp_payment_type_dom', 
            'comment' => 'How the participant paid of sbcc',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),
         'amount_paid' =>
         array(
            'name' => 'amount_paid',
            'vname' => 'LBL_AMOUNT_PAID',
            'type' => 'varchar', 
            'comment' => 'Amount paid for participant',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),
        'amount_owing' =>
         array(
            'name' => 'amount_owing',
            'vname' => 'LBL_AMOUNT_OWING',
            'type' => 'varchar', 
            'comment' => 'Amount owing for participant',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),
        'date_paid' =>
		array(
			'name' => 'date_paid',
			'vname' => 'LBL_DATE_PAID',
			'type' => 'datetimecombo',
            'dbType' => 'datetime',
			'required' => false,
			'comment' => 'Date in which participant paid for camp',
			'importable' => 'required',
			//'display_default' => 'now',
			'enable_range_search' => true,
			'options' => 'date_range_search_dom',
		),
         'confirmed' =>
         array(
            'name' => 'confirmed',
            'vname' => 'LBL_CONFIRMED',
            'comment' => 'booking confirmed',
            'type'=>'bool',
            'default' => '0',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),
        
        'purchase_cap' =>
         array(
            'name' => 'purchase_cap',
            'vname' => 'LBL_CAP',
            'type' => 'varchar', 
            'comment' => 'Cricket Camp Cap purchase',
            'type'=>'bool',
            'merge_filter' => 'enabled',
            'default' => '0',
            'massupdate' => false,
        ),
        'purchase_photo' =>
         array(
            'name' => 'purchase_photo',
            'vname' => 'LBL_PHOTO',
            'type' => 'varchar', 
            'comment' => 'Signed Group Photo purchase',
            'type'=>'bool',
            'merge_filter' => 'enabled',
            'default' => '0',
            'massupdate' => false,
        ),
        'purchase_ball' =>
         array(
            'name' => 'purchase_ball',
            'vname' => 'LBL_BALL',
            'type' => 'bool', 
            'comment' => 'Ball purchase',
            'merge_filter' => 'enabled',
            'default' => '0',
            'massupdate' => false,
        ),
        'purchase_mini_bat' =>
         array(
            'name' => 'purchase_mini_bat',
            'vname' => 'LBL_MINI_BAT',
            'type' => 'bool', 
            'comment' => 'Mini Bat purchase',
            'merge_filter' => 'enabled',
            'default' => '0',
            'massupdate' => false,
        ),
        'purchase_academy' =>
         array(
            'name' => 'purchase_academy',
            'vname' => 'LBL_ACADEMY',
            'type' => 'bool', 
            'comment' => 'Shaun Brown Cricket Academy purchase',
            'merge_filter' => 'enabled',
            'default' => '0',
            'massupdate' => false,
        ),
        'additional_purchases' =>
         array(
            'name' => 'additional_purchases',
            'vname' => 'LBL_ADDITIONAL_PURCHASES',
            'comment' => 'Field to hold string of additional purchases',
            'type' => 'varchar',
        ),
        
        'confirmation' =>
         array(
            'name' => 'confirmation',
            'vname' => 'LBL_CONFIRMATION',
            'comment' => 'confirmation of the booking',
            'type'=>'bool',
            'default' => '0',
            'merge_filter' => 'enabled',
            'massupdate' => false,
        ),
        
        'cb_create_contact' =>
         array(
            'name' => 'cb_create_contact',
            'vname' => 'LBL_CREATE_CONTACT',
            'comment' => 'create contact is black',
            'type'=>'bool',
            'source' => 'non-db',
            'default' => '0',
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
            'relationship' => 'participants_camp',
            'vname' => 'LBL_CAMP',
            'link_type' => 'one',
            'module'=>'Camps',
            'bean_name'=>'Camp',
            'source'=>'non-db',
        ),    
        
        //linked camp group
        'campgroup_id' =>
        array (
            'name' => 'campgroup_id',
            'rname' => 'id',
            'id_name' => 'campgroup_id',
            'vname' => 'LBL_CAMPGROUPS_ID',
            'type' => 'campgroup_name',
            'table' => 'campgroups',
            'group'=>'campgroup_name',
            'dbType' => 'id',
            'reportable'=>true,
            'massupdate' => false,
        ),
        'campgroup_name' => 
        array (
            'name' => 'campgroup_name',
            'vname' => 'LBL_CAMPGROUPS',
            'type' => 'relate',
            'reportable'=>false,
            'source'=>'non-db',
            'rname'=>'name',
            'table' => 'campgroups',
            'id_name' => 'campgroup_id',
            'module'=>'CampGroups',
            'link'=>'campgroup_link',
            'duplicate_merge'=>'disabled',
            'massupdate' => false,
        ),  
        'campgroup_link' =>
        array (
            'name' => 'campgroup_link',
            'type' => 'link',
            'relationship' => 'participants_campgroup',
            'vname' => 'LBL_CAMPGROUPS_LINK',
            'link_type' => 'one',
            'module'=>'CampGroups',
            'bean_name'=>'CampGroup',
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
            'metadata' => 'popupdefs_custom'
		),
		'related_contact' => array(
			'name' => 'related_contact',
			'type' => 'link',
			'relationship' => 'participants_contact',
			'source' => 'non-db',
			'vname' => 'LBL_RELATED_CONTACT',
		),    
        
        //linked camp group booking
        'campgroupbooking_id' =>
        array (
            'name' => 'campgroupbooking_id',
            'rname' => 'id',
            'id_name' => 'campgroupbooking_id',
            'vname' => 'LBL_CAMPGROUPBOOKINGS_ID',
            'type' => 'campgroupbooking_name',
            'table' => 'campgroupbookings',
            'group'=>'campgroupbooking_name',
            'dbType' => 'id',
            'reportable'=>true,
            'massupdate' => false,
        ),
        'campgroupbooking_name' => 
        array (
            'name' => 'campgroupbooking_name',
            'vname' => 'LBL_CAMPGROUPBOOKINGS',
            'type' => 'relate',
            'reportable'=>false,
            'source'=>'non-db',
            'rname'=>'name',
            'table' => 'campgroupbookings',
            'id_name' => 'campgroupbooking_id',
            'module'=>'CampGroupBookings',
            'link'=>'campgroup_link',
            'duplicate_merge'=>'disabled',
            'massupdate' => false,
        ),  
        'campgroupbooking_link' =>
        array (
            'name' => 'campgroupbooking_link',
            'type' => 'link',
            'relationship' => 'participants_campgroupbooking',
            'vname' => 'LBL_CAMPGROUPBOOKINGS_LINK',
            'link_type' => 'one',
            'module'=>'CampGroupBookings',
            'bean_name'=>'CampGroupBooking',
            'source'=>'non-db',
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
        
        'email1' => array(
            'name'        => 'email1',
            'vname'        => 'LBL_EMAIL_ADDRESS',
            'group'=>'email1',
            'type'        => 'varchar',
            'function'    => array(
            'name'        => 'getEmailAddressWidget',
               'returns'    => 'html'
            ),
            'source'    => 'non-db',
            'studio' => array('editField' => true),
        ),
  
        'email_addresses_primary' => array(
            'name' => 'email_addresses_primary',
            'type' => 'link',
            'relationship' => strtolower($module_name).'_email_addresses_primary',
            'source' => 'non-db',
            'vname' => 'LBL_EMAIL_ADDRESS_PRIMARY',
            'duplicate_merge' => 'disabled',
        ),
  
        'email_addresses' => array (
            'name' => 'email_addresses',
            'type' => 'link',
            'relationship' => strtolower($module_name).'_email_addresses',
            'source' => 'non-db',
            'vname' => 'LBL_EMAIL_ADDRESSES',
            'reportable'=>false,
            'unified_search' => true,
            'rel_fields' => array('primary_address' => array('type'=>'bool')),
        ),
            
            //new fields 11th nov 2014
            
        'friend_first_name' =>
         array(
            'name' => 'friend_first_name',
            'vname' => 'LBL_FRIEND_FIRST_NAME',
            'comment' => 'Field to friends first name',
            'type' => 'varchar',
        ),
        'friend_surname' =>
         array(
            'name' => 'friend_surname',
            'vname' => 'LBL_FRIEND_SURNAME',
            'comment' => 'Field to friends surname',
            'type' => 'varchar',
        ),
        'friend_fullname' =>
         array(
            'name' => 'friend_fullname',
            'vname' => 'LBL_FRIEND_FULLNAME',
            'comment' => 'Field to friends full name',
            'type' => 'varchar',
        ),
        'club_name' =>
         array(
            'name' => 'club_name',
            'vname' => 'LBL_CLUB_NAME',
            'comment' => 'Field to hold string of club name',
            'type' => 'varchar',
        ),

        'gender' =>
          array(
            'name' => 'gender',
            'vname' => 'LBL_GENDER',
            'type' => 'enum',
            'options' => 'gender_dom',
            'comment' => 'Gender'
          ),
	),
	'relationships' => array(
		'securitygroups_' . $_bean_name => array(
			'lhs_module' => 'SecurityGroups', 'lhs_table' => 'securitygroups', 'lhs_key' => 'id',
			'rhs_module' => $module_name, 'rhs_table' => $table_name, 'rhs_key' => 'id',
			'relationship_type' => 'many-to-many',
			'join_table' => 'securitygroups_records', 'join_key_lhs' => 'securitygroup_id', 'join_key_rhs' => 'record_id',
			'relationship_role_column' => 'module', 'relationship_role_column_value' => $module_name),
        
        
        'participants_camp' => array('lhs_module'=> 'Camps', 'lhs_table'=> 'camps', 'lhs_key' => 'id',
            'rhs_module'=> 'CampParticipants', 'rhs_table'=> 'campparticipants', 'rhs_key' => 'camp_id',
            'relationship_type'=>'one-to-many'),
        
        'participants_campgroup' => array('lhs_module'=> 'CampGroups', 'lhs_table'=> 'campgroups', 'lhs_key' => 'id',
            'rhs_module'=> 'CampParticipants', 'rhs_table'=> 'campparticipants', 'rhs_key' => 'campgroup_id',
            'relationship_type'=>'one-to-many'),
        
        'participants_campgroupbooking' => array('lhs_module'=> 'CampGroupBookings', 'lhs_table'=> 'campgroupbookings', 'lhs_key' => 'id',
            'rhs_module'=> 'CampParticipants', 'rhs_table'=> 'campparticipants', 'rhs_key' => 'campgroupbooking_id',
            'relationship_type'=>'one-to-many'),
        
        strtolower($module_name).'_email_addresses' => array(
            'lhs_module'=> $module_name, 
            'lhs_table'=> strtolower($module_name), 
            'lhs_key' => 'id',
            'rhs_module'=> 'EmailAddresses', 
            'rhs_table'=> 'email_addresses', 
            'rhs_key' => 'id',
            'relationship_type'=>'many-to-many',
            'join_table'=> 'email_addr_bean_rel', 
            'join_key_lhs'=>'bean_id', 
            'join_key_rhs'=>'email_address_id', 
            'relationship_role_column'=>'bean_module',
            'relationship_role_column_value'=>$module_name
        ),

        strtolower($module_name).'_email_addresses_primary' => array(
            'lhs_module'=> $module_name, 
            'lhs_table' => strtolower($module_name), 
            'lhs_key' => 'id',
            'rhs_module' => 'EmailAddresses', 
            'rhs_table' => 'email_addresses', 
            'rhs_key' => 'id',
            'relationship_type'=>'many-to-many',
            'join_table'=> 'email_addr_bean_rel', 
            'join_key_lhs'=>'bean_id', 
            'join_key_rhs'=>'email_address_id', 
            'relationship_role_column'=>'primary_address', 
            'relationship_role_column_value'=>'1'
        ),
        
        
        
        
        
	),
	'optimistic_lock' => true,
	'optimistic_locking' => true,
);
VardefManager::createVardef($module_name, $bean_name, array('default', 'assignable','person'));

//extend person for email capabilities 
//unset unwanted person fields
unset($dictionary[$bean_name]['fields']['title']);
unset($dictionary[$bean_name]['fields']['department']);
unset($dictionary[$bean_name]['fields']['do_not_call']);
unset($dictionary[$bean_name]['fields']['phone_fax']);
unset($dictionary[$bean_name]['fields']['alt_address_building_type']);
unset($dictionary[$bean_name]['fields']['alt_address_number']);
unset($dictionary[$bean_name]['fields']['alt_address_street']);
unset($dictionary[$bean_name]['fields']['alt_address_city']);
unset($dictionary[$bean_name]['fields']['alt_address_state']);
unset($dictionary[$bean_name]['fields']['alt_address__postalcode']);
unset($dictionary[$bean_name]['fields']['alt_address_country']);
unset($dictionary[$bean_name]['fields']['assistant']);
unset($dictionary[$bean_name]['fields']['assistant_phone']);


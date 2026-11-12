<?php 
 //WARNING: The contents of this file are auto-generated


// SBCC - search-only field: filter contacts by the association of their linked account(s).
// Options are the unique association values on accounts; the where clause is a
// subquery in modules/Contacts/metadata/SearchFields.php.
$dictionary['Contact']['fields']['account_association'] =
    array(
        'name' => 'account_association',
        'vname' => 'LBL_ACCOUNT_ASSOCIATION',
        'type' => 'enum',
        'source' => 'non-db',
        'function' => array(
            'name' => 'get_account_association_options',
            'include' => 'custom/modules/Contacts/AccountAssociationOptions.php',
            'params' => array(),
        ),
        'importable' => 'false',
        'massupdate' => false,
        'reportable' => false,
        'studio' => false,
    );


//FIELDS
$dictionary['Contact']['fields']['camps'] =
    array(
        'name' => 'camps',
        'type' => 'link',
        'relationship' => 'camp_staff',
        'module' => 'Camps',
        'bean_name' => 'camp',
        'source' => 'non-db',
        'vname' => 'LBL_CAMPS',
    );
$dictionary['Contact']['fields']['participants'] =
    array(
        'name' => 'participants',
        'type' => 'link',
        'relationship' => 'participants_contact',
        'module' => 'CampParticipants',
        'bean_name' => 'CampParticipant',
        'source' => 'non-db',
        'vname' => 'LBL_CAMPPARTICIPANTS',
    );

$dictionary['Contact']['fields']['coordinator_groupbooking'] =
    array(
        'name' => 'coordinator_groupbooking',
        'type' => 'link',
        'relationship' => 'booking_contact',
        'module' => 'CampGroupBookings',
        'bean_name' => 'CampGroupBooking',
        'source' => 'non-db',
        'vname' => 'LBL_CAMPPARTICIPANTS',
    );

$dictionary['Contact']['fields']['phone_other_alternative'] =
    array (
        'name' => 'phone_other_alternative',
        'vname' => 'LBL_OTHER_PHONE',
        'type' => 'phone',
        'dbType' => 'varchar',
        'len' => 100,
        'unified_search' => true, 
        'full_text_search' => array('boost' => 1),
        'comment' => 'Other phone number of the contact used when actual phone field is being used and other field is also used, eg: home, home (other), home (other alternative)',
        'merge_filter' => 'enabled',
    );

$dictionary['Contact']['fields']['staff_member'] =
         array(
            'name' => 'staff_member',
            'vname' => 'LBL_STATFF_MEMBER',
            'type' => 'bool', 
            'comment' => 'Flag to staff member',
            'merge_filter' => 'enabled',
            'default' => '0',
            'massupdate' => false,
        );

//RELATIONSHIPS
$dictionary['Contact']['relationships']['participants_contact'] =
    array(
        'lhs_module' => 'Contacts', 
        'lhs_table' => 'contacts', 
        'lhs_key' => 'id',

        'rhs_module' => 'CampParticipants', 
        'rhs_table' => 'campparticipants', 
        'rhs_key' => 'contact_id',
        'relationship_type' => 'one-to-many'
    );

$dictionary['Contact']['relationships']['booking_contact'] =
    array(
        'lhs_module' => 'Contacts', 
        'lhs_table' => 'contacts', 
        'lhs_key' => 'id',

        'rhs_module' => 'CampGroupBookings', 
        'rhs_table' => 'campgroupbookings', 
        'rhs_key' => 'contact_id',
        'relationship_type' => 'one-to-many'
    );



$dictionary['Contact']['fields']['do_not_sms'] =
		array (
			'name' => 'do_not_sms',
			'vname' => 'LBL_DO_NOT_SMS',
			'type' => 'bool',
			'default' => '0',
			'audited'=>true,
			'comment' => 'An indicator of whether contact can be smsed'
		);


?>
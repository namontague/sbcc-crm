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
$subpanel_layout = array(
	'top_buttons' => array(
            array('widget_class' => 'SubPanelTopCreateButton'),
            array('widget_class' => 'SubPanelTopComposeEmailButton_SBCC','linked_field' => 'campparticipants'),
	),
    
	'where' => '',
	'list_fields' => array(
    'contact_name'=>array(
      'vname' => 'LBL_CONTACT_NAME',
      'widget_class' => 'SubPanelDetailViewLink',
      'width' => '10%',
      'module' => 'Contacts',
      'sortable' => true,
    ),
    'campgroup_name'=>array(
			'vname' => 'LBL_CAMPGROUPS',
			'widget_class' => 'SubPanelDetailViewLink',
	 		'width' => '3%',
      'module' => 'CampGroups',
      'target_record_key' => 'campgroup_id',
      'target_module' => 'CampGroups',
      'sortable' => true,
		),
		'participant_age' => array(
			'vname' => 'LBL_AGE',
			'width' => '3%',
		),
    'phone_mobile' => array(
			'vname' => 'LBL_PHONE_MOBILE',
			'width' => '5%',
      //'link' => 'related_contact',     
      //'module' => 'Contacts',  
		),
    'phone_home' => array(
			'vname' => 'LBL_PHONE_HOME',
			'width' => '5%',
      //'link' => 'related_contact',     
      //'module' => 'Contacts',  
      //'target_record_key' => 'contact_id',
      //'target_module' => 'Contacts',  
		),  
    //as email is only 1 word,
    //set the size to 1% so the field only takes up as much remore as needed.
    //eg: if the default width is 15% but all the displayed address only fill 8% we loss 7%.
    //    if the default width is 15% but an address takes up 20% the columm is extended anyway as its 1 word.
    //So setting to 1% will help when the displayed addresses are less than orginal default size of 15%
    'email1'=>array(
			'name'=>'email1',		
			'vname' => 'LBL_EMAIL',
			'widget_class' => 'SubPanelEmailLink',
			'width' => '1%',
		),
    'booked_date' => array(
			'vname' => 'LBL_BOOKED_DATE',
			'width' => '8%',
		),
    'amount_paid' => array(
			'vname' => 'LBL_AMOUNT_PAID',
			'width' => '5%',
		),
    'amount_owing' => array(
			'vname' => 'LBL_AMOUNT_OWING',
			'width' => '5%',
		),
    'confirmed' => array(
			'vname' => 'LBL_CONFIRMED',
			'width' => '5%',
		),
    'additional_purchases' => array(
			'vname' => 'LBL_ADDITIONAL_PURCHASES',
			'width' => '5%',
                        'sortable'=>true,
		),
    'confirmation' => array(
			'vname' => 'LBL_CONFIRMATION',
			'width' => '5%',
		),        

    'medical' => array(
			'vname' => 'LBL_MEDICAL',
			'width' => '20%',
		),
    'comments' => array(
			'vname' => 'LBL_COMMENTS',
			'width' => '20%',
		),
    'friend_fullname' => array(
			'vname' => 'LBL_FRIEND_FULLNAME',
      'width' => '5%',
      'sortable'=>true,
		),
    'club_name' => array(
			'vname' => 'LBL_CLUB_NAME',
			'width' => '5%',
      'sortable'=>true,
		),
		'gender' => array(
			'vname' => 'LBL_GENDER',
			'width' => '5%',
			'sortable'=>true,
		),


		'edit_button' => array(
			'widget_class' => 'SubPanelPopupEditButton',
			'module' => $module_name,
			'width' => '5%',
		),
		'delete_button' => array(
			'widget_class' => 'SubPanelDeleteButton',
			'module' => $module_name,
			'width' => '5%',
		),
	),
);
?>

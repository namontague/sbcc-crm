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
        array(
            'widget_class' => 'SubPanelTopSelectButton',
            'popup_module' => 'CampParticipants', 
            'mode' => 'MultiSelect',
            'metadata' => 'popupdefs_camp',
            'initial_filter_fields' => array('camp_id' => 'camp_id_advanced','camp_name' => 'camp_name_advanced'),
        ),
    ),

	'where' => '',
	'list_fields' => array(
		
        //        'contact_name'=>array(
//			'vname' => 'LBL_CONTACT_NAME',
//			'widget_class' => 'SubPanelDetailViewLink',
//	 		'width' => '10%',
//            'module' => 'Contacts',
//            'sortable' => true,
//		),
        'first_name'=>array(
			'name'=>'first_name',
			'usage' => 'query_only',
		),
		'last_name'=>array(
			'name'=>'last_name',
		 	'usage' => 'query_only',
		),
		'salutation'=>array(
			'name'=>'salutation',
		 	'usage' => 'query_only',
		),
		'name'=>array(
			'name'=>'name',		
			'vname' => 'LBL_CONTACT_NAME',
			'widget_class' => 'SubPanelDetailViewLink',
		 	'module' => 'Contacts',
            'width' => '10%',
        ),
        'participant_age' => array(
			'vname' => 'LBL_AGE',
			'width' => '3%',
		),
        'phone_mobile' => array(
			'vname' => 'LBL_PHONE_MOBILE',
			'width' => '5%',
      'link' => 'related_contact',      
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
            'sortable'=>false,
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
		'edit_button' => array(
			'widget_class' => 'SubPanelEditButton',
			'module' => $module_name,
			'width' => '4%',
		),
		'remove_button' => array(
			'widget_class' => 'SubPanelRemoveButton',
			'module' => $module_name,
			'width' => '5%',
		),
	),
);
?>

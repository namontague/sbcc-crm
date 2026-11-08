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
$subpanel_layout = array(
	'top_buttons' => array(
            array('widget_class' => 'SubPanelTopCreateButton'),
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
        'phone_mobile' => array(
			'vname' => 'LBL_PHONE_MOBILE',
			'width' => '5%',
		),
        'phone_home' => array(
			'vname' => 'LBL_PHONE_HOME',
			'width' => '5%',
		),
        'phone_work' => array(
			'vname' => 'LBL_PHONE_WORK',
			'width' => '5%',
		),
       
        //as email is only 1 word,
        //set the size to 1% so the field only takes up as much remore as needed.
        //eg: if the default width is 15% but all the displayed address only fill 8% we loss 7%.
        //    if the default width is 15% but an address takes up 20% the columm is extended anyway as its 1 word.
        //So setting to 1% will help when the displayed addresses are less than orginal default size of 15%
        'email_address' => array(
			'vname' => 'LBL_EMAIL_ADDRESS',
			'width' => '1%',
		),
        'booked_date' => array(
			'vname' => 'LBL_BOOKED_DATE',
			'width' => '8%',
		),
        'medical' => array(
			'vname' => 'LBL_MEDICAL',
			'width' => '20%',
		),
        'allergy' => array(
			'vname' => 'LBL_ALLERGY',
			'width' => '20%',
		),
        
		'edit_button' => array(
			'widget_class' => 'SubPanelEditButton',
			'module' => $module_name,
			'width' => '5%',
		),
//		'remove_button' => array(
//			'widget_class' => 'SubPanelRemoveButton',
//			'module' => $module_name,
//			'width' => '5%',
//		),
	),
);
?>

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
$listViewDefs ['CampGroupBookings'] = array(
			'NAME' =>
			array(
				'width' => '20%',
				'label' => 'LBL_CONTACT_NAME',
				'default' => true,
				'link' => true,
            ),
            'CAMP_NAME' => array(
				'width' => '25%',
				'label' => 'LBL_CAMP',
				'link' => true,
				'id' => 'CAMP_ID',
				'module' => 'Camps',
				'default' => true,
				'ACLTag' => 'CAMPS',
				'related_fields' => array('camp_id')
			),
            'PHONE_MOBILE' => array(
                'label' => 'LBL_PHONE_MOBILE',
                'width' => '5%',
                'default' => true,
            ),
            'PHONE_HOME' => array(
                'label' => 'LBL_PHONE_HOME',
                'width' => '5%',
                'default' => true,
            ),
            'PHONE_WORK' => array(
                'label' => 'LBL_PHONE_WORK',
                'width' => '5%',
                'default' => true,
            ),
            
            //as email is only 1 word,
            //set the size to 1% so the field only takes up as much remore as needed.
            //eg: if the default width is 15% but all the displayed address only fill 8% we loss 7%.
            //    if the default width is 15% but an address takes up 20% the columm is extended anyway as its 1 word.
            //So setting to 1% will help when the displayed addresses are less than orginal default size of 15%
            'EMAIL_ADDRESS' => array(
                'label' => 'LBL_EMAIL_ADDRESS',
                'width' => '1%',
                'default' => true,
            ),
            'BOOKED_DATE' => array(
                'label' => 'LBL_BOOKED_DATE',
                'width' => '8%',
                'default' => true,
            ),
            'MEDICAL' => array(
                'label' => 'LBL_MEDICAL',
                'width' => '20%',
                'default' => true,
            ),
            'ALLERGY' => array(
                'label' => 'LBL_ALLERGY',
                'width' => '20%',
                'default' => true,
            ),
        
);
?>

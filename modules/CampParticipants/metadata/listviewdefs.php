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
$listViewDefs ['CampParticipants'] = array(
            'CONTACT_NAME' => array(
              'width' => '20%',
              'label' => 'LBL_CONTACT_NAME',
              'default' => true,
              'link' => true,
              'module' => 'CampParticipants',
              'target_record_key' => 'id',
              'target_module' => 'CampParticipants',  
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
            'CAMPGROUP_NAME' => array(
                'width' => '5%', 
                'link' => true,
                'label' => 'LBL_CAMPGROUPS',
                'id' => 'CAMPGROUP_ID',
                'module' => 'CampGroups',
                'related_fields' => array('campgroup_id'),
                'default' => true
             ),
    
            'PARTICIPANT_AGE' => array(
                'label' => 'LBL_AGE',
                'width' => '3%',
                'default' => true,
            ),
            'GENDER' => array(
              'label' => 'LBL_GENDER',
              'width' => '5%',
              'default'=>true,
            ),
            'PHONE_MOBILE' => array(
                'label' => 'LBL_PHONE_MOBILE',
                'width' => '5%',
                'default' => true,
            ),
            //as email is only 1 word,
            //set the size to 1% so the field only takes up as much remore as needed.
            //eg: if the default width is 15% but all the displayed address only fill 8% we loss 7%.
            //    if the default width is 15% but an address takes up 20% the columm is extended anyway as its 1 word.
            //So setting to 1% will help when the displayed addresses are less than orginal default size of 15%
//            'EMAIL1' => array(
//                'label' => 'LBL_EMAIL',
//                'width' => '1%',
//                'default' => true,
//            ),
            'EMAIL1' => array(
              'width' => '1%', 
              'label' => 'LBL_EMAIL',
              'sortable' => true,
              'link' => true,
              'customCode' => '{$EMAIL1_LINK}{$EMAIL1}</a>',
              'default' => true
            ),  
            'BOOKED_DATE' => array(
                'label' => 'LBL_BOOKED_DATE',
                'width' => '8%',
                'default' => true,
            ),
            'AMOUNT_PAID' => array(
                'label' => 'LBL_AMOUNT_PAID',
                'width' => '5%',
                'default' => true,
            ),
            'AMOUNT_OWING' => array(
                'label' => 'LBL_AMOUNT_OWING',
                'width' => '5%',
                'default' => true,
            ),
            'CONFIRMED' => array(
                'label' => 'LBL_CONFIRMED',
                'width' => '5%',
                'default' => true,
            ),
            'ADDITIONAL_PURCHASES' => array(
                'label' => 'LBL_ADDITIONAL_PURCHASES',
                'width' => '5%',
                'default' => true,
            ),
            'CONFIRMATION' => array(
                'label' => 'LBL_CONFIRMATION',
                'width' => '5%',
                'default' => true,
            ),        
            'MEDICAL' => array(
                'label' => 'LBL_MEDICAL',
                'width' => '20%',
                'default' => true,
            ),
            'COMMENTS' => array(
                'label' => 'LBL_COMMENTS',
                'width' => '20%',
                'default' => true,
            ),
            'CLUB_NAME' => array(
                'label' => 'LBL_CLUB_NAME',
                'width' => '5%',
                'default'=>true,
            ),


);
?>

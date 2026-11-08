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

$viewdefs[$module_name]['DetailView'] = array(
    'templateMeta' => array(
        'form' => array('buttons' => array('EDIT', 'DUPLICATE', 'DELETE')),
        'maxColumns' => '2',
        'widths' => array(
            array('label' => '10', 'field' => '30'),
            array('label' => '10', 'field' => '30')
        ),
    ),
    'panels' => array(
        'lbl_contact_details' =>
        array(
            array(
                'contact_name', 
                array(
                    'name'=> 'phone_mobile',
                    'label'=> 'LBL_PHONE_MOBILE',
                    'link' => false,
                ),
            ),
            array(
                'gender'
            ),
            array(
                'participant_age', 
                array(
                    'name'=> 'phone_home',
                    'label'=> 'LBL_PHONE_HOME',
                ),
            ),
            array(
                array (
                    'name' => 'primary_address_street',
                    'label' => 'LBL_PRIMARY_ADDRESS',
                    'type' => 'address',
                    'displayParams' => 
                    array (
                      'key' => 'primary',
                    ),
                ),
                array(
                    'name'=> 'phone_work',
                    'label'=> 'LBL_PHONE_WORK',
                )
            ),
            array(
                 array (
                    'name' => 'email1',
                    'studio' => 'false',
                    'label' => 'LBL_EMAIL',
                ),
                array(
                    'name'=> 'phone_other',
                    'label'=> 'LBL_PHONE_OTHER',
                ),
            ),
          ),
          'lbl_participant_details' => array(
            array(
                'camp_name','campgroupbooking_name'
            ),
            array(
                'campgroup_name'
            ),
            array(
                'how_booked',
            ),
            array(
                'allergy',
            ),
            array(
                'medical',
                'comments',
            ),
            array(
                'friend_fullname',
                'club_name',
            ),
            array(
                'confirmation',
            ),
        ),
        'lbl_purchase_details' => array(
//            array(
//                'additional_purchases'
//            ),
            array(
                'purchase_cap',''
            ),
            array(
                'purchase_photo',''
            ),
            array(
                'purchase_mini_bat',''
            ),
            array(
                'purchase_ball',''
            ),
            array(
                'purchase_academy',''
            ),
            
        ),
        'lbl_financial_details' => array(
            array(
                'booked_date',''
            ),
            array(
                'payment_type',''
            ),
            array(
                'amount_paid',''
            ),
            array(
                'amount_owing',''
            ),
            array(
                'date_paid',''
            ),
            array(
                'confirmed',''
            ),
            
        ),
    ),
);
?>

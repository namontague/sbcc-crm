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
        'lbl_panel_details' =>
        array(
            array(
                'camp_name','camp_clinic'
            ),
            array(
                'contact_name','club'
                
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
                
            ),
            array(
                'phone_mobile',
                'phone_home'
            ),
            array(
                'phone_work',
                'phone_other',
                
            ),
            array(
                'email_address',
            ),
            array(
                'medical',
                'allergy',
            ),
            array(
                'booked_date'
            )
        ),
        //use subpanel to display these
//        'lbl_participant_details' => array(
//        
//            
//        ),
    ),
);
?>

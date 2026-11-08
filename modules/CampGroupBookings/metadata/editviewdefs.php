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
$viewdefs [$module_name]['EditView'] = array(
    'templateMeta' => array(
        'maxColumns' => '2',
        'widths' => array(
            array('label' => '10', 'field' => '30'),
            array('label' => '10', 'field' => '30')
        ),

    ),
    'panels' => array(
        'lbl_booking_details' => array(
            array(
                'camp_name','camp_clinic'
            ),
            array(
                array(
                    'name'=>'contact_name',
                    'displayParams' => array(
                        'field_to_name_array' => array(
                            'id' => 'contact_id',
                            'name' => 'contact_name',
                            'salutation' => 'salutation',
                            'first_name' => 'first_name',
                            'last_name' => 'last_name',
                            'primary_address_number' => 'primary_address_number',
                            'primary_address_building_type' => 'primary_address_building_type',
                            'primary_address_street' => 'primary_address_street',
                            'primary_address_city' => 'primary_address_city',
                            'primary_address_state' => 'primary_address_state',
                            'primary_address_postalcode' => 'primary_address_postalcode',
                            'primary_address_country' => 'primary_address_country',
                            'primary_address_number' => 'primary_address_number',
                            'phone_mobile' => 'phone_mobile',
                            'phone_home' => 'phone_home',
                            'phone_work' => 'phone_work',
                            'phone_other' => 'phone_other',
                            'email1' => 'email_address',
                        ), 
                       'additionalFields' => array(
                            'salutation' => 'salutation',
                            'first_name' => 'first_name',
                            'last_name' => 'last_name',
                            'primary_address_number' => 'primary_address_number',
                            'primary_address_building_type' => 'primary_address_building_type',
                            'primary_address_street' => 'primary_address_street',
                            'primary_address_city' => 'primary_address_city',
                            'primary_address_state' => 'primary_address_state',
                            'primary_address_postalcode' => 'primary_address_postalcode',
                            'primary_address_country' => 'primary_address_country',
                            'primary_address_number' => 'primary_address_number',
                            'phone_mobile' => 'phone_mobile',
                            'phone_home' => 'phone_home',
                            'phone_work' => 'phone_work',
                            'phone_other' => 'phone_other',
                            'email1' => 'email_address',
                        ),
                    ),
                ),
                'club'
            ),
            
             array (
                array (
                  'name' => 'first_name',
                  'customCode' => '{html_options name="salutation" id="salutation" options=$fields.salutation.options selected=$fields.salutation.value}' 
                . '&nbsp;<input name="first_name"  id="first_name" size="25" maxlength="25" type="text" value="{$fields.first_name.value}">',
                ),
              ),
            array (
                'last_name',
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
                '',
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
        'lbl_participant_details' => array(
            array (
                'participant_lines',
            ),
        ),
    ),
);
?>

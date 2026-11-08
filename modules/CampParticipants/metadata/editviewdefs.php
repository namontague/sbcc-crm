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
$viewdefs [$module_name]['EditView'] = array(
    'templateMeta' => array(
        'maxColumns' => '2',
        'widths' => array(
            array('label' => '10', 'field' => '30'),
            array('label' => '10', 'field' => '30')
        ),
        
        'javascript' => '{if $fields.contact_id.value!=null}<link rel="stylesheet" type="text/css" href="modules/CampParticipants/css/editview.css" />{/if}
                         <script type="text/javascript" src="' . getJSPath('modules/CampParticipants/javascript/editview.js') . '"></script>',
        
    ),
    'panels' => array(
        'lbl_contact_details' => array(
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
                            'email1' => 'CampParticipants0emailAddress0',
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
                            'email1' => 'CampParticipants0emailAddress0',
                        ),
                        //'readOnlyIf' => '$fields.contact_id.value!=null'
                        
                    ),
                ),
                array(
                    'name'=> 'phone_mobile',
                    'label'=> 'LBL_PHONE_MOBILE',
                    'customCode' => '<input id="phone_mobile"  name="phone_mobile"  {if $fields.contact_id.value!=null} disabled=true style="background-color: rgb(246, 246, 246); border-color: rgb(246, 246, 246); color:#000000;"{/if} value="{$fields.phone_mobile.value}">',                 
                ),
            ),

            array (
                array (
                    'name' => 'first_name',
                    'label'=> 'LBL_FIRST_NAME',
                    'customCode' => '<input id="first_name"  name="first_name"  {if $fields.contact_id.value!=null} disabled=true style="background-color: rgb(246, 246, 246); border-color: rgb(246, 246, 246); color:#000000;"{/if} value="{$fields.first_name.value}">',
                ),
                array(
                    'name'=> 'phone_home',
                    'label'=> 'LBL_PHONE_HOME',
                    'customCode' => '<input id="phone_home"  name="phone_home"  {if $fields.contact_id.value!=null} disabled=true style="background-color: rgb(246, 246, 246); border-color: rgb(246, 246, 246); color:#000000;"{/if} value="{$fields.phone_home.value}">',

                ),
            ),
            array (              
                array(
                    'name'=> 'last_name',
                    'label'=> 'LBL_LAST_NAME',
                    'customCode' => '<input id="last_name"  name="last_name"  {if $fields.contact_id.value!=null} disabled=true style="background-color: rgb(246, 246, 246); border-color: rgb(246, 246, 246); color:#000000;"{/if} value="{$fields.last_name.value}">',

                ),
                array(
                    'name'=> 'phone_work',
                    'label'=> 'LBL_PHONE_WORK',
                    'customCode' => '<input id="phone_work"  name="phone_work"  {if $fields.contact_id.value!=null} disabled=true style="background-color: rgb(246, 246, 246); border-color: rgb(246, 246, 246); color:#000000;"{/if} value="{$fields.phone_work.value}">',

                ),
            ),
            array(
              'gender'
            ),
            array(
                array(
                    'name'=> 'participant_age', 
                    'label'=> 'LBL_AGE',
                    'displayParams' => array(
                        'readOnlyIf' => '$fields.contact_id.value!=null'
                    ),
                ),
                array(
                    'name'=> 'phone_other',
                    'label'=> 'LBL_PHONE_OTHER',
                    'customCode' => '<input id="phone_other"  name="phone_other"  {if $fields.contact_id.value!=null} disabled=true style="background-color: rgb(246, 246, 246); border-color: rgb(246, 246, 246); color:#000000;"{/if} value="{$fields.phone_other.value}">',
                    'label'=> 'LBL_PHONE_OTHER'

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
                      'readOnlyIf' => '$fields.contact_id.value!=null'  
                    ),
                ),
                array (
                    'name' => 'email1',
                    'studio' => 'false',
                    'label' => 'LBL_EMAIL',
                    'displayParams' => array(
                        'readOnlyIf' => '$fields.contact_id.value!=null'
                    ),
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
                'friend_first_name',
                'friend_surname',
            ),
            array(
                'club_name',
            ),
            array(
                'confirmation',
            ),
        ),    
        'lbl_purchase_details' => array(
            array(
                'purchase_cap',
            ),
            array(
                'purchase_photo',
            ),
            array(
                'purchase_mini_bat',
            ),
            array(
                'purchase_ball',
            ),
            array(
                'purchase_academy',
            ),
            
        ),
        'lbl_financial_details' => array(
            array(
                'booked_date',
            ),
            array(
                'payment_type',
            ),
            array(
                'amount_paid',
            ),
            array(
                'amount_owing',
            ),
            array(
                'date_paid',
            ),
            array(
                'confirmed',
            ),
            
        ),
        
    ),
			
);
?>

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

$module_name = 'Camps';

$viewdefs[$module_name]['DetailView'] = array(
    'templateMeta' => array(
        'form' => array(
            'buttons' => array(
                'EDIT', 
                'DUPLICATE', 
                'AOS_GENLET' => array (
                    'customCode' => '<input type="button" class="button" onClick="showPopup();" value="{$APP.LBL_GENERATE_PDF}">',
                ),
                'DELETE'
            )
        ),
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
                'name',
            ),
            array(
                'type',
            ),
            array(
                'start_date',
                'end_date',
            ),
            array(
                'venue',
            ),
            array(
                array (
                    'name' => 'venue_address_street',
                    'label' => 'LBL_VENUE_ADDRESS',
                    'type' => 'address',
                    'displayParams' => 
                    array (
                      'key' => 'venue',
                    ),
                ),
            ),
            
            array(
                'contact_name',
            ),
            array(
                'description',
                'assigned_user_name',
            ),
            array(
                array(
                    //'hideIf' =>'$SMS_BUTTON == null && $SMS_BUTTON ==""',  
                    'customCode' => '{$SMS_BUTTON}',
                ),
                array(
                    'customCode' => '{  $bean->groupCreate($bean) }'
                ),
            )
        ),
    ),
);
?>

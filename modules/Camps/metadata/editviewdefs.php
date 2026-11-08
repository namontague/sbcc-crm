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
$viewdefs [$module_name]['EditView'] = array(
    'templateMeta' => array(
        'maxColumns' => '2',
        'widths' => array(
            array('label' => '10', 'field' => '30'),
            array('label' => '10', 'field' => '30')
        ),

    ),
    'panels' => array(
        'default' => array(
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
                'sms_list_id',
                '',
            ),
        ),
    ),
			
);
?>

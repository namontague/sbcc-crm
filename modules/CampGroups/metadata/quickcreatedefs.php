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

$module_name = 'CampGroups';
$viewdefs [$module_name] = array(
    'QuickCreate' => array(
        'templateMeta' => array(
            'maxColumns' => '2',
            'widths' => array(
                array(
                    'label' => '10',
                    'field' => '30',
                ),
                array(
                    'label' => '10',
                    'field' => '30',
                ),
            ),
        ),
        'panels' => array(
            'lbl_panel_details' => array(
                array(
                    'camp_name',
                ),
                array(
                    'name',
                ),
                array(
                    'description',
                    'assigned_user_name',
                ),
            ),
            ),
    ),
);
?>

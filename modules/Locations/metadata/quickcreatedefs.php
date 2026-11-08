<?php
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/* * *******************************************************************************
 * (C) 2009 Enterprise Intelligence Systems Pty Ltd
 * No part of this file should be used without the express permission
 * of the authors.
 *
 * Contact: info@eintel.com.au for more information.
 *
 *
 * ****************************************************************************** */

$module_name = 'Locations';
$module_table = 'locations';
$object_name = 'Location';

$viewdefs = array(
    $module_name =>
    array(
        'QuickCreate' =>
        array(
            'templateMeta' =>
            array(
                'form' =>
                array(
                    'hidden' =>
                    array(
                        
                    ),
                ),
                'maxColumns' => '2',
                'widths' =>
                array(
                    0 =>
                    array(
                        'label' => '10',
                        'field' => '30',
                    ),
                    1 =>
                    array(
                        'label' => '10',
                        'field' => '30',
                    ),
                ),
            ),
            'panels' =>
            array(
                'LBL_DEFAULT' =>
                array(
                    array(
                        'name',
                        'description',
                    ),
                ),
            ),
        ),
    ),
);
?>


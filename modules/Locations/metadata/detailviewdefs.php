<?php
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
            array(
                'building_type'
            ),
             array(
                'number'
            ),
            array(
                'address1',
                'account_name',
            ),
            array(
                'address2',
                'phone'
            ),
            array(
                'city',
                'alternate_phone',
            ),
            array(
                'state',
                'fax',
            ),
            array(
                'postalcode',
                'isbilling',
            ),
            array(
                'country',
                'isshipping'
            ),
            array(
                '',
                'defaultinvoiceto'
            ),
            array(
                '',
                'defaultshipto'
            ),
    )
);
?>

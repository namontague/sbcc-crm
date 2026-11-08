<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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

$listViewDefs[$module_name] = array(
        'NAME' => array(
		'width' => '10',
		'label' => 'LBL_NAME',
		'default' => true,
                'link' => true
        ),
        'ACCOUNT_NAME' => array(
		'width' => '10',
		'label' => 'LBL_ACCOUNT_NAME',
		'default' => true,
                'link' => false
        ),
        'ISBILLING' => array(
		'width' => '10',
		'label' => 'LBL_BILLING',
		'default' => true,
                'link' => false
        ),
        'ISSHIPPING' => array(
		'width' => '10',
		'label' => 'LBL_SHIPPING',
		'default' => true,
                'link' => false
        ),
        'DEFAULTINVOICETO' => array(
		'width' => '10',
		'label' => 'LBL_DEFAULT_INVOICE_TO',
		'default' => true,
                'link' => false
        ),
        'DEFAULTSHIPTO' => array(
		'width' => '10',
		'label' => 'LBL_DEFAULT_SHIP_TO',
		'default' => true,
                'link' => false
        ),
);
?>

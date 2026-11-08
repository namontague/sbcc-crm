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

$popupMeta = array(
	'moduleMain' => $object_name,
	'varName' => strtoupper($module_name),
	'orderBy' => 'name',
	'whereClauses' => array(
		'name' => $module_table.'.name',
	),
	'searchInputs' => array(
		'name',
	),
);
?>



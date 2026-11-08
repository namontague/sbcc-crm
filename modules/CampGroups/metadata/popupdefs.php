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
$_module_name = strtolower($module_name);
$bean_name = 'CampGroup';

$popupMeta = array('moduleMain' => $bean_name,
	'varName' => $bean_name,
	'orderBy' => 'name',
	'whereClauses' => array(
		'name' => $_module_name . '.name',
	),
	'searchInputs' => array('name', 'type', 'status'),
	'listview' => 'modules/' . $module_name . '/metadata/listviewdefs.php',
	'search' => 'modules/' . $module_name . '/metadata/searchdefs.php',
);
?>
 


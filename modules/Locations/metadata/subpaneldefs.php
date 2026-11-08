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

$layout_defs[$module_name] = array(
	// list of what Subpanels to show in the DetailView 
	'subpanel_setup' => array(
        
         'securitygroups' => array(
             'top_buttons' => array(array('widget_class' => 'SubPanelTopSelectButton', 'popup_module' => 'SecurityGroups', 'mode' => 'MultiSelect'),),
             'order' => 900,
             'sort_by' => 'name',
             'sort_order' => 'asc',
             'module' => 'SecurityGroups',
             'refresh_page'=>1,
             'subpanel_name' => 'default',
             'get_subpanel_data' => 'SecurityGroups',
             'add_subpanel_data' => 'securitygroup_id',
             'title_key' => 'LBL_SECURITYGROUPS_SUBPANEL_TITLE',
         ),


	),
);
?>

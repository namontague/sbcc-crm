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

$subpanel_layout = array(
	'top_buttons' => array(
//		array('widget_class' => 'SubPanelTopCreateButton'),
//		array('widget_class' => 'SubPanelTopSelectButton', 'popup_module' => $module_name),
	),

	'where' => '',
	'fill_in_additional_fields' => true,
	'list_fields' => array(
		'name'=>array(
	 		'vname' => 'LBL_NAME',
			'widget_class' => 'SubPanelDetailViewLink',
	 		'width' => '45%',
		),
                
//		'date_modified'=>array(
//	 		'vname' => 'LBL_DATE_MODIFIED',
//	 		'width' => '45%',
//		),
		'edit_button'=>array(
			'widget_class' => 'SubPanelEditButton',
		 	'module' => $module_name,
	 		'width' => '4%',
		),
//		'delete_button'=>array(
//			'widget_class' => 'SubPanelDeleteButton',
//		 	'module' => $module_name,
//			'width' => '5%',
//		),
	),
);

?>

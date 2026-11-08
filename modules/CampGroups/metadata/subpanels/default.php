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
$subpanel_layout = array(
	'top_buttons' => array(
		array('widget_class' => 'SubPanelTopButtonQuickCreate'),
	),
	'where' => '',
	'list_fields' => array(
		
            'name' => array(
                'vname' => 'LBL_NAME',
                'widget_class' => 'SubPanelDetailViewLink',
                'width' => '25%',
            ),
//            'camp_name' => array(
//                'vname' => 'LBL_CAMP',
//                'widget_class' => 'SubPanelDetailViewLink',
//                'related_fields' => array('camp_id'),
//                'width' => '25%',
//            ),
		
            'edit_button' => array(
                    'widget_class' => 'SubPanelEditButton',
                    'module' => $module_name,
                    'width' => '4%',
            ),
            'remove_button' => array(
                    'widget_class' => 'SubPanelRemoveButton',
                    'module' => $module_name,
                    'width' => '5%',
            ),
	),
);
?>

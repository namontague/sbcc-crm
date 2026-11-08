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


$module_name = 'CampGroupBookings';

$layout_defs[$module_name] = array(
	// list of what Subpanels to show in the DetailView 
	'subpanel_setup' => array(
		
		'participants' => array(
			'order' => -10,
			'module' => 'CampParticipants',
			'sort_order' => 'asc',
			'sort_by' => 'participant_age',
			'subpanel_name' => 'ForGroupBookings',
			'get_subpanel_data' => 'participants',
			'title_key' => 'LBL_PARTICIPANTS_SUBPANEL_TITLE',
                        'top_buttons' => array(
//                            array(
//                                'widget_class' => 'SubPanelTopSelectButton',
//                                'popup_module' => 'CampParticipants', 
//                                'mode' => 'MultiSelect',
//                                'initial_filter_fields' => array('camp_id' => 'camp_id_advanced','camp_name' => 'camp_name_advanced'),
//                            ),
                        ),
		),
		// EINTEL Additions
		'securitygroups' => array(
			'top_buttons' => array(array('widget_class' => 'SubPanelTopSelectButton', 'popup_module' => 'SecurityGroups', 'mode' => 'MultiSelect'),),
			'order' => 900,
			'sort_by' => 'name',
			'sort_order' => 'asc',
			'module' => 'SecurityGroups',
			'refresh_page' => 1,
			'subpanel_name' => 'default',
			'get_subpanel_data' => 'SecurityGroups',
			'add_subpanel_data' => 'securitygroup_id',
			'title_key' => 'LBL_SECURITYGROUPS_SUBPANEL_TITLE',
		),
	),
);
?>

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


$module_name = 'CampParticipants';

$layout_defs[$module_name] = array(
	// list of what Subpanels to show in the DetailView 
	'subpanel_setup' => array(
		
//		'participants' => array(
//			'order' => -10,
//			'module' => 'Contacts',
//			'sort_order' => 'asc',
//			'sort_by' => 'last_name, first_name',
//			'subpanel_name' => 'default',
//			'get_subpanel_data' => 'participants',
//			'add_subpanel_data' => 'contact_id',
//			'title_key' => 'LBL_PARTICIPANTS_SUBPANEL_TITLE',
//            'top_buttons' => array(
//                array(
//                    'widget_class' => 'SubPanelTopSelectButton',
//                    'popup_module' => 'Contacts', 
//                    'mode' => 'MultiSelect'
//                ),
//            ),
//		),
//		
//		'activities' => array(
//			'order' => 90,
//			'sort_order' => 'desc',
//			'sort_by' => 'date_start',
//			'title_key' => 'LBL_ACTIVITIES_SUBPANEL_TITLE',
//			'type' => 'collection',
//			'subpanel_name' => 'activities', //this values is not associated with a physical file.
//			//'header_definition_from_subpanel' => 'meetings',
//			'module' => 'Activities',
//			'top_buttons' => array(
//				array('widget_class' => 'SubPanelTopCreateTaskButton'),
//				array('widget_class' => 'SubPanelTopScheduleMeetingButton'),
//				array('widget_class' => 'SubPanelTopScheduleCallButton'),
//			),
//			'collection_list' => array(
//				'tasks' => array(
//					'module' => 'Tasks',
//					'subpanel_name' => 'ForActivities',
//					'get_subpanel_data' => 'tasks',
//				),
//				'meetings' => array(
//					'module' => 'Meetings',
//					'subpanel_name' => 'ForActivities',
//					'get_subpanel_data' => 'meetings',
//				),
//				'calls' => array(
//					'module' => 'Calls',
//					'subpanel_name' => 'ForActivities',
//					'get_subpanel_data' => 'calls',
//				),
//			)
//		),
//		'history' => array(
//			'order' => 100,
//			'sort_order' => 'desc',
//			'sort_by' => 'date_modified',
//			'title_key' => 'LBL_HISTORY_SUBPANEL_TITLE',
//			'type' => 'collection',
//			'subpanel_name' => 'history', //this values is not associated with a physical file.
//			//'header_definition_from_subpanel' => 'meetings',
//			'module' => 'History',
//			'top_buttons' => array(
//				array(
//                                        'widget_class' => 'SubPanelTopCreateNoteButton',
//                                        'additional_form_fields_override' => array(
//                                            'account_name' => 'account_name',
//                                            'account_id' => 'account_id',
//                                            'contact_name' => 'contact_name',
//                                            'contact_id' => 'contact_id',
//                                            'assigned_user_name' => 'assigned_user_name',
//                                            'assigned_user_id' => 'assigned_user_id'
//                                        ),
//                                    ),
//				array('widget_class' => 'SubPanelTopSummaryButton'),
//			),
//			'collection_list' => array(
//				'tasks' => array(
//					'module' => 'Tasks',
//					'subpanel_name' => 'ForHistory',
//					'get_subpanel_data' => 'tasks',
//				),
//				'meetings' => array(
//					'module' => 'Meetings',
//					'subpanel_name' => 'ForHistory',
//					'get_subpanel_data' => 'meetings',
//				),
//				'calls' => array(
//					'module' => 'Calls',
//					'subpanel_name' => 'ForHistory',
//					'get_subpanel_data' => 'calls',
//				),
//				'notes' => array(
//					'module' => 'Notes',
//					'subpanel_name' => 'ForHistory',
//					'get_subpanel_data' => 'notes',
//				),
//			)
//		),
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

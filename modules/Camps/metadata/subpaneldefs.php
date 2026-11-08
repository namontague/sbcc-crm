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


$module_name = 'Camps';

$layout_defs[$module_name] = array(
	// list of what Subpanels to show in the DetailView 
	'subpanel_setup' => array(
		
		'participants' => array(     
			'order' => -10,               
            'list_max_entries' => 100,
			'module' => 'CampParticipants',
			'sort_order' => 'asc',
			//'sort_by' => 'last_name, first_name',
			'subpanel_name' => 'ForCamps',
			'get_subpanel_data' => 'campparticipants',
			'add_subpanel_data' => 'contact_id',
			'title_key' => 'LBL_PARTICIPANTS_SUBPANEL_TITLE',
//            'top_buttons' => array(
//                array(
//                    'widget_class' => 'SubPanelTopSelectButton',
//                    'popup_module' => 'Contacts', 
//                    'mode' => 'MultiSelect'
//                ),
//            ),
                ),
            
        'staff' => array(
			'order' => -10,
			'module' => 'Contacts',
			'sort_order' => 'asc',
			//'sort_by' => 'last_name, first_name',
			'subpanel_name' => 'ForCamps',
			'get_subpanel_data' => 'staff',
			'add_subpanel_data' => 'contact_id',
			'title_key' => 'LBL_STAFF_SUBPANEL_TITLE',
            'top_buttons' => array(
                array(
                    'widget_class' => 'SubPanelTopSelectButton',
                    'popup_module' => 'Contacts', 
                    'mode' => 'MultiSelect',
                    'metadata' => 'popupdefs_staff'                 
                ),
            ),
		),
		
             
        'groups' => array(
			'order' => -10,
			'module' => 'CampGroups',
			'sort_order' => 'asc',
			'sort_by' => 'name',
			'subpanel_name' => 'default',
			'get_subpanel_data' => 'campgroups',
			'add_subpanel_data' => 'camp_id',
			'title_key' => 'LBL_GROUP_SUBPANEL_TITLE',
            'top_buttons' => array(
                array(
                    'widget_class' => 'SubPanelTopButtonQuickCreate',
                    //'popup_module' => 'Contacts', 
                    //'mode' => 'MultiSelect'
                ),
            ),
		),
            
         'campgroupbookings' => array(
			'order' => -10,
			'module' => 'CampGroupBookings',
			'sort_order' => 'asc',
			'sort_by' => 'name',
			'subpanel_name' => 'ForCamps',
			'get_subpanel_data' => 'campgroupbookings',
			'add_subpanel_data' => 'camp_id',
			'title_key' => 'LBL_GROUPBOOKING_SUBPANEL_TITLE',
            'top_buttons' => array(
                array(
                    'widget_class' => 'SubPanelTopCreateButton',
                    //'popup_module' => 'Contacts', 
                    //'mode' => 'MultiSelect'
                ),
            ),
		),
        
        
        'aor_reports' => array(
            'order' => 200,
            'title_key' => 'LBL_REPORTS_SUBPANEL_TITLE',
            'module' => 'AOR_Reports',
            'subpanel_name' => 'ForModules',
            'get_subpanel_data' => 'function:get_reports_for_module_query',
            'function_parameters' => array(
                'import_function_file'=>'modules/AOR_Reports/aor_utils.php',
                'object_name'=>'Camps',
            ),
            'top_buttons' => array(),
        ),    
        
		'activities' => array(
			'order' => 90,
			'sort_order' => 'desc',
			'sort_by' => 'date_start',
			'title_key' => 'LBL_ACTIVITIES_SUBPANEL_TITLE',
			'type' => 'collection',
			'subpanel_name' => 'activities', //this values is not associated with a physical file.
			//'header_definition_from_subpanel' => 'meetings',
			'module' => 'Activities',
			'top_buttons' => array(
				array('widget_class' => 'SubPanelTopCreateTaskButton'),
				array('widget_class' => 'SubPanelTopScheduleMeetingButton'),
				array('widget_class' => 'SubPanelTopScheduleCallButton'),
			),
			'collection_list' => array(
				'tasks' => array(
					'module' => 'Tasks',
					'subpanel_name' => 'ForActivities',
					'get_subpanel_data' => 'tasks',
				),
				'meetings' => array(
					'module' => 'Meetings',
					'subpanel_name' => 'ForActivities',
					'get_subpanel_data' => 'meetings',
				),
				'calls' => array(
					'module' => 'Calls',
					'subpanel_name' => 'ForActivities',
					'get_subpanel_data' => 'calls',
				),
			)
		),
		'history' => array(
			'order' => 100,
			'sort_order' => 'desc',
			'sort_by' => 'date_modified',
			'title_key' => 'LBL_HISTORY_SUBPANEL_TITLE',
			'type' => 'collection',
			'subpanel_name' => 'history', //this values is not associated with a physical file.
			//'header_definition_from_subpanel' => 'meetings',
			'module' => 'History',
			'top_buttons' => array(
				array(
                                        'widget_class' => 'SubPanelTopCreateNoteButton',
                                        'additional_form_fields_override' => array(
                                            'account_name' => 'account_name',
                                            'account_id' => 'account_id',
                                            'contact_name' => 'contact_name',
                                            'contact_id' => 'contact_id',
                                            'assigned_user_name' => 'assigned_user_name',
                                            'assigned_user_id' => 'assigned_user_id'
                                        ),
                                    ),
				array('widget_class' => 'SubPanelTopSummaryButton'),
			),
			'collection_list' => array(
				'tasks' => array(
					'module' => 'Tasks',
					'subpanel_name' => 'ForHistory',
					'get_subpanel_data' => 'tasks',
				),
				'meetings' => array(
					'module' => 'Meetings',
					'subpanel_name' => 'ForHistory',
					'get_subpanel_data' => 'meetings',
				),
				'calls' => array(
					'module' => 'Calls',
					'subpanel_name' => 'ForHistory',
					'get_subpanel_data' => 'calls',
				),
				'notes' => array(
					'module' => 'Notes',
					'subpanel_name' => 'ForHistory',
					'get_subpanel_data' => 'notes',
				),
			)
		),
        
        'prospect_lists' => array(
			'order' => 110,
			'sort_order' => 'asc',
			'sort_by' => 'name',
			'module' => 'ProspectLists',
			'subpanel_name' => 'default',
			'get_subpanel_data' => 'prospect_lists',
			'add_subpanel_data' => 'related_id',
			'title_key' => 'LBL_PROSPECT_LIST_SUBPANEL_TITLE',
			'top_buttons' => array(
				array('widget_class' => 'SubPanelTopSelectButton'),
			),
		),
        
        
                'email' => array(
                    'order' => 20,
                    'sort_order' => 'desc',
                    'sort_by' => 'date_sent',
                    'title_key' => 'LBL_EMAILS_SUBPANEL_TITLE',
                    'type' => 'collection',
                    'subpanel_name' => 'email',   //this values is not associated with a physical file.
                    'module'=>'Emails',

                    'top_buttons' => array(
//                            array('widget_class' => 'SubPanelTopArchiveEmailButton'),
//                            array('widget_class' => 'SubPanelTopComposeEmailButton'),
                    ),

                    'collection_list' => array(
                        'emails' => array(
                            'module' => 'Emails',
                            'subpanel_name' => 'ForAll',
                            'get_subpanel_data' => 'emails',
                        ),
	    		),
		),
   
        'sms' => array(
			'order' => -10,
			'module' => 'SMS_SMS',
			'subpanel_name' => 'default',
			'get_subpanel_data' => 'sms',
          //'add_subpanel_data' => 'camp_id',
			'title_key' => 'LBL_SMS_SMS_SUBPANEL_TITLE',
            'top_buttons' => array(
                array(
                    'widget_class' => 'SubPanelTopCreateButton',
                    ),
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

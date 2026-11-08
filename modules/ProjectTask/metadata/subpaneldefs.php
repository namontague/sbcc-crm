<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 ********************************************************************************/




$layout_defs['ProjectTask'] = array(
	// list of what Subpanels to show in the DetailView
	'subpanel_setup' => array(

	'activities' => array(
			'order' => 20,
			'sort_order' => 'desc',
			'sort_by' => 'date_start',
			'title_key' => 'LBL_ACTIVITIES_SUBPANEL_TITLE',
			'type' => 'collection',
			'subpanel_name' => 'activities',   //this values is not associated with a physical file.
			'module'=>'Activities',

			'top_buttons' => array(
				array('widget_class' => 'SubPanelTopCreateTaskButton'),
				array('widget_class' => 'SubPanelTopScheduleMeetingButton'),
				array('widget_class' => 'SubPanelTopScheduleCallButton'),
				array('widget_class' => 'SubPanelTopComposeEmailButton'),
			),

			'collection_list' => array(
				'meetings' => array(
					'module' => 'Meetings',
					'subpanel_name' => 'ForActivities',
					'get_subpanel_data' => 'meetings',
				),
				'tasks' => array(
					'module' => 'Tasks',
					'subpanel_name' => 'ForActivities',
					'get_subpanel_data' => 'tasks',
				),
				'calls' => array(
					'module' => 'Calls',
					'subpanel_name' => 'ForActivities',
					'get_subpanel_data' => 'calls',
				),
			)
		),

		'history' => array(
			'order' => 30,
			'sort_order' => 'desc',
			'sort_by' => 'date_entered',
			'title_key' => 'LBL_HISTORY_SUBPANEL_TITLE',
			'type' => 'collection',
			'subpanel_name' => 'history',   //this values is not associated with a physical file.
			'module'=>'Activities',

			'top_buttons' => array(
			array('widget_class' => 'SubPanelTopCreateNoteButton'),
			array('widget_class' => 'SubPanelTopArchiveEmailButton'),
            array('widget_class' => 'SubPanelTopSummaryButton'),
			),

			'collection_list' => array(
				'meetings' => array(
					'module' => 'Meetings',
					'subpanel_name' => 'ForHistory',
					'get_subpanel_data' => 'meetings',
				),
				'tasks' => array(
					'module' => 'Tasks',
					'subpanel_name' => 'ForHistory',
					'get_subpanel_data' => 'tasks',
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
            ),
        ),
        // EINTEL Addition
        'onlinedocuments' => array(
            'order' => 35,
            'module' => 'OnlineDocuments',
            'subpanel_name' => 'default',
            'get_subpanel_data' => 'onlinedocuments',
            'title_key' => 'LBL_ONLINEDOCUMENTS_SUBPANEL_TITLE',
            'top_buttons' => array(
				array('widget_class' => 'SubPanelTopButtonQuickCreate'),
				array('widget_class' => 'SubPanelTopSelectButton', 'mode'=>'MultiSelect')
			),
        ),
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

// DISABLE UNTIL CLASS BASED RELATIONSHIP WORKS AGAIN
//        'reports' => array(
//            'order' => 10,
//            'title_key' => 'LBL_REPORTS_SUBPANEL_TITLE',
//            'type' => 'collection',
//            'subpanel_name' => 'reports',   //this values is not associated with a physical file.
//            'module'=>'Reports',
//
//            'collection_list' => array(
//                'reports' => array(
//                    'module' => 'Reports',
//                    'subpanel_name' => 'default',
//                    'get_subpanel_data' => 'reports',
//                ),
//                'zoho_reports' => array(
//                     'module' => 'ZohoReports',
//                     'subpanel_name' => 'default',
//                     'get_subpanel_data' => 'zoho_reports',
//                ),
//            )
//        ),

        'timesheet' =>  array(
              'order' => 2,
              'sort_order' => 'desc',
              'sort_by' => 'date_booked',
              'module' => 'Timesheet',
              'get_subpanel_data' => 'timesheet',
              'subpanel_name' => 'default',
              'title_key' => 'LBL_TIMESHEET_SUBPANEL_TITLE',
        ),
        'subtasks' => array(
			'order' => 90,
			'sort_order' => 'asc',
			'sort_by' => 'project_task_id',
			'module' => 'ProjectTask',
            'subpanel_name' => 'default',
			'get_subpanel_data' => 'subtasks',
			'title_key' => 'LBL_SUBPANEL_SUBTASKS',
			'top_buttons' => array(
				array(
                    'widget_class' => 'SubPanelTopSelectButton',
                    'popup_module' => 'ProjectTask',
                    'mode'=>'MultiSelect',
                    'metadata' => 'popupdefs.project_id',
                    'initial_filter_fields' => array(
                        'project_id' => 'project_id',
                        'id' => 'id',
                        ),
                ),
                array('widget_class' => 'SubPanelTopCreateProjectTaskButton', ),
            ),
		),


		'predecessor' => array(
			'order' => 100,
			'sort_order' => 'asc',
			'sort_by' => 'project_task_id',
			'module' => 'ProjectTask',
			'subpanel_name' => 'default',
			'get_subpanel_data' => 'predecessor',//varfer non db source field 
			'title_key' => 'LBL_SUBPANEL_PREDECSSOR',
			'top_buttons' => array(
				array(
                    'widget_class' => 'SubPanelTopSelectButton',
                    'popup_module' => 'ProjectTask',
                    'mode'=>'MultiSelect',
                    'metadata' => 'popupdefs.project_id',
                    'initial_filter_fields' => array(
                        'project_id' => 'project_id',
                        'id' => 'id',
                        ),
                    ),
                ),

		),


        'successors' => array(
			'order' => 110,
			'sort_order' => 'asc',
			'sort_by' => 'project_task_id',
			'module' => 'ProjectTask',
			'subpanel_name' => 'default',
			'get_subpanel_data' => 'successors',
			'title_key' => 'LBL_SUBPANEL_SUCCESORS',
			'top_buttons' => array(
				array(
                    'widget_class' => 'SubPanelTopSelectButton',
                    'popup_module' => 'ProjectTask',
                    'mode'=>'MultiSelect',
                    'metadata' => 'popupdefs.project_id',
                    'initial_filter_fields' => array(
                        'project_id' => 'project_id',
                        'id' => 'id',
                        ),
                    ),
                ),
		),

        'resources' => array(
            'order' => 120,
            'title_key' => 'LBL_SUBPANEL_RESOURCES',
            'module' => 'Users',
            'subpanel_name' => 'default',
            'get_subpanel_data' => 'resources',
            'top_buttons' => array(
				array(
                    'widget_class' => 'SubPanelTopSelectButton',
                    'popup_module' => 'Users',
                    'mode'=>'MultiSelect',
//                    'metadata' => 'popupdefs.project_id',
//                    'initial_filter_fields' => array(
//                        'project_id' => 'project_id',
//                        'id' => 'id',
//                        ),
                    ),
                )
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
				array('widget_class' => 'SubPanelTopArchiveEmailButton'),
                array('widget_class' => 'SubPanelTopComposeEmailButton'),
			),

			'collection_list' => array(
				'emails' => array(
					'module' => 'Emails',
					'subpanel_name' => 'ForAll',
					'get_subpanel_data' => 'emails',
				),
			)
		),


	),
);
?>
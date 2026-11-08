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

$dictionary['Task'] = array('table' => 'tasks',
	'unified_search' => true,
	'full_text_search' => true,
                               'fields' => array (
  'name' =>
  array (
    'name' => 'name',
    'vname' => 'LBL_SUBJECT',
    'dbType' => 'varchar',
    'type' => 'name',
    'len' => '50',
	'unified_search' => true,
	'full_text_search' => array('boost' => 3),
    'importable' => 'required',
    'required' => 'true',
  ),
  'status' =>
  array (
    'name' => 'status',
    'vname' => 'LBL_STATUS',
    'type' => 'enum',
    'options' => 'task_status_dom',
    'len' => 100,
    'required' => 'true',
    'default' => 'Not Started',
  ),
  // EINTEL Addition
  'accept_status' => array(
    'name' => 'accept_status',
    'vname' => 'LBL_ACCEPT_THIS',
    'options' => 'dom_meeting_accept_status',
    'dbType' => 'varchar',
    'type' => 'enum',
    'len' => '20',
  ),
  'date_due_flag' =>
  array (
    'name' => 'date_due_flag',
    'vname' => 'LBL_DATE_DUE_FLAG',
    'type' =>'bool',
    'default'=>0,
    'group'=>'date_due',
  	'studio' => false,
  ),
  'date_due' =>
  array (
    'name' => 'date_due',
    'vname' => 'LBL_DUE_DATE',
    'type' => 'datetimecombo',
    'dbType' => 'datetime',
    'group'=>'date_due',
    'studio' => array('required' => true, 'no_duplicate' => true),
    'enable_range_search' => true,
    'options' => 'date_range_search_dom',
    ),
  'time_due' =>
  array (
    'name' => 'time_due',
    'vname' => 'LBL_DUE_TIME',
    'type' => 'datetime',
    //'db_concat_fields'=> array(0=>'date_due'),
    'source' => 'non-db',
    'importable' => 'false',
    'massupdate' => false,
    ),
  'date_start_flag' =>
  array (
    'name' => 'date_start_flag',
    'vname' => 'LBL_DATE_START_FLAG',
    'type' =>'bool',
    'group'=>'date_start',
    'default'=>0,
  	'studio' => false,
  ),
  'date_start' =>
  array (
    'name' => 'date_start',
    'vname' => 'LBL_START_DATE',
    'type' => 'datetimecombo',
    'dbType' => 'datetime',
    'group'=>'date_start',
    'validation' => array('type' => 'isbefore', 'compareto' => 'date_due', 'blank' => false),
    'studio' => array('required' => true, 'no_duplicate' => true),
    'enable_range_search' => true,
    'options' => 'date_range_search_dom',
    'required' => true,
    'display_default' => 'now',
    ),
  'duration_hours' =>
  array (
    'name' => 'duration_hours',
    'vname' => 'LBL_DURATION_HOURS',
    'type' => 'int',
    'len' => '2',
    'comment' => 'Task duration, hours portion',
    'required' => true,
    'default' => 0,
  ),
  'duration_minutes' =>
  array (
    'name' => 'duration_minutes',
    'vname' => 'LBL_DURATION_MINUTES',
    'type' => 'int',
    'function' => array('name'=>'getTaskDurationMinutesOptions', 'returns'=>'html', 'include'=>'modules/Tasks/TaskHelper.php'),
    'len' => '2',
    'group'=>'duration_hours',
    'importable' => 'required',
    'comment' => 'Task duration, minutes portion'
  ),
 'parent_type'=>
  array(
  	'name'=>'parent_type',
  	'vname'=>'LBL_PARENT_NAME',
    'type' => 'parent_type',
    'dbType'=>'varchar',
  	'group'=>'parent_name',
    'options'=> 'parent_type_display',
  	'required'=>false,
	'len'=>'255',
    'comment' => 'The Sugar object to which the call is related',
    'options' => 'parent_type_display',
),

  'parent_name'=>
  array(
	'name'=> 'parent_name',
	'parent_type'=>'record_type_display' ,
	'type_name'=>'parent_type',
	'id_name'=>'parent_id',
    'vname'=>'LBL_LIST_RELATED_TO',
	'type'=>'parent',
	'group'=>'parent_name',
	'source'=>'non-db',
	'options'=> 'parent_type_display',
  ),

  'parent_id' =>
  array (
    'name' => 'parent_id',
    'type' => 'id',
    'group'=>'parent_name',
    'reportable'=>false,
    'vname'=>'LBL_PARENT_ID',
  ),
  'contact_id' =>
  array (
    'name' => 'contact_id',
    'type' => 'id',
    'group'=>'contact_name',
    'reportable'=>false,
    'vname'=>'LBL_CONTACT_ID',
  ),

  'contact_name' =>
  array (
    'name' => 'contact_name',
    'rname'=>'name',
    'db_concat_fields'=> array(0=>'first_name', 1=>'last_name'),
    'source' => 'non-db',
    'len' => '510',
    'group'=>'contact_name',
    'vname' => 'LBL_CONTACT_NAME',
    'reportable'=>false,
    'id_name' => 'contact_id',
    'join_name' => 'contacts',
    'type' => 'relate',
    'module' => 'Contacts',
    'link'=>'contact_tasks',
    'table'=>'contacts',
  ),

  'contact_phone'=>
    array(
        'name'=>'contact_phone',
        'type'=>'phone',
        'source'=>'non-db',
        'vname'=>'LBL_CONTACT_PHONE',
        'studio'=>array('listview' => true)
    ),

 'contact_email'=>
    array(
        'name'=>'contact_email',
        'type'=>'varchar',
		'vname' => 'LBL_EMAIL_ADDRESS',
		'source' => 'non-db',
        'studio' => false
    ),

  'priority' =>
  array (
    'name' => 'priority',
    'vname' => 'LBL_PRIORITY',
    'type' => 'enum',
    'options' => 'task_priority_dom',
    'len' => 100,
    'required' => 'true',
  ),
  'contact_tasks'=>	array(
    'name' => 'contact_tasks',
    'type' => 'link',
    'relationship' => 'contact_tasks',
    'source'=>'non-db',
    'side'=>'right',
    'vname'=>'LBL_CONTACT',
	),
  'users' => array (
    'name' => 'users',
    'type' => 'link',
    'relationship' => 'tasks_users',
    'source'=>'non-db',
    'vname'=>'LBL_USERS',
   ),
  'contacts' =>
  array (
    'name' => 'contacts',
    'type' => 'link',
    'relationship' => 'tasks_contacts',
    'source'=>'non-db',
    'vname'=>'LBL_CONTACTS',
  ),
  'leads' =>
  array (
    'name' => 'leads',
    'type' => 'link',
    'relationship' => 'tasks_leads',
    'source'=>'non-db',
    'vname'=>'LBL_LEADS',
  ),
  'accounts' =>
  array (
  	'name' => 'accounts',
    'type' => 'link',
    'relationship' => 'account_tasks',
    'source'=>'non-db',
		'vname'=>'LBL_ACCOUNT',
  ),
  'opportunities' =>
  array (
    'name' => 'opportunities',
    'type' => 'link',
    'relationship' => 'opportunity_tasks',
    'source'=>'non-db',
    'vname'=>'LBL_OPPORTUNITY',
  ),
  'cases' =>
  array (
    'name' => 'cases',
    'type' => 'link',
    'relationship' => 'case_tasks',
    'source'=>'non-db',
    'vname'=>'LBL_CASE',
  ),
  'bugs' =>
  array (
    'name' => 'bugs',
    'type' => 'link',
    'relationship' => 'bug_tasks',
    'source'=>'non-db',
    'vname'=>'LBL_BUGS',
  ),
  'lead_tasks' =>
  array (
    'name' => 'lead_tasks',
    'type' => 'link',
    'relationship' => 'lead_tasks',
    'source'=>'non-db',
    'vname'=>'LBL_LEADS',
  ),
  'projects' =>
    array (
    'name' => 'projects',
    'type' => 'link',
    'relationship' => 'projects_tasks',
    'source'=>'non-db',
    'vname'=>'LBL_PROJECTS',
  ),
  'project_tasks' =>
    array (
    'name' => 'project_tasks',
    'type' => 'link',
    'relationship' => 'project_tasks_tasks',
    'source'=>'non-db',
    'vname'=>'LBL_PROJECT_TASKS',
  ),
    'notes' =>
  array (
  	'name' => 'notes',
    'type' => 'link',
    'relationship' => 'tasks_notes',
    'module'=>'Notes',
    'bean_name'=>'Note',
    'source'=>'non-db',
		'vname'=>'LBL_NOTES',
  ),
  
	'contact_parent'=>
		array (
			'name' => 'contact_parent',
			'type' => 'link',
			'relationship' => 'contact_tasks_parent',
			'source' => 'non-db',
            'reportable' => false
	),





  // EINTEL Addition

'date_due_field' => array(
    'name' => 'date_due_field',
    'group'=>'date_due',
    'vname' => 'LBL_DUE_DATE_AND_TIME',
    'type' => 'datetimecombo',
    'date' => 'date_due',
    'time' => 'time_due',
    'date_readonly' => 'date_due_readonly',
    'time_readonly' => 'time_due_readonly',
    'noneCheckbox' => true,
    'noneCheckboxJavascript' => 'onClick="set_date_due_values(this.form);"',
    'checkboxId' => 'date_due_flag',
    'checked' => 'date_due_checked',
    'meridian' => 'date_due_meridian',
    'showFormats' => true,
    'source' => 'non-db',
    'comment' => 'Used for meta-data framework',
    'importable' => 'false',
),

'date_start_field' => array(
	 'group'=>'date_start',
    'name' => 'date_start_field',
    'vname' => 'LBL_DUE_DATE_AND_TIME',
    'type' => 'datetimecombo',
    'date' => 'date_start',
    'time' => 'time_start',
    'date_readonly' => 'date_start_readonly',
    'time_readonly' => 'time_start_readonly',
    'noneCheckbox' => true,
    'noneCheckboxJavascript' => 'onClick="set_date_start_values(this.form);"',
    'checkboxId' => 'date_start_flag',
    'checked' => 'date_start_checked',
    'meridian' => 'date_start_meridian',
    'showFormats' => true,
    'source' => 'non-db',
    'comment' => 'Used for meta-data framework',
    ),

    'category' =>
    array (
      'name' => 'category',
      'vname' => 'LBL_CATEGORY',
      'type' => 'enum',
      'options' => 'note_categories_dom',
      'comment' => 'Field for categorizing',
    ),                                

    'activity_schedule_id'=> array(
         'name'=>'activity_schedule_id',
         'type'=>'id',
         'comment' => 'The ID of the schedule for this call'
  ),

    'activity_schedule' => array (
          'name' => 'activity_schedule',
          'type' => 'link',
          'custom_type'=> 'activitySchedule',
          'relationship' => 'calls_activity_schedule',
          'source'=>'non-db',
          'link_type'=>'one',
          'vname'=>'LBL_ACTIVITY_SCHEDULE',
    ),

    'SecurityGroups' => array (
            'name' => 'SecurityGroups',
        'type' => 'link',
            'relationship' => 'securitygroups_tasks',
            'module'=>'SecurityGroups',
            'bean_name'=>'SecurityGroup',
        'source'=>'non-db',
            'vname'=>'LBL_SECURITYGROUPS',
    ),

  		'notes' =>
  		array (
  			'name' => 'notes',
    		'type' => 'link',
    		'relationship' => 'tasks_notes',
    		'source'=>'non-db',
			'vname'=>'LBL_NOTES',
  		),
       'onlinedocuments' => array(
            'name' => 'onlinedocuments',
            'type' => 'link',
            'relationship' => 'task_onlinedocuments',
            'module' => 'OnlineDocuments',
            'bean_name' => 'OnlineDocument',
            'source' => 'non-db',
            'vname' => 'LBL_ONLINEDOCUMENTS',
        ),
  		'meetings' =>
  			array (
  			'name' => 'meetings',
    		'type' => 'link',
    		'relationship' => 'tasks_meetings',
    		'source'=>'non-db',
            'vname'=>'LBL_MEETINGS',
  		),
		'calls' =>
  			array (
  			'name' => 'calls',
    		'type' => 'link',
    		'relationship' => 'tasks_calls',
    		'source'=>'non-db',
			'vname'=>'LBL_CALLS',
  		),

  		'emails' =>
  			array (
  			'name' => 'emails',
    		'type' => 'link',
    		'relationship' => 'tasks_emails',
    		'source'=>'non-db',
			'vname'=>'LBL_EMAILS',
  		),


    'timesheet' =>
     array (
        'name' => 'timesheet', //Make this the same as the relationship name
        'type' => 'link',
        'relationship' => 'tasks_timesheet', //relationship definition in vardef
        //'module'=>'Tasks', // Child (many) part of the relationship
        //'bean_name'=>'Tasks', // This is the name of the sugar bean. Usually same as the module name
        'source'=>'non-db', // This field is not sourced from the database
        //'vname'=>'LBL_Tasks', // Should not be required but is listed as mandatory for all fields in sugarcrm docs.
    ),

)
,
 'relationships' => array (
		'tasks_notes' => array(
			'lhs_module'		=> 'Tasks',
			'lhs_table'			=> 'tasks',
			'lhs_key'			=> 'id',
			'rhs_module'		=> 'Notes',
			'rhs_table'			=> 'notes',
			'rhs_key'			=> 'parent_id',
			'relationship_type'	=> 'one-to-many',
		),

  'tasks_assigned_user' =>
   array('lhs_module'=> 'Users', 'lhs_table'=> 'users', 'lhs_key' => 'id',
   'rhs_module'=> 'Tasks', 'rhs_table'=> 'tasks', 'rhs_key' => 'assigned_user_id',
   'relationship_type'=>'one-to-many')

   ,'tasks_modified_user' =>
   array('lhs_module'=> 'Users', 'lhs_table'=> 'users', 'lhs_key' => 'id',
   'rhs_module'=> 'Tasks', 'rhs_table'=> 'tasks', 'rhs_key' => 'modified_user_id',
   'relationship_type'=>'one-to-many')

   ,'tasks_created_by' =>
   array('lhs_module'=> 'Users', 'lhs_table'=> 'users', 'lhs_key' => 'id',
   'rhs_module'=> 'Tasks', 'rhs_table'=> 'tasks', 'rhs_key' => 'created_by',
   'relationship_type'=>'one-to-many')

	,'tasks_notes' => array('lhs_module'=> 'Tasks', 'lhs_table'=> 'tasks', 'lhs_key' => 'id',
							  'rhs_module'=> 'Notes', 'rhs_table'=> 'notes', 'rhs_key' => 'parent_id',
							  'relationship_type'=>'one-to-many', 'relationship_role_column'=>'parent_type',
							  'relationship_role_column_value'=>'Tasks')
    ,'task_onlinedocuments' => array('lhs_module' => 'Tasks', 'lhs_table' => 'tasks', 'lhs_key' => 'id',
            'rhs_module' => 'OnlineDocuments', 'rhs_table' => 'onlinedocuments', 'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many', 'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Tasks')
    ,'tasks_emails' => array('lhs_module'=> 'Tasks', 'lhs_table'=> 'tasks', 'lhs_key' => 'id',
							  'rhs_module'=> 'Emails', 'rhs_table'=> 'emails', 'rhs_key' => 'parent_id',
							  'relationship_type'=>'one-to-many', 'relationship_role_column'=>'parent_type',
							  'relationship_role_column_value'=>'Tasks')

  ,
     // EINTEL Addition
     'tasks_timesheet' =>
     array(
       'lhs_module'=> 'Tasks',
       'lhs_table'=> 'tasks',
       'lhs_key' => 'id',
       'rhs_module'=> 'Timesheet',
       'rhs_table'=> 'timesheet',
       'rhs_key' => 'parent_id',
       'relationship_type'=>'one-to-many',
       'relationship_role_column'=>'parent_type',
	   'relationship_role_column_value'=>'Tasks'),

)
     

                                                      , 'indices' => array (
       array('name' =>'idx_tsk_name', 'type'=>'index', 'fields'=>array('name')),
       array('name' =>'idx_task_con_del', 'type'=>'index', 'fields'=>array('contact_id','deleted')),
       array('name' =>'idx_task_par_del', 'type'=>'index', 'fields'=>array('parent_id','parent_type','deleted')),
       array('name' =>'idx_task_assigned', 'type'=>'index', 'fields'=>array('assigned_user_id')),
        array('name' =>'idx_task_status', 'type'=>'index', 'fields'=>array('status')),
        array(
            'name' =>'idx_task_activity_schedule',
            'type' =>'index',
            'fields'=>array( 'activity_schedule_id')),

             )

        //This enables optimistic locking for Saves From EditView
	,'optimistic_locking'=>true,
                            );
VardefManager::createVardef('Tasks','Task', array('default', 'assignable',
));
?>

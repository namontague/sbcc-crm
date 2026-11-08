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




$dictionary['Project'] = array(
	'table' => 'project',
	'unified_search' => true,
	'full_text_search' => true,
	'unified_search_default_enabled' => false,
	'comment' => 'Project',
	'fields' => array(
		'id' => array(
			'name' => 'id',
			'vname' => 'LBL_ID',
			'required' => true,
			'type' => 'id',
			'reportable'=>true,
			'comment' => 'Unique identifier'
		),
		'date_entered' => array(
			'name' => 'date_entered',
			'vname' => 'LBL_DATE_ENTERED',
			'type' => 'datetime',
			'comment' => 'Date record created',
		    'enable_range_search' => true,
		    'options' => 'date_range_search_dom',
		),
		'date_modified' => array(
			'name' => 'date_modified',
			'vname' => 'LBL_DATE_MODIFIED',
			'type' => 'datetime',
			'comment' => 'Date record last modified',
		    'enable_range_search' => true,
		    'options' => 'date_range_search_dom',
		),
		'assigned_user_id' => array(
			'name' => 'assigned_user_id',
			'rname' => 'user_name',
			'id_name' => 'assigned_user_id',
			'type' => 'assigned_user_name',
			'vname' => 'LBL_ASSIGNED_USER_ID',
			'required' => false,
			'len' => 36,
			'dbType' => 'id',
			'table' => 'users',
			'isnull' => false,
			'reportable'=>true,
			'comment' => 'User assigned to this record'
		),
		'modified_user_id' => array(
			'name' => 'modified_user_id',
			'rname' => 'user_name',
			'id_name' => 'modified_user_id',
			'vname' => 'LBL_MODIFIED_USER_ID',
			'type' => 'assigned_user_name',
			'table' => 'users',
			'isnull' => 'false',
			'dbType' => 'id',
			'reportable'=>true,
			'comment' => 'User who last modified record'
		),
		'modified_by_name' => 
	  array (
	    'name' => 'modified_by_name',
	    'vname' => 'LBL_MODIFIED_NAME',
	    'type' => 'relate',
	    'reportable'=>false,
	    'source'=>'non-db',
	    'rname'=>'user_name',
	    'table' => 'users',
	    'id_name' => 'modified_user_id',
	    'module'=>'Users',
	    'link'=>'modified_user_link',
	    'duplicate_merge'=>'disabled' 
	  ),  
		'created_by' => array(
			'name' => 'created_by',
			'rname' => 'user_name',
			'id_name' => 'modified_user_id',
			'vname' => 'LBL_CREATED_BY',
			'type' => 'assigned_user_name',
			'table' => 'users',
			'isnull' => 'false',
			'dbType' => 'id',
			'comment' => 'User who created record',
		),
		'created_by_name' => 
	  array (
	    'name' => 'created_by_name',
		'vname' => 'LBL_CREATED',
		'type' => 'relate',
		'reportable'=>false,
	    'link' => 'created_by_link',
	    'rname' => 'user_name',
		'source'=>'non-db',
		'table' => 'users',
		'id_name' => 'created_by',
		'module'=>'Users',
		'duplicate_merge'=>'disabled',
        'importable' => 'false',
	),
		'name' => array(
			'name' => 'name',
			'vname' => 'LBL_NAME',
			'required' => true,
			'dbType' => 'varchar',
			'type' => 'name',
			'len' => 50,
			'unified_search' => true,
			'full_text_search' => array('boost' => 3),
			'comment' => 'Project name',
			'importable' => 'required',
            'required' => true,
		),
		'description' => array(
			'name' => 'description',
			'vname' => 'LBL_DESCRIPTION',
			'required' => false,
			'type' => 'text',
			'comment' => 'Project description'
		),
		'deleted' => array(
			'name' => 'deleted',
			'vname' => 'LBL_DELETED',
			'type' => 'bool',
			'required' => false,
            'reportable'=>false,
			'default' => '0',
			'comment' => 'Record deletion indicator'
		),
        'estimated_start_date' =>
        array(
            'name' => 'estimated_start_date',
            'vname' => 'LBL_DATE_START',
            'required' => true,
            'validation' => array('type' => 'isbefore', 'compareto' => 'estimated_end_date', 'blank' => true),
            'type' => 'date',
            'importable' => 'required',
            'required' => true,
            'enable_range_search' => true,
        ),
        'estimated_end_date' =>
        array(
            'name' => 'estimated_end_date',
            'vname' => 'LBL_DATE_END',
            'required' => true,
            'type' => 'date',
            'importable' => 'required',
            'required' => true,
            'enable_range_search' => true,
        ),
        'status' =>
        array(
            'name' => 'status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'options' => 'project_status_dom',
        ),

        'priority' =>
        array(
            'name' => 'priority',
            'vname' => 'LBL_PRIORITY',
            'type' => 'enum',
            'options' => 'projects_priority_options',
        ),
		'estimated_effort' =>
  		array (
  			'name' => 'estimated_effort',
			'type' => 'float',
    		'source'=>'non-db',
			'vname'=>'LBL_LIST_ESTIMATED_EFFORT',
  		),
        // EINTEL modifiaction
		'actual_effort' =>
  		array (
  			'name' => 'actual_effort',
            'type' => 'float',
            'dbtype' => 'double',
            'audited'=>true,
			'vname'=>'LBL_LIST_ACTUAL_EFFORT',
  		),
        // EINTEL Addition
        'billable_effort' =>
        array (
            'name' => 'billable_effort',
            'vname' => 'LBL_LIST_BILLABLE_EFFORT',
            'type' => 'float',
            'dbtype' => 'double',
            'audited'=>true,
            'comment' => 'Billable Effort',
        ),
        // END


		'accounts' =>
  		array (
  			'name' => 'accounts',
    		'type' => 'link',
    		'relationship' => 'projects_accounts',
    		'source'=>'non-db',
    		'ignore_role'=>true,
				'vname'=>'LBL_ACCOUNTS',
  		),
		'quotes' =>
  		array (
  			'name' => 'quotes',
    		'type' => 'link',
    		'relationship' => 'projects_quotes',
    		'source'=>'non-db',
    		'ignore_role'=>true,
				'vname'=>'LBL_QUOTES',
  		),
		'contacts' =>
  		array (
  			'name' => 'contacts',
    		'type' => 'link',
    		'relationship' => 'projects_contacts',
    		'source'=>'non-db',
    		'ignore_role'=>true,
				'vname'=>'LBL_CONTACTS',
  		),
		'opportunities' =>
  		array (
  			'name' => 'opportunities',
    		'type' => 'link',
    		'relationship' => 'projects_opportunities',
    		'source'=>'non-db',
    		'ignore_role'=>true,
				'vname'=>'LBL_OPPORTUNITIES',
  		),
  		'notes' =>
  		array (
  			'name' => 'notes',
    		'type' => 'link',
    		'relationship' => 'projects_notes',
    		'source'=>'non-db',
				'vname'=>'LBL_NOTES',
  		),
		'tasks' =>
  			array (
  			'name' => 'tasks',
    		'type' => 'link',
    		'relationship' => 'projects_tasks',
    		'source'=>'non-db',
				'vname'=>'LBL_TASKS',
  		),
  		'meetings' =>
  			array (
  			'name' => 'meetings',
    		'type' => 'link',
    		'relationship' => 'projects_meetings',
    		'source'=>'non-db',
				'vname'=>'LBL_MEETINGS',
  		),
		'calls' =>
  			array (
  			'name' => 'calls',
    		'type' => 'link',
    		'relationship' => 'projects_calls',
    		'source'=>'non-db',
				'vname'=>'LBL_CALLS',
  		),
  		'emails' =>
  			array (
  			'name' => 'emails',
    		'type' => 'link',
    		'relationship' => 'emails_projects_rel',
    		'source'=>'non-db',
				'vname'=>'LBL_EMAILS',
  		),
  		'projecttask' =>
  			array (
  			'name' => 'projecttask',
    		'type' => 'link',
    		'relationship' => 'projects_project_tasks',
    		'source'=>'non-db',
				'vname'=>'LBL_PROJECT_TASKS',
  		),
          'created_by_link' =>
          array (
                'name' => 'created_by_link',
            'type' => 'link',
            'relationship' => 'projects_created_by',
            'vname' => 'LBL_CREATED_BY_USER',
            'link_type' => 'one',
            'module'=>'Users',
            'bean_name'=>'User',
            'source'=>'non-db',
          ),
          'modified_user_link' =>
          array (
                'name' => 'modified_user_link',
            'type' => 'link',
            'relationship' => 'projects_modified_user',
            'vname' => 'LBL_MODIFIED_BY_USER',
            'link_type' => 'one',
            'module'=>'Users',
            'bean_name'=>'User',
            'source'=>'non-db',
          ),
          'assigned_user_link' =>
          array (
                'name' => 'assigned_user_link',
            'type' => 'link',
            'relationship' => 'projects_assigned_user',
            'vname' => 'LBL_ASSIGNED_TO_USER',
            'link_type' => 'one',
            'module'=>'Users',
            'bean_name'=>'User',
            'source'=>'non-db',
          ),
        'assigned_user_name' =>
        array (
        	'name' => 'assigned_user_name',
        	'rname' => 'user_name',
        	'id_name' => 'assigned_user_id',
        	'vname' => 'LBL_ASSIGNED_USER_NAME',
        	'type' => 'relate',
        	'table' => 'users',
        	'module' => 'Users',
        	'dbType' => 'varchar',
        	'link'=>'users',
        	'len' => '255',
        	'source'=>'non-db',
        	),
        'cases' =>
            array (
            'name' => 'cases',
            'type' => 'link',
            'relationship' => 'projects_cases',
            'side' => 'right',
            'source'=>'non-db',
            'vname'=>'LBL_CASES',
        ),
        'bugs' =>
            array (
            'name' => 'bugs',
            'type' => 'link',
            'relationship' => 'projects_bugs',
            'side' => 'right',
            'source'=>'non-db',
            'vname'=>'LBL_BUGS',
        ),
            
        'products' =>
            array (
            'name' => 'products',
            'type' => 'link',
            'relationship' => 'projects_products',
            'side' => 'right',
            'source'=>'non-db',
            'vname'=>'LBL_PRODUCTS',
        ),

         // EINTEL Addition

        'template_id' =>
        array(
            'name' => 'template_id',
            'id_name' => 'template_id',
            'vname' => 'LBL_TEMPLATE_ID',
            'source' => 'non-db',
            'type' => 'id',
        ),

        'template_dates' => array(
            'name' => 'template_dates',
            'vname' => 'LBL_TEMPLATE_DATES',
            'comment' => 'Use dates set in the template if creating from a project from a template',
            'help' => 'Use dates set in the template if creating from a project from a template',
            'required' => false,
            'default' => 0,
            'source' => 'non-db',
            'type' => 'bool',
            'massupdate' => false,
        ),

        'template_flag' => array(
            'name' => 'template_flag',
            'vname' => 'LBL_TEMPLATE_FLAG',
            'type' =>'bool',
            'required' => false,
            'massupdate' => false,
        ),

	 
         'onlinedocuments' => array(
            'name' => 'onlinedocuments',
            'type' => 'link',
            'relationship' => 'project_onlinedocuments',
            'module' => 'OnlineDocuments',
            'bean_name' => 'OnlineDocument',
            'source' => 'non-db',
            'vname' => 'LBL_ONLINEDOCUMENTS',
  		),

        'SecurityGroups' => array (
                'name' => 'SecurityGroups',
                'type' => 'link',
                'relationship' => 'securitygroups_project',
                'module'=>'SecurityGroups',
                'bean_name'=>'SecurityGroup',
                'source'=>'non-db',
                'vname'=>'LBL_SECURITYGROUPS',
        ),

      'reports' =>
            array (
            'name' => 'reports',
            'type' => 'link',
            'relationship' => 'projects_reports',
            'side' => 'left',
            'source'=>'non-db',
            'vname'=>'LBL_REPORTS',
            'bean_filter_field'=>'object_name'
        ),
        'zoho_reports' =>
        array(
            'name' => 'zoho_reports',
            'type' => 'link',
            'relationship' => 'projects_zoho_reports',
            'side' => 'left',
            'source' => 'non-db',
            'vname' => 'LBL_ZOHO_REPORTS',
            'bean_filter_field' => 'object_name'
        ),

        'timesheet' => array(
            'name' => 'timesheet',
          'type' => 'link',
          'relationship' => 'project_timesheet',
          'source'=>'non-db'
        ),

        'project_team' =>
        array (
	    'name' => 'project_team',
            'type' => 'link',
            'relationship' => 'projects_team_members',
            'source'=>'non-db',
            'ignore_role'=>true,
            'vname'=>'LBL_PROJECT_TEAM',
	),
        #eintel addition EISCRM-66
        'accounts_names' =>
        array (
            'name' => 'accounts_names',
            'source'=>'non-db',
            'vname'=>'LBL_ACCOUNTS',
        ),
	),
	'indices' => array(
		array('name' =>'projects_primary_key_index',
			'type' =>'primary',
			'fields'=>array('id')
		),
	),
	'relationships' => array(
		'projects_notes' => array(
			'lhs_module'=> 'Project', 'lhs_table'=> 'project', 'lhs_key' => 'id',
			'rhs_module'=> 'Notes', 'rhs_table'=> 'notes', 'rhs_key' => 'parent_id',
			'relationship_type'=>'one-to-many', 'relationship_role_column'=>'parent_type',
			'relationship_role_column_value'=>'Project'),
		'projects_tasks' => array(
			'lhs_module'=> 'Project', 'lhs_table'=> 'project', 'lhs_key' => 'id',
			'rhs_module'=> 'Tasks', 'rhs_table'=> 'tasks', 'rhs_key' => 'parent_id',
			'relationship_type'=>'one-to-many', 'relationship_role_column'=>'parent_type',
			'relationship_role_column_value'=>'Project'),
		'projects_meetings' => array(
			'lhs_module'=> 'Project', 'lhs_table'=> 'project', 'lhs_key' => 'id',
			'rhs_module'=> 'Meetings', 'rhs_table'=> 'meetings', 'rhs_key' => 'parent_id',
			'relationship_type'=>'one-to-many', 'relationship_role_column'=>'parent_type',
			'relationship_role_column_value'=>'Project'),
		'projects_calls' => array(
			'lhs_module'=> 'Project', 'lhs_table'=> 'project', 'lhs_key' => 'id',
			'rhs_module'=> 'Calls', 'rhs_table'=> 'calls', 'rhs_key' => 'parent_id',
			'relationship_type'=>'one-to-many', 'relationship_role_column'=>'parent_type',
			'relationship_role_column_value'=>'Project'),
		'projects_emails' => array(
			'lhs_module'=> 'Project', 'lhs_table'=> 'project', 'lhs_key' => 'id',
			'rhs_module'=> 'Emails', 'rhs_table'=> 'emails', 'rhs_key' => 'parent_id',
			'relationship_type'=>'one-to-many', 'relationship_role_column'=>'parent_type',
			'relationship_role_column_value'=>'Project'),
		'projects_project_tasks' => array(
			'lhs_module'=> 'Project', 'lhs_table'=> 'project', 'lhs_key' => 'id',
			'rhs_module'=> 'ProjectTask', 'rhs_table'=> 'project_task', 'rhs_key' => 'project_id',
			'relationship_type'=>'one-to-many'),
        'projects_assigned_user' =>
           array('lhs_module'=> 'Users', 'lhs_table'=> 'users', 'lhs_key' => 'id',
           'rhs_module'=> 'Project', 'rhs_table'=> 'project', 'rhs_key' => 'assigned_user_id',
           'relationship_type'=>'one-to-many')

           ,'projects_modified_user' =>
           array('lhs_module'=> 'Users', 'lhs_table'=> 'users', 'lhs_key' => 'id',
           'rhs_module'=> 'Project', 'rhs_table'=> 'project', 'rhs_key' => 'modified_user_id',
           'relationship_type'=>'one-to-many')

           ,'projects_created_by' =>
           array('lhs_module'=> 'Users', 'lhs_table'=> 'users', 'lhs_key' => 'id',
           'rhs_module'=> 'Project', 'rhs_table'=> 'project', 'rhs_key' => 'created_by',
           'relationship_type'=>'one-to-many')

           // EINTEL Addition
           ,'projects_reports' => array(
              'lhs_module'=> 'Project', 'lhs_table'=> 'project', 'lhs_key' => 'object_name',
              'rhs_module'=> 'ReportViewer', 'rhs_table'=> 'reportviewer', 'rhs_key' => 'related_module',
              'relationship_type'=>'one-to-many')
          , 'projects_zoho_reports' => array(
              'lhs_module' => 'Project', 'lhs_table' => 'project', 'lhs_key' => 'object_name',
              'rhs_module' => 'ZohoReports', 'rhs_table' => 'zoho_reports', 'rhs_key' => 'related_module',
              'relationship_type' => 'one-to-many')

           ,'project_timesheet' => array(
                'lhs_module'=> 'Project',
                'lhs_table'=>'project',
                'lhs_key' => 'id',
                'rhs_module'=> 'Timesheet',
                'rhs_table'=> 'timesheet',
                'rhs_key' => 'parent_id',
                'relationship_type'=>'one-to-many',
                'relationship_role_column'=>'parent_type',
                'relationship_role_column_value'=>'Project'
            )
            , 'project_onlinedocuments' => array(
            'lhs_module' => 'Project', 'lhs_table' => 'project', 'lhs_key' => 'id',
            'rhs_module' => 'OnlineDocuments', 'rhs_table' => 'onlinedocuments', 'rhs_key' => 'parent_id',
			'relationship_type'=>'one-to-many', 'relationship_role_column'=>'parent_type',
			'relationship_role_column_value'=>'Project')

	),
);

VardefManager::createVardef('Project','Project', array(
));
?>

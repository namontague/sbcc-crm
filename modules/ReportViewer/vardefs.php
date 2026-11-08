<?php
/*********************************************************************************
 * (C) 2009 Enterprise Intelligence Systems Pty Ltd
 * No part of this file should be used without the express permission
 * of the authors.
 *
 * Contact: info@eintel.com.au for more information.
 *
 *
 ********************************************************************************/
$dictionary['ReportViewer'] = array(
	'table'=>'reportviewer',
	'audited'=>true,
	'fields'=>array (

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
                'required' => true,
                'comment' => 'Date record created'
        ),
        'date_modified' => array(
                'name' => 'date_modified',
                'vname' => 'LBL_DATE_MODIFIED',
                'type' => 'datetime',
                'required' => true,
                'comment' => 'Date record last modified'
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
        'modified_by_name' => array (
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
        'created_by_name' => array (
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
                'comment' => 'Project name',
                'importable' => 'required',
        ),
        'description' => array(
                'name' => 'description',
                'vname' => 'LBL_DESCRIPTION',
                'required' => false,
                'type' => 'varchar',
                'comment' => 'Project description'
        ),
        'comment' => array(
                'name' => 'comment',
                'vname' => 'LBL_COMMENT',
                'required' => false,
                'type' => 'text',
                'comment' => 'Enter report comment here',
        ),
        'format' => array(
                'name' => 'format',
                'vname' => 'LBL_FORMAT',
                'id_name' => 'format',
                'required' => false,
                'type' => 'enum',
                'options' => 'report_formats',
                'comment' => 'Select report format',
        ),
        'status' => array(
                'name' => 'status',
                'vname' => 'LBL_STATUS',
                'required' => false,
                'type' => 'enum',
                'options' => 'report_status',
                'comment' => 'Select status',
        ),
        'report_location' => array(
                'name' => 'report_location',
                'vname' => 'LBL_REPORT_LOCATION',
                'required' => false,
                'type' => 'varchar',
                'comment' => 'Select report from report server',
        ),
        'related_module' => array(
                'name' => 'related_module',
                'vname' => 'LBL_RELATED_MODULE',
                'required' => false,
                'type' => 'enum',
                'function' => 'reports_related_modules',
                'comment' => 'Select related module',
        ),

        'SecurityGroups' => array (
                'name' => 'SecurityGroups',
                'type' => 'link',
                'relationship' => 'securitygroups_reports',
                'module'=>'SecurityGroups',
                'bean_name'=>'SecurityGroup',
                'source'=>'non-db',
                'vname'=>'LBL_SECURITYGROUPS',
         ),
	'email_templates' => array (
                'name' => 'email_templates',
                'type' => 'link',
                'relationship' => 'reports_email_templates_rel',
                'source'=>'non-db',
                'vname'=>'LBL_REPORTS',
            ),

        ),
	'relationships'=>array (
        'securitygroups_reports' => array(
			'lhs_module'=> 'SecurityGroups', 'lhs_table'=> 'securitygroups', 'lhs_key' => 'id',
			'rhs_module'=> 'ReportViewer', 'rhs_table'=> 'reportviewer', 'rhs_key' => 'id',
			'relationship_type'=>'many-to-many',
			'join_table'=> 'securitygroups_records', 'join_key_lhs'=>'securitygroup_id', 'join_key_rhs'=>'record_id',
			'relationship_role_column'=>'module','relationship_role_column_value'=>'ReportViewer'),

        ),
	'optimistic_lock'=>true,

        );
require_once('include/SugarObjects/VardefManager.php');
VardefManager::createVardef('ReportViewer','ReportViewer', array('basic','assignable'));
?>

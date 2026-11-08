<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * SugarCRM is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2010 SugarCRM Inc.
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
 * FOR A PARTICULAR PURPOSE.  SGNU Affero General Public LicenseLicense for more
 * details.
 * 
 * You should have received GNU Affero General Public LicensePublic License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required underGNU Affero General Public Licenseeneral Public License version 3.
 * 
 * In accordanGNU Affero General Public Licensee GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 ********************************************************************************/
$dictionary['OnlineDocument'] = array(
        'table' => 'onlinedocuments',
        'fields' => array (
                'document_name' => array (
                        'name' => 'document_name',
                        'vname' => 'LBL_NAME',
                        'type' => 'varchar',
                        'len' => '255',
                        'required'=>true,
                        'importable' => 'required',
                ),
                'name' => array(
                        'name' => 'name',
                        'source' => 'non-db',
                        'type'=>'varchar',
                ),
                'filename' => array (
                        'name' => 'filename',
                        'vname' => 'LBL_FILENAME',
                        'type' => 'file',
                        'source' => 'non-db',
                        'comment' => 'The filename of the online document attachment',
                ),
                'active_date' => array (
                        'name' => 'active_date',
                        'vname' => 'LBL_DOC_ACTIVE_DATE',
                        'type' => 'date',
                        'importable' => 'required',
                ),
                'exp_date' => array (
                        'name' => 'exp_date',
                        'vname' => 'LBL_DOC_EXP_DATE',
                        'type' => 'date',
                ),
                'category_id' => array (
                        'name' => 'category_id',
                        'vname' => 'LBL_SF_CATEGORY',
                        'type' => 'enum',
                        'len' => '25',
                        'options' => 'document_category_dom',
                        'reportable'=>false,
                ),
                'subcategory_id' => array (
                        'name' => 'subcategory_id',
                        'vname' => 'LBL_SF_SUBCATEGORY',
                        'type' => 'enum',
                        'len' => '25',
                        'options' => 'document_subcategory_dom',
                        'reportable'=>false,
                ),
                'status_id' => array (
                        'name' => 'status_id',
                        'vname' => 'LBL_DOC_STATUS',
                        'type' => 'enum',
                        'len' => '25',
                        'options' => 'document_status_dom',
                        'reportable'=>false,
                ),
                'status' => array (
                        'name' => 'status',
                        'vname' => 'LBL_DOC_STATUS',
                        'type' => 'varchar',
                        'source' => 'non-db',
                        'Comment' => 'Online Document status for Meta-Data framework',
                ),
                'document_revision_id' => array (
                        'name' => 'document_revision_id',
                        'vname' => 'LBL_LATEST_REVISION',
                        'type' => 'varchar',
                        'len' => '36',
                        'reportable'=>false,
                ),
                'onlinerevisions' => array (
                        'name' => 'onlinerevisions',
                        'type' => 'link',
                        'relationship' => 'onlinedocument_revisions',
                        'source'=>'non-db',
                        'vname'=>'LBL_REVISIONS',
                ),

                'revision' => array (
                        'name' => 'revision',
                        'vname' => 'LBL_DOC_VERSION',
                        'type' => 'varchar',
                        'reportable' => false,
                        'required' => true,
                        'source'=>'non-db',
                        'importable' => 'required',
                ),
                'last_rev_created_name' => array (
                        'name' => 'last_rev_created_name',
                        'vname' => 'LBL_LAST_REV_CREATOR',
                        'type' => 'varchar',
                        'reportable' => false,
                        'source' => 'non-db'
                ),
                'latest_revision' => array (
                        'name' => 'latest_revision',
                        'vname' => 'LBL_LATEST_REVISION',
                        'type' => 'varchar',
                        'reportable' => false,
                        'source' => 'non-db'
                ),
                'last_rev_create_date' => array (
                        'name' => 'last_rev_create_date',
                        'type' => 'date',
                        'table' => 'onlinedocument_revisions',
                        'link' => 'onlinerevisions',
                        'join_name' => 'onlinedocument_revisions',
                        'vname' => 'LBL_LAST_REV_CREATE_DATE',
                        'rname' => 'date_entered',
                        'reportable' => false,
                        'source' => 'non-db'
                ),
                'is_template' =>
                array (
                        'name' => 'is_template',
                        'vname' => 'LBL_IS_TEMPLATE',
                        'type' => 'bool',
                        'default'=> 0,
                        'reportable'=>false,
                ),
                'template_type' =>
                array (
                        'name' => 'template_type',
                        'vname' => 'LBL_TEMPLATE_TYPE',
                        'type' => 'enum',
                        'len' => '25',
                        'options' => 'document_template_type_dom',
                        'reportable'=>false,
                ),
                'latest_revision_name' =>
                array (
                        'name' => 'latest_revision_name',
                        'type' => 'varchar',
                        'reportable'=>false,
                        'source'=>'non-db'
                ),

                'selected_revision_name' =>
                array (
                        'name' => 'selected_revision_name',
                        'type' => 'varchar',
                        'reportable'=>false,
                        'source'=>'non-db'
                ),
                'linked_id'=>
                array (
                        'name' => 'linked_id',
                        'type' => 'varchar',
                        'reportable'=>false,
                        'source'=>'non-db'
                ),
                'selected_revision_id'=>
                array (
                        'name' => 'selected_revision_id',
                        'type' => 'varchar',
                        'reportable'=>false,
                        'source'=>'non-db'
                ),
                'latest_revision_id'=>
                array (
                        'name' => 'latest_revision_id',
                        'type' => 'varchar',
                        'reportable'=>false,
                        'source'=>'non-db'
                ),
                'selected_revision_filename'=>
                array (
                        'name' => 'selected_revision_filename',
                        'type' => 'varchar',
                        'reportable'=>false,
                        'source'=>'non-db'
                ),

                // The physical online document reocrd within sugar
                'googledoc_id' => array(
                    'name' => 'googledoc_id',
                    'vname' => 'LBL_GOOGLEDOC_ID',
                    'rname' => 'id',
                    'id_name' => 'googledoc_id',
                    'type' => 'id',
                    'table' => 'googledocuments',
                    'isnull' => 'false',
                    'module' => 'GoogleDocuments',
                ),
                'googledoc_name' => array(
                    'name' => 'googledoc_name',
                    'rname' => 'name',
                    'vname' => 'LBL_GOOGLEDOC',
                    'type' => 'relate',
                    'reportable' => false,
                    'table' => 'googledocuments',
                    'id_name' => 'googledoc_id',
                    'link' => 'googledocuments',
                    'module' => 'GoogleDocuments',
                    'isnull' => 'false',
                    'len' => 255,
                ),
                'googledocuments' => array(
                    'name' => 'googledocuments',
                    'type' => 'link',
                    'module' => 'GoogleDocuments',
                    'bean_name' => 'GoogleDocument',
                    'source' => 'non-db',
                    'vname' => 'LBL_GOOGLEDOCUMENTS',
                  ),
                'export_urls' => array(
                    'name' => 'export_urls',
                    'vname' => 'LBL_EXPORTURLS',
                    'type' => 'array',
                    'source' => 'non-db',
                    'comment' => 'Array of supported export formats & urls for the google document ',
                ),
                'document_url' => array(
                    'name' => 'document_url',
                    'vname' => 'LBL_DOCUMENT_URL',
                    'type' => 'varchar',
                    'source' => 'non-db',
                    'comment' => 'Direct link to edit in google',
                ),
                'parent_type' => array(
                        'name'=>'parent_type',
                        'vname'=>'LBL_PARENT_TYPE',
                        'type' =>'parent_type',
                        'dbType' => 'varchar',
                        'group'=>'parent_name',
                        'len'=> '25',
                        'comment' => 'Sugar module the Google Document is associated with'
                ),
                'parent_id' => array(
                        'name'=>'parent_id',
                        'vname'=>'LBL_PARENT_ID',
                        'type'=>'id',
                        'required'=>false,
                        'reportable'=>true,
                        'comment' => 'The ID of the Sugar item specified in parent_type'
                ),
                'parent_name' => array(
                        'name'=> 'parent_name',
                        'parent_type'=>'record_type_display' ,
                        'type_name'=>'parent_type',
                        'id_name'=>'parent_id',
                        'vname'=>'LBL_RELATED_TO',
                        'type'=>'parent',
                        'source'=>'non-db',
                        'options'=> 'record_type_display_onlinedocuments',
                ),
                'account_id' => array (
                    'name' => 'account_id',
                    'vname' => 'LBL_ACCOUNT_ID',
                    'type' => 'id',
                    'reportable' => false,
                    'source' => 'non-db',
                ),
                'accounts' => array (
                    'name' => 'accounts',
                    'type' => 'link',
                    'relationship' => 'account_onlinedocuments',
                    'source' => 'non-db',
                    'vname' => 'LBL_ACCOUNTS',
                ),
                'contact_id' => array(
                        'name'=>'contact_id',
                        'vname'=>'LBL_CONTACT_ID',
                        'type'=>'id',
                        'reportable' => false,
                        'source' => 'non-db',
                ),
                'contacts' => array (
                        'name' => 'contacts',
                        'type' => 'link',
                        'relationship' => 'contact_onlinedocuments',
                        'source'=>'non-db',
                        'vname'=>'LBL_CONTACTS',
                ),
                'opportunity_id' => array(
                        'name'=>'opportunity_id',
                        'vname'=>'LBL_OPPORTUNITY_ID',
                        'type'=>'id',
                        'reportable' => false,
                        'source' => 'non-db',
                ),
                'opportunities' => array (
                        'name' => 'opportunities',
                        'type' => 'link',
                        'relationship' => 'opportunity_onlinedocuments',
                        'source'=>'non-db',
                        'vname'=>'LBL_OPPORTUNITIES',
                ),
                'case_id' => array(
                        'name'=>'case_id',
                        'vname'=>'LBL_CASE_ID',
                        'type'=>'id',
                        'reportable' => false,
                        'source' => 'non-db',
                ),
                'cases' => array (
                        'name' => 'cases',
                        'type' => 'link',
                        'relationship' => 'case_onlinedocuments',
                        'source'=>'non-db',
                        'vname'=>'LBL_CASES',
                ),
                'lead_id' => array(
                        'name'=>'lead_id',
                        'vname'=>'LBL_LEAD_ID',
                        'type'=>'id',
                        'reportable' => false,
                        'source' => 'non-db',
                ),
                'leads' => array (
                        'name' => 'leads',
                        'type' => 'link',
                        'relationship' => 'lead_onlinedocuments',
                        'source'=>'non-db',
                        'vname'=>'LBL_LEADS',
                ),
                'bug_id' => array(
                        'name'=>'bug_id',
                        'vname'=>'LBL_BUG_ID',
                        'type'=>'id',
                        'reportable' => false,
                        'source' => 'non-db',
                ),
                'bugs' => array (
                        'name' => 'bugs',
                        'type' => 'link',
                        'relationship' => 'bug_onlinedocuments',
                        'source'=>'non-db',
                        'vname'=>'LBL_BUGS',
                ),
                'email_id' => array(
                        'name'=>'email_id',
                        'vname'=>'LBL_EMAIL_ID',
                        'type'=>'id',
                        'reportable' => false,
                        'source' => 'non-db',
                ),
                'emails' => array (
                        'name' => 'emails',
                        'type' => 'link',
                        'relationship' => 'email_onlinedocuments',
                        'source'=>'non-db',
                        'vname'=>'LBL_EMAILS',
                ),
                'project_id' => array(
                        'name'=>'project_id',
                        'vname'=>'LBL_PROJECT_ID',
                        'type'=>'id',
                        'reportable' => false,
                        'source' => 'non-db',
                ),
                'projects' => array (
                        'name' => 'projects',
                        'type' => 'link',
                        'relationship' => 'project_onlinedocuments',
                        'source'=>'non-db',
                        'vname'=>'LBL_PROJECTS',
                ),
                'projecttask_id' => array(
                        'name'=>'projecttask_id',
                        'vname'=>'LBL_PROJECTTASK_ID',
                        'type'=>'id',
                        'reportable' => false,
                        'source' => 'non-db',
                ),
                'projecttasks' => array (
                        'name' => 'projecttasks',
                        'type' => 'link',
                        'relationship' => 'projecttask_documents',
                        'source'=>'non-db',
                        'vname'=>'LBL_PROJECTTASKS',
                ),
                'meeting_id' => array(
                        'name'=>'meeting_id',
                        'vname'=>'LBL_MEETING_ID',
                        'type'=>'id',
                        'reportable' => false,
                        'source' => 'non-db',
                ),
                'meetings' => array (
                        'name' => 'meetings',
                        'type' => 'link',
                        'relationship' => 'meeting_onlinedocuments',
                        'source'=>'non-db',
                        'vname'=>'LBL_MEETINGS',
                ),
                'call_id' => array(
                        'name'=>'call_id',
                        'vname'=>'LBL_CALL_ID',
                        'type'=>'id',
                        'reportable' => false,
                        'source' => 'non-db',
                ),
                'calls' => array (
                        'name' => 'calls',
                        'type' => 'link',
                        'relationship' => 'call_onlinedocuments',
                        'source'=>'non-db',
                        'vname'=>'LBL_CALLS',
                ),
                'task_id' => array(
                        'name'=>'task_id',
                        'vname'=>'LBL_TASK_ID',
                        'type'=>'id',
                        'reportable' => false,
                        'source' => 'non-db',
                ),
                'tasks' => array (
                        'name' => 'tasks',
                        'type' => 'link',
                        'relationship' => 'task_onlinedocuments',
                        'source'=>'non-db',
                        'vname'=>'LBL_TASKS',
                ),
                //END fields used for contract documents subpanel.
                // EINTEL Addition
                'SecurityGroups' => array (
                        'name' => 'SecurityGroups',
                        'type' => 'link',
                        'relationship' => 'securitygroups_onlinedocuments',
                        'module'=>'SecurityGroups',
                        'bean_name'=>'SecurityGroup',
                        'source'=>'non-db',
                        'vname'=>'LBL_SECURITYGROUPS',
                ),

        ),
        'indices' => array (
            array(
                'name' => 'idx_doc_cat',
                'type' => 'index',
                'fields' => array(
                    'category_id',
                    'subcategory_id',
                ),
            ),
            array(
                'name' =>'idx_onlinedocuments_parent',
                'type'=>'index',
                'fields'=>array('parent_id', 'parent_type')
            ),
        ),
        'relationships' => array (
            'onlinedocument_revisions' => array('lhs_module'=> 'OnlineDocuments', 'lhs_table'=> 'onlinedocuments', 'lhs_key' => 'id',
                        'rhs_module'=> 'OnlineDocumentRevisions', 'rhs_table'=> 'onlinedocument_revisions', 'rhs_key' => 'document_id',
                        'relationship_type'=>'one-to-many'),
            'onlinedocuments_modified_user' => array('lhs_module'=> 'Users', 'lhs_table'=> 'users', 'lhs_key' => 'id',
                        'rhs_module'=> 'OnlineDocuments', 'rhs_table'=> 'onlinedocuments', 'rhs_key' => 'modified_user_id',
                        'relationship_type'=>'one-to-many'),
            'onlinedocuments_created_by' => array('lhs_module'=> 'Users', 'lhs_table'=> 'users', 'lhs_key' => 'id',
                        'rhs_module'=> 'OnlineDocuments', 'rhs_table'=> 'onlinedocuments', 'rhs_key' => 'created_by',
                        'relationship_type'=>'one-to-many'),
        ),

);
VardefManager::createVardef('OnlineDocuments','OnlineDocument', array('default',));
?>

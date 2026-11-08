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
/*********************************************************************************

 * Description:  Defines the English language pack for the base application.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

$mod_strings = array (
	//module
	'LBL_MODULE_NAME' => 'OnlineDocuments',
	'LBL_MODULE_TITLE' => 'OnlineDocuments: Home',
	'LNK_NEW_DOCUMENT' => 'Create Online Doc',
	'LNK_DOCUMENT_LIST'=> 'Online Documents List',
	'LBL_DOC_REV_HEADER' => 'Online Document Revisions',
	'LBL_SEARCH_FORM_TITLE'=> 'Online Document Search',
	//vardef labels
	'LBL_DOCUMENT_ID' => 'Online Document ID',
	'LBL_NAME' => 'Online Document Name',
	'LBL_DESCRIPTION' => 'Description',
	'LBL_CATEGORY' => 'Category',
	'LBL_SUBCATEGORY' => 'Sub Category',
	'LBL_STATUS' => 'Status',
	'LBL_CREATED_BY'=> 'Created by',
	'LBL_DATE_ENTERED'=> 'Date Entered',
	'LBL_DATE_MODIFIED'=> 'Date Modified',
	'LBL_DELETED' => 'Deleted',
	'LBL_MODIFIED'=> 'Modified by ID',
	'LBL_MODIFIED_USER' => 'Modified by',
	'LBL_CREATED'=> 'Created by',
	'LBL_REVISIONS'=>'Revisions',
	'LBL_IS_TEMPLATE'=>'Is a Template',
	'LBL_TEMPLATE_TYPE'=>'Online Document Type',
	'LBL_ASSIGNED_TO_NAME'=>'Assigned to:',
	'LBL_REVISION_NAME' => 'Revision Number',
	'LBL_REVISION' => 'Revision',
	'LBL_DOCUMENT' => 'Related Online Document',
	'LBL_LATEST_REVISION' => 'Latest Revision',
	'LBL_CHANGE_LOG'=> 'Change Log',
	'LBL_ACTIVE_DATE'=> 'Publish Date',
	'LBL_EXPIRATION_DATE' => 'Expiration Date',

	'LBL_CAT_OR_SUBCAT_UNSPEC'=>'Unspecified',
	//quick search
	'LBL_NEW_FORM_TITLE' => 'New Online Document',
	//online document edit and detail view
	'LBL_DOC_NAME' => 'Online Document Name:',
	'LBL_FILENAME' => 'File Name:',
	'LBL_DOC_VERSION' => 'Revision:',
	'LBL_CATEGORY_VALUE' => 'Category:',
	'LBL_SUBCATEGORY_VALUE'=> 'Sub Category:',
	'LBL_DOC_STATUS'=> 'Status:',
	'LBL_LAST_REV_CREATOR' => 'Revision Created By:',
	'LBL_LAST_REV_DATE' => 'Revision Date:',
	'LBL_DOWNNLOAD_FILE'=> 'Download File:',
	'LBL_DET_IS_TEMPLATE'=>'Template? :',
	'LBL_DET_TEMPLATE_TYPE'=>'Online Document Type:',
	'LBL_DOC_DESCRIPTION'=>'Description:',
	'LBL_DOC_ACTIVE_DATE'=> 'Publish Date:',
	'LBL_DOC_EXP_DATE'=> 'Expiration Date:',

	//online document list view.
	'LBL_LIST_FORM_TITLE' => 'Online Document List',
	'LBL_LIST_DOCUMENT' => 'Online Document',
	'LBL_LIST_CATEGORY' => 'Category',
	'LBL_LIST_SUBCATEGORY' => 'Sub Category',
	'LBL_LIST_REVISION' => 'Revision',
	'LBL_LIST_LAST_REV_CREATOR' => 'Published By',
	'LBL_LIST_LAST_REV_DATE' => 'Revision Date',
	'LBL_LIST_VIEW_DOCUMENT'=>'View',
    'LBL_LIST_DOWNLOAD'=> 'Download',
	'LBL_LIST_ACTIVE_DATE' => 'Publish Date',
	'LBL_LIST_EXP_DATE' => 'Expiration Date',
	'LBL_LIST_STATUS'=>'Status',

	//online document search form.
	'LBL_SF_DOCUMENT' => 'Online Document Name:',
	'LBL_SF_CATEGORY' => 'Category:',
	'LBL_SF_SUBCATEGORY'=> 'Sub Category:',
	'LBL_SF_ACTIVE_DATE' => 'Publish Date:',
	'LBL_SF_EXP_DATE'=> 'Expiration Date:',

	'DEF_CREATE_LOG' => 'Document Created',
    'DEF_REVISE_LOG' => 'Document Revised',

	//error messages
	'ERR_DOC_NAME'=>'Online Document Name',
	'ERR_DOC_ACTIVE_DATE'=>'Publish Date',
	'ERR_DOC_EXP_DATE'=> 'Expiration Date',
	'ERR_FILENAME'=> 'File Name',
	'ERR_DOC_VERSION'=> 'Online Document Version',
	'ERR_DELETE_CONFIRM'=> 'Do you want to delete this online document revision?',
	'ERR_DELETE_LATEST_VERSION'=> 'You are not allowed to delete the latest revision of a online document.',
	'LNK_NEW_MAIL_MERGE' => 'Mail Merge',
	'LBL_MAIL_MERGE_DOCUMENT' => 'Mail Merge Template:',

	'LBL_TREE_TITLE' => 'OnlineDocuments',
	//sub-panel vardefs.
	'LBL_LIST_DOCUMENT_NAME'=>'Online Document Name',
	'LBL_LIST_IS_TEMPLATE'=>'Template?',
	'LBL_LIST_TEMPLATE_TYPE'=>'Document Type',
	'LBL_LIST_SELECTED_REVISION'=>'Selected Revision',
	'LBL_LIST_LATEST_REVISION'=>'Latest Revision',
	'LBL_LAST_REV_CREATE_DATE'=>'Last Revision Create Date',
    //'LNK_DOCUMENT_CAT'=>'Online Document Categories',
    'LBL_CREATED_USER' => 'Created User',
    'LBL_THEREVISIONS_SUBPANEL_TITLE' => 'Reversions',

    // EINTEL Addition
    'LBL_SECURITYGROUPS_SUBPANEL_TITLE' => "Security Groups",
    'LBL_GOOGLEDOC_NAME' => 'Google Document',
    'LBL_GOOGLEDOC' => 'Google Document',
    'LBL_EXPORTURLS' => 'Download As:',
    'LBL_DOCUMENT_URL' => 'Edit in Google:',

    // Links to other modules
    'LBL_PARENT_TYPE' => 'Parent Type',
    'LBL_PARENT_ID' => 'Parent ID:',
    'LBL_RELATED_TO' => 'Related To:',
    'LBL_ACCOUNT_ID' => 'Account ID:',
    'LBL_LIST_RELATED_TO' => 'Related To',
    'LBL_CONTACT_ID' => 'Contact ID:',
    'LBL_OPPORTUNITY_ID' => 'Opportunity ID:',
    'LBL_CASE_ID' => 'Case ID:',
    'LBL_LEAD_ID' => 'Lead ID:',
    'LBL_BUG_ID' => 'Bug ID:',
    'LBL_EMAIL_ID' => 'Email ID:',
    'LBL_PROJECT_ID' => 'Project ID:',
    'LBL_PROJECTTASK_ID' => 'Project Task ID:',
    'LBL_MEETING_ID' => 'Meeting ID:',
    'LBL_CALL_ID' => 'Call ID:',
    'LBL_TASK_ID' => 'Task ID:',
);


?>

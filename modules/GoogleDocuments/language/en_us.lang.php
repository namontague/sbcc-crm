<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
* This file is an customisation to SugarCRM developed by Enterprise Intelligence
* Systems Pty Ltd.  Copyright (C) 2011 Enterprise Intelligence Systems Pty Ltd.
*
* SugarCRM is a customer relationship management program developed by
* SugarCRM, Inc.
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
* You can contact Enterprise Intelligence Systems Pty Ltd. headquarters at
* 25/20 Enterprise Dr, Bundoora, Vic, 3083, Australia or at email address
* info@eintel.com.au
*
* The interactive user interfaces in modified source and object code versions
* of this program must display Appropriate Legal Notices, as required undeGNU Affero General Public Licenseeneral Public License version 3.
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

$mod_strings = array(
    // module
    'LBL_MODULE_NAME' => 'GoogleDocuments',
    'LBL_MODULE_TITLE' => 'Google Documents: Home',
    'LNK_NEW_DOCUMENT' => 'Create Google Doc',
    'LNK_GOOGLE_DOCUMENT_LIST' => 'Google Documents List',
    'LBL_SEARCH_FORM_TITLE'=> 'Google Document Search',
    //vardef labels
    'LBL_DOCUMENT_ID' => 'Google Document ID',
    'LBL_NAME' => 'Document Name',
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

    'LBL_RELATED_DOCUMENT_ID'=>'Related Google Document ID',
    'LBL_RELATED_DOCUMENT_REVISION_ID'=>'Related Google Document Revision ID',
    'LBL_IS_TEMPLATE'=>'Is a Template',
    'LBL_TEMPLATE_TYPE'=>'Google Document Type',
    'LBL_ASSIGNED_TO_NAME'=>'Assigned to:',
    'LBL_REVISION_NAME' => 'Revision Number',
    'LBL_MIME' => 'Mime Type',
    'LBL_REVISION' => 'Revision',
    'LBL_DOCUMENT' => 'Related Google Document',
    'LBL_CHANGE_LOG'=> 'Change Log',
    'LBL_ACTIVE_DATE'=> 'Publish Date',
    'LBL_EXPIRATION_DATE' => 'Expiration Date',
    'LBL_FILE_EXTENSION'  => 'File Extension',

    'LBL_CAT_OR_SUBCAT_UNSPEC'=>'Unspecified',
    //quick search
    'LBL_NEW_FORM_TITLE' => 'New Google Document',
    //googledocument edit and detail view
    'LBL_DOCURL' => 'Edit in Google:',
    'LBL_CONTENTURL' => 'Content:',
    'LBL_EXPORTURLS' => 'Download As:',
    'LBL_DOCTITLE' => 'Title:',
    'LBL_DOCTYPE' => 'Type:',

    'LBL_CATEGORY_VALUE' => 'Category:',
    'LBL_SUBCATEGORY_VALUE'=> 'Sub Category:',
    'LBL_DOC_STATUS'=> 'Status:',
    'LBL_LAST_REV_CREATOR' => 'Revision Created By:',
    'LBL_LAST_REV_DATE' => 'Revision Date:',
    'LBL_DOWNNLOAD_FILE'=> 'Download File:',
    'LBL_DET_RELATED_DOCUMENT'=>'Related Google Document:',
    'LBL_DET_RELATED_DOCUMENT_VERSION'=>"Related Google Document Revision:",
    'LBL_DET_IS_TEMPLATE'=>'Template? :',
    'LBL_DET_TEMPLATE_TYPE'=>'Google Document Type:',
    'LBL_DOC_DESCRIPTION'=>'Description:',
    'LBL_DOC_ACTIVE_DATE'=> 'Publish Date:',
    'LBL_DOC_EXP_DATE'=> 'Expiration Date:',
    'LBL_DOCID' => 'Document ID',
    'LBL_CONTENTTYPE' => 'Content Type',

    //googledocument list view.
    'LBL_LIST_FORM_TITLE' => 'Google Document List',
    'LBL_LIST_DOCUMENT' => 'Google Document',
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

    //googledocument search form.
    'LBL_SF_DOCUMENT' => 'Google Document Name:',
    'LBL_SF_CATEGORY' => 'Category:',
    'LBL_SF_SUBCATEGORY'=> 'Sub Category:',
    'LBL_SF_ACTIVE_DATE' => 'Publish Date:',
    'LBL_SF_EXP_DATE'=> 'Expiration Date:',

    'DEF_CREATE_LOG' => 'Google Document Created',

    //error messages
    'ERR_DOC_NAME'=>'Google Document Name',
    'ERR_DOC_ACTIVE_DATE'=>'Publish Date',
    'ERR_DOC_EXP_DATE'=> 'Expiration Date',
    'ERR_FILENAME'=> 'File Name',
    'ERR_DOC_VERSION'=> 'Google Document Version',
    'ERR_DELETE_CONFIRM'=> 'Do you want to delete this google document revision?',
    'ERR_DELETE_LATEST_VERSION'=> 'You are not allowed to delete the latest revision of a google document.',
    'LNK_NEW_MAIL_MERGE' => 'Mail Merge',
    'LBL_MAIL_MERGE_DOCUMENT' => 'Mail Merge Template:',

    'LBL_TREE_TITLE' => 'GoogleDocuments',
    //sub-panel vardefs.
    'LBL_LIST_DOCUMENT_NAME'=>'Google Document Name',
    'LBL_CONTRACT_NAME'=>'Contract Name:',
    'LBL_LIST_IS_TEMPLATE'=>'Template?',
    'LBL_LIST_TEMPLATE_TYPE'=>'Google Document Type',
    'LBL_LIST_SELECTED_REVISION'=>'Selected Revision',
    'LBL_LIST_LATEST_REVISION'=>'Latest Revision',
    'LBL_CONTRACTS_SUBPANEL_TITLE'=>'Related Contracts',
    'LBL_LAST_REV_CREATE_DATE'=>'Last Revision Create Date',
    //'LNK_DOCUMENT_CAT'=>'Google Document Categories',
    'LBL_CONTRACTS' => 'Contracts',
    'LBL_CREATED_USER' => 'Created User',
    'LBL_THEREVISIONS_SUBPANEL_TITLE' => 'Reversions',

    // EINTEL Addition
    'LBL_SECURITYGROUPS_SUBPANEL_TITLE' => "Security Groups",

    // Google Document Revisions
    'LBL_THEREVISIONS_SUBPANEL_TITLE' => 'Revisions',
    'LBL_DOC_REV_HEADER' => 'Document Revisions',
    'LBL_LATEST_REVISION' => 'Latest Revision',
    'LBL_REVISIONS'=>'Revisions',
    'LBL_DOC_VERSION' => 'Revision:',
);
?>

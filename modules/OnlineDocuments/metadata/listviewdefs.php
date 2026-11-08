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



$listViewDefs['OnlineDocuments'] = array(
    'FILE_URL' => array(
        'width' => '2', 
        'label' => '&nbsp;', 
        'link' => true,
        'default' => true,
        'related_fields' => array('document_revision_id'),
        'sortable' => false), 
	'DOCUMENT_NAME' => array(
		'width' => '20',
		'label' => 'LBL_NAME', 
		'link' => true,
        'default' => true),
    'MODIFIED_BY_NAME' => array(
        'width' => '10',
        'label' => 'LBL_MODIFIED_USER',
        'module' => 'Users',
        'id' => 'USERS_ID',      
        'default' => false,
        'sortable' => false,
        'related_fields' => array('modified_user_id')),         
    'CATEGORY_ID' => array(
        'width' => '40', 
        'label' => 'LBL_LIST_CATEGORY',
        'default' => true),
    'SUBCATEGORY_ID' => array(
        'width' => '20',
        'label' => 'LBL_LIST_SUBCATEGORY',
        'default' => true),
    'CREATED_BY_NAME' => array(
        'width' => '2', 
        'label' => 'LBL_LIST_LAST_REV_CREATOR',
        'default' => true,
        'sortable' => false),
    'LAST_REV_CREATE_DATE' => array(
        'width' => '10', 
        'label' => 'LBL_LIST_LAST_REV_DATE',
        'default' => true,
        'sortable' => false,
        'related_fields' => array('document_revision_id')),
    'ACTIVE_DATE' => array(
        'width' => '10', 
        'label' => 'LBL_LIST_ACTIVE_DATE',
        'default' => true),
    'EXP_DATE' => array(
        'width' => '10', 
        'label' => 'LBL_LIST_EXP_DATE',
        'default' => true),
    'EXPORT_URLS' => array(
        'width' => '40',
        'label' => 'LBL_EXPORTURLS',
        'link' => false,
        'customCode' => '{$EXPORT_URLS}',
        // 'customCode' =>'{foreach from=$fields.export_urls.value key="lbl" item="url"}<a href="{$url}" target="_blank">{$lbl}</a> {/foreach}',
        'default' => true,
    ),
    'DOCUMENT_URL' => array(
        'width' => '20',
        'label' => 'LBL_DOCUMENT_URL',
        'link' => false,
        'customCode' =>'<a href="{$DOCUMENT_URL}" target="_blank">Edit</a>',
        'default' => true,
    ),
);
?>

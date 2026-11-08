<?php
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
$viewdefs['OnlineDocumentRevisions']['DetailView'] = array(
        'templateMeta' => array('maxColumns' => '2',
                'form' => array(
                    'hidden' => array(
                        '<input type="hidden" name="old_id" value="{$fields.document_revision_id.value}">'
                        ),
                    'buttons' => '',
                    ),
                'widths' => array(
                        array('label' => '10', 'field' => '30'),
                        array('label' => '10', 'field' => '30')
                ),
        ),
        'panels' =>array (

                array (

                        array (
                                'name' => 'document_name',
                                'label' => 'LBL_DOC_NAME',
                                'customCode' => '<a href="index.php?module=OnlineDocuments&action=DetailView&record={$fields.document_id.value}">{$fields.document_name.value}</a>'
                        ),

                        array (
                                'name' => 'revision',
                                'label' => 'LBL_DOC_VERSION',
                        ),
                ),

                array (
                        '',
                        'latest_revision',
                ),

                array (
                        'created_by_name',
                        'date_entered',
                ),


                array (
                        'change_log',
                ),

                array(
                        array(
                                'name' => 'export_urls',
                                'customCode' => '{foreach from=$fields.export_urls.value key="lbl" item="url"}<a href="{$url}" target="_blank">{$lbl}</a> {/foreach}'
                        ),
                        array(
                                'name' => 'document_url',
                                'label' => 'LBL_DOCUMENT_URL',
                                'customCode' => '<a href="{$fields.document_url.value}" target="_blank">Edit</a>'
                        ),
                ),
        )
);
?>
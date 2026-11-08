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
$viewdefs['OnlineDocumentRevisions']['EditView'] = array(
    'templateMeta' => array(
        'form' => array(
            'enctype' => 'multipart/form-data',
            'hidden' => array(
                '<input type="hidden" name="SaveRevision" value="1">',
                ),
            ),
            'maxColumns' => '2',
            'widths' => array(
                    array('label' => '10', 'field' => '30'),
                    array('label' => '10', 'field' => '30'),
            ),
            'javascript' => '<script type="text/javascript" src="include/javascript/popup_parent_helper.js?s={$SUGAR_VERSION}&c={$JS_CUSTOM_VERSION}"></script>
                            <script type="text/javascript" src="include/jsolait/init.js?s={$SUGAR_VERSION}&c={$JS_CUSTOM_VERSION}"></script>
                            <script type="text/javascript" src="include/jsolait/lib/urllib.js?s={$SUGAR_VERSION}&c={$JS_CUSTOM_VERSION}"></script>
                            <script type="text/javascript" src="include/javascript/jsclass_base.js"></script>
                            <script type="text/javascript" src="include/javascript/jsclass_async.js"></script>
                            <script type="text/javascript" src="include/JSON.js?s={$SUGAR_VERSION}"></script>
                            <script type="text/javascript" src="modules/OnlineDocuments/documents.js?s={$SUGAR_VERSION}&c={$JS_CUSTOM_VERSION}"></script>',
    ),
    'panels' =>array (
        'default' => array (
            array (
                array(
                    'name' => 'filename',
                    'label' => 'LBL_DOC_NAME',
                    'customCode' => '<a href="index.php?module=OnlineDocuments&action=DetailView&record={$ONLINE_DOC_ID}">{$ONLINE_DOC_NAME}</a>',
                ),
                array(
                    'label' => 'LBL_LATEST_REVISION',
                    'customCode' => '<label>{$LATEST_REVISION}</label>'
                ),
            ),
            array(
                array(
                    'name' => 'googledoc_name',
                    'type' => 'GoogleDoc',
                    'displayParams' => array('required' => true),
                ),
                array(
                    'name' => 'revision',
                    'displayParams' => array('required' => true),
                ),
            ),
            array(
                array(
                    'name' => 'change_log',
                ),
            ),
        ),
    )
);
?>
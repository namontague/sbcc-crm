<?php
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
 * of this program must display Appropriate Legal Notices, as required underGNU Affero General Public Licenseeneral Public License version 3.
 *
 * In accordanGNU Affero General Public Licensee GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 ********************************************************************************/

$viewdefs['GoogleDocuments']['EditView'] = array(
    'templateMeta' => array(
        'maxColumns' => '2',
        'widths' => array(
            array('label' => '10', 'field' => '30'),
            array('label' => '10', 'field' => '30'),
        ),
        'javascript' => '<script type="text/javascript" src="modules/GoogleDocuments/javascript/editview.js"></script>',

    ),
    'panels' => array(
        'default' => array(
            array(
                'name',
                '',
            ),
            array(
                'description',
            ),
            array(
                array(
                    'name' => 'document_title',
                ),
                array(
                    'name' => 'document_type',
                ),
            ),
            array(
                array(
                    'name' => 'List',
                    'customCode' => '<input class="reportid" type="button" value="Select Google Document" name="button_google_doc_list" onClick=onExecute(); />' .
                                    '<input name="document_id" type="hidden" value="{$fields.document_id.value}"/>' .
                                    '<input name="document_url" type="hidden" value="{$fields.document_url.value}"/>' .
                                    '<input name="content_type" type="hidden" value="{$fields.content_type.value}"/>' .
                                    '<input name="content_url" type="hidden" value="{$fields.content_url.value}"/>'
                ),
            ),
        ),
    )
);
?>
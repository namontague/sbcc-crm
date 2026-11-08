<?php
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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


require_once('include/generic/SugarWidgets/SugarWidgetField.php');
//TODO Rename this to edit link
class SugarWidgetSubPanelGoogleDocumentDownloadButton extends SugarWidgetField
{
    function displayHeaderCell(&$layout_def)
    {
        global $app_strings;
        return $app_strings['GOOGLEDOCUMENT_DOWNLOAD_AS'];
    }

    function displayList(&$layout_def)
    {
        global $app_strings, $beanList;

        $edit_icon_html = SugarThemeRegistry::current()->getImage('edit_inline',
            'align="absmiddle" alt="' . $app_strings['LNK_EDIT'] . '" border="0"');

        if ($layout_def['EditView']) {

            if ($layout_def['module'] == 'OnlineDocumentRevisions') {
                $currentBean = new OnlineDocumentRevision();
                $currentBean = $currentBean->retrieve($layout_def['fields']['ID']);

                $list_item = new GoogleDocument();
                $list_item = $list_item->retrieve($currentBean->googledoc_id);
            } else if ($layout_def['module'] == 'OnlineDocuments') {
                $currentBean = new OnlineDocument();
                $currentBean = $currentBean->retrieve($layout_def['fields']['ID']);

                $list_item = new GoogleDocument();
                $list_item = $list_item->retrieve($currentBean->googledoc_id);
            } else {
                $list_item = new $beanList[$layout_def['module']]();
                $list_item = $list_item->retrieve($layout_def['fields']['ID']);
            }

            if ($list_item) {
                $list_item->fill_in_export_urls();

                $export_urls = $list_item->export_urls;

                $returnHTML = '';

                foreach ($export_urls as $key => $value) {
                    $returnHTML = $returnHTML . '<a href="' . $value . '" target="_blank">' . $key . '</a>&nbsp;';
                }
            }
            return $returnHTML;

        } else {
            return '';
        }
    }

}

?>
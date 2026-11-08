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

 * Description:  TODO To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

global $mod_strings;
global $current_user;


if(ACLController::checkAccess('GoogleDocuments', 'edit', true))
// eintel code for displaying popup for the Google Docs API integratation
$module_menu[]=Array("index.php?module=GoogleDocuments&action=EditView&return_module=GoogleDocuments&return_action=DetailView", $mod_strings['LNK_NEW_DOCUMENT'],"CreateDocuments");
// $module_menu[]=Array("javascript:onExecute(2)", $mod_strings['LNK_NEW_DOCUMENT'],"CreateDocuments");
// 'customCode' => '<input class="reportid" type="button" value="Execute Report" name="report_run"  onClick=onExecute("{$fields.id.value}"); />'
// eintel code for displaying popup for the Google Docs API integratation

if(ACLController::checkAccess('GoogleDocuments', 'list', true))
$module_menu[]=Array("index.php?module=GoogleDocuments&action=index", $mod_strings['LNK_GOOGLE_DOCUMENT_LIST'],"GoogleDocuments");
// Google Docs Integration
// if(ACLController::checkAccess('GoogleDocuments', 'list', true))$module_menu[]=Array("index.php?module=GoogleDocuments&action=index", $mod_strings['LNK_GOOGLE_DOCUMENT_LIST'],"GoogleDocuments");
// Google Docs Integration

if(ACLController::checkAccess('GoogleDocuments', 'edit', true)) {

    $admin = new Administration();
    $admin->retrieveSettings();
    $user_merge = $current_user->getPreference('mailmerge_on');
    if ($user_merge == 'on' && isset($admin->settings['system_mailmerge_on']) && $admin->settings['system_mailmerge_on']) {
        $module_menu[] = Array("index.php?module=MailMerge&action=index&reset=true", $mod_strings['LNK_NEW_MAIL_MERGE'],"GoogleDocuments");
    }
}
?>

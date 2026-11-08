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

 * Description:  Base Form For Notes
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/


require_once('include/formbase.php');

global $mod_strings;
$mod_strings = return_module_language($current_language, 'OnlineDocuments');

$prefix='';

$Revision = new OnlineDocumentRevision();
$Document = new OnlineDocument();
if (isset($_REQUEST['record'])) {
	$Document->retrieve($_REQUEST['record']);
}
if(!$Document->ACLAccess('Save')){
		ACLController::displayNoAccess(true);
		sugar_cleanup(true);
}
	
$Document = populateFromPost($prefix, $Document);

// Create a google document record
if ((!empty($_REQUEST['document_id'])) && (empty($Document->id))) {

    $googleDoc = new GoogleDocument();
    
    $googleDoc->name = $_REQUEST['document_title'];
    $googleDoc->document_title = $_REQUEST['document_title'];
    $googleDoc->document_type = $_REQUEST['document_type'];
    $googleDoc->document_id = $_REQUEST['document_id'];
    $googleDoc->document_url = $_REQUEST['document_url'];
    $googleDoc->content_type = $_REQUEST['content_type'];
    $googleDoc->content_url = $_REQUEST['content_url'];

    $db = DBManagerFactory::getInstance();
    $query = "select * from googledocuments where document_id='".$_REQUEST['document_id']."'";
    $result = $db->query($query);

    if (!empty($result)) {
        $row = $db->fetchByAssoc($result);
        if (empty($row)) {
            $googleDoc->save();
            $Document->googledoc_id = $googleDoc->id;
        } else {
            $Document->googledoc_id  = $row['id'];
        }
    }
    
}


$Revision->filename = $Document->googledoc_name;
$Revision->googledoc_name = $Document->googledoc_name;
$Revision->googledoc_id = $Document->googledoc_id;

if (isset($Document->id)) {
	//save online document
	$return_id = $Document->save();
    // $Revision->change_log = $mod_strings['DEF_REVISE_LOG'];
} else {
	//save online document
	$return_id = $Document->save();

	//save online document revision.
	$Revision->change_log = $mod_strings['DEF_CREATE_LOG'];
	$Revision->revision = $Document->revision;
	$Revision->document_id = $Document->id;
	$Revision->save();
	
	//update online document with latest revision id
	$Document->process_save_dates=false; //make sure that conversion does not happen again.
	$Document->document_revision_id = $Revision->id;
	$Document->save();
}

$return_id = $Document->id;


$GLOBALS['log']->debug("Saved record with id of ".$return_id);
handleRedirect($return_id, "OnlineDocuments");
?>
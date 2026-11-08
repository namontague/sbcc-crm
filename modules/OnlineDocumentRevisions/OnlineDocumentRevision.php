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

 * Description: TODO:  To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/




require_once('include/upload_file.php');

// User is used to store Forecast information.
class OnlineDocumentRevision extends SugarBean {

	var $id;
	var $document_id;
	var $date_entered;
	var $created_by;
	var $filename;
	var $revision;
	var $change_log;
	var $document_name;
	var $latest_revision;
	var $file_url;
	var $created_by_name;

	var $img_name;
	var $img_name_bare;
	
	var $table_name = "onlinedocument_revisions";	
	var $object_name = "OnlineDocumentRevision";
	var $module_dir = 'OnlineDocumentRevisions';
	var $new_schema = true;
	var $latest_revision_id;

    var $googledoc_name;
	var $googledoc_id;
    var $export_urls;
    var $document_url;
    
	var $encodeFields = Array();

	// This is used to retrieve related fields from form posts.
	var $additional_column_fields = Array(
        'googledoc_id',
        );
    
	// This is the list of fields that are in the lists.
	var $list_fields = Array("id"
		,"document_id"
		,"date_entered"
		,"created_by"
		,"filename"	
		,"revision"
		,"file_url"
		,"change_log"
		,"created_by_name"
		);
		
	var $required_fields = Array("revision");
	
	

	function OnlineDocumentRevision() {
		parent::SugarBean();
		$this->setupCustomFields('OnlineDocumentRevisions');  //parameter is module name
		$this->disable_row_level_security =true; //no direct access to this module. 
	}

	function save($check_notify = false){	
		parent::save($check_notify);
				
		//update Onlinedocuments table.
		//$query = "UPDATE Onlinedocuments set document_version_id='$this->id' where id = '$this->document_id'";	
		//$this->db->query($query);
	}
	function get_summary_text()
	{
		return "$this->filename";
	}

	function retrieve($id, $encode=false){
		$ret = parent::retrieve($id, $encode);	
		
		return $ret;
	}

	function is_authenticated()
	{
		return $this->authenticated;
	}

	function fill_in_additional_list_fields() {
		$this->fill_in_additional_detail_fields();
	}

	function fill_in_additional_detail_fields()
	{
		global $theme;
		global $current_language;
		
		parent::fill_in_additional_detail_fields();
		
		$mod_strings=return_module_language($current_language, 'OnlineDocuments');
		
		//find the online document name and current version.
		$query = "SELECT document_name, revision, document_revision_id FROM onlinedocuments, onlinedocument_revisions where onlinedocuments.id = '$this->document_id' AND onlinedocument_revisions.id = onlinedocuments.document_revision_id";
		$result = $this->db->query($query,true,"Error fetching document details...:");
		$row = $this->db->fetchByAssoc($result);
		if ($row != null) {
			$this->document_name = $row['document_name'];
			$this->latest_revision = $row['revision'];	
			$this->latest_revision_id = $row['document_revision_id'];
		}
		//populate the file url. 
		//image is selected based on the extension name <ext>_image_inline, extension is stored in onlinedocument_revisions.
		//if file is not found then default image file will be used.
		global $img_name;
		global $img_name_bare;
		
		//set default file name.
		if (!empty($img_name) && file_exists($img_name)) {
			$img_name = $img_name_bare;			
		}
		else {
			$img_name = "def_image_inline";  //todo change the default image.						
		}

        // google document record
        $googleDoc = new GoogleDocument();
        $googleDoc->retrieve($this->googledoc_id);
        $googleDoc->fill_in_additional_detail_fields();
        $this->export_urls = $googleDoc->export_urls;
        $this->document_url = $googleDoc->document_url;
	}
	
	/**
	 * Returns a filename based off of the logical (Sugar-side) Online Document name and combined with the revision. Tailor
	 * this to needs created by email RFCs, filesystem name conventions, charset conventions etc.
	 * @param string revId Revision ID if not latest
	 * @return string formatted name
	 */
	function getDocumentRevisionNameForDisplay($revId='') {
		global $sugar_config;
		global $current_language;
		
		$localLabels = return_module_language($current_language, 'OnlineDocumentRevisions');
		
		// prep - get source Document
		if(!class_exists('OnlineDocuments')) {
			
		}
		$document = new OnlineDocument();
		
		// use passed revision ID
		if(!empty($revId)) {
			$tempDoc = new OnlineDocumentRevision();
			$tempDoc->retrieve($revId);
		} else {
			$tempDoc = $this;
		}
		
		// get logical name
		$document->retrieve($tempDoc->document_id);
		$logicalName = $document->document_name;
		
		// get revision string
		$revString = '';
		if(!empty($tempDoc->revision)) {
			$revString = "-{$localLabels['LBL_REVISION']}_{$tempDoc->revision}";
		}
		
		// get extension
		$realFilename = $tempDoc->filename;
		$fileExtension_beg = strrpos($realFilename, ".");
		$fileExtension = "";
		
		if($fileExtension_beg > 0) {
			$fileExtension = substr($realFilename, $fileExtension_beg + 1);
		}
		//check to see if this is a file with extension located in "badext"
		foreach($sugar_config['upload_badext'] as $badExt) {
	       	if(strtolower($fileExtension) == strtolower($badExt)) {
		       	//if found, then append with .txt to filename and break out of lookup
		       	//this will make sure that the file goes out with right extension, but is stored
		       	//as a text in db.
		        $fileExtension .= ".txt";
		        break; // no need to look for more
	       	}
        }
		$fileExtension = ".".$fileExtension;
		
		$return = $logicalName.$revString.$fileExtension;
		
		// apply RFC limitations here
		if(mb_strlen($return) > 1024) {
			// do something if we find a real RFC issue
		}
		
		return $return;
	}

	function fill_document_name_revision($doc_id) {

		//find the online document name and current version.
		$query = "SELECT onlinedocuments.document_name, revision FROM onlinedocuments, onlinedocument_revisions where onlinedocuments.id = '$doc_id'";
		$query .= " AND onlinedocument_revisions.id = onlinedocuments.document_revision_id";
		$result = $this->db->query($query,true,"Error fetching online document details...:");
		$row = $this->db->fetchByAssoc($result);
		if ($row != null) {
			$this->name = $row['document_name'];
			$this->latest_revision = $row['revision'];	
		}	
	}
	
	function list_view_parse_additional_sections(&$list_form, $xTemplateSection){
		return $list_form;
	}
	
	function get_list_view_data(){
		$revision_fields = $this->get_list_view_array();

		$forecast_fields['FILE_URL'] = $this->file_url;						
		return $revision_fields;
	}

	//static function..
	function get_document_revision_name($doc_revision_id){
		if (empty($doc_revision_id)) return null;
		
		$db = DBManagerFactory::getInstance();				
		$query="select revision from onlinedocument_revisions where id='$doc_revision_id'";
		$result=$db->query($query);
		if (!empty($result)) {
			$row=$db->fetchByAssoc($result);
			if (!empty($row)) {
				return $row['revision'];
			}
		}
		return null;
	}
	
	//static function.
	function get_latest_document_revision($doc_id){
		$return_array= Array();
		if (empty($doc_id)) return $return_array;
		
		$db = DBManagerFactory::getInstance();
		$query="select revision from onlinedocument_revisions where document_id='$doc_id' and deleted=0 and date_entered=(SELECT MAX(date_entered) FROM onlinedocument_revisions)";

		$result=$db->query($query);
		if (!empty($result)) {
			while (($row=$db->fetchByAssoc($result)) != null) {
				$return_value = $row['revision'];
			}
		}
		return $return_value;
	}	
}
?>
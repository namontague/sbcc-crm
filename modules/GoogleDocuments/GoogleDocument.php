<?php
if(!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');
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
require_once ('include/upload_file.php');
require_once('modules/GoogleDocuments/GoogleClient/GoogleClientLogin.php');
require_once('modules/GoogleDocuments/GoogleClient/GoogleDocsFeed.php');

// User is used to store Forecast information.
class GoogleDocument extends Basic {

    var $id;
    var $document_name;

    var $created_by;
    var $date_entered;
    var $date_modified;
    var $modified_user_id;

    var $table_name = "googledocuments";
    var $object_name = "GoogleDocument";
    var $user_preferences;

    var $encodeFields = Array ();

    var $new_schema = true;
    var $module_dir = 'GoogleDocuments';

    var $document_type;
    var $content_url;
    var $export_urls;

    
    function GoogleDocument() {
        parent::Basic();
    }

    function save($check_notify = false) {

       return parent :: save($check_notify);        
    }
    
    function get_summary_text() {
        return "$this->document_name";
    }

    function fill_in_additional_detail_fields() {
        $this->fill_in_export_urls();
        parent::fill_in_additional_detail_fields();
        error_log(print_r($this->export_urls,true));
    }

    function fill_in_additional_list_fields() {
        parent::fill_in_additional_list_fields();
        $this->fill_in_export_urls();
        $exportLinks ='';
        foreach($this->export_urls as $lbl => $url) {
            $exportLinks .= "<a href='$url' target='_blank'>$lbl</a> ";
        }
        $this->export_urls = $exportLinks;
        error_log(print_r($this->export_urls,true));
    }

    function fill_in_export_urls() {
        $this->export_urls = array();
        if( $this->content_url && $this->document_type) {
            $doctype_to_exportformats = array(
                'document' => array('doc','odt','pdf','rtf','html','txt','jpeg','png','zip'),
                'drawing'=> array('jpeg','png','svg','pdf'),
                'presentation'=> array('ppt','swf','pdf','png','txt'),
                'spreadsheet'=> array('xls','ods','csv','tsv','pdf','html'),
            );
            $export_format_labels = array(
                'doc' => 'Word Doc' ,//	Microsoft Word
                'html' => 'HTML', //	HTML Format
                'jpeg' => 'JPEG', //	Joint Photographic Experts Group Image Format (used for Google Drawings)
                'odt' => 'ODT', //	Open Document Format
                'pdf' => 'PDF', //	Portable Document Format
                'png' => 'PNG',//	Portable Networks Graphic Image Format
                'rtf' => 'RTF', //	Rich Text Format
                'txt' => 'Text', //	Text file
                'zip' => 'Zip', //ZIP archive. Contains the images (if any) used in the document, as well as a .html file containing the document's text.
                'svg' => 'SVG', //Scalable Vector Graphics Image Format
                'ppt' => 'Powerpoint',
                'swf' => 'Flash',
                'xls' => 'Excel',//	XLS (Microsoft Excel)
                'csv' => 'CSV',//	CSV (Comma Seperated Value). Only the first worksheet is returned in CSV by default.
                'ods' => 'ODS',//	ODS (Open Document Spreadsheet)
                'tsv' => 'TSV',//	TSV (Tab Seperated Value). Only the first worksheet is returned in TSV by default.
            );
            if( array_key_exists($this->document_type, $doctype_to_exportformats)) {
                $exportFormats = $doctype_to_exportformats[$this->document_type];
                foreach($exportFormats as $format) {
                    $exportLabel = $export_format_labels[$format];
                    $exportUrl = $this->content_url."&exportFormat=".$format."&format=".$format;
                    $this->export_urls[$exportLabel]=$exportUrl;
                }
            } else {
                $this->export_urls['Default'] = $this->content_url;
            }
        }
    }


    //static function.
    function get_document_name($doc_id) {
        if (empty($doc_id)) return null;

        $db = DBManagerFactory::getInstance();
        $query="select document_name from googledocuments where id='$doc_id'";
        $result=$db->query($query);
        if (!empty($result)) {
            $row=$db->fetchByAssoc($result);
            if (!empty($row)) {
                return $row['document_name'];
            }
        }
        return null;
    }

    /*
     * Function which will return the xml retrieved from the Google DOCS API
     */
    function getGoogleDocsAPIXml($search_title){

        $auth_token = '';

        //check if the google doc auth token exists within the session variable
        if($_SESSION['GoogleAuthToken'] == null) {
            // get the auth token from google
            $gcl = new GoogleClientLogin();
            $gcl->getHttpRequestObject();
            $auth_token = $gcl->startGoogleLoginProcess();
            $_SESSION['GoogleAuthToken'] = $auth_token;
        } else {
            $auth_token = (string)($_SESSION['GoogleAuthToken']);
        }

        // get the XML feed from Google Docs API
        $gdf = new GoogleDocsFeed();
        return $gdf->getGoogleDocsFeed($auth_token, $search_title);
    }

    /*
     * Function which will create an empty google document and return the
     * url link to access the file
     */
    function createEmptyGoogleDoc($title, $kind){

        $auth_token = '';

        //check if the google doc auth token exists within the session variable
        if($_SESSION['GoogleAuthToken'] == null) {
            // get the auth token from google
            $gcl = new GoogleClientLogin();
            $gcl->getHttpRequestObject();
            $auth_token = $gcl->startGoogleLoginProcess();
            $_SESSION['GoogleAuthToken'] = $auth_token;
        } else {
            $auth_token = (string)($_SESSION['GoogleAuthToken']);
        }

        // get the XML feed from Google Docs API
        $gdf = new GoogleDocsFeed();
        return $gdf->getCreatedGoogleDocLink($auth_token, $title, $kind);
    }

    /*
     * Function which will upload a document to google docs and return the
     * document parameters
     */
    function uploadGoogleDoc($fileName, $fileType, $fileLocation, $convert) {

        $auth_token = '';

        //check if the google doc auth token exists within the session variable
        if($_SESSION['GoogleAuthToken'] == null) {
            // get the auth token from google
            $gcl = new GoogleClientLogin();
            $gcl->getHttpRequestObject();
            $auth_token = $gcl->startGoogleLoginProcess();
            $_SESSION['GoogleAuthToken'] = $auth_token;
        } else {
            $auth_token = (string)($_SESSION['GoogleAuthToken']);
        }

        // get the XML feed from Google Docs API
        $gdf = new GoogleDocsFeed();
        return $gdf->getUploadedGoogleDocLink($auth_token, $fileName, $fileType, $fileLocation, $convert);
    }
}
?>
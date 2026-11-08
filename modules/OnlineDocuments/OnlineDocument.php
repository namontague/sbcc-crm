<?php
if (!defined('sugarEntry') || !sugarEntry) {
  die('Not A Valid Entry Point');
}
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
require_once ('include/upload_file.php');


// User is used to store Forecast information.
class OnlineDocument extends SugarBean {

  var $id;
  var $document_name;
  var $description;
  var $category_id;
  var $subcategory_id;
  var $status_id;
  var $status;
  var $created_by;
  var $date_entered;
  var $date_modified;
  var $modified_user_id;
  var $active_date;
  var $exp_date;
  var $document_revision_id;
  var $filename;

  var $img_name;
  var $img_name_bare;
  var $is_template;
  var $template_type;

  //additional fields.
  var $revision;
  var $last_rev_create_date;
  var $last_rev_created_by;
  var $last_rev_created_name;
  var $file_url;
  var $file_url_noimage;

  var $table_name = "onlinedocuments";
  var $object_name = "OnlineDocument";
  var $user_preferences;

  var $encodeFields = Array();

  var $new_schema = true;
  var $module_dir = 'OnlineDocuments';

  var $save_file;

  var $googledoc_name;
  var $googledoc_id;
  var $export_urls;
  var $document_url;
  var $parent_type;
  var $parent_id;
  var $parent_name;

  var $additional_column_fields = array(
    'parent_name',
    'googledoc_id',
    'revision',
  );

  var $relationship_fields = array(
  );

  function OnlineDocument() {
    parent :: SugarBean();
    $this->setupCustomFields('OnlineDocuments'); //parameter is module name
    $this->disable_row_level_security = false;

    global $timedate;
    // Get current time in db date time format & server time
    $now = date($timedate->get_db_date_time_format(), strtotime("now"));
    // Convert from db format to display format
    $this->active_date = $timedate->to_display_date_time($now, true, false);
    $this->revision = 1;
  }

  function save($check_notify = false) {
    return parent :: save($check_notify);
    if( empty($this->document_name)) {
      $this->document_name = $this->googledoc_name;
    }
  }

  function get_summary_text() {
    return "$this->document_name";
  }


  function is_authenticated() {
    return $this->authenticated;
  }

  function fill_in_additional_list_fields() {
    parent::fill_in_additional_list_fields();
    $this->retrieve($this->id);

    // google document record
    $googleDoc = new GoogleDocument();
    $googleDoc->retrieve($this->googledoc_id);

    $googleDoc->fill_in_export_urls();
    $exportLinks = '';
    foreach ($googleDoc->export_urls as $lbl => $url) {
      $exportLinks .= "<a href='$url' target='_blank'>$lbl</a> ";
    }

    $this->export_urls = $exportLinks;
    $this->document_url = $googleDoc->document_url;

    error_log(print_r($this->export_urls, true));
  }

  function fill_in_additional_detail_fields() {
    global $theme;
    global $current_language;
    global $timedate;

    parent::fill_in_additional_detail_fields();

    $mod_strings = return_module_language($current_language, 'OnlineDocuments');

    $query = "SELECT filename, revision FROM onlinedocument_revisions WHERE id='$this->document_revision_id'";

    $result = $this->db->query($query);
    $row = $this->db->fetchByAssoc($result);

    //popuplate filename
    if (isset($row['filename'])) {
      $this->filename = $row['filename'];
    }
    //$this->latest_revision = $row['revision'];
    if (isset($row['revision'])) {
      $this->revision = $row['revision'];
    }

    //populate the file url.
    //image is selected based on the extension name <ext>_icon_inline, extension is stored in onlinedocument_revisions.
    //if file is not found then default image file will be used.
    global $img_name;
    global $img_name_bare;

    //set default file name.
    if (!empty ($img_name) && file_exists($img_name)) {
      $img_name = $img_name_bare;
    } else {
      $img_name = "def_image_inline"; //todo change the default image.
    }

    //get last_rev_by user name.
    $query = "SELECT first_name,last_name, onlinedocument_revisions.date_entered as rev_date FROM users, onlinedocument_revisions WHERE users.id = onlinedocument_revisions.created_by and onlinedocument_revisions.id = '$this->document_revision_id'";
    $result = $this->db->query($query, true, "Eror fetching user name: ");
    $row = $this->db->fetchByAssoc($result);
    if (!empty ($row)) {
      $this->last_rev_created_name = $row['first_name'] . ' ' . $row['last_name'];

      $this->last_rev_create_date = $timedate->to_display_date_time($row['rev_date']);
    }

    global $app_list_strings;
    if (!empty($this->status_id)) {
      //_pp($this->status_id);
      $this->status = $app_list_strings['document_status_dom'][$this->status_id];
    }
    $this->save_file = basename($this->file_url_noimage);

    // google document record
    $googleDoc = new GoogleDocument();
    $googleDoc->retrieve($this->googledoc_id);
    $googleDoc->fill_in_export_urls();
    $this->export_urls = $googleDoc->export_urls;
    $this->document_url = $googleDoc->document_url;

  }

  function list_view_parse_additional_sections(& $list_form, $xTemplateSection) {
    return $list_form;
  }

  function create_export_query(&$order_by, &$where, $relate_link_join = '') {
    $custom_join = $this->custom_fields->getJOIN(true, true, $where);
    if ($custom_join) {
      $custom_join['join'] .= $relate_link_join;
    }
    $query = "SELECT
						onlinedocuments.*";
    if ($custom_join) {
      $query .= $custom_join['select'];
    }
    $query .= " FROM onlinedocuments ";
    if ($custom_join) {
      $query .= $custom_join['join'];
    }

    $where_auto = " onlinedocuments.deleted = 0";

    if ($where != "") {
      $query .= " WHERE $where AND " . $where_auto;
    }
    else
    {
      $query .= " WHERE " . $where_auto;
    }

    if ($order_by != "") {
      $query .= " ORDER BY $order_by";
    }
    else
    {
      $query .= " ORDER BY onlinedocuments.document_name";
    }

    return $query;
  }

  function get_list_view_data() {
    global $current_language;
    $app_list_strings = return_app_list_strings_language($current_language);

    $document_fields = $this->get_list_view_array();
    $document_fields['FILE_URL'] = $this->file_url;
    $document_fields['FILE_URL_NOIMAGE'] = $this->file_url_noimage;
    $document_fields['LAST_REV_CREATED_BY'] = $this->last_rev_created_name;
    $document_fields['CATEGORY_ID'] = empty ($this->category_id) ? ""
      : $app_list_strings['document_category_dom'][$this->category_id];
    $document_fields['SUBCATEGORY_ID'] = empty ($this->subcategory_id) ? ""
      : $app_list_strings['document_subcategory_dom'][$this->subcategory_id];

    $document_fields['DOCUMENT_NAME_JAVASCRIPT'] = $GLOBALS['db']->helper->escape_quote($document_fields['DOCUMENT_NAME']);
    return $document_fields;
  }

  function mark_relationships_deleted($id) {
    //do nothing, this call is here to avoid default delete processing since
    //delete.php handles deletion of online document revisions.
  }

  function bean_implements($interface) {
    switch ($interface) {
      case 'ACL' :
        return true;
    }
    return false;
  }

  //static function.
  function get_document_name($doc_id) {
    if (empty($doc_id)) {
      return null;
    }

    $db = DBManagerFactory::getInstance();
    $query = "select document_name from onlinedocuments where id='$doc_id'";
    $result = $db->query($query);
    if (!empty($result)) {
      $row = $db->fetchByAssoc($result);
      if (!empty($row)) {
        return $row['document_name'];
      }
    }
    return null;
  }
}

?>
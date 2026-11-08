<?PHP

/* * *******************************************************************************
 * (C) 2009 Enterprise Intelligence Systems Pty Ltd
 * No part of this file should be used without the express permission
 * of the authors.
 *
 * Contact: info@eintel.com.au for more information.
 *
 *
 * ****************************************************************************** */

class ZohoReport extends SugarBean {

    var $new_schema = true;
    var $module_dir = 'ZohoReports';
    var $object_name = 'ZohoReport';
    var $table_name = 'zoho_reports';
    var $importable = false;
    var $disable_row_level_security = true;
    // to ensure that modules created and deployed under CE will continue to function under team security if the instance is upgraded to PRO
    var $id;
    var $name;
    var $date_entered;
    var $date_modified;
    var $modified_user_id;
    var $modified_by_name;
    var $created_by;
    var $created_by_name;
    var $description;
    var $deleted;
    var $created_by_link;
    var $modified_user_link;
    var $url;
    var $status;


    function ZohoReport() {
        parent::SugarBean();
    }

    function bean_implements($interface) {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }
}

function zoho_reports_related_modules() {
    static $relatedModules = null;
    if (!$relatedModules) {
        global $beanList;
        global $app_list_strings;
        $relatedModules = array('' => '');
        $db = DBManagerFactory::getInstance();
        $result = $db->query("select lhs_module from relationships where rhs_module='ZohoReports' and rhs_key='related_module' and deleted = 0");
        if ($result) {
            while (($row = $db->fetchByAssoc($result)) != null) {
                $module = $row['lhs_module'];
                if (!empty($app_list_strings['moduleListSingular'][$module])) {
                    $name = $app_list_strings['moduleListSingular'][$module];
                } else if (!empty($app_list_strings['moduleList'][$module])) {
                    $name = $app_list_strings['moduleList'][$module];
                } else {
                    $name = $module;
                }
                $relatedModules[$beanList[$module]] = $name;
            }
        }
    }
    return $relatedModules;
}

?>
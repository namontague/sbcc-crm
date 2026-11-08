<?PHP
/*********************************************************************************
 * (C) 2009 Enterprise Intelligence Systems Pty Ltd
 * No part of this file should be used without the express permission
 * of the authors.
 *
 * Contact: info@eintel.com.au for more information.
 *
 *
 ********************************************************************************/


class ReportViewer extends SugarBean {
  var $new_schema = true;
  var $module_dir = 'ReportViewer';
  var $object_name = 'ReportViewer';
  var $table_name = 'reportviewer';
  var $importable = false;

  var $disable_row_level_security = true; // to ensure that modules created and deployed under CE will continue to function under team security if the instance is upgraded to PRO

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
  var $assigned_user_id;
  var $assigned_user_name;
  var $assigned_user_link;
  var $format;
  var $status;
  var $report_location;
  var $related_module;


  function ReportViewer() {
    parent::SugarBean();
  }

  function bean_implements($interface) {
    switch ($interface) {
      case 'ACL':
        return true;
    }
    return false;
  }

  function get_summary_text()  {
    return $this->name;
  }

  function execute($bean) {
    if( $this->status != 'Enabled' || empty($this->report_location)) {
      return null;
    }
    if($this->related_module) {
      if($bean == null || $bean->module_dir != $this->related_module ) {
//        return null;
      }
    }
    require_once ('modules/ReportServer/ReportRestClient.php');
    $missing_params = false;

    $rc = new ReportRestClient();
    $rc->format($this->format);
    $rc->reportUnit = $this->report_location;
    $rc->report_params_xml = '';
            
    $paramsList = $rc->getRestInputsArray($rc->reportUnit);
   
    $userInputCount = count($paramsList);
    if( $userInputCount > 0 ) {
        foreach($paramsList as $param) {
            $value = $this->paramDefaultValue((string)$param->id, $bean);
            if( $value != null ) {
                $rc->report_params[(string)$param->id] = $value;
                $rc->report_params_xml .= "<parameter name='{$param->id}'>{$value}</parameter>";
            }
        }
    }
    
    return $rc->displayBinaryResult();
    //need to display an error to the user to say why report was not added as an attachement
  }

  function paramDefaultValue($paramName, $bean) {
      $paramPath = explode('.',$paramName, 2);
      if( count($paramPath) > 1 ) {
          if( $paramPath[0] == 'bean') {
              $bean = $bean;
          } else if( $paramPath[0] == 'user') {
              global $current_user;
              $bean = $current_user;
          }
          $paramName = $paramPath[1];
      }
      $value = null;
      if( isset($bean)){
          if(array_key_exists($paramName, $bean->field_defs)) {
              $value = $bean->$paramName;
          }
      }
      return $value;
  }

  function getFileName() {
    return $this->name.'.'.strtolower($this->format);
  }

  public function getMimeType() {
    switch($this->format) {
      case "PDF":
        return 'application/pdf';
      case "HTML":
        return 'text/html';
      case "XLS":
        return 'application/xls';
      case "CSV":
        return 'application/csv';
      case "RTF":
        return 'application/rtf';
      case "XML":
        return 'application/xml';
      default:
        return 'application/octet-stream';
    }
  }

  public function getFileNameFor($bean) {
    $query = "select filename from reportviewer_beans where report_id='{$this->id}' and bean_module='{$bean->module_dir}' and bean_id='{$bean->id}' and deleted=0";
    $name = $this->db->getOne($query);
    return $this->buildFileNameFrom($name);
  }

  public function buildFileNameFrom($name) {
    if( empty($name) ) {
      return $this->getFileName();
    }
    return $name.'.'.strtolower($this->format);
  }
}


function reports_related_modules() {
  static $relatedModules = null;
  if (!$relatedModules) {
    global $beanList;
    global $app_list_strings;;
    $relatedModules = array('' => '');
    $db = DBManagerFactory::getInstance();
    $result = $db->query("select lhs_module from relationships where rhs_module='ReportViewer' and rhs_key='related_module' and deleted = 0");
    if ($result) {
      while (($row = $db->fetchByAssoc($result)) != null) {
        $module=$row['lhs_module'];
        if(!empty($app_list_strings['moduleListSingular'][$module])) {
          $name = $app_list_strings['moduleListSingular'][$module];
        } else if (!empty($app_list_strings['moduleList'][$module])) {
          $name = $app_list_strings['moduleList'][$module];
        } else {
          $name= $module;
        }
        $relatedModules[$beanList[$module]] = $name;
      }
    }
  }
  return $relatedModules;
}

?>
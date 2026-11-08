<?PHP
/*********************************************************************************
 * SugarCRM is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2011 SugarCRM Inc.
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

class asol_Activity extends Basic {
  var $new_schema = true;
  var $module_dir = 'asol_Activity';
  var $object_name = 'asol_Activity';
  var $table_name = 'asol_activity';
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
  var $condition_activity;
  var $asol_activity_id_c;
  var $next_activity;
  var $predecessor_id;
  var $delay;


  function asol_Activity() {
    parent::Basic();
  }

  /**
   *
   * This function is intented to scan the database looking for the parent activity.
   * Used by getTrigger($act_id), getParentEvent()
   * @param (string) $activity_id
   */
  function getTriggerArray($activity_id) {

    global $db;

    $this->load_relationship('asol_events');
    $rel = $this->asol_events;
    $relInfo = $rel->_relationship;

    $sqlTriggerModule = "SELECT " . $relInfo->lhs_table . "." . $relInfo->lhs_key . ", " . $relInfo->lhs_table . ".trigger_event";
    $sqlTriggerModule .= " FROM " . $relInfo->lhs_table;
    $sqlTriggerModule .= " LEFT JOIN " . $relInfo->join_table . " ON " . $relInfo->lhs_table . "." . $relInfo->lhs_key . "=" . $relInfo->join_table . "." . $relInfo->join_key_lhs;
    $sqlTriggerModule .= " WHERE " . $relInfo->join_table . "." . $relInfo->join_key_rhs . "='" . $activity_id . "'";

    $eventQuery = $db->query($sqlTriggerModule);
    $eventRow = $db->fetchByAssoc($eventQuery);

    return $eventRow;
  }

  /**
   *
   * This function gets the trigger for the function getContextModule().
   * @param (string) $act_id
   */
  function getTrigger($act_id) {

    $activity_id = $act_id;

    $eventRow = $this->getTriggerArray($activity_id);

    $trigger = explode(" - ", $eventRow['trigger_event']);

    return $trigger[0];
  }

  /**
   *
   * This function gets the parent activity id (directly related with the event by sugar) for any activity.
   * @param (string) $activity_id
   */
  function getParentActivityId($activity_id) {

    global $db;

    $parent_query = $db->query("SELECT predecessor_id FROM asol_activity WHERE id='" . $activity_id . "' AND predecessor_id IS NOT NULL AND deleted=0");
    $parent_row = $db->fetchByAssoc($parent_query);
    if ($parent_row != null) {
      $activity_id = $parent_row['predecessor_id'];
    }

    return $activity_id;
  }

  /**
   *
   * This function gets the parent module(trigger) for any activity(new or already created).
   */
  function getContextModule() {

    $activity_id = $this->id;

    if (!empty($activity_id)) { // If the activity is already created then it prints the module name(trigger).
      $activity_id = $this->getParentActivityId($activity_id);

      return $this->getTrigger($activity_id);
    }
    else { // Else, it will take the name from the hidden information in the save-sugar button.

      if ($_REQUEST["return_module"] == "asol_Events") { // The Activity is going to be created from asol_Events
        $event_module_id = $_REQUEST["return_id"]; // return_id is within save-sugar button?s php code

        return $this->getTriggerEventModuleName($event_module_id);
      }
      elseif ($_REQUEST['return_module'] == "asol_Activity") { // The Activity is going to be created from asol_Activity
        $activity_id = $_REQUEST['return_id'];
        $activity_id = $this->getParentActivityId($activity_id);

        return $this->getTrigger($activity_id);
      }
      else {

      }
    }
  }

  /**
   *
   * This function gets the parent event?s id for any activity.
   */
  function getParentEvent() {

    global $db;
    $activity_id = $this->id;

    $activity_id = $this->getParentActivityId($activity_id);
    $event_array = $this->getTriggerArray($activity_id);

    return $event_array["id"];
  }

  /**
   *
   * When a activity is going to be created, we need to get the event module?s name. We do that by means of the save-sugar button.
   * @param (string) $event_module_id
   */
  function getTriggerEventModuleName($event_module_id) {

    global $db;

    $eventQuery = $db->query("SELECT trigger_event  FROM asol_events WHERE id='" . $event_module_id . "' AND deleted=0");
    $eventRow = $db->fetchByAssoc($eventQuery);

    $trigger = explode(' - ', $eventRow['trigger_event']);

    return $trigger[0]; /////////////////////////////////////////////////////////////////$trigger["name"]
  }

  /**
   *
   * This function gets the event module?s name from an event modules?s id.
   * @param (string) $event_module_id
   */
  function getEventModuleNameFromEventModuleId($event_module_id) {

    global $db;

    $eventQuery = $db->query("SELECT name  FROM asol_events WHERE id='" . $event_module_id . "' AND deleted=0");
    $eventRow = $db->fetchByAssoc($eventQuery);

    return $eventRow["name"];
  }

  /**
   *
   * This function gets the delay that was saved in the database.
   */
  function getSavedDelay() {

    global $db;
    $activity_id = $this->id;

    $parent_query = $db->query("SELECT delay  FROM asol_activity WHERE id='" . $activity_id . "' AND deleted=0");
    $parent_row = $db->fetchByAssoc($parent_query);

    return $parent_row;
  }

  function getSelectionResults($query, $offset = 0, $entries = 0) {

    global $db;

    $queryResults = $db->query($query);

    while ($queryRow = $db->fetchByAssoc($queryResults))
    {
      $retArray[] = $queryRow;
    }
    return $retArray;
  }

  function getFieldInfoFromVardefs($module, $field) {
    ////////////////////////////////////////////////////////////////////////////////////////
    // patch to solve a bug in table "relationships": the module "campaigns" is in lowercase
    if ($module == 'campaigns') {
      $module = 'Campaigns';
    }
    ////////////////////////////////////////////////////////////////////////////////////////

    global $beanList, $beanFiles, $app_list_strings;
    $class_name = $beanList[$module];
    require_once($beanFiles[$class_name]);
    $bean = new $class_name();
    $field_defs = $bean->field_defs;

    $result = '';
    foreach ($field_defs as $name => $values) {
      if ($name == $field) {

        if ($values['type'] == 'currency') {
          $result = 'currency';
        } else {
          if (($values['type'] == 'enum') || ($values['type'] == 'multienum') || ($values['type'] == 'radioenum')) {
            if ($values['options'] != '') {
              $result = $app_list_strings[$values['options']];
            } else {
                if (isset($values['function'])) {
                    $function = $values['function'];
                    if(is_array($values['function'])){
                        $function = $values['function']['name'];
                        if(isset($values['function']['include']) && file_exists($values['function']['include'])){
                            require_once($values['function']['include']);
                        }
                    }
                    $result = call_user_func($function);
                }
            }
          }
        }
        break;
      }
    }

    return $result;
  }

}

?>

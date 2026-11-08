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

class asol_task extends Basic {
	var $new_schema = true;
	var $module_dir = 'asol_Task';
	var $object_name = 'asol_Task';
	var $table_name = 'asol_task';
	var $importable = false;
	var $disable_row_level_security = true ; // to ensure that modules created and deployed under CE will continue to function under team security if the instance is upgraded to PRO
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
	var $delay_type;
	var $delay;
	var $order_task;
	var $task_type;
	var $task_implementation;
  var $asol_activity_id;

	function asol_task(){
    parent::Basic();
	}

	function getSelectionResults($query, $offset = 0, $entries = 0){

		global $db;

		$queryResults = $db->query($query);

		while($queryRow = $db->fetchByAssoc($queryResults))
		{
			$retArray[] = $queryRow;
		}
		return $retArray;
	}

	function isRequiredField($module, $field) {
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

		return $field_defs[$field]["required"];

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
		foreach ($field_defs as $name=>$values) {
			if ($name == $field) {

				if ($values['type'] == 'currency') {
					$result = 'currency';
				} else if (($values['type'] == 'enum') || ($values['type'] == 'multienum') || ($values['type'] == 'radioenum')) {

          if ($values['options'] != '') {
            $result = $app_list_strings[$values['options']];
          } else {
            if ($values['function'] != '') {
              $result = $values['function']();
            }
          }
				}
				break;
			}
		}

		return $result;
	}


	/**
	 *
	 * This function gets the Context Module for any task (new or already created).
	 */
	function getContextModule() {

		global $db;
		$task_id = $this->id;
			
		if (!empty($task_id)) {// If the task is already created then it prints the module name(trigger).
			$activity_id = $this->asol_activity_id;
		}
		else {// Else, it will take the name from the hidden information in the save-sugar button.
			$activity_id = $_REQUEST["relate_id"];
		}

		$focus = new asol_Activity();
		$focus->retrieve($activity_id);
			
		return $focus->getContextModule();
	}


}
?>

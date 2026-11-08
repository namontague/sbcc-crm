<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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

//$eventType = (isset($_REQUEST['eventType'])) ? $_REQUEST['eventType'] : "";//////////*******de cuando estaban separados los events por type en las vistas... lo puso dani as? y luego se lo cambi? a todo junto

$module_name = 'asol_Events';
$object_name = 'asol_Events';
$_module_name = 'asol_events';



$parent_module = (isset($_REQUEST['parent_module'])) ? $_REQUEST['parent_module'] : "";
$parent_id = (isset($_REQUEST['parent_id'])) ? $_REQUEST['parent_id'] : ""; // If parent_module=asol_Task -> the parent_id is the id of the process selected in the task call_process (not the task_id).


$GLOBALS['log']->debug("*********************** ASOL: popupdefs.php from asol_Events \$parent_module=[".$parent_module."]");
$GLOBALS['log']->debug("*********************** ASOL: popupdefs.php from asol_Events \$parent_id=[".$parent_id."]");

if ($parent_module == 'asol_Process') {
	$GLOBALS['log']->debug("*********************** ASOL: popupdefs.php asol_Process ");
		//global $db;
		$sql = 'asol_events.asol_process_id IS NULL';
}
elseif ($parent_module == 'asol_Task') {
	$GLOBALS['log']->debug("*********************** ASOL: popupdefs.php asol_Task ");
	
	$sql = "asol_events.trigger_type='subprocess' and asol_events.asol_process_id='".$parent_id."'";
}



$popupMeta = array('moduleMain' => $module_name,
						'varName' => $object_name,
						'orderBy' => $_module_name.'.name',
						'whereClauses' => 
							array('name' => $_module_name . '.name', 
								),
							'searchInputs'=> array($_module_name. '_number', 'name', 'priority','status'),
							
							/*'whereStatement' => 'asol_events.id NOT IN (
																		SELECT DISTINCT asol_procea8ca_events_idb 
																		FROM asol_proces_asol_events_c 
																		WHERE deleted = 0
																	) 
											',
							*/	
							'whereStatement' => $sql,
						);
?>
 
 

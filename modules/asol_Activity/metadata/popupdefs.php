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
$GLOBALS['log']->debug("*********************** ASOL: popupdefs.php  ");
$module_name = 'asol_Activity';
$object_name = 'asol_Activity';
$_module_name = 'asol_activity';

$parent_module = (isset($_REQUEST['parent_module'])) ? $_REQUEST['parent_module'] : "";
$parent_id = (isset($_REQUEST['parent_id'])) ? $_REQUEST['parent_id'] : "";
//$relate_table = ($_REQUEST['parent_module'] == 'activity') ? "asol_activisol_activity_c" : "asol_eventssol_activity_c";
//$relate_field = ($_REQUEST['parent_module'] == 'activity') ? "asol_activ9e2dctivity_idb" : "asol_eventssol_activity_c";


//$relate_table = "asol_activisol_activity_c";
//$relate_field_left = "asol_activ898activity_ida";
//$relate_field_right = "asol_activ9e2dctivity_idb";
//
//$relate_sql = 'AND ('.$_module_name.'.id NOT IN (SELECT DISTINCT '.$relate_field_left.' FROM '.$relate_table.' WHERE deleted=0)) '; //************** que la actividad no tenga seleccionada a otra actividad??????????????
//$relate_sql .= 'AND ('.$_module_name.'.id NOT IN (SELECT DISTINCT '.$relate_field_right.' FROM '.$relate_table.' WHERE deleted=0))';//***************que la actividad no est? seleccionada por otra actividad

//	$processes_not_parent_process__query = $db->query("
	//															SELECT asol_proce6f14process_ida AS process_id
	//															FROM asol_proces_asol_events_c
	//															WHERE asol_procea8ca_events_idb != '".$parent_id."'
	//														");
	//	$processes_not_parent_process__array = Array();
	//	while($processes_not_parent_process__row = $db->fetchByAssoc($processes_not_parent_process__query)){
	//		$processes_not_parent_process__array[]= $processes_not_parent_process__row['process_id'];
	//	}
	//	foreach($processes_not_parent_process__array as $key => $value) {
	//
	//	}

	//
	//	$activities_not_from_parent_process__query = $db->query("
	//																SELECT asol_eventssol_activity_c.asol_activ9e2dctivity_idb AS activity_id
	//																FROM asol_eventssol_activity_c
	//																INNER JOIN asol_proces_asol_events_c ON (asol_proces_asol_events_c.asol_procea8ca_events_idb !='".$parent_id."' AND  asol_proces_asol_events_c.deleted = 0)
	//																WHERE asol_eventssol_activity_c.deleted = 0
	//															");
	//	$activities_not_from_parent_process__row = $db->fetchByAssoc($activities_not_from_parent_process__query);



	//	$events_from_parent_process_id_query = $db->query("
	//															SELECT asol_procea8ca_events_idb
	//															FROM asol_proces_asol_events_c
	//															WHERE asol_proce6f14process_ida = '".$parent_process_id_row['parent_process_id']."'
	//														");


$GLOBALS['log']->debug("*********************** ASOL: popupdefs.php \$parent_module=[".$parent_module."]");
$GLOBALS['log']->debug("*********************** ASOL: popupdefs.php \$parent_id=[".$parent_id."]");

if ($parent_module == 'asol_Events') {
	$GLOBALS['log']->debug("*********************** ASOL: popupdefs.php asol_Events ");
		global $db;

	
	
	$parent_process_id__query = $db->query("
                                SELECT asol_process_id AS process_id
                                FROM asol_events
                                WHERE id  = '" . $parent_id . "'
														");
	$parent_process_id__row=$db->fetchByAssoc($parent_process_id__query);
	$parent_process_id = $parent_process_id__row['asol_proce6f14process_ida'];
	
	$sql = "
				(
					asol_activity.id NOT IN (SELECT DISTINCT asol_activ9e2dctivity_idb FROM asol_activisol_activity_c WHERE deleted = 0)
					AND asol_activity.id NOT IN (
														SELECT asol_events_asol_activity.asol_activity_id
														FROM asol_events_asol_activity
														INNER JOIN asol_events
														ON (asol_events.id = asol_events_asol_activity.asol_events_id AND asol_events.asol_process_id !='".$parent_process_id."' AND  asol_events.deleted = 0)
														WHERE ( asol_events_asol_activity.deleted = 0)
												)
					AND asol_activity.id NOT IN (
													SELECT asol_activity_id
													FROM asol_events_asol_activity
													WHERE asol_events_id = '".$parent_id."' AND deleted = 0
												)
					AND asol_activity.deleted = 0
				)
			";

}
elseif ($parent_module == 'asol_Activity') { // next_activity
	$GLOBALS['log']->debug("*********************** ASOL: popupdefs.php asol_Activity ");
	$sql = "
				( 
					asol_activity.id != '".$parent_id."'
          AND asol_activity.id NOT IN (SELECT DISTINCT asol_activity_id FROM asol_events_asol_activity WHERE deleted = 0)
					AND asol_activity.deleted = 0
				)
		   ";
}



$popupMeta = array(
					'moduleMain' => $module_name,
					'varName' => $object_name,
					'orderBy' => $_module_name.'.name',
					'whereClauses' => 
										array(
												'name' => $_module_name . '.name',
												),
										'searchInputs'=> 
												array(
														$_module_name. '_number', 'name', 'priority','status'
														),

										//'whereStatement' => '('.$_module_name.'.id!="'.$parent_id.'") '.$relate_sql,
					
										'whereStatement' => $sql,				
				);
?>


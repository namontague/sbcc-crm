<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Professional End User
 * License Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/products/sugar-professional-eula.html
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004-2006 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
/*********************************************************************************

 * Description: TODO:  To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('XTemplate/xtpl.php');
require_once('data/Tracker.php');
require_once('modules/Timesheet/Timesheet.php');
require_once('modules/Timesheet/Forms.php');

if(module_enabled('Timesheet')) {
global $timedate;
global $current_user;
global $app_strings;
global $app_list_strings;
global $mod_strings;
global $sugar_version, $sugar_config;



echo "<p>";
echo get_module_title($mod_strings['LBL_MODULE_NAME'], $mod_strings['LBL_MODULE_NAME'].": Report", true);
echo "</p>";
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

//layout_utils.php no longer valid
//require_once($theme_path.'layout_utils.php');

$GLOBALS['log']->info("Timesheet Report");

$xtpl=new XTemplate('modules/Timesheet/Report.html');
$xtpl->assign("MOD", $mod_strings);
$xtpl->assign("APP", $app_strings);

$xtpl->assign("CALENDAR_LANG", "en");
$xtpl->assign("USER_DATEFORMAT", '('.$timedate->get_user_date_format().')');
$xtpl->assign("CALENDAR_DATEFORMAT", $timedate->get_cal_date_format());


if (isset($_REQUEST['return_module'])) $xtpl->assign("RETURN_MODULE", $_REQUEST['return_module']);
if (isset($_REQUEST['return_action'])) $xtpl->assign("RETURN_ACTION", $_REQUEST['return_action']);
if (isset($_REQUEST['return_id'])) $xtpl->assign("RETURN_ID", $_REQUEST['return_id']);
// handle Create $module then Cancel
if (empty($_REQUEST['return_id'])) {
	$xtpl->assign("RETURN_ACTION", 'index');
}
$xtpl->assign("THEME", $theme);
$xtpl->assign("IMAGE_PATH", $image_path);$xtpl->assign("PRINT_URL", "index.php?".$GLOBALS['request_string']);

$user_id = NULL;
if (!empty($_REQUEST['user_id'])) {
  $user_id = $_REQUEST['user_id'];
}

$project_id = NULL;
if (!empty($_REQUEST['project_id'])) {
  $project_id = $_REQUEST['project_id'];
}

$date_start = NULL;
if (!empty($_REQUEST['date_start'])) {
  $date_start = $_REQUEST['date_start'];
  $xtpl->assign("date_start", $date_start);
}

$date_end = NULL;
if (!empty($_REQUEST['date_end'])) {
  $date_end = $_REQUEST['date_end'];
  $xtpl->assign("date_end", $date_end);
}

/*
 * Nathan
 */
$invoice_id = NULL;
if (!empty($_REQUEST['invoice_id'])) {
  $invoice_id = $_REQUEST['invoice_id'];
}

$xtpl->assign("INVOICE_ID", $invoice_id);
$xtpl->assign("INVOICE_FILTER", get_select_options_with_id(Timesheet::get_invoices_array(), $invoice_id));
/*
 * End
 */


$xtpl->assign("USER_ID", $user_id);
$xtpl->assign("USER_FILTER", get_select_options_with_id(get_user_array(true), $user_id));

$xtpl->assign("PROJECT_FILTER", get_select_options_with_id(Timesheet::get_projects_array(), $project_id));

function sortEntries($entries) {
  global $timedate;
  $aEntries = array();

  foreach ($entries as $entry) {
    $entry['date_booked'] = $timedate->to_display_date($entry['date_booked'], false);
    if ($entry['parent_type'] == 'ProjectTask' && isset($aEntries[$entry['project_id']])) {
      $aEntries[$entry['project_id']][] = $entry;
    }
    else {
      $aEntries[$entry['parent_id']][] = $entry;
    }
  }
  return $aEntries;
}

if ($project_id) {
  // return result
  $args['project_id'] = $project_id;
  $args['user_id'] = $user_id;
  $args['date_start'] = $date_start;
  $args['date_end'] = $date_end;
  $args['invoice_id'] = $invoice_id;

  $result = Timesheet::getTotals($args);

  $aEntries = sortEntries($result['entries']);
  $k = 0;
  foreach ($aEntries as $entries) {
    foreach ($entries as $entry) {
      $k++;
      $xtpl->assign("ENTRY", $entry);
      if (!empty($entry['project_id'])) {
        $xtpl->parse("main.entries.entry_parent");
      }
      $xtpl->parse("main.entries");
    }
  }
  $xtpl->assign("actual", $result['actual']);
  $xtpl->assign("billable", $result['billable']);
  $xtpl->assign("invoice_name", $result['invoice']);

  if ($k > 20) {
    $xtpl->parse("main.bottom");
  }
}


$xtpl->parse("main");
$xtpl->out("main");
}else{
    echo '<script>function set_focus(){}</script><p class="error">' . translate('LBL_NO_ACCESS', 'ACL') . '</p>';
}
?>

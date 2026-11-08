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

$focus = new Timesheet();


echo "\n<p>\n";
echo get_module_title($mod_strings['LBL_MODULE_NAME'], $mod_strings['LBL_MODULE_NAME'].": ".$mod_strings['LBL_FILL_DAILY_MATRIX'], true);
echo "\n</p>\n";
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once($theme_path.'layout_utils.php');

$GLOBALS['log']->info("Timesheet Edit view");

$xtpl=new XTemplate('modules/Timesheet/EditDailyMatrix.html');
$xtpl->assign("MOD", $mod_strings);
$xtpl->assign("APP", $app_strings);

// Set the number grouping and decimal separators
$seps = get_number_seperators();
$dec_sep = $seps[1];
$num_grp_sep = $seps[0];
$xtpl->assign('NUM_GRP_SEP', $num_grp_sep);
$xtpl->assign('DEC_SEP', $dec_sep);


$xtpl->assign("CALENDAR_LANG", "en");


if (isset($_REQUEST['return_module'])) $xtpl->assign("RETURN_MODULE", $_REQUEST['return_module']);
if (isset($_REQUEST['return_action'])) $xtpl->assign("RETURN_ACTION", $_REQUEST['return_action']);
if (isset($_REQUEST['return_id'])) $xtpl->assign("RETURN_ID", $_REQUEST['return_id']);
// handle Create $module then Cancel
if (empty($_REQUEST['return_id'])) {
	$xtpl->assign("RETURN_ACTION", 'index');
}
$xtpl->assign("THEME", $theme);
$xtpl->assign("IMAGE_PATH", $image_path);
$xtpl->assign("PRINT_URL", "index.php?".$GLOBALS['request_string']);
$xtpl->assign("ID", $focus->id);

$aDays = array(
$mod_strings['LBL_SUNDAY'],
$mod_strings['LBL_MONDAY'],
$mod_strings['LBL_TUESDAY'],
$mod_strings['LBL_WEDNESDAY'],
$mod_strings['LBL_THURSDAY'],
$mod_strings['LBL_FRIDAY'],
$mod_strings['LBL_SATURDAY']
);

//EINTEL add time zone adjustment
$now = time() + ($timedate->adjustmentForUserTimeZone($timedate->getUserTimeZone()) * 60);//mktime(1,1,1,12,29,2008);
//END

$dayUnixTime = !empty($_REQUEST['day']) ? intval($_REQUEST['day']) : $now;

// Numeric representation of the chosen day
$idxDay = gmdate("w", $dayUnixTime);

$style = date("dmY",$now) == date("dmY",$dayUnixTime) ? "font-weight:bold" : "";
$xtpl->assign("DAY_OF_WEEK", "<span style='$style'>".$aDays[$idxDay]."</span>");
$xtpl->assign("DAY", "<span style='$style'>".gmdate("m/d", $dayUnixTime)."</span>");
$xtpl->assign("DAYUNIXTIME", $dayUnixTime);

$date = array($dayUnixTime, $dayUnixTime);

//EINTEL re#103
$timesheet = $focus->getUserTimesheetsMatrix($current_user->id, $date);
//END

$jobs = $focus->getAssignedParents($current_user->id, date("Y-m-d", $dayUnixTime), date("Y-m-d", $dayUnixTime));

//re#100 EINTEL fetch related accounts to the current projects we have
$jobs = $focus->fetch_related_accounts_ofprojects($jobs);
//end

$k = 0; // row

$total = 0;
/** EINTEL NM Re:96
 * Adding tasks to matrix resulted in turning the code that would build the matrix into
 * a recursive function to call its self as the depth of the tree is unlimited.
 */
$focus->buildDailyMatrix($focus->build_Parents($jobs), $k, $total, $xtpl, $timesheet, $depth = 0, $mod_strings, $dayUnixTime);

function buildMatrix1($aJobs, &$k, &$total, $xtpl, $timesheet, $depth = 0, $mod_strings, $dayUnixTime){
    foreach ($aJobs as $job) {
        $assignee = array();
        $assignee['rowid'] = $k;
        $assignee['id'] = $job['id'];
        $assignee['type'] = $job['type'];
        $assignee['date'] = gmdate("Y-m-d", $dayUnixTime);
        $assignee['actual'] = "";
        $assignee['disabled'] = "disabled";
        $assignee['timesheet_id'] = "";
        $assignee['description'] = "";
        /** EINTEL re#103 NM
         * Changed to return all the timesheets for a project/ptask/case/task along with
         * their description and then displayed under each other.
         */
        foreach($timesheet[$assignee['id']] as $timesheet_list){
            if (!empty($timesheet_list[$assignee['date']]['actual'])) {
              $assignee['actual'] = $timesheet_list[$assignee['date']]['actual'];
              $assignee['disabled'] = "";
              $assignee['timesheet_id'] = $timesheet_list[$assignee['date']]['id'];
              $assignee['description'] = $timesheet_list[$assignee['date']]['description'];

              $total += $assignee['actual'];
              // format number
              $assignee['actual'] = format_number($assignee['actual']);
            }
            $xtpl->assign("MATRIX", $assignee);
            $xtpl->parse("main.matrix_row.matrix_subrow");
        }
        //END
        if(!isset($timesheet[$assignee['id']]) || empty($timesheet[$assignee['id']])){
            $xtpl->assign("MATRIX", $assignee);
            $xtpl->parse("main.matrix_row.matrix_subrow");
        }
        $assignee = $job;
        $assignee['type_lbl'] = $mod_strings['LBL_TYPE_'.strtoupper($assignee['type'])];
        $xtpl->assign("MATRIX", $assignee);
        if (!empty($assignee['parent_id'])) {
            $assignee['depth'] = 10 * $depth;
            $assignee['depth'] = $assignee['depth'].'px';
            $xtpl->assign("MATRIX", $assignee);
            $xtpl->parse("main.matrix_row.matrix_parent");
        }else{
            $xtpl->assign("MATRIX", $assignee);
        }

        /** re#85 EINTEL
         * Assigns the new accounts field to the template to appear after the parents name like so:
         * parent_type : name (acount_name)(acount_name)
         */
        if (!empty($assignee['accounts'])) {
          foreach($assignee['accounts'] as $account){
            $xtpl->assign("MATRIX_accounts", $account);
            if (!empty($account['account_id'])) {
              $xtpl->parse("main.matrix_row.matrix_account_project");
            }
          }
        }
        //END

        if (!empty($assignee['account_id'])) {
         $xtpl->assign("MATRIX_accounts", $account);
         $xtpl->parse("main.matrix_row.matrix_account");
        }


        $xtpl->parse("main.matrix_row");
        $k++;
        if(isset($job['children'])){
            buildMatrix($job['children'], $k, $total, $xtpl, $timesheet, $depth + 1, $mod_strings, $dayUnixTime);
        }
    }
}
//END

$xtpl->assign("TOTAL", format_number($total));

if(is_admin($current_user) && $_REQUEST['module'] != 'DynamicLayout' && !empty($_SESSION['editinplace'])){	
	$record = '';
	if(!empty($_REQUEST['record'])){
		$record = 	$_REQUEST['record'];
	}
	$xtpl->assign("ADMIN_EDIT","<a href='index.php?action=index&module=DynamicLayout&from_action=".$_REQUEST['action'] ."&from_module=".$_REQUEST['module'] ."&record=".$record. "'>".get_image($image_path."EditLayout","border='0' alt='Edit Layout' align='bottom'")."</a>");	
}
//Add Custom Fields
require_once('modules/DynamicFields/templates/Files/EditView.php');

$xtpl->parse("main");
$xtpl->out("main");


}else{
    echo '<script>function set_focus(){}</script><p class="error">' . translate('LBL_NO_ACCESS', 'ACL') . '</p>';
}
?>

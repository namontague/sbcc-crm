<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

require_once('XTemplate/xtpl.php');

global $mod_strings;
global $app_list_strings;
global $app_strings;
global $current_user;

if (!is_admin($current_user)) sugar_die("Unauthorized access to administration.");

echo "\n<p>\n";
echo "<h2>Connector Mass Sync Settings</h2>";
echo "\n</p>\n";
global $theme;
global $currentModule;
$theme_path = "themes/".$theme."/";
$image_path = $theme_path."images/";

require_once('include/utils/layout_utils.php');


$GLOBALS['log']->info("Connector Sync Configure Settings view");

$xtpl=new XTemplate ('modules/Administration/connectorSyncConfig.html');
$xtpl->assign("MOD", $mod_strings);
$xtpl->assign("APP", $app_strings);

$xtpl->assign("RETURN_MODULE", "Administration");
$xtpl->assign("RETURN_ACTION", "index");

$xtpl->assign("MODULE", $currentModule);
$xtpl->assign("THEME", $theme);
$xtpl->assign("IMAGE_PATH", $image_path);$xtpl->assign("PRINT_URL", "index.php?".$GLOBALS['request_string']);

$xtpl->assign("HEADER", get_module_title("ERPSync", "{MOD.LBL_CONFIGURE_SETTINGS}", true));

$xtpl->parse("main");

$xtpl->out("main");
?>
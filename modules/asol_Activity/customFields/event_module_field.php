<?php

global $db;

require_once("modules/asol_Activity/asol_Activity.php");

$focus = new asol_Activity();
$focus->retrieve($_REQUEST['record']);

$module_name = $focus->getContextModule();
echo "<b>".$module_name."</b>";

?>

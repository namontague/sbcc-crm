<?php

global $db;

require_once("modules/asol_Events/asol_Events.php");

$focus = new asol_Events();
$focus->retrieve($_REQUEST['record']);

$module_name = $focus->getContextModule();
echo "<span id='triggerModule' style='font-weight: bold'>".$module_name."</span>";

?>

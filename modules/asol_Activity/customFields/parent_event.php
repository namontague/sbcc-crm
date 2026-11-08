<?php

require_once("modules/asol_Activity/asol_Activity.php");

$focus = new asol_Activity();
$focus->retrieve($_REQUEST['record']);

$parent_event_id = $focus->getParentEvent();

$parent_event_name = $focus->getEventModuleNameFromEventModuleId($parent_event_id);
$href = "index.php?module=asol_Events&action=DetailView&record=" . $parent_event_id;
echo "<a href=" . $href . ">" . $parent_event_name . "</a>";

?>

<?php
//echo "trigger_type.php";


/*Get the trigger_type options*/
require_once("modules/asol_Events/asol_Events.php");
global $app_list_strings;

$focus = new asol_Events();
$focus->retrieve($_REQUEST['record']);


$trigger_type = isset($_REQUEST['trigger_type']) ? $_REQUEST['trigger_type'] : $focus->trigger_type;

/*if ($trigger_type == "logic_hook") 
		echo "<body onload='document.getElementById(\"TRIGGERING PANEL\").style.display = \"block\"; document.getElementById(\"CONDITION PANEL\").style.display = \"block\";' >";
	else
		echo "<body onload='document.getElementById(\"TRIGGERING PANEL\").style.display = \"none\"; document.getElementById(\"CONDITION PANEL\").style.display = \"none\";'  >";
*/
//echo "\$trigger_type=[".$trigger_type."]";


$select_trigger_type_field = "<select name='trigger_type' id='trigger_type' onChange='if (this.value == \"subprocess\"){document.getElementById(\"type\").style.display = \"none\"; document.getElementById(\"type_label\").style.display = \"none\"; document.getElementById(\"LBL_ASOL_TRIGGERING_PANEL\").style.display = \"none\";document.getElementById(\"LBL_ASOL_EVENT_CONDITION_PANEL\").style.display = \"none\";} else {document.getElementById(\"type\").style.display = \"block\"; document.getElementById(\"type_label\").style.display = \"block\"; document.getElementById(\"LBL_ASOL_TRIGGERING_PANEL\").style.display = \"block\"; document.getElementById(\"LBL_ASOL_EVENT_CONDITION_PANEL\").style.display = \"block\"; }'>";


foreach ($app_list_strings['wfm_trigger_type_list'] as $key=>$trigger_type_field) {
	//echo "\$key=[".$key."]";
	$select_trigger_type_field .= ($trigger_type == $key) ? "<option value='". $key ."' selected> ". $trigger_type_field ." </option>" : "<option value='". $key ."'> ". $trigger_type_field ." </option>";
}
$select_trigger_type_field .= "</select>";
echo $select_trigger_type_field;
?>

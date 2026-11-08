<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');


/**
 * The javascript function selected_option( item ) is intended to generate the dynamic select for the delay field.
 */
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$dynamicSelect = '<script>
function selected_option( item ) {
	var time_amount = document.getElementById("time_amount");
	var i= 0;
	
	switch (item.selectedIndex) {
	case 0: //minutes
		
		time_amount.length=60;
		for ( i=0; i<time_amount.length; i++) {
			time_amount.options[i].text= i;
		}
		break;
	case 1: //hours
		
		time_amount.length=24;
		for ( i=0; i<time_amount.length; i++) {
			time_amount.options[i].text= i;
		}
		break;
	case 2:  //days
		
		time_amount.length=31;
		for ( i=0; i<time_amount.length; i++) {
			time_amount.options[i].text= i;
		}
		break;
	case 3: //weeks
		
		time_amount.length=4;
		for ( i=0; i<time_amount.length; i++) {
			time_amount.options[i].text= i;
		}
		break;
	case 4:  //months
		
		time_amount.length=12;
		for ( i=0; i<time_amount.length; i++) {
			time_amount.options[i].text= i;
		}
		break;
	default:
	}
}
</script>';
echo $dynamicSelect;	// Prints a dynamic select for a delay field.
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


global $mod_strings;
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
 * Prints a static select for the delay field.
 */
$select_time = "<select name='time' id='time' onChange='selected_option(document.getElementById(\"time\"))'>";
$array_time = array(
					"minutes" => $mod_strings['LBL_ASOL_MINUTES'], 
					"hours" => $mod_strings['LBL_ASOL_HOURS'], 
					"days" => $mod_strings['LBL_ASOL_DAYS'], 
					"weeks"=> $mod_strings['LBL_ASOL_WEEKS'], 
					"months" => $mod_strings['LBL_ASOL_MONTHS']
				);
foreach ($array_time as $key => $value) {
	$select_time .= "<option value='". $key ."'> ". $value ." </option>";
}
$select_time .= "</select>";

$select_time_amount = "<select name='time_amount' id='time_amount'>";
for ($i=0; $i<60; $i++) {
	$select_time_amount .= "<option value='". $i ."'> ". $i ." </option>";
}
echo $select_time . " " .$select_time_amount;
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
 * If a activity is already created, this code will access to the mysql database and will get the saved delay.
 */
global $db;

require_once("modules/asol_Activity/asol_Activity.php");

$focus = new asol_Activity();
$focus->retrieve($_REQUEST['record']);

$savedDelay= $focus->delay;

if (!empty ($savedDelay)) {
	$arrayDelay = explode(' - ', $savedDelay);
	$saved_time = $arrayDelay[0];
	$saved_time_amount = $arrayDelay[1];
	
	switch ($saved_time) {
		case "minutes":
			$saved_time_amount_integer_index = 0;
			break;
		case "hours":
			$saved_time_amount_integer_index = 1;
			break;
		case "days":
			$saved_time_amount_integer_index = 2;
			break;
		case "weeks":
			$saved_time_amount_integer_index = 3;
			break;
		case "months":
			$saved_time_amount_integer_index = 4;
			break;
		default:
	}

	/*
	 * This javascript prints the saved delay from the mysql database.
	 */
	echo '<script>
	
	var time_amount = document.getElementById("time_amount");
	var time        = document.getElementById("time");

	time_amount.selectedIndex = "'.$saved_time_amount.'"
	time.selectedIndex = "'.$saved_time_amount_integer_index.'"
	
	</script>';
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>

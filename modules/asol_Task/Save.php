<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

global $timedate;

$focus = new asol_Task();

if (isset($_REQUEST['record']))
$focus->id = $_REQUEST['record'];
$focus->name = $_REQUEST['name'];
$focus->assigned_user_id = $_REQUEST['assigned_user_id'];
$focus->delay_type = $_REQUEST['delay_type'];
$focus->delay = $_REQUEST['time']." - ".$_REQUEST['time_amount'];
$focus->description = $_REQUEST['description'];
$focus->order_task = $_REQUEST['order_task'];
$focus->task_type = $_REQUEST['task_type'];


/*if ( ($focus->task_type == "create_object") || ($focus->task_type == "modify_object") ) {

	$object_array = explode('${pipe}', $_REQUEST['task_implementation']);
	//$object_datetime_array = explode(" ", $call_array[2]);

	if((!$timedate->check_matching_format($call_datetime_array[0], $GLOBALS['timedate']->dbDayFormat)) && ($call_datetime_array[0]!=""))
	$call_datetime_array[0] = $timedate->swap_formats($call_datetime_array[0], $timedate->get_date_format(), $GLOBALS['timedate']->dbDayFormat );

	$call_array[2] = implode(" ", $call_datetime_array);

	$focus->task_implementation = implode('${pipe}', $call_array);

} else*/
if ($focus->task_type == "php_custom") {

	$phpCode = str_replace(array("\n", "\r"), array('\n', '\r'), $_REQUEST['task_implementation']);

	$focus->task_implementation = $phpCode;
	
	///////////////////////////////////////////////////
	$script = $focus->task_implementation;
	$script_to_disk_file = urldecode($script);
	$myFile = $focus->id.".php";
	$fh = fopen("modules/asol_Task/temp_php_custom_Files/".$myFile, 'w') or die("can't open file");

	$stringData = "<?php ".$script_to_disk_file."?>";
	fwrite($fh, $stringData);

	fclose($fh);// *********************flush????
	/////////////////////////////////////////////////

}
else {

	$focus->task_implementation = $_REQUEST['task_implementation'];

}

$recordId = $focus->save();

$return_id = (!empty($_REQUEST['return_id'])) ? $_REQUEST['return_id'] : $recordId;
$return_record = (empty($return_id)) ? $_REQUEST['record'] : $return_id;

//echo "recordId=[". $recordId. "]";
//echo "return_id=[". $return_id. "]";
//echo "return_recor=[".$return_record. "]";
header("Location: index.php?action=".$_REQUEST['return_action']."&module=".$_REQUEST['return_module']."&record=".$return_record);

?>

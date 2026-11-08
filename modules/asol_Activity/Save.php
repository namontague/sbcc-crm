<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$focus = new asol_Activity();

if (isset($_REQUEST['record']))
	$focus->id = $_REQUEST['record'];
$focus->name = $_REQUEST['name'];
$focus->assigned_user_id = $_REQUEST['assigned_user_id'];
$focus->delay = $_REQUEST['time']." - ".$_REQUEST['time_amount'];
$focus->description = $_REQUEST['description'];


$filterValues = json_decode(htmlspecialchars_decode($_REQUEST['activity_conditions']));

foreach ($filterValues as $key=>$condition){


	if ((($condition->type  == "date") || ($condition->type  == "datetime")) && (($condition->operator  != "last") && ($condition->operator  != "this") && ($condition->operator  != "next") && ($condition->operator  != "not last") && ($condition->operator  != "not this") && ($condition->operator  != "not next"))){

		if((!$timedate->check_matching_format($condition->param1 , $GLOBALS['timedate']->dbDayFormat)) && ($condition->param1 !="")) {
			$filterValues[$key]->param1 = $timedate->swap_formats($condition->param1 , $timedate->get_date_format(), $GLOBALS['timedate']->dbDayFormat );
		}

		if((!$timedate->check_matching_format($condition->param2 , $GLOBALS['timedate']->dbDayFormat)) && ($condition->param2 !="")) {
			$filterValues[$key]->param2  = $timedate->swap_formats($condition->param2 , $timedate->get_date_format(), $GLOBALS['timedate']->dbDayFormat );
		}

	}


}

$focus->condition_activity = json_encode($filterValues);


$recordId = $focus->save();

$return_id = (!empty($_REQUEST['return_id'])) ? $_REQUEST['return_id'] : $recordId; 
$return_record = (empty($return_id)) ? $_REQUEST['record'] : $return_id;

header("Location: index.php?action=".$_REQUEST['return_action']."&module=".$_REQUEST['return_module']."&record=".$return_record);

?>

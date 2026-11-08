<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$focus = new asol_Process();

if (isset($_REQUEST['record']))
	$focus->id = $_REQUEST['record'];
$focus->name = $_REQUEST['name'];
$focus->assigned_user_id = $_REQUEST['assigned_user_id'];
$focus->description = $_REQUEST['description'];

$focus->status = $_REQUEST['status'];
$focus->bean_module = $_REQUEST['triggerModule'];




$recordId = $focus->save();
//////////////////////////
// generar el fichero logic hook, ids unicos
//check_hook_file

//////////////////////////
$return_id = (!empty($_REQUEST['return_id'])) ? $_REQUEST['return_id'] : $recordId; 
$return_record = (empty($return_id)) ? $_REQUEST['record'] : $return_id;

header("Location: index.php?module=asol_Process&action=DetailView&record=".$recordId);

?>

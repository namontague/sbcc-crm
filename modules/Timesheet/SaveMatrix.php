<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
require_once('modules/Timesheet/Timesheet.php');
require_once('include/formbase.php');

global $current_user;

$focus = new Timesheet();

if(!$focus->ACLAccess('Save')){
  ACLController::displayNoAccess(true);
  sugar_cleanup(true);
}

$data = array();
$data = $_POST['data'];

$records = array();
$records = $_POST['records'];

$temp = new Timesheet();

$php5 = version_compare(phpversion(), '5.0');

foreach ($data as $parentData => $actual) {
  if (isset($actual) && $actual != '') {
    if ($php5 < 0) {
      $focus = $temp;
    }
    else {
      $focus = @clone($temp);
    }
    $parentData = explode(".", $parentData);
    $recid = $parentData[0].".".$parentData[2];
    if (isset($records[$recid])) {
      $focus->retrieve($records[$recid]);
    }
    
    //re#76 EINTEL ADDITION
    if(isset($focus->billable)){
        if(isset($focus->actual)){
            $billable = 0;
            $difference = $actual - $focus->actual;
            $billable = $focus->billable + $difference;
            $focus->billable = $billable;
            if($focus->billable < 0){
                $focus->billable = 0;
            }
        }
    }
    //END

    $focus->actual = $actual;

    $focus->date_booked = $parentData[2];
    $focus->parent_id = $parentData[0];
    $focus->parent_type = $parentData[1];
    $focus->assigned_user_id = $current_user->id;
    $focus->save($GLOBALS['check_notify']);
  }
}
if (!empty($_REQUEST['ajax'])) {
  echo "success";
  exit;
}
handleRedirect();
?>
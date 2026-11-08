<?php
$searchdefs['Timesheet'] = array(
  'templateMeta' => array(
    'maxColumns' => '3', 
    'widths' => array('label' => '10', 'field' => '30'),
  ),
  'layout' => array(
    'basic_search' => array(
      array('name' => 'project_name', 'label' => 'LBL_TYPE_PROJECT', 'type' => 'name'),
      array('name' => 'project_task_name', 'label' => 'LBL_TYPE_PROJECTTASK', 'type' => 'name'),
      array('name' => 'task_name', 'label' => 'LBL_TYPE_TASK', 'type' => 'name'),
      array('name' => 'case_name', 'label' => 'LBL_TYPE_CASES', 'type' => 'name'),
      array('name' => 'assigned_user_id', 'type' => 'enum', 'label' => 'LBL_EMPLOYEE', 'function' => array('name' => 'get_user_array', 'params' => array(true))),
      array('name'=>'current_user_only', 'label'=>'LBL_CURRENT_USER_FILTER', 'type'=>'bool'),
    ),
    'advanced_search' => array(
      array('name' => 'project_name', 'label' => 'LBL_TYPE_PROJECT', 'type' => 'name'),
      array('name' => 'project_task_name', 'label' => 'LBL_TYPE_PROJECTTASK', 'type' => 'name'),
      array('name' => 'task_name', 'label' => 'LBL_TYPE_TASK', 'type' => 'name'),
      array('name' => 'case_name', 'label' => 'LBL_TYPE_CASES', 'type' => 'name'),
      array('name' => 'assigned_user_id', 'type' => 'enum', 'label' => 'LBL_EMPLOYEE', 'function' => array('name' => 'get_user_array', 'params' => array(true))),
      array('name'=>'current_user_only', 'label'=>'LBL_CURRENT_USER_FILTER', 'type'=>'bool'),
      array('name'=>'status', 'displayParams' => array( 'size' => '1'), 'type'=>'enum'  )
    ),
  ),
);
?>


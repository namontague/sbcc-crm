<?php
$module_name = 'asol_Task';
$listViewDefs [$module_name] = 
array (
  'NAME' => 
  array (
    'width' => '32%',
    'label' => 'LBL_NAME',
    'default' => true,
    'link' => true,
  ),
  'TASK_TYPE' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_TASK_TYPE',
    'width' => '10%',
    'default' => true,
  ),
  'DELAY_TYPE' => 
  array (
    'type' => 'enum',
    'default' => true,
    'studio' => 'visible',
    'label' => 'LBL_DELAY_TYPE',
    'sortable' => false,
    'width' => '10%',
  ),
  'DELAY' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_DELAY',
    'width' => '10%',
    'default' => true,
  ),
  'ORDER_TASK' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_ORDER_TASK',
    'width' => '10%',
    'default' => true,
  ),
  
  
  /*'ASSIGNED_USER_NAME' => 
  array (
    'width' => '9%',
    'label' => 'LBL_ASSIGNED_TO_NAME',
    'module' => 'Employees',
    'id' => 'ASSIGNED_USER_ID',
    'default' => true,
  ),*/
);
?>

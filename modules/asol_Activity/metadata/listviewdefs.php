<?php
$module_name = 'asol_Activity';
$listViewDefs [$module_name] = 
array (
  'NAME' => 
  array (
    'width' => '32%',
    'label' => 'LBL_NAME',
    'default' => true,
    'link' => true,
  ),
  'DELAY' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_DELAY',
    'width' => '10%',
    'default' => true,
  ),
  'NEXT_ACTIVITY' => 
  array (
    'type' => 'relate',
    'studio' => 'visible',
    'label' => 'LBL_NEXT_ACTIVITY',
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

<?php
$module_name = 'asol_WorkingNodes';
$listViewDefs [$module_name] = 
array (
 /* 'ASOL_PROCESSINSTANCES_ASOL_WORKINGNODES_NAME' => 
  array (
    'type' => 'relate',
    'link' => 'asol_processinstances_asol_workingnodes',
    'label' => 'LBL_ASOL_PROCESSINSTANCES_ASOL_WORKINGNODES_FROM_ASOL_PROCESSINSTANCES_TITLE',
    'width' => '10%',
    'default' => true,
  ),*/
  'NAME' => 
  array (
    'width' => '10%',
    'label' => 'LBL_NAME',
    'default' => true,
    'link' => true,
  ),
  'ID' => 
  array (
    'width' => '10%',
    'label' => 'LBL_ID',
    'default' => true,
  ),
  'PROCESS_INSTANCE_ID' => 
  array (
    'type' => 'relate',
    'studio' => 'visible',
    'label' => 'LBL_PROCESS_INSTANCE_ID',
    'width' => '10%',
    'default' => true,
  ),
  'PRIORITY' => 
  array (
    'type' => 'int',
    'label' => 'LBL_PRIORITY',
    'width' => '10%',
    'default' => true,
  ),
  'CURRENT_ACTIVITY' => 
  array (
    'type' => 'relate',
    'studio' => 'visible',
    'label' => 'LBL_CURRENT_ACTIVITY',
    'width' => '10%',
    'default' => true,
  ),
  'CURRENT_TASK' => 
  array (
    'type' => 'relate',
    'studio' => 'visible',
    'label' => 'LBL_CURRENT_TASK',
    'width' => '10%',
    'default' => true,
  ),
  'DELAY_WAKEUP_TIME' => 
  array (
    'type' => 'datetimecombo',
    'label' => 'LBL_DELAY_WAKEUP_TIME',
    'width' => '10%',
    'default' => true,
  ),
  'STATUS' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_STATUS',
    'width' => '10%',
    'default' => true,
  ),
);
?>

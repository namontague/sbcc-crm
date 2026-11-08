<?php
$module_name = 'asol_ProcessInstances';
$listViewDefs [$module_name] = 
array (
  'NAME' => 
  array (
    'width' => '32%',
    'label' => 'LBL_NAME',
    'default' => true,
    'link' => true,
  ),
  'PROCESS_ID' => 
  array (
    'type' => 'relate',
    'studio' => 'visible',
    'label' => 'LBL_PROCESS_ID',
    'width' => '10%',
    'default' => true,
  ),
  'BEAN_MODULE' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_BEAN_MODULE',
    'width' => '10%',
    'default' => true,
  ),
  'BEAN_ID' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_BEAN_ID',
    'width' => '10%',
    'default' => true,
  ),
  'PARENT_PROCESS_INSTANCE_ID' => 
  array (
    'type' => 'relate',
    'studio' => 'visible',
    'label' => 'LBL_PARENT_PROCESS_INSTANCE_ID',
    'width' => '10%',
    'default' => true,
  ),
  'BEAN_UNGREEDY_COUNT' => 
  array (
    'type' => 'int',
    'label' => 'LBL_BEAN_UNGREEDY_COUNT',
    'width' => '10%',
    'default' => true,
  ),
);
?>

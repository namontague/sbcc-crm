<?php
$module_name = 'asol_OnHold';
$listViewDefs [$module_name] = 
array (
  /*'ASOL_WORKINGNODES_ASOL_ONHOLD_NAME' => 
  array (
    'type' => 'relate',
    'link' => 'asol_workingnodes_asol_onhold',
    'label' => 'LBL_ASOL_WORKINGNODES_ASOL_ONHOLD_FROM_ASOL_WORKINGNODES_TITLE',
    'width' => '10%',
    'default' => true,
  ),*/
  'NAME' => 
  array (
    'width' => '32%',
    'label' => 'LBL_NAME',
    'default' => true,
    'link' => true,
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
  'PROCESS_INSTANCE_ID' => 
  array (
    'type' => 'relate',
    'studio' => 'visible',
    'label' => 'LBL_PROCESS_INSTANCE_ID',
    'width' => '10%',
    'default' => true,
  ),
  'WORKING_NODE_ID' => 
  array (
    'type' => 'relate',
    'studio' => 'visible',
    'label' => 'LBL_WORKING_NODE_ID',
    'width' => '10%',
    'default' => true,
  ),
);
?>

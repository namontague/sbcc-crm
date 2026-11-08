<?php
$module_name = 'Invoice';
$searchdefs [$module_name] = 
array (
  'layout' => 
  array (
    'basic_search' => 
    array (
      'name' =>
      array (
        'width' => '10%',
        'label' => 'LBL_NAME',
        'default' => true,
        'name' => 'name',
      ),
    ),
    'advanced_search' => 
    array (
      'name' =>
      array (
        'width' => '10%',
        'label' => 'LBL_NAME',
        'default' => true,
        'name' => 'name',
      ),
      'account_name' =>
      array (
        'width' => '10%',
        'label' => 'LBL_ACCOUNT',
        'default' => true,
        'name' => 'account_name',
      ),
    ),
  ),
  'templateMeta' => 
  array (
    'maxColumns' => '3',
    'widths' => 
    array (
      'label' => '10',
      'field' => '30',
    ),
  ),
);
?>

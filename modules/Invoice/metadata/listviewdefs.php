<?php
$module_name = 'Invoice';
$listViewDefs [$module_name] = 
array (
  'NAME' => array (
    'width' => '10',
    'label' => 'LBL_NAME',
    'default' => true,
    'link' => true,
    ),
  'INVOICE_DATE' =>  array (
    'width' => '10',
    'label' => 'LBL_INVOICE_DATE',
    'link' => false,
    'default' => true,
    ),
  'INVOICE_SUMMARY' =>  array (
    'width' => '70',
    'label' => 'LBL_INVOICE_SUMMARY',
    'link' => false,
    'default' => true,
    ),

);
?>

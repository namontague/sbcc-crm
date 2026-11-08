<?php
$dashletData['InvoiceDashlet']['searchFields'] = array (
  'invoice_number' => 
  array (
    'default' => '',
  ),
);
$dashletData['InvoiceDashlet']['columns'] = array (
  'name' =>
  array (
    'width' => '10%',
    'label' => 'LBL_NAME',
    'default' => true,
  ),
  'date_modified' => 
  array (
    'width' => '15%',
    'label' => 'LBL_DATE_MODIFIED',
    'name' => 'date_modified',
    'default' => false,
  ),
  'created_by' => 
  array (
    'width' => '8%',
    'label' => 'LBL_CREATED',
    'name' => 'created_by',
    'default' => false,
  ),
  'assigned_user_name' => 
  array (
    'width' => '8%',
    'label' => 'LBL_LIST_ASSIGNED_USER',
    'name' => 'assigned_user_name',
    'default' => false,
  ),
);

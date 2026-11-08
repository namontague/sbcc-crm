<?php
$module_name = 'Invoice';
$viewdefs [$module_name] = 
array (
  'EditView' => 
  array (
    'templateMeta' => 
    array (
      'maxColumns' => '2',
      'widths' => 
      array (
        0 => 
        array (
          'label' => '10',
          'field' => '30',
        ),
        1 => 
        array (
          'label' => '10',
          'field' => '30',
        ),
      ),
    ),
    'panels' => 
    array (
      'default' => 
      array (
        array (
          array (
            'name' => 'name',
            'label' => 'LBL_NAME',
          ),
          array (
            'name' => 'invoice_date',
            'label' => 'LBL_INVOICE_DATE',
          ),
        ),
        array (
          array (
            'name' => 'account_name',
            'label' => 'LBL_ACCOUNT_NAME',
          ),
        ),
        array (
          array (
            'name' => 'invoice_summary',
            'label' => 'LBL_INVOICE_SUMMARY',
          ),
        ),
      ),
    ),
  ),
);
?>

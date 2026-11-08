<?php
$module_name = 'Invoice';
$viewdefs [$module_name] = 
array (
  'DetailView' => 
  array (
    'templateMeta' => 
    array (
      'form' => 
      array (
        'buttons' => 
        array (
          0 => 'EDIT',
          1 => 'DUPLICATE',
          2 => 'DELETE',
        ),
      ),
      'maxColumns' => '2',
      'widths' => 
      array (
        array (
          'label' => '20',
          'field' => '30',
        ),
        array (
          'label' => '20',
          'field' => '50',
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

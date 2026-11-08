<?php
$module_name = 'asol_Task';
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
      'useTabs' => false,
    ),
    'panels' => 
    array (
      'default' => 
      array (
        0 => 
        array (
          0 => 'name',
          /*1 => 'assigned_user_name',*/
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'date_entered',
            'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}',
            'label' => 'LBL_DATE_ENTERED',
          ),
          1 => 
          array (
            'name' => 'date_modified',
            'customCode' => '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}',
            'label' => 'LBL_DATE_MODIFIED',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'task_type',
            'label' => 'LBL_TASK_TYPE',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'delay_type',
            'studio' => 'visible',
            'label' => 'LBL_DELAY_TYPE',
          ),
          1 => 
          array (
            'name' => 'delay',
            'label' => 'LBL_DELAY',
          ),
        ),
        4 => 
        array (
          0 => 
          array (
            'name' => 'order_task',
            'label' => 'LBL_ORDER_TASK',
          ),
          1 => 
          array (
            'name' => 'asol_activity_name',
          ),
        ),
        5 => 
        array (
          0 => 'description',
        ),
      ),
    ),
  ),
);
?>

<?php
$module_name = 'asol_Task';
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
      'includes' => array(
          array('file' => 'modules/asol_Task/js/asol_task.js'),
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
            'name' => 'delay_type',
            'studio' => 'visible',
            'label' => 'LBL_DELAY_TYPE',
          ),
          1 => 
          array (
            'name' => 'delay',
            'label' => 'LBL_DELAY',
            'customCode' => '{php} require_once("modules/asol_Task/customFields/delay_field.php"); {/php}',
          ),
        ),
        2 => 
        array (
          0 => 'description',
          1 => 
          array (
            'name' => 'order_task',
            'label' => 'LBL_ORDER_TASK',
          ),
        ),
      ),
      
      'LBL_TASK_IMPLEMENTATION_PANEL' => 
      array (
        'widths' =>
        array (
          array (
            'label' => '10',
            'field' => '10',
          ),
          array (
            'label' => '10',
            'field' => '50',
          ),
        ),

        0 => 
        array (
          0 => 
          array (
            'name' => 'task_type',
            'label' => 'LBL_TASK_TYPE',
            'customCode' => '{php} require_once("modules/asol_Task/customFields/task_type_field.php"); {/php}',
          ),
          1 => 
          array (
            'name' => 'task_implementation',
            'label' => 'LBL_TASK_IMPLEMENTATION',
            'customCode' => '{php} require_once("modules/asol_Task/customFields/task_implementation_field.php"); {/php}',
          ),
        ),
      ),
      
    ),
  ),
);
?>

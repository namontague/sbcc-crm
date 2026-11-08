<?php
$module_name = 'asol_Activity';
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
          0 => 'description',
          1 => 
          array (
            'name' => 'delay',
            'label' => 'LBL_DELAY',
            'customCode' => '{php} require_once("modules/asol_Activity/customFields/delay_field.php"); {/php}',
          ),
        ),
      ),
      
      'LBL_ASOL_ACTIVITY_CONDITION_PANEL' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'module_name',
            'label' => 'LBL_ASOL_MODULE_NAME',
            'customCode' => '{php} require_once("modules/asol_Activity/customFields/event_module_field.php"); {/php}',
          ),
        ),
        1 => 
        array (

          0 => 
          array (
            'name' => 'module_fields',
            'label' => 'LBL_ASOL_MODULE_FIELDS',
            'customCode' => '
            	{php}
            		require_once("modules/asol_Activity/customFields/module_fields.php"); 
            	{/php}',
          ),
          
          1 => 
          array (
            'name' => 'module_conditions',
            'label' => 'LBL_ASOL_ACTIVITY_CONDITIONS',
            'customCode' => '
            	{php}
            		require_once("modules/asol_Activity/customFields/module_conditions.php"); 
            	{/php}',
          ),
        
        ),
      ),
      
    ),
  ),
);
?>

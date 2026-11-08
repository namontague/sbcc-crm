<?php
$module_name = 'asol_Events';
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
          0 => array(
          				'name' => 'trigger_type',
            			//'studio' => 'visible',
            			'label' => 'LBL_TRIGGER_TYPE',
          				 'customCode' => '
          	  				  <input type="hidden" value="" id="trigger_type" name="trigger_type">
          	  					{php} 
          	  						require_once("modules/asol_Events/customFields/trigger_type.php");
          	  					{/php}',
          			),
          1 => 
          array (
		            'name' => 'type',
		            'studio' => 'visible',
		            'label' => 'LBL_TYPE',
		          ),
        ),
        2 => 
        array (
          0 => 'description',
        ),
      ),
      
      'LBL_ASOL_TRIGGERING_PANEL' => 
      array (
        0 => 
        array (
          0 => 
          	array (
          	  'name' => 'trigger',
          	  'label' => 'LBL_ASOL_TRIGGER_MODULE',
          	  'customCode' => '
          	    <input type="hidden" value="" id="trigger_event" name="trigger_event">
          	  	{php} 
          	  		require_once("modules/asol_Events/customFields/trigger_field_module.php");
          	  	{/php}',
          	),
          1 => 
          array (
            'name' => 'condition',
            'label' => 'LBL_ASOL_TRIGGER_EVENT',
            'customCode' => '
            	<input type="hidden" value="" id="condition_event" name="condition_event">
            	{php} 
          	  		require_once("modules/asol_Events/customFields/trigger_field_hook.php");
          	  	{/php}',
          ),
        ),
      ),
      
      'LBL_ASOL_EVENT_CONDITION_PANEL' => 
      array (
      
        0 => 
        array (
          
          0 => 
          array (
            'name' => 'module_fields',
            'label' => 'LBL_ASOL_MODULE_FIELDS',
            'customCode' => '
            	{php}
            		require_once("modules/asol_Events/customFields/module_fields.php"); 
            	{/php}',
          ),
          
          1 => 
          array (
            'name' => 'module_conditions',
            'label' => 'LBL_ASOL_EVENT_CONDITIONS',
            'customCode' => '
            	{php}
            		require_once("modules/asol_Events/customFields/module_conditions.php"); 
            	{/php}',
          ),
          
        ),
      
      ),

    ),
  ),
);
?>

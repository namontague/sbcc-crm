<?php
$module_name = 'asol_Process';
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
          1 => 
          array (
            'name' => 'status',
            'studio' => 'visible',
            'label' => 'LBL_STATUS',
          ),
        /*  1 => 'assigned_user_name',*/
        ),
        1 => 
        array (
          0 => 'description',
          /*1 => 
          array (
            'name' => 'status',
            'studio' => 'visible',
            'label' => 'LBL_STATUS',
          ),*/
        ),
      ),
      'LBL_ASOL_MODULE_PANEL' => 
      array (
        0 => 
        array (
          0 => 
          	array (
          	  'name' => 'bean_module',
          	  'label' => 'LBL_ASOL_BEAN_MODULE',
          	  'customCode' => '
          	    <input type="hidden" value="" id="bean_module" name="bean_module">
          	  	{php} 
          	  		require_once("modules/asol_Process/customFields/bean_module.php");
          	  	{/php}',
          	),
         
        ),
      ),
    ),
  ),
);
?>

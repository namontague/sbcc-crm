<?php
$module_name = 'asol_Process';
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
          /*3 => array (
          				'customCode' => '{php} require_once("modules/asol_Process/export_button.php"); {/php}',			
          ),*/
         /* 4 => array (
          				'customCode' => '{php} require_once("modules/asol_Process/import_button.php"); {/php}',			
          ),*/
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
         /* 1 => 'assigned_user_name',*/
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
          0 => array (
			            'name' => 'bean_module',
			            'studio' => 'visible',
			            'label' => 'LBL_BEAN_MODULE',
			          ),
          1 => 
          array (
		            'name' => 'status',
		            'studio' => 'visible',
		            'label' => 'LBL_STATUS',
		          ),
        	),
        3 => 
        array (
          0 => 'description',
         /* 1 => 'assigned_user_name',*/
        ),
      ),
    ),
  ),
);
?>

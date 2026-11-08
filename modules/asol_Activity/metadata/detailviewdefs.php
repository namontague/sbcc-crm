<?php
$module_name = 'asol_Activity';
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
          /*0 => 
          array (
            'name' => 'asol_events_asol_activity_name',
          	'customCode' => '{php} require_once("modules/asol_Activity/customFields/parent_event.php"); {/php}',
          ),*/
          0 => 
          array (
            'name' => 'predecessor_name',
          	//'customCode' => '{php} require_once("modules/asol_Activity/customFields/parent_event.php"); {/php}',
          ),
          1 => 
          array (
            'name' => 'delay',
            'label' => 'LBL_DELAY',
          ),
        ),
        3 => 
        array (
          0 => 'description',
        ),
        
        4 => 
        array (
          0 => 
          array (
            'name' 			=> 'asol_order_task_asol_activity',
          	'label' 		=> 'LBL_TASK_ORDER',
          	'customCode' 	=> '{php} require_once("modules/asol_Activity/customFields/order_task.php"); {/php}',
          ),
        ),
        
      ),
    ),
  ),
);
?>

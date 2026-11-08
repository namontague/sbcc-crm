<?php
if (!defined('sugarEntry') || !sugarEntry) {
  die('Not A Valid Entry Point');
}

$index = (isset($_REQUEST['record'])) ? $_REQUEST['record'] : "";

$layout_defs['asol_Activity'] = array(
  // list of what Subpanels to show in the DetailView
  'subpanel_setup' => array(
    'asol_events_asol_activity' => array (
      'order' => 100,
      'module' => 'asol_Events',
      'subpanel_name' => 'default',
      'sort_order' => 'asc',
      'sort_by' => 'id',
      'title_key' => 'LBL_ASOL_EVENTS',
      'get_subpanel_data' => 'asol_events',
      'top_buttons' =>
      array (
        array (
          'widget_class' => 'SubPanelTopButtonQuickCreate',
        ),
        array (
          'widget_class' => 'SubPanelTopSelectButton',
          'mode' => 'MultiSelect',
        ),
      ),
    ),
   'asol_next_activities' => array (
      'order' => 100,
      'module' => 'asol_Activity',
      'subpanel_name' => 'default',
      'sort_order' => 'asc',
      'sort_by' => 'id',
      'title_key' => 'LBL_NEXT_ACTIVITIES',
      'get_subpanel_data' => 'next_activities',
      'top_buttons' =>
      array (
        array (
          //'widget_class' => 'SubPanelTopButtonQuickCreate',
          'widget_class' => 'SubPanelTopButtonAsolCreateNextActivity',
        ),
        array (
          'widget_class' => 'SubPanelTopSelectButton',
          'mode' => 'MultiSelect&parent_module=asol_Activity&parent_id='.$index,
        ),
      ),
    ),
    'asol_activity_asol_task' => array (
      'order' => 100,
      'module' => 'asol_Task',
      'subpanel_name' => 'default',
      'sort_order' => 'asc',
      'sort_by' => 'id',
      'title_key' => 'LBL_ASOL_ACTIVITY_TASKS',
      'get_subpanel_data' => 'asol_tasks',
      'top_buttons' =>
      array (
        array (
          //'widget_class' => 'SubPanelTopButtonQuickCreate',
          'widget_class' => 'SubPanelTopButtonAsolCreateTask',
        ),
        array (
          'widget_class' => 'SubPanelTopSelectButton',
          'mode' => 'MultiSelect',
        ),
      ),
    ),
  )
);

<?php
if (!defined('sugarEntry') || !sugarEntry) {
  die('Not A Valid Entry Point');
}

$index = (isset($_REQUEST['record'])) ? $_REQUEST['record'] : "";


$layout_defs['asol_Events'] = array(
  // list of what Subpanels to show in the DetailView
  'subpanel_setup' => array(
    'asol_events_asol_activity' => array(
      'order' => 100,
      'module' => 'asol_Activity',
      'subpanel_name' => 'default',
      'sort_order' => 'asc',
      'sort_by' => 'id',
      'title_key' => 'LBL_ASOL_ACTIVITY',
      'get_subpanel_data' => 'asol_activities',
      'top_buttons' =>
      array(
        array(
          //'widget_class' => 'SubPanelTopButtonQuickCreate',
          'widget_class' => 'SubPanelTopButtonAsolCreateActivity',
        ),
        array(
          'widget_class' => 'SubPanelTopSelectButton',
          'mode' => 'MultiSelect&parent_module=asol_Events&parent_id='.$index,
        ),
      ),
    ),

  )
);

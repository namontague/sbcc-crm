<?php
if (!defined('sugarEntry') || !sugarEntry) {
  die('Not A Valid Entry Point');
}

$index = (isset($_REQUEST['record'])) ? $_REQUEST['record'] : "";


$layout_defs['asol_Process'] = array(
  // list of what Subpanels to show in the DetailView
  'subpanel_setup' => array(

    'asol_process_asol_events2' =>array (
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
          'widget_class' => 'SubPanelTopButtonAsolCreateEvent',
        ),
        array (
          'widget_class' => 'SubPanelTopSelectButton',
          'mode' => 'MultiSelect&parent_module=asol_Process&parent_id='.$index,
        ),
      ),
    ),
  )
);

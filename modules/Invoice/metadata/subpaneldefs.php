<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
 * Layout definition for Invoice
 */


$layout_defs['Invoice'] = array(
    // list of what Subpanels to show in the DetailView
    'subpanel_setup' => array(
        'timesheet' => array(
            'order' => 10,// the order in which this subpanel is displayed with other subpanels
            'module' => 'Timesheet',
            'subpanel_name' => 'default',
            'get_subpanel_data' => 'invoice_timesheet',
            'add_subpanel_data' => 'timesheet_id',
            'title_key' => 'LBL_TIMESHEET_SUBPANEL_TITLE',
            'top_buttons' => array( // this array defines the top buttons
                array('widget_class' => 'SubPanelTopButtonQuickCreate'),
                array('widget_class' => 'SubPanelTopSelectButton', 'mode'=>'MultiSelect')
            ),
        ),
    ),
);
?>

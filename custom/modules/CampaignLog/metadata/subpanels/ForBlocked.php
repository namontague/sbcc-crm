<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
// SBCC - campaign status page "Suppressed by Email Address or domain" subpanel, with the likely suppression reason
$subpanel_layout = array(
    'top_buttons' => array(
        array('widget_class' => 'SubPanelAddToProspectListButton', 'create' => 'true'),
    ),

    'where' => '',

    'list_fields' => array(
        'recipient_name' => array(
            'vname' => 'LBL_LIST_RECIPIENT_NAME',
            'width' => '18%',
            'sortable' => false,
        ),
        'recipient_email' => array(
            'vname' => 'LBL_LIST_RECIPIENT_EMAIL',
            'width' => '20%',
            'sortable' => true,
        ),
        'marketing_name' => array(
            'vname' => 'LBL_LIST_MARKETING_NAME',
            'width' => '14%',
            'sortable' => false,
        ),
        'activity_date' => array(
            'vname' => 'LBL_ACTIVITY_DATE',
            'width' => '12%',
        ),
        'suppression_reason' => array(
            'vname' => 'LBL_SUPPRESSION_REASON',
            'width' => '36%',
            'sortable' => false,
        ),
        'target_id' => array(
            'usage' => 'query_only',
        ),
        'target_type' => array(
            'usage' => 'query_only',
        ),
        'related_id' => array(
            'usage' => 'query_only',
        ),
        'related_type' => array(
            'usage' => 'query_only',
        ),
    ),
);

<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
 * Layout for the "Reply Lists" subpanel on an SMS_SMS detail view. Read-only:
 * the lists aren't related to the SMS through a relationship, so there's
 * nothing to select or remove here - lists are set up from the target list
 * itself (SMS Reply Keyword(s) / Collects SMS Replies For List).
 */

$subpanel_layout = array(
    'top_buttons' => array(),

    'where' => '',

    'list_fields' => array(
        'name' => array(
            'vname' => 'LBL_LIST_PROSPECT_LIST_NAME',
            'widget_class' => 'SubPanelDetailViewLink',
            'width' => '45%',
        ),
        'reply_keywords' => array(
            'vname' => 'LBL_REPLY_KEYWORDS',
            'width' => '30%',
        ),
        'contact_count' => array(
            'vname' => 'LBL_LIST_ENTRIES',
            'widget_class' => 'SubPanelReplyListContactCount',
            'usage' => 'display_only',
            'width' => '10%',
            'sortable' => false,
        ),
        'date_entered' => array(
            'vname' => 'LBL_DATE_ENTERED',
            'width' => '15%',
        ),
    ),
);

<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$module_name = 'SMS_SMS';

$layout_defs[$module_name] = array(
	'subpanel_setup' => array(

		'replies' => array(
			'order' => 10,
			'module' => 'SMS_Replies',
			'sort_order' => 'desc',
			'sort_by' => 'received_at',
			// 100 a page rather than the site-wide 10 (list_max_entries_per_subpanel)
			'list_max_entries' => 100,
			'subpanel_name' => 'ForSMS',
			'get_subpanel_data' => 'replies',
			'title_key' => 'LBL_REPLIES_SUBPANEL_TITLE',
			'top_buttons' => array(),
		),

		// Target lists collecting this SMS's replies - see
		// modules/SMS_Replies/ReplyListsSubpanel.php for how they're found.
		'reply_lists' => array(
			'order' => 20,
			'module' => 'ProspectLists',
			'sort_order' => 'asc',
			'sort_by' => 'name',
			'subpanel_name' => 'ForSMSReplies',
			'get_subpanel_data' => 'function:get_sms_reply_lists_query',
			'generate_select' => true,
			'function_parameters' => array(
				'import_function_file' => 'modules/SMS_Replies/ReplyListsSubpanel.php',
			),
			'title_key' => 'LBL_REPLY_LISTS_SUBPANEL_TITLE',
			'top_buttons' => array(),
		),

	),
);

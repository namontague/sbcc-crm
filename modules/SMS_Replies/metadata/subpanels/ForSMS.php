<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$module_name = 'SMS_Replies';
$subpanel_layout = array(
	// replies only ever arrive via the transmitsms webhook, so there is no
	// create button here - just a read-only log with the ability to remove
	// a bad entry.
	'top_buttons' => array(),

	'where' => '',
	'list_fields' => array(
		'received_at' => array(
			'vname' => 'LBL_RECEIVED_AT',
			'widget_class' => 'SubPanelDetailViewLink',
			'width' => '13%',
			'default' => true,
		),
		'msisdn' => array(
			'vname' => 'LBL_MSISDN',
			'width' => '12%',
			'default' => true,
		),
		// the contact the reply was matched to, and the list they were added
		// to because of it - both can be sorted on. The list is a dropdown
		// that moves the contact to another of the SMS's reply lists.
		'contact_name' => array(
			'vname' => 'LBL_CONTACT_NAME',
			'widget_class' => 'SubPanelDetailViewLink',
			'target_module' => 'Contacts',
			'target_record_key' => 'contact_id',
			'width' => '15%',
			'default' => true,
		),
		'response' => array(
			'vname' => 'LBL_RESPONSE',
			'width' => '30%',
			'default' => true,
		),
		// Not a real field: ListView ignores a custom widget_class on a field
		// that's in the vardefs (it renders those through SugarFields), so the
		// dropdown gets its own display-only column, sorted by the list name
		// that the query-only reply_list_name below brings into the query.
		'reply_list_select' => array(
			'vname' => 'LBL_REPLY_LIST_NAME',
			'widget_class' => 'SubPanelReplyListSelect',
			'usage' => 'display_only',
			'sort_by' => 'reply_list_name',
			'width' => '25%',
			'default' => true,
		),
		'reply_list_name' => array(
			'usage' => 'query_only',
		),
		'sms_id' => array(
			'usage' => 'query_only',
		),
		'contact_id' => array(
			'usage' => 'query_only',
		),
		'reply_list_id' => array(
			'usage' => 'query_only',
		),
		'remove_button' => array(
			'widget_class' => 'SubPanelRemoveButton',
			'module' => $module_name,
			'width' => '5%',
		),
	),
);

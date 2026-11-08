<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/* * *******************************************************************************
 * SMS_Replies - stores inbound SMS replies received via the transmitsms webhook
 * and links them back to the originating SMS_SMS record.
 * ****************************************************************************** */

$module_name = 'SMS_Replies';
$bean_name = 'SMS_Replies';
$table_name = 'sms_replies';

$dictionary[$bean_name] = array(
	'table' => $table_name,
	'audited' => false,
	'fields' => array(

		// linked SMS_SMS record this reply belongs to
		'sms_id' =>
		array(
			'name' => 'sms_id',
			'type' => 'id',
			'group' => 'sms_name',
			'reportable' => true,
			'vname' => 'LBL_SMS_ID',
		),
		'sms_name' =>
		array(
			'name' => 'sms_name',
			'rname' => 'name',
			'group' => 'sms_name',
			'vname' => 'LBL_SMS',
			'reportable' => false,
			'source' => 'non-db',
			'id_name' => 'sms_id',
			'type' => 'relate',
			'module' => 'SMS_SMS',
			'table' => 'sms_sms',
			'link' => 'sms_sms_link',
			'duplicate_merge' => 'disabled',
		),
		'sms_sms_link' =>
		array(
			'name' => 'sms_sms_link',
			'type' => 'link',
			'relationship' => 'sms_replies',
			'vname' => 'LBL_SMS',
			'link_type' => 'one',
			'module' => 'SMS_SMS',
			'bean_name' => 'SMS_SMS',
			'source' => 'non-db',
		),

		// transmitsms reply id - used to de-duplicate retried webhook deliveries
		'transmit_id' =>
		array(
			'name' => 'transmit_id',
			'vname' => 'LBL_TRANSMIT_ID',
			'type' => 'varchar',
			'len' => '50',
			'comment' => 'transmitsms response id (webhook "id" field)',
		),

		// transmitsms message_id of the original outbound SMS being replied to
		'message_id' =>
		array(
			'name' => 'message_id',
			'vname' => 'LBL_MESSAGE_ID',
			'type' => 'varchar',
			'len' => '100',
			'comment' => 'transmitsms message_id this is a reply to',
		),

		'received_at' =>
		array(
			'name' => 'received_at',
			'vname' => 'LBL_RECEIVED_AT',
			'type' => 'datetime',
			'comment' => 'Date/time transmitsms received the reply',
			'enable_range_search' => true,
			'options' => 'date_range_search_dom',
		),

		'first_name' =>
		array(
			'name' => 'first_name',
			'vname' => 'LBL_FIRST_NAME',
			'type' => 'varchar',
			'len' => '100',
		),

		'last_name' =>
		array(
			'name' => 'last_name',
			'vname' => 'LBL_LAST_NAME',
			'type' => 'varchar',
			'len' => '100',
		),

		'msisdn' =>
		array(
			'name' => 'msisdn',
			'vname' => 'LBL_MSISDN',
			'type' => 'varchar',
			'len' => '50',
			'comment' => 'Mobile number the reply was sent from',
			'unified_search' => true,
		),

		'response' =>
		array(
			'name' => 'response',
			'vname' => 'LBL_RESPONSE',
			'type' => 'text',
			'comment' => 'Text content of the SMS reply',
			'unified_search' => true,
		),

		'longcode' =>
		array(
			'name' => 'longcode',
			'vname' => 'LBL_LONGCODE',
			'type' => 'varchar',
			'len' => '50',
			'comment' => 'Number the reply was sent to',
		),

		// The contact the reply was matched to, and the target list they were
		// added to because of it - both set by routeSmsReplyToTargetList()
		// (ReplyRouting.php). Relate fields with links so they can be shown
		// and sorted on in the SMS Replies subpanel.
		'contact_id' =>
		array(
			'name' => 'contact_id',
			'vname' => 'LBL_CONTACT_ID',
			'type' => 'id',
			'reportable' => false,
			'comment' => 'Contact the reply\'s mobile number was matched to',
		),
		'contact_name' =>
		array(
			'name' => 'contact_name',
			'rname' => 'last_name',
			'db_concat_fields' => array(0 => 'first_name', 1 => 'last_name'),
			'id_name' => 'contact_id',
			'vname' => 'LBL_CONTACT_NAME',
			'type' => 'relate',
			'link' => 'contact_link',
			'table' => 'contacts',
			'isnull' => 'true',
			'module' => 'Contacts',
			'join_name' => 'contacts',
			'dbType' => 'varchar',
			'source' => 'non-db',
			'len' => 36,
			'massupdate' => false,
			'duplicate_merge' => 'disabled',
		),
		'contact_link' =>
		array(
			'name' => 'contact_link',
			'type' => 'link',
			'relationship' => 'sms_replies_contact',
			'vname' => 'LBL_CONTACT_NAME',
			'link_type' => 'one',
			'module' => 'Contacts',
			'bean_name' => 'Contact',
			'source' => 'non-db',
		),
		'reply_list_id' =>
		array(
			'name' => 'reply_list_id',
			'vname' => 'LBL_REPLY_LIST_ID',
			'type' => 'id',
			'reportable' => false,
			'comment' => 'Target list the contact was added to because of this reply',
		),
		'reply_list_name' =>
		array(
			'name' => 'reply_list_name',
			'rname' => 'name',
			'id_name' => 'reply_list_id',
			'vname' => 'LBL_REPLY_LIST_NAME',
			'type' => 'relate',
			'link' => 'reply_list_link',
			'table' => 'prospect_lists',
			'isnull' => 'true',
			'module' => 'ProspectLists',
			'join_name' => 'reply_lists',
			'dbType' => 'varchar',
			'source' => 'non-db',
			'len' => 255,
			'massupdate' => false,
			'duplicate_merge' => 'disabled',
		),
		'reply_list_link' =>
		array(
			'name' => 'reply_list_link',
			'type' => 'link',
			'relationship' => 'sms_replies_reply_list',
			'vname' => 'LBL_REPLY_LIST_NAME',
			'link_type' => 'one',
			'module' => 'ProspectLists',
			'bean_name' => 'ProspectList',
			'source' => 'non-db',
		),
	),
	'relationships' => array(
		'sms_replies_contact' => array(
			'lhs_module' => 'Contacts', 'lhs_table' => 'contacts', 'lhs_key' => 'id',
			'rhs_module' => 'SMS_Replies', 'rhs_table' => 'sms_replies', 'rhs_key' => 'contact_id',
			'relationship_type' => 'one-to-many',
		),
		'sms_replies_reply_list' => array(
			'lhs_module' => 'ProspectLists', 'lhs_table' => 'prospect_lists', 'lhs_key' => 'id',
			'rhs_module' => 'SMS_Replies', 'rhs_table' => 'sms_replies', 'rhs_key' => 'reply_list_id',
			'relationship_type' => 'one-to-many',
		),
	),
	// The 'sms_replies' relationship itself is defined once, on the "one" side,
	// in modules/SMS_SMS/vardefs.php - the 'sms_sms_link' field above just refers
	// to it by name (same convention as the existing 'camp_sms' relationship).
	'indices' => array(
		array('name' => 'idx_sms_replies_sms_id', 'type' => 'index', 'fields' => array('sms_id')),
		array('name' => 'idx_sms_replies_transmit_id', 'type' => 'index', 'fields' => array('transmit_id')),
		array('name' => 'idx_sms_replies_message_id', 'type' => 'index', 'fields' => array('message_id')),
		array('name' => 'idx_sms_replies_contact_id', 'type' => 'index', 'fields' => array('contact_id')),
		array('name' => 'idx_sms_replies_reply_list_id', 'type' => 'index', 'fields' => array('reply_list_id')),
	),
	'optimistic_lock' => true,
);
VardefManager::createVardef($module_name, $bean_name, array('default'));

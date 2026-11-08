<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/* * *******************************************************************************
 * SMS_Replies - a reply to an outbound SMS_SMS message, captured via the
 * transmitsms inbound webhook (see sms_reply_webhook.php).
 * ****************************************************************************** */

class SMS_Replies extends Basic {

	var $new_schema = true;
	var $module_dir = 'SMS_Replies';
	var $object_name = 'SMS_Replies';
	var $table_name = 'sms_replies';
	var $importable = false;

	var $id;
	var $name;
	var $date_entered;
	var $date_modified;
	var $modified_user_id;
	var $modified_by_name;
	var $created_by;
	var $created_by_name;
	var $description;
	var $deleted;
	var $created_by_link;
	var $modified_user_link;

	var $sms_id;
	var $sms_name;
	var $transmit_id;
	var $message_id;
	var $received_at;
	var $first_name;
	var $last_name;
	var $msisdn;
	var $response;
	var $longcode;
	var $contact_id;
	var $contact_name;
	var $reply_list_id;
	var $reply_list_name;

	function SMS_Replies() {
		parent::Basic();
	}

	function bean_implements($interface) {
		switch ($interface) {
			case 'ACL': return true;
		}
		return false;
	}

	function get_summary_text() {
		return "$this->name";
	}
}

<?php
//FIELDS
$dictionary['ProspectList']['fields']['name']['len'] = '255';

$dictionary['ProspectList']['fields']['camp_master_list'] = 
    array (
            'name' => 'camp_master_list',
            'vname' => 'LBL_CAMP_MASTER_LIST',
            'comment' => 'Master List for a Camp',
            'type'=>'bool',
            'default' => '0',
            'merge_filter' => 'enabled',
            'massupdate' => false,
    );



$dictionary['ProspectList']['fields']['camps'] =
        array (
  			'name' => 'camps',
    		'type' => 'link',
    		'relationship' => 'prospect_list_camps',
    		'source'=>'non-db',
  		);

$dictionary['ProspectList']['fields']['emails'] =
        array(
            'name' => 'emails',
            'type' => 'link',
            'relationship' => 'prospect_list_emails', /* reldef in emails */
            'module' => 'Emails',
            'bean_name' => 'Email',
            'source' => 'non-db',
            'vname' => 'LBL_EMAILS',
        );

$dictionary['ProspectList']['fields']['sms_list_id'] =
		array(
                        'required' => true,
			'name' => 'sms_list_id',
			'vname' => 'LBL_SMS_LIST_ID',
			'type' => 'varchar',
		);

$dictionary['ProspectList']['fields']['reply_keywords'] =
		array(
			'name' => 'reply_keywords',
			'vname' => 'LBL_REPLY_KEYWORDS',
			'comment' => 'Comma-separated SMS reply keyword(s) this camp target list should collect (e.g. "1,yes,y"). Use * for the catch-all list that collects replies matching no other tagged list on the camp. Blank for normal (non-reply) lists.',
			'type' => 'varchar',
			'len' => '255',
			'massupdate' => false,
		);

$dictionary['ProspectList']['fields']['reply_source_list_id'] =
		array(
			'name' => 'reply_source_list_id',
			'vname' => 'LBL_REPLY_SOURCE_LIST_ID',
			'comment' => 'For a reply list on an SMS sent from a target list (rather than a camp): the target list whose SMS replies this list collects',
			'type' => 'id',
			'reportable' => false,
		);

$dictionary['ProspectList']['fields']['reply_source_list_name'] =
		array(
			'name' => 'reply_source_list_name',
			'vname' => 'LBL_REPLY_SOURCE_LIST',
			'type' => 'relate',
			'source' => 'non-db',
			'rname' => 'name',
			'id_name' => 'reply_source_list_id',
			'module' => 'ProspectLists',
			'table' => 'prospect_lists',
			'massupdate' => false,
			'reportable' => false,
			'duplicate_merge' => 'disabled',
		);

$dictionary['ProspectList']['fields']['reply_sms_id'] =
		array(
			'name' => 'reply_sms_id',
			'vname' => 'LBL_REPLY_SMS_ID',
			'comment' => 'For a reply list created for one SMS (the SMS "Create Reply Lists" option): that SMS. Its replies are routed only to its own lists, and other SMS from the same camp/list ignore it.',
			'type' => 'id',
			'reportable' => false,
		);

$dictionary['ProspectList']['fields']['sms'] =
        array(
            'name' => 'sms',
            'type' => 'link',
            'module' => 'SMS_SMS',
            'relationship' => 'prospectlist_sms',
            'bean_name' => 'SMS_SMS',
            'source' => 'non-db',
            'vname' => 'LBL_SMS_SMS',
        );

//RELATIONSHIPS
$dictionary['ProspectList']['relationships']['prospect_list_camps'] =
        array(
            'lhs_module'=> 'ProspectLists', 
            'lhs_table'=> 'prospect_lists', 
            'lhs_key' => 'id',
            'rhs_module'=> 'Camps', 
            'rhs_table'=> 'camps', 
            'rhs_key' => 'id',
            'relationship_type'=>'many-to-many',
            'join_table'=> 'prospect_lists_prospects', 
            'join_key_lhs'=>'prospect_list_id', 
            'join_key_rhs'=>'related_id',
            'relationship_role_column'=>'related_type',
            'relationship_role_column_value'=>'Camps'
        );


$dictionary['ProspectList']['relationships']['prospect_list_emails'] = 
        array(
            'lhs_module'=> 'ProspectLists', 
            'lhs_table'=> 'prospect_lists', 
            'lhs_key' => 'id',
            'rhs_module'=> 'Emails', 
            'rhs_table'=> 'emails', 
            'rhs_key' => 'parent_id',
            'relationship_type'=>'one-to-many', 
            'relationship_role_column'=>'parent_type',
            'relationship_role_column_value'=>'ProspectLists'
        );

$dictionary['ProspectList']['relationships']['prospectlist_sms'] =
        array(
            'lhs_module'	=> 'ProspectLists',
            'lhs_table'		=> 'prospect_lists',
            'lhs_key'		=> 'id',
            'rhs_module'	=> 'SMS_SMS',
            'rhs_table'		=> 'sms_sms',
            'rhs_key'		=> 'parent_id',
            'relationship_type'	=> 'one-to-many',
            'relationship_role_column'=>'parent_type',
            'relationship_role_column_value'=>'ProspectLists'
        );

?>

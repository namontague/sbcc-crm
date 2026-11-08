<?php
/* * *******************************************************************************
 * (C) 2013 Enterprise Intelligence Systems Pty Ltd
 * No part of this file should be used without the express permission
 * of the authors.
 *
 * Contact: info@eintel.com.au for more information.
 *
 *
 * ****************************************************************************** */

$dictionary['SMS_SMS'] = array(
	'table'=>'sms_sms',
	'audited'=>false,
	'fields'=>array (
            'name' =>
            array (
                'name' => 'name',
                'vname' => 'LBL_SUBJECT',
                'type' => 'text',
                'comment' => 'SMS Message',
                'importable' => 'required',
                'len' => '160',
            ),
            'description' =>
            array (
                'name' => 'description',
                'vname' => 'LBL_DESCRIPTION',
                'type' => 'text',
                'comment' => 'SMS Message',
            ),
            'date_entered' =>
            array (
                'name' => 'date_entered',
                'vname' => 'LBL_DATE_CREATED',
                'type' => 'datetime',
                'comment' => 'SMS Message',
                'importable' => 'required',
            ),
            'date_sent' =>
            array (
                'name' => 'date_sent',
                'vname' => 'LBL_DATE_SENT',
                'type' => 'datetime',
                'comment' => 'Date SMS sent',
                'importable' => 'required',
            ),
            'sent_by_name' =>
            array (
                'name' => 'sent_by_name',
                'vname' => 'LBL_SEND_BY_NAME',
                'type' => 'varchar',
                'comment' => 'Name of user who sent sms',
                'importable' => 'required',
            ),
            'status' =>
            array (
                'name' => 'status',
                'vname' => 'LBL_STATUS',
                'type' => 'varchar',
                //'comment' => 'SMS status (ex: Planned, Held, Not held)'
            ),
               

            'caller_id' =>
            array (
                'name' => 'caller_id',
                'vname' => 'LBL_SENDER',
                'type' => 'varchar',
                //'default' => 'Shared Number',
                'default' => '0481 074 423',
                'len' => '36',    
                'comment' => 'SMS Sender Data'
            ),
            'to_list_id' =>
            array (
                'name' => 'to_list_id',
                'vname' => 'LBL_TO_LIST',
                'type' => 'enum',
                'function' => array(
                    'name' => 'getToListOpions',
                   // 'returns' => 'html',
                    'include' => 'modules/SMS_SMS/SMSFunctions.php'
                ) ,
                //'len' => '36',    
                'comment' => 'SMS To List Data'
            ),
            'to_list_name' =>
            array (
                'name' => 'to_list_name',
                'vname' => 'LBL_TO_LIST',
                'type' => 'varchar',
                //'len' => '36',    
                'comment' => 'SMS To List Name'
            ),
            'message' =>
            array (
                'name' => 'message',
                'vname' => 'LBL_SUBJECT',
                'type' => 'text',
                'comment' => 'SMS Message',
                'importable' => 'required',
            ),
            'msgCnt' =>
            array (
                'name' => 'msgCnt',    
                'type' => 'text',
                'source' => "non-db",   

            ),
            'detailCampaignView' =>
            array (
                'name' => 'detailCampaignView',    
                'type' => 'text',
                'source' => "non-db",   

            ),
            'response_data' =>
            array (
                'name' => 'response_data',
                'vname' => 'LBL_RESPONSE_DATA',
                'type' => 'text',
            ),
            'message_id' =>
            array (
                'name' => 'message_id',
                'vname' => 'LBL_MESSAGE_ID',
                'type' => 'varchar',
                'len' => '100',
                'comment' => 'transmitsms message_id returned when this SMS was sent',
            ),

            'create_reply_lists' =>
            array (
                'name' => 'create_reply_lists',
                'vname' => 'LBL_CREATE_REPLY_LISTS',
                'type' => 'bool',
                'default' => '0',
                'massupdate' => false,
                'comment' => 'When saved, create target lists to collect replies to this SMS - see modules/SMS_Replies/CreateReplyLists.php',
            ),
            'reply_list_keywords' =>
            array (
                'name' => 'reply_list_keywords',
                'vname' => 'LBL_REPLY_LIST_KEYWORDS',
                'type' => 'text',
                'rows' => 4,
                'cols' => 40,
                'comment' => 'One reply list per line, as comma-separated keywords (e.g. "yes,y,1"); a catch-all list is always created as well',
            ),

            'send_later_date' =>
            array (
                'name' => 'send_later_date',
                'vname' => 'LBL_SENT_AT_LATER_DATE',
                'type' => 'datetime',
                'comment' => 'Send sms at a later date',
                'importable' => 'required',
            ),
        
            'number_list' =>
            array (
                'name' => 'number_list',
                'vname' => 'LBL_NUMBERS',
                'type' => 'text',
                'source' => "non-db",   
                'function' => array(
                    'name' => 'getNumbers',
                    'returns' => 'html',
                    'include' => 'modules/SMS_SMS/SMSFunctions.php'
                ) ,
                'comment' => 'SMS numbers Data'
            ),
        
       //relate to fields
       'parent_name' => array(
            'required' => false,
            'source' => 'non-db',
            'name' => 'parent_name',
            'vname' => 'LBL_RELATE_TO',
            'type' => 'parent',
            'massupdate' => 0,
            'comments' => '',
            'help' => '',
            'importable' => 'true',
            'duplicate_merge' => 'disabled',
            'duplicate_merge_dom_value' => '0',
            'audited' => false,
            'reportable' => true,
            'len' => 25,
            'size' => '20',
            'options' => 'sms_parent_options',
            'studio' => 'visible',
            'type_name' => 'parent_type',
            'id_name' => 'parent_id',
            'parent_type' => 'sms_parent_options',
        ),
 
        'parent_type' => array(
            'required' => false,
            'name' => 'parent_type',
            'vname' => 'LBL_PARENT_TYPE',
            'type' => 'parent_type',
            'massupdate' => 0,
            'comments' => '',
            'help' => '',
            'importable' => 'true',
            'duplicate_merge' => 'disabled',
            'duplicate_merge_dom_value' => 0,
            'audited' => false,
            'reportable' => true,
            'len' => 255,
            'size' => '20',
            'dbType' => 'varchar',
            'studio' => 'hidden',
        ),
 
        'parent_id' => array(
            'required' => false,
            'name' => 'parent_id',
            'vname' => 'LBL_PARENT_ID',
            'type' => 'id',
            'massupdate' => 0,
            'comments' => '',
            'help' => '',
            'importable' => 'true',
            'duplicate_merge' => 'disabled',
            'duplicate_merge_dom_value' => 0,
            'audited' => false,
            'reportable' => true,
            'len' => 36,
            'size' => '20',
        ),     
            
        'camp' => array(
            'name' => 'camp',
            'type' => 'link',
            'relationship' => 'camp_sms',
            'module'=>'Camps',
            'bean_name'=>'Camp',
            'source'=>'non-db',
            'vname'=>'LBL_CAMPS',
        ),
        'prospectlist' => array(
            'name' => 'prospectlist',
            'type' => 'link',
            'relationship' => 'prospectlist_sms',
            'module'=>'ProspectLists',
            'bean_name'=>'ProspectList',
            'source'=>'non-db',
            'vname'=>'LBL_PROSPECTLISTS',
        ),
        'replies' => array(
            'name' => 'replies',
            'type' => 'link',
            'relationship' => 'sms_replies',
            'module'=>'SMS_Replies',
            'bean_name'=>'SMS_Replies',
            'source'=>'non-db',
            'vname'=>'LBL_SMS_REPLIES',
        ),
            
     
            
            
),
	'relationships'=>array (

        'sms_replies' => array(
            'lhs_module'=> 'SMS_SMS', 'lhs_table'=> 'sms_sms', 'lhs_key' => 'id',
            'rhs_module'=> 'SMS_Replies', 'rhs_table'=> 'sms_replies', 'rhs_key' => 'sms_id',
            'relationship_type'=>'one-to-many',
        ),

),
	'optimistic_lock'=>true,
);
VardefManager::createVardef('SMS_SMS','SMS_SMS', array('default'));

?>
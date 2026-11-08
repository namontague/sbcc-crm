<?php
/*********************************************************************************
 * SugarCRM is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004 - 2009 SugarCRM Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 ********************************************************************************/

$module_name = 'SMS_SMS';
$viewdefs[$module_name]['EditView'] = array(
'templateMeta' => array(
        'maxColumns' => '2',
        'widths' => array(
            array('label' => '10', 'field' => '30'),
            array('label' => '10', 'field' => '30')
        ),

    									
        'javascript' => '<script type="text/javascript" src="' . getJSPath('modules/SMS_SMS/javascript/editview.js') . '"></script>',
    ),	        
    'panels' => array (
        'default' => array (
            array(
                array(
                    'name'=>'parent_name',
                    'displayParams' => array(
                        'field_to_name_array' => array(
                            'id' => 'parent_id',
                            'name' => 'parent_name',
                            'sms_list_id' => 'to_list_id_value'
                        ),  
                        'additionalFields' => array(
                            'sms_list_id' => 'to_list_id_value'
                        )
                    )
                )
            ),
            array('description'),
            array (
                array(
                    'name'=>'caller_id', 
                    'customCode' => '<span id="caller_id"> {if $fields.caller_id.value == null}0481 074 423{else} {$fields.caller_id.value} {/if} </span>',
                ),      				 
            ),
            array (
                array(
                   'name' => 'to_list_id',
                   'customCode' => '<select name="{$fields.to_list_id.name}" id="{$fields.to_list_id.name}" tabindex="0"  >
                                       {if $fields.id.value == null}
                                            {html_options options=$fields.to_list_id.options selected=$sms_list_id}
                                       {else}
                                            {html_options options=$fields.to_list_id.options selected=$fields.to_list_id.value}
                                       {/if}
                                   </select>
                                   <input type="hidden" name="to_list_id_value" id="to_list_id_value" value="" title="">
                                   <input type="hidden" name="to_list_name" id="to_list_name" value="{$fields.to_list_name.value}" title="">
                                    ',
               )
            ),
            
            array (
                array(
                    'name'=>'number_list', 
                    'customCode' => '<span id="number_list">{$fields.number_list.value}</span>')
            ),
            
            
            array (
                array(
                    'name'=>'message', 
                    'displayParams'=>array('required'=>true,'size'=>160),
                    'customCode' => '<textarea onfocus="chngcnt(3);" onblur="chngcnt(3);" onkeydown="chngcnt(3);" onkeyup="strcount(3);chngcnt(3);"  tabindex="0" title="" cols="60" rows="4" name="message" id="message">{$fields.message.value}</textarea>
                    <br><div id="nameMsg" class="required"></div>'
                )
            ),
            array (
                'create_reply_lists',
            ),
            array (
                array(
                    'name' => 'reply_list_keywords',
                    'customCode' => '<textarea tabindex="0" cols="40" rows="4" name="reply_list_keywords" id="reply_list_keywords">{$fields.reply_list_keywords.value}</textarea>
                    <br><span>With "Create Reply Lists" ticked, a target list is created for each line when you save, collecting the replies that match one of its keywords. For example, enter <b>yes,y,1</b> on one line and <b>no,n,2</b> on the next. An "Other replies" list for everything else is always created.</span>'
                ),
            ),
            array (
                array(
                    'name'=>'msgCnt', 
                    'customLabel' => "Chars", 
                    'customCode' => '<span id="msgCnt"> {$fields.msgCnt.value}</span></br></br>
                                    <p><span>SMS Messages can be up to 612 chars each, and will all be delivered as one message, however messages longer than 160 characters will cost you an extra unit of SMS.</span></p>
                                    <p><span>Up to 160 chars = 1 SMS</span></p>
                                    <p><span>Up to 306 chars = 2 SMS</span></p>
                                    <p><span>Up to 459 chars = 3 SMS</span></p>
                                    <p><span>Up to 612 chars = 4 SMS</span></p>'
                ),
            ),      				 
               				 
        ),
  			
    ),
);
?>
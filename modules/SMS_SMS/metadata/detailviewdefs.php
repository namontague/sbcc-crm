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

$viewdefs[$module_name]['DetailView'] = array(
    'templateMeta' => array(
        'form' => array(
            'buttons' => array(
                        'EDIT', 
                        'DUPLICATE', 
                        'DELETE'
            ),
        ),
        'maxColumns' => '1', 
        'widths' => array(
            array('label' => '10', 'field' => '30'), 

        ),
        'javascript' => '<script type="text/javascript" src="' . getJSPath('modules/SMS_SMS/javascript/detailview.js') . '"></script>',
    ),
    'panels' => array(
        'lbl_panel_details' => array(
            array(
                'parent_name'
            ),
            array(
                'description'
            ),
            array (
                'caller_id',
            ),
            array(
                'to_list_name'
            ),
            array(
                //live count of numbers
                array(
                    'name' => 'number_list',
                ),
            ),
            array(
                'message'
            ),
            array(
                'create_reply_lists'
            ),
            array(
                'reply_list_keywords'
            ),
            array (
                array (
                    'name' => 'date_sent',
                    'customCode' => '{$fields.date_sent.value} by {$fields.sent_by_name.value}',
                    'label' => 'LBL_DATE_SENT',
                ),
            ),
            array(
                array(
                    'name'=>'', 
                    'customCode'=>'<input class="button" type="submit" value="Send SMS" id="send_sms" name="button" onclick="return sendSMS();" accesskey="S" title="Send SMS [Alt+S]"/><span id="status_msg" class="required"></span>'
                )
            ),
        ),
        'lbl_panel_extra_details' => array(
            array(
                'status'
            ),
            array(
                'response_data'
            ),
        ),
    ),
);
?>

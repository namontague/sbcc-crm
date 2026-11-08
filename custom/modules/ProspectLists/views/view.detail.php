<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

/*********************************************************************************
 * SugarCRM is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2010 SugarCRM Inc.
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

require_once('include/MVC/View/views/view.detail.php');

class ProspectListsViewDetail extends ViewDetail {


 	function ProspectListsViewDetail(){
 		parent::ViewDetail();
 	}

 	/**
 	 * display
 	 * Override the display method to support customization for the buttons that display
 	 * a popup and allow you to copy the account's address into the selected contacts.
 	 * The custom_code_billing and custom_code_shipping Smarty variables are found in
 	 * include/SugarFields/Fields/Address/DetailView.tpl (default).  If it's a English U.S.
 	 * locale then it'll use file include/SugarFields/Fields/Address/en_us.DetailView.tpl.
 	 */
 	function display(){
				
            if(empty($this->bean->id)){
                    global $app_strings;
                    sugar_die($app_strings['ERROR_NO_RECORD']);
            }
        
            require_once('modules/AOS_PDF_Templates/formLetter.php');
            formLetter::DVPopupHtml('ProspectLists', $this->bean);
		
            if(module_enabled('SMS_SMS')) {
                require_once('modules/SMS_SMS/SMSFunctions.php');
                if(is_null($this->bean->sms_list_id) || empty($this->bean->sms_list_id)){
                     //create a sms list for this camp
                    $sms_button_html = getSMSButton();
                    if($sms_button_html != null){
                        $this->ss->assign('BUTTON',$sms_button_html);
                    }
                }else{
                    if(isset($this->bean->sms_list_id)){
                        $sms_button_html = getSMSSyncButton();
                        if($sms_button_html != null){
                            $this->ss->assign('BUTTON',$sms_button_html);
                        }
                        if(isset($this->bean->camp_master_list) && $this->bean->camp_master_list == true) {
                          $this->ss->assign('BUTTON', "<script type='text/javascript' src='custom/modules/ProspectLists/javascript/button.js'></script>
                              <input type='button' id='reset_target_list' title='Reset Target List' value='Reset Target List' onClick='resetTargets();' />
                              <img id='ajax_wait' alt='waiting' src='modules/SMS_SMS/img/ajax-loader.gif' style='display:none'>
                              <span id='button_info'></span>");
                        }
                    }
                }
            }
            
            
            $this->dv->process();
            echo $this->dv->display();
 	}
}

?>

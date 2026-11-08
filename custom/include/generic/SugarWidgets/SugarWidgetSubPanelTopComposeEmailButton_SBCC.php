<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 ********************************************************************************/






class SugarWidgetSubPanelTopComposeEmailButton_SBCC extends SugarWidgetSubPanelTopButton {
	var $form_value = '';
    
    public function getWidgetId() {
    	global $app_strings;
        $this->form_value = $app_strings['LBL_COMPOSE_EMAIL_BUTTON_LABEL'];
    	return parent::getWidgetId();
    }

    function display($defines) {

        if((ACLController::moduleSupportsACL($defines['module'])  && !ACLController::checkAccess($defines['module'], 'edit', true) ||
            $defines['module'] == "Activities" & !ACLController::checkAccess("Emails", 'edit', true))){
            $temp = '';
            return $temp;
        }

        global $app_strings,$current_user,$sugar_config,$beanList,$beanFiles;
        $title = $app_strings['LBL_COMPOSE_EMAIL_BUTTON_TITLE'];
        //$accesskey = $app_strings['LBL_COMPOSE_EMAIL_BUTTON_KEY'];
        $value = $app_strings['LBL_COMPOSE_EMAIL_BUTTON_LABEL'];
        $parent_type = $defines['focus']->module_dir;
        $parent_id = $defines['focus']->id;

        $related_module = $defines['module'];
        
        
        $subpanel_definition = $defines['subpanel_definition'];
        
        //martin Bug 19660
        $userPref = $current_user->getPreference('email_link_type');
        $defaultPref = $sugar_config['email_default_client'];

        //Generate the compose package for the quick create options.
        $composeData = array("parent_id" => $parent_id, "parent_type"=>$parent_type);

        $relationship = $this->getLinkedField($subpanel_definition->panel_definition);

        if(is_null($relationship)){
            //relationship missing
            return;
        }

        //The to addresses are not retrieved here, large subpanels take too long. They are retrieved on
        //demand by a button in the quick compose window (entryPoint sbccSubpanelEmailAddresses).
        global $app_list_strings;
        $related_label = isset($app_list_strings['moduleList'][$related_module]) ? $app_list_strings['moduleList'][$related_module] : $related_module;
        $json_flags = JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP;
        $j_context = json_encode(array(
            'parent_type' => $parent_type,
            'parent_id' => $parent_id,
            'linked_field' => $relationship,
            'label' => "Add all {$related_label} email addresses",
        ), $json_flags);

        require_once('modules/Emails/EmailUI.php');
        $eUi = new EmailUI();
        $j_quickComposeOptions = $eUi->generateComposePackageForQuickCreate($composeData, http_build_query($composeData), false, $defines['focus']);

        //the js is loaded from the onclick as the top buttons are rebuilt into the action menu
        $onclick = "var o=$j_quickComposeOptions,c=$j_context;"
            . "if(window.SBCCQuickCompose){SBCCQuickCompose.open(o,c);}"
            . "else{jQuery.getScript(&quot;custom/include/javascript/sbccQuickComposeRecipients.js&quot;,function(){SBCCQuickCompose.open(o,c);});}"
            . "return false;";

        $button = "<input title='$title'  id='". $this->getWidgetId()."'  onclick='$onclick' class='button' type='submit' name='".preg_replace('[ ]', '', $value)."_button' value='$value' />";

        return $button;
      }

    /*
     * get linked field value (relationship value)
     */
    function getLinkedField($subpanel) {
        if(isset($subpanel['top_buttons'])){
            foreach($subpanel['top_buttons'] as $widget){
                if($widget['widget_class'] == 'SubPanelTopComposeEmailButton_SBCC'){
                    return isset($widget['linked_field'])?$widget['linked_field']:null;
                }
            }
        }
        return;
    }
    
    
    /*
     * Return the primary email address for the bean
     * Does not return opted out address
     */
    function getAddress($focus,$parent_id=null,$parent_type=null) {

        $parent_type=empty($parent_type) ? $focus->module_dir : $parent_type;
        // Bug63174: Email address is not shown in the list view for employees
        $parent_type = ($parent_type == "Employees")? "Users" : $parent_type;
        
        $parent_id=empty($parent_id) ? $focus->id : $parent_id;

        $q = "SELECT ea.email_address FROM email_addresses ea
                LEFT JOIN email_addr_bean_rel ear ON ea.id = ear.email_address_id
                WHERE ear.bean_module = '".$focus->db->quote($parent_type)."'
                AND ear.bean_id = '".$focus->db->quote($parent_id)."'
                AND ear.deleted = 0
                AND ea.invalid_email = 0
                AND ea.opt_out = 0
                ORDER BY ear.primary_address DESC";
        $r = $focus->db->limitQuery($q, 0, 1);
        $a = $focus->db->fetchByAssoc($r);

        if(isset($a['email_address'])) {
            return $a['email_address'];
        }
        return '';
    }
    
    
}

<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
* This file is an customisation to SugarCRM developed by Enterprise Intelligence
* Systems Pty Ltd.  Copyright (C) 2009-2010 Enterprise Intelligence Systems Pty Ltd.
*
* SugarCRM is a customer relationship management program developed by
* SugarCRM, Inc.
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
* FOR A PARTICULAR PURPOSE.  SGNU Affero General Public LicenseLicense for more
* details.
*
* You should have received GNU Affero General Public LicensePublic License along with
* this program; if not, see http://www.gnu.org/licenses or write to the Free
* Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
* 02110-1301 USA.
*
* You can contact Enterprise Intelligence Systems Pty Ltd. headquarters at
* 25/20 Enterprise Dr, Bundoora, Vic, 3083, Australia or at email address
* info@eintel.com.au
*
* The interactive user interfaces in modified source and object code versions
* of this program must display Appropriate Legal Notices, as required undeGNU Affero General Public Licenseeneral Public License version 3.
*
* In accordanGNU Affero General Public Licensee GNU General Public License version 3,
* these Appropriate Legal Notices must retain the display of the "Powered by
* SugarCRM" logo. If the display of the logo is not reasonably feasible for
* technical reasons, the Appropriate Legal Notices must display the words
* "Powered by SugarCRM".
********************************************************************************/
require_once('include/MVC/Controller/SugarController.php');

class ConfigListsController extends SugarController
{

    function action_AddDefault() {
        $this->addDefaultListValues();
        $this->view = null;
        $this->redirect_url='index.php?module=ConfigLists&return_module=ConfigLists&action=DetailView&record='.$this->bean->id;
    }

    function addDefaultListValues() {
        global $app_list_strings;
        $configlist = $this->bean;

        $linked_bean = $configlist->get_linked_beans('values','ConfigListsValues');
        $list_key = $this->bean->reference;
        if( isset($app_list_strings[$list_key.'_original'])) {
            $list_key =$list_key.'_original';
        }
        $current_app_string_list = $app_list_strings[$list_key];
        $position= 1;
        foreach($current_app_string_list as $app_string_item_value => $app_string_item_name){
            $exists = false;
            foreach($linked_bean as $bean){
               if($bean->name == $app_string_item_name){
                   $exists = true;
               }
            }
            if(!$exists){
                $configlistvalue = new ConfigListsValues();
                $configlistvalue->name = $app_string_item_name;
                $configlistvalue->value = $app_string_item_value;
                $configlistvalue->position = $position;
                $configlistvalue->configlists_name = $this->bean->name;
                $configlistvalue->configlists_id = $this->bean->id;
                $configlistvalue->save();
            }
            $position++;
        }

    }

}
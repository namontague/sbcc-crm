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

require_once('modules/ConfigLists/ConfigLists.php');

class ConfigListsManager {
    static $configListReferences;
    static $configList;


    function ConfigListsManager() {
        if( !isset(self::$configListReferences )) {
            $this->loadConfigListReferences();
        }
        if( !isset(self::$configList)) {
            self::$configList = array();            
        }
    }

    function loadConfigListReferences() {
        self::$configListReferences = array();
        $db = DBManagerFactory::getInstance();
        $query = "select id, reference from config_lists where reference is not null and reference <> '' and deleted = 0";
        $result = $db->query($query);
        while($row = $db->fetchByAssoc($result) ) {
            self::$configListReferences[$row['reference']] = $row['id'];
        }
    }

    function getListByReference($reference) {
        if( isset(self::$configListReferences[$reference])) {
            return $this->getListById(self::$configListReferences[$reference]);
        } else {
            global $app_list_strings;
            return $app_list_strings[$reference];
        }
    }

    function getListById($id) {
        if( isset(self::$configList[$id]) == false) {
            $db = DBManagerFactory::getInstance();
            $query = "select value, name from config_lists_values where configlists_id={$id} and deleted = 0 order by position";
            $result = $db->query($query);
            $list = array();
            while($row = $db->fetchByAssoc($result) ) {
                $list[$row['value']] = $row['name'];
            }
            self::$configList[$id] = $list;
        }
        return self::$configList[$id];
    }

    function getAllIndexedByReference() {
        $db = DBManagerFactory::getInstance();
        $query = "select l.id, l.reference, v.name, v.value from config_lists l  
                    left join config_lists_values v on v.configlists_id = l.id
                    where l.deleted = 0 and v.deleted = 0 and l.reference is not null and l.reference <> ''
                    order by l.reference, v.position";
        $result = $db->query($query);
        $current_list = '';
        $list = array();
        while($row = $db->fetchByAssoc($result) ) {
            if( $current_list != $row['id']) {
                self::$configListReferences[$row['reference']] = $row['id'];
                self::$configList[$row['id']] = array();
                $list[$row['reference']] = array();
                $current_list = $row['id'];
            }
            self::$configList[$current_list][$row['value']] = $row['name'];
            $list[$row['reference']][$row['value']] = $row['name'];
        }
        return $list;

    }
}
 

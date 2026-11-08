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

$dictionary['ConfigListsValues'] = array(
    'table'=>'config_lists_values',
    'fields'=>array (
        'name' => array (
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'varchar',
            'len' => '255',
            'required' => false,
            'comment' => 'Name'
          ),
        'description' => array (
            'name' => 'description',
            'vname' => 'LBL_DESCRIPTION',
            'type' => 'text',
            'comment' => 'Full description'
          ),
         'value' => array (
            'name' => 'value',
            'type' => 'varchar',
            'len' => '255',
            'vname'=>'LBL_VALUE',
          ),
         'position' => array (
            'name' => 'position',
            'type' => 'int',
            'default' => 1,
            'len' => '10',
            'vname'=>'LBL_POSITION',
          ),
          'configlists_name' => array (
            'required' => true,
            'name' => 'configlists_name',
            'rname' => 'name',
            'id_name' => 'configlists_id',
            'vname' => 'LBL_CONFIG_LISTS_NAME',
            'join_name'=>'config_lists',
            'type' => 'relate',
            'link' => 'config_lists',
            'table' => 'config_lists',
            'isnull' => 'true',
            'module' => 'ConfigLists',
            'dbType' => 'varchar',
            'len' => '255',
            'unified_search' => true,
        ),
        'configlists_id' => array (
           'name' => 'configlists_id',
           'rname' => 'configlists_id',
           'id_name' => 'configlists_id',
           'vname' => 'LBL_CONFIG_LISTS_ID',
           'type' => 'relate',
           'table' => 'config_lists',
           'isnull' => 'true',
           'module' => 'ConfigLists',
           'dbType' => 'id',
           'reportable'=>false,
           'massupdate' => false,
           'duplicate_merge'=> 'disabled',
           'hideacl'=>true,
        ),
        'values' => array (
            'name' => 'values',
            'type' => 'link',
            'relationship' => 'config_lists_values_rel',
            'module'=>'ConfigLists',
            'bean_name'=>'ConfigLists',
            'source'=>'non-db',
            'vname'=>'LBL_VALUES',
          ),   
    ),
    'relationships'=>array (
        'config_lists_values_rel' => array (
            'lhs_module'        => 'ConfigLists',
            'lhs_table'         => 'config_lists',
            'lhs_key'           => 'id',
            'rhs_module'        => 'ConfigListsValues',
            'rhs_table'         => 'config_lists_values',
            'rhs_key'           => 'configlists_id',
            'relationship_type' => 'one-to-many',
        ),


    ),
    'optimistic_lock'=>true,
);
require_once('include/SugarObjects/VardefManager.php');
VardefManager::createVardef('ConfigListsValues','ConfigListsValues', array('basic','assignable'));




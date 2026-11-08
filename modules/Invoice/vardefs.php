<?php
/*********************************************************************************
 * SugarCRM is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004 - 2009 SugarCRM Inc.
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
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required underGNU Affero General Public Licenseeneral Public License version 3.
 *
 * In accordanGNU Affero General Public Licensee GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 ********************************************************************************/
$dictionary['Invoice'] = array(
    'table'=>'invoice',
    'audited'=>true,
    'fields'=>array (

    'invoice_summary' => array (
            'required' => false,
            'name' => 'invoice_summary',
            'vname' => 'LBL_INVOICE_SUMMARY',
            'type' => 'varchar',
            'massupdate' => true,
            'comments' => 'invoice summary',
            'help' => 'invoice summary',
            'importable' => 'true',
            'duplicate_merge' => 'disabled',
            'duplicate_merge_dom_value' => '0',
            'audited' => 0,
            'reportable' => 0,
            'len' => '11',
            'disable_num_format' => '',
        ),

    'invoice_date' => array (
            'required' => false,
            'name' => 'invoice_date',
            'vname' => 'LBL_INVOICE_DATE',
            'type' => 'date',
        ),

    'account_name' => array (
            'required' => true,
            'name' => 'account_name',
            'rname' => 'name',
            'id_name' => 'account_id',
            'vname' => 'LBL_ACCOUNT_NAME',
            'join_name'=>'accounts',
            'type' => 'relate',
            'link' => 'accounts',
            'table' => 'accounts',
            'isnull' => 'true',
            'module' => 'Accounts',
            'dbType' => 'varchar',
            'len' => '255',
            'unified_search' => true,
        ),
    'account_id' => array (
           'name' => 'account_id',
           'rname' => 'id',
           'id_name' => 'account_id',
           'vname' => 'LBL_ACCOUNT_ID',
           'type' => 'relate',
           'table' => 'accounts',
           'isnull' => 'true',
           'module' => 'Accounts',
           'dbType' => 'id',
           'reportable'=>false,
           'massupdate' => false,
           'duplicate_merge'=> 'disabled',
           'hideacl'=>true,            ),
  'invoice_timesheet' =>
     array (
        'name' => 'invoice_timesheet',
        'type' => 'link',
        'relationship' => 'invoice_timesheet', //relationship definition in vardef
        'module'=>'Timesheet', // Child (many) part of the relationship
        'bean_name'=>'Timesheet', // This is the name of the sugar bean. Usually same as the module name
        'source'=>'non-db', // This field is not sourced from the database
        'vname'=>'LBL_COLLECTIONS', // Should not be required but is listed as mandatory for all fields in sugarcrm docs.
    ),

    ),
    'relationships'=>array (
        'invoice_timesheet' =>      array(
           'lhs_module'=> 'Invoice',
           'lhs_table'=> 'invoice',
           'lhs_key' => 'id',
           'rhs_module'=> 'Timesheet',
           'rhs_table'=> 'timesheet',
           'rhs_key' => 'invoice_id',
           'relationship_type'=>'one-to-many'
           ),

    ),
    'optimistic_lock'=>true,
);
require_once('include/SugarObjects/VardefManager.php');
VardefManager::createVardef('Invoice','Invoice', array('basic','assignable'));
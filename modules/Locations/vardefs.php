<?php
/* * *******************************************************************************
 * (C) 2009 Enterprise Intelligence Systems Pty Ltd
 * No part of this file should be used without the express permission
 * of the authors.
 *
 * Contact: info@eintel.com.au for more information.
 *
 *
 * ****************************************************************************** */

$module_name = 'Locations';
$module_table = 'locations';
$object_name = 'Location';

$dictionary[$object_name] = array(
    'table' => $module_table,
    'audited' => true,
    'fields' => array(

         'account_name' => array(
            'name' => 'account_name',
            'id_name' => 'account_id',
            'vname' => 'LBL_ACCOUNT',
            'join_name'=>'accounts',
            'type' => 'relate',
            'table' => 'accounts',
            'isnull' => 'true',
            'module' => 'Accounts',
            'dbType' => 'varchar',
            'required' => true,
         ),
        
         'account_id' => array(
            'name' => 'account_id',
            'vname' => 'LBL_ACCOUNT_ID',
            'type' => 'id',
            'reportable'=> false,
            'massupdate' => false,
        ),

        'building_type' => array(
            'name' => 'building_type',
            'vname' => 'LBL_BUILDING_TYPE',
            'type' => 'varchar',
            'comments' => 'Unit/Flat/Suite/Appartment etc..',
        ),
        
        'number' => array(
            'name' => 'number',
            'vname' => 'LBL_NUMBER',
            'type' => 'varchar',
        ),
        
        
        'address1' => array(
            'name' => 'address1',
            'vname' => 'LBL_ADDRESS1',
            'type' => 'text',
        ),


        'address2' => array(
            'name' => 'address2',
            'vname' => 'LBL_ADDRESS2',
            'type' => 'text',
        ),

        'city' => array(
            'name' => 'city',
            'vname' => 'LBL_CITY',
            'type' => 'varchar',
        ),
        /*make as a drop down???*/
        'state' => array(
            'name' => 'state',
            'vname' => 'LBL_STATE',
            'type' => 'varchar',
        ),

        'postalcode' => array(
            'name' => 'postalcode',
            'vname' => 'LBL_POSTALCODE',
            'type' => 'varchar',
        ),

        'country' => array(
            'name' => 'country',
            'vname' => 'LBL_COUNTRY',
            'type' => 'varchar',
        ),

        'isbilling' => array(
            'name' => 'isbilling',
            'vname' => 'LBL_BILLING',
            'type' => 'bool',
            'default' => false

        ),

        'isshipping' => array(
            'name' => 'isshipping',
            'vname' => 'LBL_SHIPPING',
            'type' => 'bool',
            'default' => false
        ),
        
        'defaultinvoiceto' => array(
            'name' => 'defaultinvoiceto',
            'vname' => 'LBL_DEFAULT_INVOICE_TO',
            'type' => 'bool',
            'default' => false

        ),

        'defaultshipto' => array(
            'name' => 'defaultshipto',
            'vname' => 'LBL_DEFAULT_SHIP_TO',
            'type' => 'bool',
            'default' => false
        ),
        
        'phone' => array(
            'name' => 'phone',
            'vname' => 'LBL_PHONE',
            'type' => 'varchar',
        ),

        'alternate_phone' => array(
            'name' => 'alternate_phone',
            'vname' => 'LBL_ALT_PHONE',
            'type' => 'varchar',
        ),

        'fax' => array(
            'name' => 'fax',
            'vname' => 'LBL_FAX',
            'type' => 'varchar',
        ),
        'erp_id' =>
        array(
            'name' => 'erp_id',
            'vname' => 'LBL_ERP_ID',
            'type' => 'varchar',
            'len' => '32',
            'comment' => 'Reference to location information held in external ERP application',
            'importable' => 'true',
        ),


        'SecurityGroups' => array(
            'name' => 'SecurityGroups',
            'type' => 'link',
            'relationship' => 'securitygroups_'.$module_table,
            'module' => 'SecurityGroups',
            'bean_name' => 'SecurityGroup',
            'source' => 'non-db',
            'vname' => 'LBL_SECURITYGROUPS',
        ),
       

    ),
    'relationships' => array(      

        'securitygroups_'.$module_table => array(
            'lhs_module' => 'SecurityGroups', 'lhs_table' => 'securitygroups', 'lhs_key' => 'id',
            'rhs_module' => $module_name, 'rhs_table' => $module_table, 'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'securitygroups_records', 'join_key_lhs' => 'securitygroup_id', 'join_key_rhs' => 'record_id',
            'relationship_role_column' => 'module', 'relationship_role_column_value' => $module_name),

    ),
    'optimistic_lock' => true,
);
require_once('include/SugarObjects/VardefManager.php');
VardefManager::createVardef($module_name,$object_name, array('basic', 'assignable'));

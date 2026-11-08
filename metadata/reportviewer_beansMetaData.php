<?php
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * (C) 2011 Enterprise Intelligence Systems Pty Ltd
 * No part of this file should be used without the express permission
 * of the authors.
 *
 * Contact: info@eintel.com.au for more information.
 *
 *
 ********************************************************************************/


/**
 * Relationship table linking reports with 1 or more SugarBeans
 */
$dictionary['reportviewer_beans'] = array(
    'table' => 'reportviewer_beans',
    'fields' => array(
        array(
            'name' => 'id',
            'type' => 'varchar',
            'len' => '36'
        ),
        array(
            'name' => 'report_id',
            'type' => 'varchar',
            'len' => '36',
            'comment' => 'FK to reports table',
        ),
        array(
            'name' => 'bean_id',
            'type' => 'varchar',
            'len' => '36',
            'comment' => 'FK to various beans\'s tables',
        ),
        array(
            'name' => 'bean_module',
            'type' => 'varchar',
            'len' => '36',
            'comment' => 'bean\'s Module',
        ),
        array(
            'name' => 'filename',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'name of file that will be generated',
        ),
        array(
            'name' => 'date_modified',
            'type' => 'datetime'
        ),
        array(
            'name' => 'deleted',
            'type' => 'bool',
            'len' => '1',
            'default' => '0',
            'required' => true
        )
    ),
    'indices' => array(
        array(
            'name' => 'reports_beanspk',
            'type' => 'primary',
            'fields' => array('id')
        ),
        array(
            'name' => 'idx_reports_beans_bean_id',
            'type' => 'index',
            'fields' => array('bean_id')
        ),
        array(
            'name' => 'idx_reports_beans_report_bean',
            'type' => 'alternate_key',
            'fields' => array('report_id', 'bean_id', 'deleted')
        ),
    ),
    'relationships' => array(
        'reports_email_templates_rel' => array(
            'lhs_module' => 'ReportViewer',
            'lhs_table' => 'reportviewer',
            'lhs_key' => 'id',
            'rhs_module' => 'EmailTemplates',
            'rhs_table' => 'email_templates',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'reportviewer_beans',
            'join_key_lhs' => 'report_id',
            'join_key_rhs' => 'bean_id',
            'relationship_role_column' => 'bean_module',
            'relationship_role_column_value' => 'EmailTemplates',
        ),

    )
);


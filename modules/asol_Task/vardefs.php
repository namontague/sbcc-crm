<?php
/*********************************************************************************
 * SugarCRM is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2011 SugarCRM Inc.
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

$dictionary['asol_Task'] = array(
  'table' => 'asol_task',
  'audited' => true,
  'fields' => array(
    'delay_type' =>array(
      'required' => false,
      'name' => 'delay_type',
      'vname' => 'LBL_DELAY_TYPE',
      'type' => 'enum',
      'massupdate' => 0,
      'default' => 'no_delay',
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => false,
      'reportable' => true,
      'len' => 100,
      'size' => '20',
      'options' => 'wfm_task_delay_type_list',
      'studio' => 'visible',
      'dependency' => false,
    ),
    'delay' => array(
      'required' => false,
      'name' => 'delay',
      'vname' => 'LBL_DELAY',
      'type' => 'varchar',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => false,
      'reportable' => true,
      'len' => '255',
      'size' => '20',
    ),
    /////////////////////////////////////////////////////////////
    'task_type' =>array(
      'required' => false,
      'name' => 'task_type',
      'vname' => 'LBL_TASK_TYPE',
      'type' => 'enum',
      'massupdate' => 0,
      'default' => 'send_email',
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => false,
      'reportable' => true,
      'len' => 100,
      'size' => '20',
      'options' => 'wfm_task_type_list',
      'studio' => 'visible',
      'dependency' => false,
    ),
    /////////////////////////////////////////////////////////////
    'order_task' =>array(
      'required' => false,
      'name' => 'order_task',
      'vname' => 'LBL_ORDER_TASK',
      'type' => 'int',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => false,
      'reportable' => true,
      'len' => '2',
      'size' => '20',
      'default' => '0',
    ),

    'task_implementation' => array(
      'required' => false,
      'name' => 'task_implementation',
      'vname' => 'LBL_TASK_IMPLEMENTATION',
      'type' => 'text',
    ),

    'asol_activity_id' =>array (
       'name' => 'asol_activity_id',
       'vname' => 'LBL_ASOL_ACTIVITY',
       'type' => 'id',
       'reportable'=>false,
       'massupdate' => false,
     ),
    'asol_activity_name' => array (
      'name' => 'asol_activity_name',
      'type' => 'relate',
      'source' => 'non-db',
      'vname' => 'LBL_ASOL_ACTIVITY',
      'save' => true,
      'id_name' => 'asol_activity_id',
      'link' => 'asol_activity',
      'rname' => 'name',
    ),
   'asol_activity' => array (
      'name' => 'asol_activity',
      'type' => 'link',
      'relationship' => 'asol_activity_asol_tasks',
      'link_type' => 'one',
      'side'=>'right',
      'source'=>'non-db',
      'vname'=>'LBL_ASOL_ACTIVITY',
    ),

  ),
  'relationships' => array(
  ),
  'optimistic_locking' => true,
);
if (!class_exists('VardefManager')) {
  require_once('include/SugarObjects/VardefManager.php');
}
VardefManager::createVardef('asol_Task', 'asol_Task', array('basic', 'assignable'));

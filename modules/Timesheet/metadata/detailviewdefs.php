<?php
/*********************************************************************************
 * SugarCRM is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004 - 2007 SugarCRM Inc.
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
 * *******************************************************************************/

$viewdefs['Timesheet']['DetailView'] = array(
'templateMeta' => array(
  'form' => array('buttons' => array('EDIT', 'DUPLICATE', 'DELETE',),),
  'maxColumns' => '2',
  'widths' => array(
    array('label' => '10', 'field' => '30'),
    array('label' => '10', 'field' => '30')
  ),
),

'panels' => array (
  array(
        array('name' => 'assigned_user_name', 'label' => 'LBL_EMPLOYEE'),
      ),

  array(
        array('name' => 'parent_name'),
      ),

  array(
        array('name' => 'actual'),
      ),
  array(
        array('name' => 'billable'),
      ),
  array(
        array('name' => 'date_booked'),
      ),

  array(
        array('name' => 'status'),
      ),

  array(
        array('name' => 'description'),
        array (
          'name' => 'modified_by_name',
          'group'=>'modified_by_name',
          'customCode' => '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}&nbsp;',
          'label' => 'LBL_DATE_MODIFIED',
    	  ),
      ),
  
  array(
        array('name' => 'text_for_bill'),
        array (
          'name' => 'created_by_name',
          'group'=>'created_by_name',
          'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}&nbsp;',
          'label' => 'LBL_DATE_ENTERED',
        ),
      ),
      array(
        'dependsOn' => 'Invoice',
          array(
              'name' => 'invoice_name',
              'label' => 'LBL_INVOICE_NAME',
          )
      )
  ),
);
?>

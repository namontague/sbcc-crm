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

$module_name = 'ConfigLists';
$viewdefs [$module_name] = 
array (
  'DetailView' => 
  array (
    'templateMeta' => 
    array (
      'form' =>
      array (
        'closeFormBeforeCustomButtons' => false,
        'buttons'=>array(
              0 => 'EDIT',
              1 => 'DELETE',
              array('customCode' =>
                     ' <form action="index.php" method="post" name="AddDefaultValues" id="form_2"> ' .
                     ' <input type="hidden" value="ConfigLists" name="module"/> ' .
                     ' <input type="hidden" value="{$fields.id.value}" name="record"/> ' .
                     ' <input type="hidden" value="AddDefault" name="action"/>' .
                     ' <input type="hidden" name="return_module"/> ' .
                     ' <input type="hidden" name="return_action"/> ' .
                     ' <input type="hidden" name="return_id"/>' .
                     ' <input title="{$APP.LBL_ADD_DEFAULT_VALUES_TITLE}" ' .
                     ' accessKey="{$APP.LBL_ADD_DEFAULT_VALUES_KEY}" ' .
                     ' class="button" ' .
                     ' onclick="this.form.action.value=\'AddDefault\';this.form.return_module.value=\'ConfigLists\';this.form.return_action.value=\'DetailView\';this.form.return_id.value=\'{$fields.id.value}\';" ' .
                     ' name="add_button" ' .
                     ' value="{$APP.LBL_ADD_DEFAULT_VALUES_LABEL}" ' .
                     ' type="submit"/></form>'),
                ),
             ),
      'maxColumns' => '2',
      'widths' => 
      array (
        array (
          'label' => '20',
          'field' => '30',
        ),
        array (
          'label' => '20',
          'field' => '50',
        ),
      ),
    ),
    'panels' => 
    array (
      'default' => 
      array (
        array (
          array (
            'name' => 'name',
            'label' => 'LBL_NAME',
          ),
          array (
            'name' => 'reference',
            'label' => 'LBL_REFERENCE',
          ),
        ),
          array(
            array('name'=>'date_entered','label'=>'LBL_DATE_CREATED', 'customCode'=>'{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}'),
            array('name'=>'date_modified','label'=>'LBL_DATE_MODIFIED', 'customCode'=>'{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}'),
        ),

        array (
          array (
            'name' => 'description',
            'label' => 'LBL_DESCRIPTION',
          ),
      ),
      ),
    ),
  ),
);
?>

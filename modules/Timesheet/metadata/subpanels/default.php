<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
 * Subpanel Layout definition for Accounts
 *
 * The contents of this file are subject to the SugarCRM Public License Version
 * 1.1.3 ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied.  See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *    (i) the "Powered by SugarCRM" logo and
 *    (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * The Original Code is: SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) 2004-2006 SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 */


$subpanel_layout = array(
	'top_buttons' => array(
		array('widget_class' => 'SubPanelTopButtonQuickCreate'),
	),
	'where' => '',
	
	

	'list_fields' => array(
		'assigned_user_name' => array (
			'name' => 'assigned_user_name',
			'vname' => 'LBL_LIST_EMPLOYEE',
      'widget_class' => 'SubPanelDetailViewLink',
		),
    'actual'=>array(
			'vname' => 'LBL_LIST_ACTUAL',
			'width' => '15%',
		),
    'date_booked'=>array(
			'vname' => 'LBL_LIST_DATE_BOOKED',
			'width' => '15%',
            'sort_by' => 'date_booked',
            'sort_order' => 'desc',
		),
    'status'=>array(
			'vname' => 'LBL_LIST_STATUS',
			'width' => '15%',
		),
    'billable'=>array(
			'vname' => 'LBL_LIST_BILLABLE',
			'width' => '15%',
		),
		'edit_button' => array(
			'widget_class' => 'SubPanelEditButton',
			'width' => '4%',
		),
		'delete_button' => array(
			'widget_class' => 'SubPanelDeleteButton',
			'width' => '4%',
		),
	),
);

?>

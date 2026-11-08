<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
$viewdefs['Timesheet']['SideQuickCreate'] = array(
  'templateMeta' => array(
    'form'=>array(
      'buttons'=>array('SAVE'),
      'button_location'=>'bottom',
      'headerTpl'=>'include/EditView/header.tpl',
      'footerTpl'=>'include/EditView/footer.tpl',
     ),
     'maxColumns' => '2',
     'panelClass'=>'none',
     'labelsOnTop'=>false,
     'widths' => array(
        array('label' => '10', 'field' => '30'),
     ),
  ),
  'panels' =>array (
    'default' => array (
      array(
        array('name' => 'assigned_user_name', 'label' => 'LBL_EMPLOYEE', 'displayParams' => array('required' => true, 'size'=>11, 'selectOnly' => true)),
      ),
      array(
        array('name' => 'parent_name', 'displayParams' => array('required' => true, 'size' => 20)),
      ),
      array(
        array('name' => 'actual', 'displayParams' => array('required' => true, 'size' => 10)),
      ),
      array(
        array('name' => 'billable', 'displayParams' => array('size' => 10)),
      ),
      array(
        array('name' => 'date_booked', 'displayParams' => array('required' => true)),
      ),
      array(
        array('name' => 'status', 'displayParams' => array('required' => true)),
      ),
      array(
        array('name' => 'description', 
        'displayParams' => 
        array (
          'rows' => '4',
          'cols' => '20',
        ),
        'nl2br' => true,),
      ),
      array(
        array('name' => 'text_for_bill',
        'displayParams' => 
        array (
          'rows' => '4',
          'cols' => '20',
        ),
        'nl2br' => true,),
      ),
  	),
	),
);
?>

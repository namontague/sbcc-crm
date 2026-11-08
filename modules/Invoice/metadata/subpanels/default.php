<?php
$module_name='Invoice';
$subpanel_layout = array (
  'top_buttons' => 
  array (
    0 => 
    array (
      'widget_class' => 'SubPanelTopCreateButton',
    ),
    1 => 
    array (
      'widget_class' => 'SubPanelTopSelectButton',
      'popup_module' => 'Invoice',
    ),
  ),
  'where' => '',
  'list_fields' => 
  array (
    'invoice_number' => 
    array (
      'width' => '10%',
      'vname' => 'LBL_INVOICE_NUMBER',
      'default' => true,
    ),
    'edit_button' => 
    array (
      'widget_class' => 'SubPanelEditButton',
      'module' => 'Invoice',
      'width' => '4%',
      'default' => true,
    ),
    'remove_button' => 
    array (
      'widget_class' => 'SubPanelRemoveButton',
      'module' => 'Invoice',
      'width' => '5%',
      'default' => true,
    ),
  ),
);
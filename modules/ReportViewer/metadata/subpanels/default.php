<?php



$module_name='ReportViewer';
$subpanel_layout = array(

	'where' => '',

	'fill_in_additional_fields'=>true,
	'list_fields' => array(
        'name' =>
            array (
                  'vname' => 'LBL_NAME',
                  'widget_class' => 'SubPanelDetailViewLink',
                  'width' => '25%',
                  'default' => true,
            ),
        'description' =>
            array (
                  'vname' => 'LBL_DESCRIPTION',
                  'widget_class' => 'SubPanelDetailViewLink',
                  'width' => '45%',
                  'default' => false,
                  'link' => false,
            ),
       'execute_button' =>
            array(
                'vname' => 'LBL_EXECUTE_BUTTON',
                'widget_class' => 'SubPanelExecute',
                'width' => '30%',
		),

    ),
    'javascript' => '<script type="text/javascript" src="modules/ReportViewer/javascript/detailview.js"></script>'
);
<?php
$module_name = 'ReportViewer';
$viewdefs [$module_name] =
    array(
      'DetailView' =>
      array(
        'templateMeta' =>
        array(
          'form' =>
          array(
            'buttons' =>
            array(
              0 => 'EDIT',
              1 => 'DUPLICATE',
              2 => 'DELETE',
            ),
          ),
          'maxColumns' => '2',
          'widths' =>
          array(
            array(
              'label' => '10',
              'field' => '30',
            ),
            array(
              'label' => '10',
              'field' => '30',
            ),
          ),
          'javascript' => '<script type="text/javascript" src="modules/ReportViewer/javascript/detailview.js"></script>',

        ),
        'panels' =>
        array(
          'default' =>
          array(
            array(
              'name',
              'description',
            ),
            array(
              'comment',
              'related_module',
            ),
            array(
              'format',
            ),
            array(
              'report_location',
            ),
            array(
              array(
                'displayIf' => 'empty($fields.related_module.value)',
                'customCode' => '<input class="reportid" type="button" value="Execute Report" name="report_run"  onClick=onExecute("{$fields.id.value}"); />',
              )
            ),

          ),
        ),
      ),
    );
?>

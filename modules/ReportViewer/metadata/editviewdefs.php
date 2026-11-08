<?php
$module_name = 'ReportViewer';
$viewdefs [$module_name] =
    array(
      'EditView' =>
      array(
        'templateMeta' =>
        array(
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
          'javascript' => '
       <script type="text/javascript" src="' . getJSPath('modules/ReportViewer/javascript/editview.js') . '"></script>

       <script type="text/javascript" src="include/javascript/yui/build/yahoo-dom-event.js"></script>
       <script type="text/javascript" >
            YAHOO.util.Event.addListener("report_button","click",onReportSelectGetListofReports);
       </script>

      <script>
            window.onload=function()
                {literal} { {/literal}
                    document.getElementById(\'report_location\').readOnly = \'true\';
                {literal} } {/literal}
      </script>',


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
            ),
            array(
              'format',
              'status',
            ),
            array(
              'related_module',

            ),
            array(
              'report_location',
              array(
                'label' => 'Select Report',
                'customCode' => '<input id="report_button" type="button" value="Select" class="button" title="Select" tabindex="0" name="report_button"/>',
              ),

            ),
          ),
        ),
      ),
    );
?>

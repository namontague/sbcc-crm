<?php
if (!defined('sugarEntry') || !sugarEntry) {
  die('Not A Valid Entry Point');
}

require_once('include/generic/SugarWidgets/SugarWidgetField.php');
class SugarWidgetSubPanelExportAOR_Report extends SugarWidgetField {
    protected static $defs = array();
    protected static $edit_icon_html;

	function displayHeaderCell($layout_def)
	{
		return '';
	}

	function displayList($layout_def)	{

            global $app_strings;
            global $subpanel_item_count;
            $unique_id = $layout_def['subpanel_id']."_export_".$subpanel_item_count; //bug 51512

            $href = 'index.php?module=' . $layout_def['module']
                . '&action=' . 'RunReport'
                . '&record=' . $_REQUEST['record']    
                . '&report_action=' . 'Export'    
                . '&report_id=' . $layout_def['fields']['ID']
                . '&related_module=' . $_REQUEST['module']   
                . '&return_module=' . $_REQUEST['module']
                . '&return_action=' . 'DetailView'
                . '&return_id=' . $_REQUEST['record'];
  
            if($layout_def['EditView']){
                return '<a href="' . $href . '"'
                    . "id=\"$unique_id\""
                    . 'class="listViewTdToolsS1">Export</a>&nbsp;';
            }else{
                return '';
            }
	
        
        
    }  

}

?>

<?php

require_once('include/MVC/View/views/view.list.php');



class asol_ProcessViewList extends ViewList {

	function asol_ProcessViewList(){
		parent::ViewList();
	}
	function listViewProcess(){
		global $mod_strings;
		
		$this->processSearchForm();
		$this->lv->searchColumns = $this->searchForm->searchColumns;

		if(!$this->headers)
		return;
		if(empty($_REQUEST['search_form_only']) || $_REQUEST['search_form_only'] == false){
			$this->lv->ss->assign("SEARCH",true);
			$this->lv->setup($this->seed, 'include/ListView/ListViewGeneric.tpl', $this->where, $this->params);
			$savedSearchName = empty($_REQUEST['saved_search_select_name']) ? '' : (' - ' . $_REQUEST['saved_search_select_name']);
			echo $this->lv->display();
			echo '<input type="button" value="'.$mod_strings['LBL_ASOL_EXPORT'].'" onClick="return sListView.send_form(true, \'asol_Process\', \'index.php?entryPoint=asol_export\',\''.$mod_strings['LBL_ASOL_EXPORT_PLEASE'].'\')" />';
		}
	}


}
//return sListView.send_form(true, 'asol_Process', 'index.php?entryPoint=export','Please select at least 1 record to proceed.')
?>

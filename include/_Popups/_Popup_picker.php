<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2012 SugarCRM Inc.
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
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 ********************************************************************************/







class _Popup_Picker
{
	var $_popupMeta;
	var $_create = false;
	var $_hide_clear_button = false;

	/**
	 * Creates a new _Popup_Picker object. Controls displaying of single select and multi select popups
	 * 
	 */
	function _Popup_Picker()
	{
		global $currentModule, $popupMeta;

		// cn: bug 12269 - directory navigation attack - detect and stop.
		if(isset($_REQUEST['metadata']) && strpos($_REQUEST['metadata'], "..") !== false)
			die("Directory navigation attack denied.");
		if(empty($popupMeta)){
			if(!empty($_REQUEST['metadata']) && $_REQUEST['metadata'] != 'undefined') // if custom metadata is requested
				require_once('modules/' . $currentModule . '/metadata/' . $_REQUEST['metadata'] . '.php'); 
			else 
				require_once('modules/' . $currentModule . '/metadata/popupdefs.php');
		}
		$this->_popupMeta = $popupMeta;
		
		require_once('modules/' . $currentModule . '/' . $this->_popupMeta['moduleMain'] . '.php');
		if(isset($this->_popupMeta['create']['formBase']) && isset($_REQUEST['create']) && $_REQUEST['create'] == 'true') { // include create form
			require_once('modules/' . $currentModule . '/' . $this->_popupMeta['create']['formBase']);
			$this->_create = true;
		}
	}
	
	/*
	 * 
	 */
	function _get_where_clause()
	{
		$where = '';
		$whereClauses = array();
		if(isset($_REQUEST['query']))
		{
			foreach(array_keys($this->_popupMeta['whereClauses']) as $key) {
				append_where_clause($whereClauses, $key, $this->_popupMeta['whereClauses'][$key]);
			}

			$where = generate_where_statement($whereClauses);
		}
		if(!empty($this->_popupMeta['whereStatement'])){
            if(!empty($where))$where .= ' AND ';
            $where .= $this->_popupMeta['whereStatement'];
		}

		return $where;
	}
	
	/**
	 *
	 */
	function process_page()
	{
		global $theme;
		global $mod_strings;
		global $app_strings;
		global $currentModule;
		global $app_list_strings, $sugar_version, $sugar_config;
		
		$output_html = "<script type=\"text/javascript\" src=\"" . getJSPath('include/javascript/sugar_3.js'). "\"></script>";
		$where = '';
		
		if(empty($_REQUEST[$currentModule . '_' . strtoupper($this->_popupMeta['moduleMain']) . '_offset'])) {
			$_POST[$currentModule . '_' . strtoupper($this->_popupMeta['moduleMain']) . '_offset'] = '';
		}
		if(empty($_REQUEST['saved_associated_data'])) {
			$_POST['saved_associated_data'] = '';
		}
		$where = $this->_get_where_clause();
		
		// search request inputs
		$searchInputs = array();
		foreach($this->_popupMeta['searchInputs'] as $input) 
			$searchInputs[$input] = empty($_REQUEST[$input]) ? '' : $_REQUEST[$input];
		 
		$request_data = empty($_REQUEST['request_data']) ? '' : $_REQUEST['request_data'];
		$hide_clear_button = empty($_REQUEST['hide_clear_button']) && empty($this->_hide_clear_button) ? false : true;
		$button = '<script>eval("var request_data = " + window.document.forms[\'popup_query_form\'].request_data.value);</script>';

		if(isset($_REQUEST['mass'])) {
			foreach(array_unique($_REQUEST['mass']) as $record) {
				$button .= "<input style='display: none' checked type='checkbox' name='mass[]' value='$record'>\n";
			}		
		}
	
		//START:FOR MULTI-SELECT
		$multi_select = false;
		if (!empty($_REQUEST['mode']) && strtoupper($_REQUEST['mode']) == 'MULTISELECT') {
			$multi_select = true;
			$button .= "<input type='hidden' name='mode' value='MultiSelect'>";
			$button .= "<input type='button' name='button' class='button' onclick=\"send_back_selected('$currentModule',document.MassUpdate,'mass[]','" .$app_strings['ERR_NOTHING_SELECTED']."', request_data.field_to_name_array);\" title='"
				.$app_strings['LBL_SELECT_BUTTON_TITLE']."' value='  "
				.$app_strings['LBL_SELECT_BUTTON_LABEL']."  ' />\n";
		}

		//END:FOR MULTI-SELECT
		if(!$hide_clear_button)
		{
			$button .= "<input type='button' name='button' class='button' onclick=\"send_back('','');\" title='"
				.$app_strings['LBL_CLEAR_BUTTON_TITLE']."' value='  "
				.$app_strings['LBL_CLEAR_BUTTON_LABEL']."  ' />\n";
		}
		$button .= "<input type='submit' name='button' class='button' onclick=\"window.close();\" title='"
			.$app_strings['LBL_CANCEL_BUTTON_TITLE']."' value='  "
			.$app_strings['LBL_CANCEL_BUTTON_LABEL']."  ' />\n";

		if(file_exists('include/_Popups/_Popup_picker.html')) { 
			$form = new XTemplate('include/_Popups/_Popup_picker.php');
		}
		else {
			$form = new XTemplate('modules/' . $currentModule . '/Popup_picker.html');
		}
		
		$form->assign('MOD', $mod_strings);
		$form->assign('APP', $app_strings);
		$form->assign('THEME', $theme);
		$form->assign('MODULE_NAME', $currentModule);
		$form->assign('request_data', $request_data);
		
	
		if(isset($this->_popupMeta['className'])) $seed_bean = new $this->_popupMeta['className']();
		else $seed_bean = new $this->_popupMeta['moduleMain']();

		// assign search inputs to xtemplates
		foreach(array_keys($searchInputs) as $key) {
			if(!empty($_REQUEST[$key]) && (isset($seed_bean->field_name_map[$key]['type']) && $seed_bean->field_name_map[$key]['type'] == 'bool')) {
				$form->assign(strtoupper($key), ' checked ');
			} else {
				$form->assign(strtoupper($key), $searchInputs[$key]);
			}
		}
		
		if($this->_create) $form->assign('CREATE', 'true');
		else $form->assign('CREATE', 'false');
		
		// fill any doms
		if(isset($this->_popupMeta['selectDoms']))
			foreach($this->_popupMeta['selectDoms'] as $key => $value) {
				$form->assign($key, get_select_options_with_id($app_list_strings[$value['dom']], $value['searchInput']));
			}

		$form->assign('MULTI_SELECT', !empty($_REQUEST['mode']) ? strtoupper($_REQUEST['mode']) : '');
		
		ob_start();
		insert_popup_header($theme);
		$output_html .= ob_get_contents();
		ob_end_clean();
		
		$output_html .= get_form_header($mod_strings['LBL_SEARCH_FORM_TITLE'], '', false);
		
		$form->parse('main.SearchHeader');
		$output_html .= $form->text('main.SearchHeader');
		
		// Reset the sections that are already in the page so that they do not print again later.
		$form->reset('main.SearchHeader');

                
               
                
                
		$ListView = new ListView();
            
                $this->subpanel_query=$query;
                
		$ListView->show_select_menu = false;
		$ListView->show_delete_button = false;
		$ListView->show_export_button = false;
		$ListView->process_for_popups = true;
		$ListView->setXTemplate($form);

                
                
		$ListView->multi_select_popup = $multi_select; 
		$ListView->xTemplate->assign('TAG_TYPE', 'A');
		if(isset($this->_popupMeta['listTitle'])) {
                    $ListView->setHeaderTitle($this->_popupMeta['listTitle']);
		}
		else {  
                    $ListView->setHeaderTitle($mod_strings['LBL_LIST_FORM_TITLE']);
		}
                
                 include_once('include/SubPanel/SubPanelDefinitions.php');
                //$tab = subpanel name
                
                $sugarbean = BeanFactory::getBean($_REQUEST['parent_module'], $_REQUEST['parent_bean']);
                $panelsdef= new SubPanelDefinitions($sugarbean);
                $subpanelDef=$panelsdef->load_subpanel($_REQUEST['subpanel_name']);
                $subpanel_def=$subpanelDef;
                
                $ListView->source_module = $_REQUEST['parent_module'];
                $ListView->subpanel_module = $subpanel_def->name;
                if(!isset($ListView->xTemplate))
                    $this->createXTemplate();

                $html_var = $ListView->subpanel_module . "_CELL";

                $list_data = $ListView->processUnionBeans($sugarbean,$subpanel_def, $html_var);

                if($subpanel_def->isCollection()) {
                    $thepanel=$subpanel_def->get_header_panel_def();
                } else {
                    $thepanel=$subpanel_def;
                }
                
                $data = $sugarbean->process_list_query($list_data['query'], 0);
		$data2 = $data['list'];
                
                
                $ListView->setHeaderText($button);
                
		$ListView->setQuery($where, '', $this->_popupMeta['orderBy'], $this->_popupMeta['varName']);
		$ListView->setModStrings($mod_strings);

                
                $seed_bean = new Contact();
                $result = $seed_bean->db->query($list_data['query'],true,"Error retrieving $seed_bean->object_name list: ");
                $list = Array();
                    while(($row = $seed_bean->db->fetchByAssoc($result)) != null)
                    {
                                    $seed_bean = new Contact();
                                    foreach($seed_bean->field_defs as $field=>$value)
                                    {
                                            if (isset($row[$field])) 
                                            {
                                                    $seed_bean->$field = $row[$field];
                                            }
                                            else if (isset($row[$seed_bean->table_name .'.'.$field])) 
                                            {
                                                    $seed_bean->$field = $row[$seed_bean->table_name .'.'.$field];
                                            }
                                            else
                                            {
                                                    $seed_bean->$field = "";
                                            }	
                                    }
                                    $seed_bean->fill_in_additional_list_fields();

                                    $list[] = $seed_bean;
                    }
                ob_start();		
                $ListView->processListViewTwo($list, 'main', 'CONTACT');
                
		
                //$ListView->processListView($seed_bean, 'main', $this->_popupMeta['varName']);
                //$data2;
		$output_html .= ob_get_contents();
		ob_end_clean();
		$json = getJSONobj(); 
		
		// decode then encode to escape "'s
		$output_html .= "</form>
		<script type=\"text/javascript\">
		function save_checks(offset) {
			checked_ids = Array();
			for (i = 0; i < document.MassUpdate.elements.length; i++){
				if(document.MassUpdate.elements[i].name == 'mass[]' && document.MassUpdate.elements[i].checked) {
					temp_string = '';
					temp_string += '\"' + document.MassUpdate.elements[i].value + '\": {';
					for(the_key in associated_javascript_data[document.MassUpdate.elements[i].value]) {
						temp_string += '\"' + the_key + '\":\"' + associated_javascript_data[document.MassUpdate.elements[i].value][the_key] + '\",'; 
					}
					temp_string = temp_string.substring(0,temp_string.length - 1);
					temp_string += '}';
					checked_ids.push(temp_string);
				}				 
			}
			document.MassUpdate.saved_associated_data.value = escape('{' + checked_ids.join(',') + '}');

			document.MassUpdate.action.value = \"_Popup\";
			document.MassUpdate.$currentModule" . '_' . strtoupper($this->_popupMeta['moduleMain']) . '_offset.value = offset;
			document.MassUpdate.submit();
		}
		// reassigned the saved data from the saved checks
		if(typeof(document.MassUpdate) != \'undefined\' && document.MassUpdate.saved_associated_data.value != \'\') {
			temp_array = ' . (!empty($_REQUEST['saved_associated_data']) ? $json->encode($json->decode(urldecode($_REQUEST['saved_associated_data']))) : '\'\'') . ';
			for(the_key in temp_array) {
				associated_javascript_data[the_key] = temp_array[the_key];
			}
		}

		// save checks across pages for multiselects 
		if(typeof(document.MassUpdate) != "undefined") {		
			checked_items = Array();
			inputs_array = document.MassUpdate.elements;
	
			for(wp = 0 ; wp < inputs_array.length; wp++) {
				if(inputs_array[wp].name == "mass[]" && inputs_array[wp].style.display == "none") {
					checked_items.push(inputs_array[wp].value);
				} 
			}
			for(i in checked_items) {
				for(wp = 0 ; wp < inputs_array.length; wp++) {
					if(inputs_array[wp].name == "mass[]" && inputs_array[wp].value == checked_items[i]) {
						inputs_array[wp].checked = true;
					}
				}
			}
		}
		</script>'; 
		$output_html .= insert_popup_footer();
        return $output_html;
	}
} // end of class Popup_Picker
?>
		
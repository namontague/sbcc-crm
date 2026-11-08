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

require_once('include/ListView/ListViewSmarty.php');
require_once('include/Popups/PopupSmarty.php');

require_once('include/TemplateHandler/TemplateHandler.php');
require_once('include/SearchForm/SearchForm2.php');
define("NUM_COLS", 2);
class _PopupSmarty extends PopupSmarty {

	var $contextMenus = false;
	var $export = false;
	var $mailmerge = false;
	var $mergeduplicates = false;
	var $quickViewLinks = false;
	var $multiSelect = false;
	var $headerTpl;
    var $footerTpl;
    var $th;
    var $tpl;
    var $view;
    var $field_defs;
    var $formData;
    var $_popupMeta;
    var $_create = false;
    var $searchdefs = array();
    var $listviewdefs = array();
    var $searchFields = array();
    var $customFieldDefs;
    var $filter_fields = array();
    //rrs
    var $searchForm;
    var $module;
    var $massUpdateData = '';

	function _PopupSmarty($seed, $module){
		parent::PopupSmarty($seed, $module);
        $this->headerTpl = 'include/_Popups/tpls/header.tpl';
        $this->footerTpl = 'include/_Popups/tpls/footer.tpl';

	}
	/**
     * Processes the request. Calls ListViewData process. Also assigns all lang strings, export links,
     * This is called from ListViewDisplay
     *
     * @param file file Template file to use
     * @param data array from ListViewData
     * @param html_var string the corresponding html var in xtpl per row
     *
     */
	function process($file, $data, $htmlVar) {

		global $odd_bg, $even_bg, $hilite_bg, $click_bg, $app_strings;
		parent::process($file, $data, $htmlVar);

		$this->tpl = $file;
		$this->data = $data;

        $totalWidth = 0;
        foreach($this->displayColumns as $name => $params) {
            $totalWidth += $params['width'];
        }
        $adjustment = $totalWidth / 100;

        $contextMenuObjectsTypes = array();
        foreach($this->displayColumns as $name => $params) {
            $this->displayColumns[$name]['width'] = round($this->displayColumns[$name]['width'] / $adjustment, 2);
            // figure out which contextMenu objectsTypes are required
            if(!empty($params['contextMenu']['objectType']))
                $contextMenuObjectsTypes[$params['contextMenu']['objectType']] = true;
        }
		$this->th->ss->assign('displayColumns', $this->displayColumns);


		$this->th->ss->assign('bgHilite', $hilite_bg);
		$this->th->ss->assign('colCount', count($this->displayColumns) + 1);
		$this->th->ss->assign('htmlVar', strtoupper($htmlVar));
		$this->th->ss->assign('moduleString', $this->moduleString);
        $this->th->ss->assign('editLinkString', $GLOBALS['app_strings']['LBL_EDIT_BUTTON']);
        $this->th->ss->assign('viewLinkString', $GLOBALS['app_strings']['LBL_VIEW_BUTTON']);

        //rrs
        $this->searchForm->parsedView = 'popup_query_form';
        $this->searchForm->displayType = 'popupView';
		$this->th->ss->assign('searchForm', $this->searchForm->display(false));
        //rrs
                

                //subpanel relation details
                $this->th->ss->assign('parent_module', $_REQUEST['parent_module']);
                $this->th->ss->assign('parent_bean', $_REQUEST['parent_bean']);
                $this->th->ss->assign('subpanel_name', $_REQUEST['subpanel_name']);


		if($this->export) $this->th->ss->assign('exportLink', $this->buildExportLink());
		$this->th->ss->assign('quickViewLinks', $this->quickViewLinks);
		if($this->mailMerge) $this->th->ss->assign('mergeLink', $this->buildMergeLink()); // still check for mailmerge access
		if($this->mergeduplicates) $this->th->ss->assign('mergedupLink', $this->buildMergeDuplicatesLink());


		if (!empty($_REQUEST['mode']) && strtoupper($_REQUEST['mode']) == 'MULTISELECT') {
			$this->multiSelect = true;
		}
		// handle save checks and stuff
		if($this->multiSelect) {
			$this->th->ss->assign('selectedObjectsSpan', $this->buildSelectedObjectsSpan());
			$this->th->ss->assign('multiSelectData', $this->getMultiSelectData());
			$this->th->ss->assign('MODE', "<input type='hidden' name='mode' value='MultiSelect'>");
            $pageTotal = $this->data['pageData']['offsets']['next'] - $this->data['pageData']['offsets']['current'];
            if($this->data['pageData']['offsets']['next'] < 0){ // If we are on the last page, 'next' is -1, which means we have to have a custom calculation
                $pageTotal = $this->data['pageData']['offsets']['total'] - $this->data['pageData']['offsets']['current'];
            }
    		$this->th->ss->assign('selectLink', $this->buildSelectLink('select_link', $this->data['pageData']['offsets']['total'], $pageTotal));
		}

		$this->processArrows($data['pageData']['ordering']);
		$this->th->ss->assign('prerow', $this->multiSelect);
		$this->th->ss->assign('rowColor', array('oddListRow', 'evenListRow'));
		$this->th->ss->assign('bgColor', array($odd_bg, $even_bg));
        $this->th->ss->assign('contextMenus', $this->contextMenus);


        if($this->contextMenus && !empty($contextMenuObjectsTypes)) {
            $script = '';
            $cm = new contextMenu();
            foreach($contextMenuObjectsTypes as $type => $value) {
                $cm->loadFromFile($type);
                $script .= $cm->getScript();
                $cm->menuItems = array(); // clear menuItems out
            }
            $this->th->ss->assign('contextMenuScript', $script);
        }

        //rrs
        $this->_build_field_defs();

            // arrow image attributes
        $this->processArrowVars();
	}

	/*
	 * Setup up the smarty template. we added an extra step here to add the order by from the popupdefs.
	 */
	function setup($file) {
		$params = array();
		if(!empty($this->_popupMeta['orderBy'])){
			$params['orderBy'] = $this->_popupMeta['orderBy'];
		}

		if(file_exists('custom/modules/'.$this->module.'/metadata/metafiles.php')){
			require('custom/modules/'.$this->module.'/metadata/metafiles.php');
		}elseif(file_exists('modules/'.$this->module.'/metadata/metafiles.php')){
			require('modules/'.$this->module.'/metadata/metafiles.php');
		}

		if(!empty($metafiles[$this->module]['searchfields'])) {
			require($metafiles[$this->module]['searchfields']);
		} elseif(file_exists('modules/'.$this->module.'/metadata/SearchFields.php')) {
			require('modules/'.$this->module.'/metadata/SearchFields.php');
	    }

        //Eintel - Load custom SearchFields.php if it exists
        if(file_exists("custom/modules/{$this->module}/metadata/SearchFields.php"))
        {
            require "custom/modules/{$this->module}/metadata/SearchFields.php" ;
        }

        $this->searchdefs[$this->module]['templateMeta']['maxColumns'] = 2;
        $this->searchdefs[$this->module]['templateMeta']['widths']['label'] = 10;
        $this->searchdefs[$this->module]['templateMeta']['widths']['field'] = 30;

        $this->searchForm->view = 'PopupSearchForm';
		$this->searchForm->setup($this->searchdefs, $searchFields, 'SearchFormGenericAdvanced.tpl', 'advanced_search', $this->listviewdefs);

		$lv = new ListViewSmarty();
		$displayColumns = array();
		if(!empty($_REQUEST['displayColumns'])) {
		    foreach(explode('|', $_REQUEST['displayColumns']) as $num => $col) {
		        if(!empty($listViewDefs[$this->module][$col]))
		            $displayColumns[$col] = $this->listviewdefs[$this->module][$col];
		    }
		}
		else {
		    foreach($this->listviewdefs[$this->module] as $col => $para) {
		        if(!empty($para['default']) && $para['default'])
		            $displayColumns[$col] = $para;
		    }
		}
		$params['massupdate'] = true;
		if(!empty($_REQUEST['orderBy'])) {
		    $params['orderBy'] = $_REQUEST['orderBy'];
		    $params['overrideOrder'] = true;
		    if(!empty($_REQUEST['sortOrder'])) $params['sortOrder'] = $_REQUEST['sortOrder'];
		}

		$lv->displayColumns = $displayColumns;
        $this->searchForm->lv = $lv;
        $this->searchForm->displaySavedSearch = false;


        $this->searchForm->populateFromRequest('advanced_search');
        $searchWhere = $this->_get_where_clause();
        
        if(empty($searchWhere)){
            $searchWhere = "";
        }else{
            $searchWhere .= " AND ";
        }
        
        $this->searchColumns = $this->searchForm->searchColumns;
        //parent::setup($this->seed, $file, $searchWhere, $params, 0, -1, $this->filter_fields);

        $this->should_process = true;

        if(isset($params['export'])) {
          $this->export = $params['export'];
        }
        if(!empty($params['multiSelectPopup'])) {
		  $this->multi_select_popup = $params['multiSelectPopup'];
        }
		if(!empty($params['massupdate']) && $params['massupdate'] != false) {
			$this->show_mass_update_form = true;
			$this->mass = new MassUpdate();
			$this->mass->setSugarBean($this->seed);
			if(!empty($params['handleMassupdate']) || !isset($params['handleMassupdate'])) {
                $this->mass->handleMassUpdate();
            }
		}

        // create filter fields based off of display columns
        if(empty($this->filter_fields) || $this->mergeDisplayColumns) {
            foreach($this->displayColumns as $columnName => $def) {
               $this->filter_fields[strtolower($columnName)] = true;
               if(!empty($def['related_fields'])) {
                    foreach($def['related_fields'] as $field) {
                        //id column is added by query construction function. This addition creates duplicates
                        //and causes issues in oracle. #10165
                        if ($field != 'id') {
                            $this->filter_fields[$field] = true;
                        }
                    }
                }
                if (!empty($this->seed->field_defs[strtolower($columnName)]['db_concat_fields'])) {
	                foreach($this->seed->field_defs[strtolower($columnName)]['db_concat_fields'] as $index=>$field){
	                    if(!isset($this->filter_fields[strtolower($field)]) || !$this->filter_fields[strtolower($field)])
	                    {
	                        $this->filter_fields[strtolower($field)] = true;
	                    }
	                }
                }
            }
            foreach ($this->searchColumns as $columnName => $def )
            {
                $this->filter_fields[strtolower($columnName)] = true;
            }
        }

        /**
         * Bug #46842 : The relate field field_to_name_array fails to copy over custom fields 
         * By default bean's create_new_list_query function loads fields displayed on the page or used in the search
         * add fields used to populate forms from _viewdefs :: field_to_name_array to retrive from db
         */
        if ( isset($_REQUEST['field_to_name']) && $_REQUEST['field_to_name'] )
        {
            $_REQUEST['field_to_name'] = is_array($_REQUEST['field_to_name']) ? $_REQUEST['field_to_name'] : array($_REQUEST['field_to_name']);
            foreach ( $_REQUEST['field_to_name'] as $add_field )
            {
                $add_field = strtolower($add_field);
                if ( $add_field != 'id' && !isset($this->filter_fields[$add_field]) && isset($this->seed->field_defs[$add_field]) )
                {
                    $this->filter_fields[$add_field] = true;
                }
            }
            
        }
                //need to use the subpanel query to return the same data
                include_once('include/SubPanel/SubPanelDefinitions.php');
                $parent = BeanFactory::getBean($_REQUEST['parent_module'], $_REQUEST['parent_bean']);
                $panelsdef = new SubPanelDefinitions($parent);
                $subpanelDef = $panelsdef->load_subpanel($_REQUEST['subpanel_name']);
                $subpanel_def = $subpanelDef;
                
                
                $ListView = new ListView();
                $ListView->source_module = $_REQUEST['parent_module'];
                $ListView->subpanel_module = $subpanel_def->name;
                $html_var = $ListView->subpanel_module . "_CELL";
                
                if($subpanel_def->isCollection()) {
                    $thepanel = $subpanel_def->get_header_panel_def();
                } else {
                    $thepanel = $subpanel_def;
                }
                
                if (!empty($_REQUEST['query']) || (!empty($GLOBALS['sugar_config']['save_query']) && $GLOBALS['sugar_config']['save_query'] != 'populate_only')) {
                    //use a copy of the getlistviewdata function to allow us to define the query    
                    $data = $this->getListViewData($this->seed, $searchWhere, 0, -1, $this->filter_fields, $params, 'id', true,$parent,$subpanel_def);
		} else {
			$this->should_process = false;
			$data = array(
				'data'=>array(),
			    'pageData'=>array(
			    	'bean'=>array('moduleDir'=>$this->seed->module_dir),
					'ordering'=>'',
					'offsets'=>array('total'=>0,'next'=>0,'current'=>0),
				),
			);
		}

           
		foreach($this->displayColumns as $columnName => $def)
		{
			$seedName =  strtolower($columnName);

			if(empty($this->displayColumns[$columnName]['type'])){
				if(!empty($this->lvd->seed->field_defs[$seedName]['type'])){
					$seedDef = $this->lvd->seed->field_defs[$seedName];
		            $this->displayColumns[$columnName]['type'] = (!empty($seedDef['custom_type']))?$seedDef['custom_type']:$seedDef['type'];
		        }else{
		        	$this->displayColumns[$columnName]['type'] = '';
		        }
			}//fi empty(...)

			if(!empty($this->lvd->seed->field_defs[$seedName]['options'])){
					$this->displayColumns[$columnName]['options'] = $this->lvd->seed->field_defs[$seedName]['options'];
			}

	        //C.L. Fix for 11177
	        if($this->displayColumns[$columnName]['type'] == 'html') {
	            $cField = $this->seed->custom_fields;
	               if(isset($cField) && isset($cField->bean->$seedName)) {
	                 	$seedName2 = strtoupper($columnName);
	                 	$htmlDisplay = html_entity_decode($cField->bean->$seedName);
	                 	$count = 0;
	                 	while($count < count($data['data'])) {
	                 		$data['data'][$count][$seedName2] = &$htmlDisplay;
	                 	    $count++;
	                 	}
	            	}
	        }//fi == 'html'

			if (!empty($this->lvd->seed->field_defs[$seedName]['sort_on'])) {
		    	$this->displayColumns[$columnName]['orderBy'] = $this->lvd->seed->field_defs[$seedName]['sort_on'];
		    }
		}

		$this->process($file, $data, $this->seed->object_name);
	}

	function _getAddForm(){
        $addform = '';
        if(!$this->seed->ACLAccess('save')){
            return;
        }
        return $addform;
	}


        
    //copied from listviewData so we can use the query from the subpanel
    function getListViewData($seed, $where, $offset=-1, $limit = -1, $filter_fields=array(),$params=array(),$id_field = 'id',$singleSelect=true,$parent,$subpanel_def) {
            global $current_user;
            SugarVCR::erase($seed->module_dir);
            $this->lvd->seed =& $seed;
            $totalCounted = empty($GLOBALS['sugar_config']['disable_count_query']);
            $_SESSION['MAILMERGE_MODULE_FROM_LISTVIEW'] = $seed->module_dir;
            if(empty($_REQUEST['action']) || $_REQUEST['action'] != 'Popup'){
                $_SESSION['MAILMERGE_MODULE'] = $seed->module_dir;
            }

            $this->lvd->setVariableName($seed->object_name, $where, $this->lvd->listviewName);

            $this->lvd->seed->id = '[SELECT_ID_LIST]';

            // if $params tell us to override all ordering
            if(!empty($params['overrideOrder']) && !empty($params['orderBy'])) {
                $order = $this->lvd->getOrderBy(strtolower($params['orderBy']), (empty($params['sortOrder']) ? '' : $params['sortOrder'])); // retreive from $_REQUEST
            }
            else {
                $order = $this->lvd->getOrderBy(); // retreive from $_REQUEST
            }

            // else use stored preference
            $userPreferenceOrder = $current_user->getPreference('listviewOrder', $this->lvd->var_name);

            if(empty($order['orderBy']) && !empty($userPreferenceOrder)) {
                $order = $userPreferenceOrder;
            }
            // still empty? try to use settings passed in $param
            if(empty($order['orderBy']) && !empty($params['orderBy'])) {
                $order['orderBy'] = $params['orderBy'];
                $order['sortOrder'] =  (empty($params['sortOrder']) ? '' : $params['sortOrder']);
            }

            //rrs - bug: 21788. Do not use Order by stmts with fields that are not in the query.
            // Bug 22740 - Tweak this check to strip off the table name off the order by parameter.
            // Samir Gandhi : Do not remove the report_cache.date_modified condition as the report list view is broken
            $orderby = $order['orderBy'];
            if (strpos($order['orderBy'],'.') && ($order['orderBy'] != "report_cache.date_modified")) {
                $orderby = substr($order['orderBy'],strpos($order['orderBy'],'.')+1);
            }
            if ($orderby != 'date_entered' && !in_array($orderby, array_keys($filter_fields))) {
                    $order['orderBy'] = '';
                    $order['sortOrder'] = '';
            }

            if (empty($order['orderBy'])) {
                $orderBy = '';
            } else {
                $orderBy = $order['orderBy'] . ' ' . $order['sortOrder'];
                //wdong, Bug 25476, fix the sorting problem of Oracle.
                if (isset($params['custom_order_by_override']['ori_code']) && $order['orderBy'] == $params['custom_order_by_override']['ori_code'])
                    $orderBy = $params['custom_order_by_override']['custom_code'] . ' ' . $order['sortOrder'];
            }

            if (empty($params['skipOrderSave'])) { // don't save preferences if told so
                $current_user->setPreference('listviewOrder', $order, 0, $this->lvd->var_name); // save preference
            }

            // If $params tells us to override for the special last_name, first_name sorting
            if (!empty($params['overrideLastNameOrder']) && $order['orderBy'] == 'last_name') {
                    $orderBy = 'last_name '.$order['sortOrder'].', first_name '.$order['sortOrder'];
            }
            
            $related_field_name = $subpanel_def->get_data_source_name();
            if (!$parent->load_relationship($related_field_name)){
                unset ($parent->$related_field_name);
                continue;
            }
            $query_array = $parent->$related_field_name->getSubpanelQuery(array(), true);
            $ret_array = $seed->create_new_list_query($orderBy,'' ,$filter_fields,$params, 0,'', true,$parent, $singleSelect);
            $main_query = $ret_array['select'] . $params['custom_select'] . $ret_array['from'] . $params['custom_from'] . $query_array['join']. $ret_array['where'] . $params['custom_where'] . $ret_array['order_by'] . $params['custom_order_by'];   
            if(!empty($where)){
                $where = "WHERE ".$where;
                $pos = strripos($main_query, "WHERE");

                if($pos !== false) {
                    $main_query = substr_replace($main_query, $where, $pos, strlen("WHERE"));
                }
            }

            if($limit < -1) {
                    $result = $this->lvd->db->query($main_query);
            }else {
                if($limit == -1) {
                    $limit = $this->lvd->getLimit();
                }
                $dyn_offset = $this->lvd->getOffset();
                if($dyn_offset > 0 || !is_int($dyn_offset))$offset = $dyn_offset;

                if(strcmp($offset, 'end') == 0){
                    $totalCount = $this->lvd->getTotalCount($main_query);
                    $offset = (floor(($totalCount -1) / $limit)) * $limit;
                }
                if($this->lvd->seed->ACLAccess('ListView')) {
                    $result = $this->lvd->db->limitQuery($main_query, $offset, $limit + 1);
                }else {
                    $result = array();
                }

            }

            $data = array();

            $temp = clone $seed;

            $rows = array();
            $count = 0;
            $idIndex = array();
            $id_list = '';

            while(($row = $this->lvd->db->fetchByAssoc($result)) != null)  {
                if($count < $limit) {
                    $id_list .= ',\''.$row[$id_field].'\'';
                    $idIndex[$row[$id_field]][] = count($rows);
                    $rows[] = $seed->convertRow($row);
                }
                $count++;
            }

            if (!empty($id_list)) {
                $id_list = '('.substr($id_list, 1).')';
            }

            SugarVCR::store($this->lvd->seed->module_dir,  $main_query);
            if($count != 0) {
                // retrieve parent names
                if(!empty($filter_fields['parent_name']) && !empty($filter_fields['parent_id']) && !empty($filter_fields['parent_type'])) {
                    foreach($idIndex as $id => $rowIndex) {
                        if(!isset($post_retrieve[$rows[$rowIndex[0]]['parent_type']])) {
                            $post_retrieve[$rows[$rowIndex[0]]['parent_type']] = array();
                        }
                        if(!empty($rows[$rowIndex[0]]['parent_id'])) $post_retrieve[$rows[$rowIndex[0]]['parent_type']][] = array('child_id' => $id , 'parent_id'=> $rows[$rowIndex[0]]['parent_id'], 'parent_type' => $rows[$rowIndex[0]]['parent_type'], 'type' => 'parent');
                    }
                    if(isset($post_retrieve)) {
                        $parent_fields = $seed->retrieve_parent_fields($post_retrieve);
                        foreach($parent_fields as $child_id => $parent_data) {
                            //add it to every row with the given id
                            foreach($idIndex[$child_id] as $index){
                                $rows[$index]['parent_name']= $parent_data['parent_name'];
                            }
                        }
                    }
                }

                $pageData = array();

                reset($rows);
                while($row = current($rows)){

                    $temp = clone $seed;
                    $dataIndex = count($data);

                    $temp->setupCustomFields($temp->module_dir);
                    $temp->loadFromRow($row);
                    if($idIndex[$row[$id_field]][0] == $dataIndex){
                        $pageData['tag'][$dataIndex] = $temp->listviewACLHelper();
                    }else{
                        $pageData['tag'][$dataIndex] = $pageData['tag'][$idIndex[$row[$id_field]][0]];
                    }
                    $data[$dataIndex] = $temp->get_list_view_data($filter_fields);
                    $pageData['rowAccess'][$dataIndex] = array('view' => $temp->ACLAccess('DetailView'), 'edit' => $temp->ACLAccess('EditView'));
                    $additionalDetailsAllow = $this->lvd->additionalDetails && $temp->ACLAccess('DetailView') && (file_exists('modules/' . $temp->module_dir . '/metadata/additionalDetails.php') || file_exists('custom/modules/' . $temp->module_dir . '/metadata/additionalDetails.php'));
                    //if($additionalDetailsAllow) $pageData['additionalDetails'] = array();
                    $additionalDetailsEdit = $temp->ACLAccess('EditView');
                    if($additionalDetailsAllow) {
                        if($this->lvd->additionalDetailsAjax) {
                                               $ar = $this->lvd->getAdditionalDetailsAjax($data[$dataIndex]['ID']);
                        } else {
                            $additionalDetailsFile = 'modules/' . $this->lvd->seed->module_dir . '/metadata/additionalDetails.php';
                            if(file_exists('custom/modules/' . $this->lvd->seed->module_dir . '/metadata/additionalDetails.php')){
                                    $additionalDetailsFile = 'custom/modules/' . $this->lvd->seed->module_dir . '/metadata/additionalDetails.php';
                            }
                            require_once($additionalDetailsFile);
                            $ar = $this->lvd->getAdditionalDetails($data[$dataIndex],
                                        (empty($this->lvd->additionalDetailsFunction) ? 'additionalDetails' : $this->lvd->additionalDetailsFunction) . $this->lvd->seed->object_name,
                                    $additionalDetailsEdit);
                        }
                        $pageData['additionalDetails'][$dataIndex] = $ar['string'];
                        $pageData['additionalDetails']['fieldToAddTo'] = $ar['fieldToAddTo'];
                    }
				next($rows);
                }
            }
            $nextOffset = -1;
            $prevOffset = -1;
            $endOffset = -1;
            if($count > $limit) {
                $nextOffset = $offset + $limit;
            }

            if($offset > 0) {
                $prevOffset = $offset - $limit;
                if($prevOffset < 0)$prevOffset = 0;
            }
            $totalCount = $count + $offset;

            if( $count >= $limit && $totalCounted){
                $totalCount  = $this->lvd->getTotalCount($main_query);
            }
            SugarVCR::recordIDs($this->lvd->seed->module_dir, array_keys($idIndex), $offset, $totalCount);
            $module_names = array(
                'Prospects' => 'Targets'
                );
            $endOffset = (floor(($totalCount - 1) / $limit)) * $limit;
            $pageData['ordering'] = $order;
            $pageData['ordering']['sortOrder'] = $this->lvd->getReverseSortOrder($pageData['ordering']['sortOrder']);
            //get url parameters as an array
            $pageData['queries'] = $this->generateQueries($pageData['ordering']['sortOrder'], $offset, $prevOffset, $nextOffset,  $endOffset, $totalCounted);
            //join url parameters from array to a string
            $pageData['urls'] = $this->generateURLS($pageData['queries']);
            $pageData['offsets'] = array( 'current'=>$offset, 'next'=>$nextOffset, 'prev'=>$prevOffset, 'end'=>$endOffset, 'total'=>$totalCount, 'totalCounted'=>$totalCounted);
            $pageData['bean'] = array('objectName' => $seed->object_name, 'moduleDir' => $seed->module_dir, 'moduleName' => strtr($seed->module_dir, $module_names));
            $pageData['stamp'] = $this->lvd->stamp;
            $pageData['access'] = array('view' => $this->lvd->seed->ACLAccess('DetailView'), 'edit' => $this->lvd->seed->ACLAccess('EditView'));
            $pageData['idIndex'] = $idIndex;
            if(!$this->lvd->seed->ACLAccess('ListView')) {
                $pageData['error'] = 'ACL restricted access';
            }
        
            $queryString = '';
		
            if( isset($_REQUEST["searchFormTab"]) && $_REQUEST["searchFormTab"] == "advanced_search" || 
                    isset($_REQUEST["type_basic"]) && (count($_REQUEST["type_basic"] > 1) || $_REQUEST["type_basic"][0] != "") ||
                    isset($_REQUEST["module"]) && $_REQUEST["module"] == "MergeRecords")
            {
                $queryString = "-advanced_search";
            } else if (isset($_REQUEST["searchFormTab"]) && $_REQUEST["searchFormTab"] == "basic_search")  {
                if($seed->module_dir == "Reports") $searchMetaData = SearchFormReports::retrieveReportsSearchDefs();
                else $searchMetaData = SearchForm::retrieveSearchDefs($seed->module_dir);

                $basicSearchFields = array();

                if( isset($searchMetaData['searchdefs']) && isset($searchMetaData['searchdefs'][$seed->module_dir]['layout']['basic_search']) )
                    $basicSearchFields = $searchMetaData['searchdefs'][$seed->module_dir]['layout']['basic_search'];

                foreach( $basicSearchFields as $basicSearchField)
                {
                    $field_name = (is_array($basicSearchField) && isset($basicSearchField['name'])) ? $basicSearchField['name'] : $basicSearchField;
                    $field_name .= "_basic";
                    if( isset($_REQUEST[$field_name])  && ( !is_array($basicSearchField) || !isset($basicSearchField['type']) || $basicSearchField['type'] == 'text' || $basicSearchField['type'] == 'name') )
                    {
                        $queryString = htmlentities($_REQUEST[$field_name]);
                        break;
                    }
                }
            }


            return array('data'=>$data , 'pageData'=>$pageData, 'query' => $queryString);
	}
    
    //The 3 functions below have been copied along with the getListViewData 
    //as they are now proctected functions in the ListViewData class
    /**
     * generates queries for use by the display layer
     *
     * @param int $sortOrder
     * @param int $offset
     * @param int $prevOffset
     * @param int $nextOffset
     * @param int $endOffset
     * @param int $totalCounted
     * @return array of queries orderBy and baseURL are always returned the others are only returned  according to values passed in.
     */
    function generateQueries($sortOrder, $offset, $prevOffset, $nextOffset, $endOffset, $totalCounted)
    {
        $queries = array();
        $queries['baseURL'] = $this->getBaseQuery();
        $queries['baseURL']['lvso'] = $sortOrder;

        $queries['orderBy'] = $queries['baseURL'];
        $queries['orderBy'][$this->lvd->var_order_by] = '';

        if($nextOffset > -1)
        {
            $queries['nextPage'] = $queries['baseURL'];
            $queries['nextPage'][$this->lvd->var_offset] = $nextOffset;
        }
        if($offset > 0)
        {
            $queries['startPage'] = $queries['baseURL'];
            $queries['startPage'][$this->lvd->var_offset] = 0;
        }
        if($prevOffset > -1)
        {
            $queries['prevPage'] = $queries['baseURL'];
            $queries['prevPage'][$this->lvd->var_offset] = $prevOffset;
        }
        if($totalCounted)
        {
            $queries['endPage'] = $queries['baseURL'];
            $queries['endPage'][$this->lvd->var_offset] = $endOffset;
        }
        else
        {
            $queries['endPage'] = $queries['baseURL'];
            $queries['endPage'][$this->lvd->var_offset] = 'end';
        }
        return $queries;
    }
    /**
     * generates urls as a string for use by the display layer
     *
     * @param array $queries
     * @return array of urls orderBy and baseURL are always returned the others are only returned  according to values passed in.
     */
    function generateURLS($queries)
    {
        foreach ($queries as $name => $value)
        {
            $queries[$name] = 'index.php?' . http_build_query($value);
        }
        $this->lvd->base_url = $queries['baseURL'];
        return $queries;
    }
     /**
     * generates the base url without
     * any files in the block variables will not be part of the url
     *
     * @return Array (the base url as an array)
     */
    function getBaseQuery()
    {
        global $beanList;

        $blockVariables = array('mass', 'uid', 'massupdate', 'delete', 'merge', 'selectCount',$this->lvd->var_order_by, $this->lvd->var_offset, 'lvso', 'sortOrder', 'orderBy', 'request_data', 'current_query_by_page');
        foreach($beanList as $bean)
        {
            $blockVariables[] = 'Home2_'.strtoupper($bean).'_ORDER_BY';
        }
        $blockVariables[] = 'Home2_CASE_ORDER_BY';

        // Added mostly for the unit test runners, which may not have these superglobals defined
        $params = array_merge($_POST, $_GET);
        $params = array_diff_key($params, array_flip($blockVariables));

        return $params;
    }
}
?>

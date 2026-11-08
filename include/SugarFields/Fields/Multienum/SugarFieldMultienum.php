<?php
/*********************************************************************************
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
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

require_once('include/SugarFields/Fields/Enum/SugarFieldEnum.php');

class SugarFieldMultienum extends SugarFieldEnum
{
	var $firstCall = true;
    function setup($parentFieldArray, $vardef, $displayParams, $tabindex, $twopass=true) {
        if ( !isset($vardef['options_list']) && isset($vardef['options']) && !is_array($vardef['options'])) {
            $vardef['options_list'] = $GLOBALS['app_list_strings'][$vardef['options']];
        }
        return parent::setup($parentFieldArray, $vardef, $displayParams, $tabindex, $twopass);
    }

    function getDetailViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex) {
     $this->setup($parentFieldArray, $vardef, $displayParams, $tabindex);
     if(isset($displayParams['checkboxSelect']) && $displayParams['checkboxSelect']) {
        return $this->fetch('include/SugarFields/Fields/Multienum/DetailView.CheckboxSelect.tpl');
     } else {
        return $this->fetch('include/SugarFields/Fields/Multienum/DetailView.tpl');
     }
    }
    
    function getEditViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex) {
        if(isset($displayParams['enhancedSelect']) && $displayParams['enhancedSelect']) {
            $displayParams['firstCall'] = $this->firstCall;
            $this->firstCall= false;
            $this->setup($parentFieldArray, $vardef, $displayParams, $tabindex);
            return $this->fetch('include/SugarFields/Fields/Multienum/EditView.EnhancedSelect.tpl');
        } else if(isset($displayParams['checkboxSelect']) && $displayParams['checkboxSelect']) {
            $this->setup($parentFieldArray, $vardef, $displayParams, $tabindex);
            return $this->fetch('include/SugarFields/Fields/Multienum/EditView.CheckboxSelect.tpl');
        } else {
            $this->setup($parentFieldArray, $vardef, $displayParams, $tabindex);
            return $this->fetch('include/SugarFields/Fields/Multienum/EditView.tpl');
        }
    }

	function getSearchViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex) {
    	if(!empty($vardef['function']['returns']) && $vardef['function']['returns']== 'html'){
    	   $this->setup($parentFieldArray, $vardef, $displayParams, $tabindex);
           return $this->fetch($this->findTemplate('EditViewFunction'));
    	}else{
    	   $this->setup($parentFieldArray, $vardef, $displayParams, $tabindex);
           return $this->fetch($this->findTemplate('SearchView'));
    	}
    }

    function displayFromFunc( $displayType, $parentFieldArray, $vardef, $displayParams, $tabindex ) {
        if ( isset($vardef['function']['returns']) && $vardef['function']['returns'] == 'html' ) {
            return parent::displayFromFunc($displayType, $parentFieldArray, $vardef, $displayParams, $tabindex);
        }

        $displayTypeFunc = 'get'.$displayType.'Smarty';
        return $this->$displayTypeFunc($parentFieldArray, $vardef, $displayParams, $tabindex);
    }

	public function save(&$bean, $params, $field, $properties, $prefix = ''){
		if ( isset($params[$prefix.$field]) ) {
			if($params[$prefix.$field][0] === '' && !empty($params[$prefix.$field][1]) ) {
				unset($params[$prefix.$field][0]);
			}

			$bean->$field = encodeMultienumValue($params[$prefix.$field]);
		}
        else  if (isset($params[$prefix.$field.'_multiselect']) && $params[$prefix.$field.'_multiselect']==true) {
			// if the value in db is not empty and
			// if the data is not set in params (means the user has deselected everything) and
			// if the corresponding multiselect flag is true
			// then set field to ''
			if (!empty($bean->$field)) {
				$bean->$field = '';
			}
		}
    }

    /**
     * @see SugarFieldBase::importSanitize()
     */
    public function importSanitize(
        $value,
        $vardef,
        $focus,
        ImportFieldSanitize $settings
        )
    {
        if(!empty($value) && is_array($value)) {
            $enum_list = $value;
        }
        else {
            // If someone was using the old style multienum import technique
            $value = str_replace("^","",$value);

            // We will need to break it apart to put test it.
            $enum_list = explode(",",$value);
        }
        // parse to see if all the values given are valid
        foreach ( $enum_list as $key => $enum_value ) {
            $enum_list[$key] = $enum_value = trim($enum_value);
            $sanitizedValue = parent::importSanitize($enum_value,$vardef,$focus,$settings);
            if ($sanitizedValue  === false ) {
                return false;
            }
            else {
                $enum_list[$key] = $sanitizedValue;
            }
        }
        $value = encodeMultienumValue($enum_list);

        return $value;
    }

	// EINTEL
    function getListViewSmarty($parentFieldArray, $vardef, $displayParams, $col)
    {
        if((isset($displayParams['inline_edit']) && $displayParams['inline_edit'])
			|| (isset($vardef['inline_edit']) && $vardef['inline_edit']))
		{
			// Ensure this only runs for *_roles fields
			if (preg_match("/_roles$/i", $vardef['name']))
			{
				$displayParams['firstCall'] = $this->firstCall;
				$this->firstCall= false;
				$tabindex = 1;

				// Determine bean
				global $beanList;
				// Try getting from subpanel
				if (isset($beanList[$displayParams['module']]))
				{
					$bean_name = $beanList[$displayParams['module']];
					$module = $displayParams['module'];
				}
				// Try getting from listview
				elseif (isset($beanList[$_REQUEST['module']]))
				{
					$bean_name = $beanList[$_REQUEST['module']];
					$module = $_REQUEST['module'];
				}
				else
					sugar_die("SugarFieldMultienum-EditRoles: 'module' not found in def");

				$value = null;
				if (!empty($displayParams['table_key'] ) &&( empty ($vardef['fields']) || empty ($vardef['fields'][0]) || empty ($vardef['fields'][1]))){
					$value = $this->_get_list_value($displayParams);
				}else if(!empty($displayParams['name']) && !empty($parentFieldArray)){
					$key = strtoupper($displayParams['name']);
					if( isset($parentFieldArray[$key])) {
						$value = $parentFieldArray[$key];
					}
				}
				$cell = '';

				if(isset($vardef['options'])) {
					$cell = translate($vardef['options'], $vardef['module'], $value);
				}
				else if(isset($vardef['function'])) {
					global $beanFiles;
					if(empty($beanFiles)) {
						include('include/modules.php');
					}
					$bean_name = get_singular_bean_name($vardef['module']);
					require_once($beanFiles[$bean_name]);
					$options= $vardef['function']();
					if( isset($options[$value])) {
						$cell = $options[$value];
					} else {
						$cell = $options;
					}
				}
				if (is_array($cell)) {
					if( $value && $value != "^^") {
						$value = unencodeMultienum($value);
						$cell=array();
						foreach($value as $val){
							if( isset($options)) {
								$c = $options[$val];
							} else {
								$c = translate($vardef['options'], $vardef['module'], $val);
							}
							array_push( $cell, $c);
						}
						$cell = implode(", ",$cell);
					} else {
						$cell = '';
					}
				}

				
//				$module = $displayParams['module'];

				$this->ss->assign('cell', $cell);
				$this->ss->assign('module', $module);

				// Porjects
				if ($module == 'Users') {
					$project_id = $GLOBALS['db']->getOne("SELECT project_id FROM projects_team_members WHERE id = '{$parentFieldArray['USER_PROJECT_ROLES_ID']}'");
					if ($project_id !== false) {
						$related_name = $GLOBALS['db']->getOne("SELECT name FROM project WHERE id='{$project_id}'");
						if ($related_name !== false) {
							$this->ss->assign('related_name', $related_name);
						}
					}			
				}
				// Accounts
				else if ($module =='Contacts'){
					$related_name = $GLOBALS['db']->getOne("SELECT name FROM accounts WHERE id='{$parentFieldArray['ACCOUNT_ID']}'");
					if ($related_name !== false) {
						$this->ss->assign('related_name', $related_name);
					}
				}

				$role_source = '';
				$role_source_raw='';
				if(!isset($options)) {
				$options= translate($vardef['options'], $vardef['module']);
				}
				foreach($options as $k => $v)
				{
					$sel = "";
					if(is_array($value))
					{
						foreach($value as $pk => $pv)
						{
							if($pv == $k) $sel = "selected='selected'";
						}
					}
					$role_source .= "<option value='".$k."' ".$sel." >".$v."</option>";
					$role_source_raw .= "<option value='".$k."' >".$v."</option>";
				}

				$this->ss->assign('role_source', $role_source);
				$this->ss->assign('role_source_raw', $role_source_raw);
				// END DUMP

				$this->setup($parentFieldArray, $vardef, $displayParams, $tabindex, false);
				$this->ss->left_delimiter = '{';
				$this->ss->right_delimiter = '}';
				$this->ss->assign('bean_name', $bean_name);


				return $this->fetch($this->findTemplate('InlineEdit.EditRoles'));
			}
        } else {
            return parent::getListViewSmarty($parentFieldArray, $vardef, $displayParams, $col);
        }
    }

}

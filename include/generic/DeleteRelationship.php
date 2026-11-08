<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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

/*********************************************************************************

 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

/*
 Removes Relationships, input is a form POST

ARGS:
 $_REQUEST['module']; : the module associated with this Bean instance (will be used to get the class name)
 $_REQUEST['record']; : the id of the Bean instance
 $_REQUEST['linked_field']; : the linked field name of the Parent Bean
 $_REQUEST['linked_id']; : the id of the Related Bean instance to

 $_REQUEST['return_url']; : the URL to redirect to
  or use:
  1) $_REQUEST['return_id']; :
  2) $_REQUEST['return_module']; :
  3) $_REQUEST['return_action']; :
*/
//_ppd($_REQUEST);


require_once('include/formbase.php');

 global $beanFiles,$beanList;
 $bean_name = $beanList[$_REQUEST['module']];
 require_once($beanFiles[$bean_name]);
 $focus = new $bean_name();
 if (  empty($_REQUEST['linked_id']) || empty($_REQUEST['linked_field'])  || empty($_REQUEST['record']))
 {
	if(!isset($_REQUEST['select_entire_list'])){   //allow missing variables if select all is selected                 
            die("need linked_field, linked_id and record fields");
        }
 }
 $linked_field = $_REQUEST['linked_field'];
 $record = $_REQUEST['record'];
 $linked_id = $_REQUEST['linked_id'];
 if($bean_name == 'Team')
 {
 	$focus->retrieve($record);
 	$focus->remove_user_from_team($linked_id);
 }
 else
 {
 	
        // if select all is selected
        // If the user selected "All records" from the selection menu, we pull up the list
 	// based on the query they used on that popup to relate them to the parent record
 	if(!empty($_REQUEST['select_entire_list']) &&  $_REQUEST['select_entire_list'] != 'undefined' && isset($_REQUEST['current_query_by_page'])){
            $current_query_by_page = $_REQUEST['current_query_by_page'];
            $current_query_by_page_array = unserialize(base64_decode($current_query_by_page));

            $module = $current_query_by_page_array['module'];
            $seed = loadBean($module);
            $where_clauses = '';
            require_once('include/SearchForm/SearchForm2.php');

            if(file_exists('custom/modules/'.$module.'/metadata/metafiles.php')){
                require('custom/modules/'.$module.'/metadata/metafiles.php');
            }elseif(file_exists('modules/'.$module.'/metadata/metafiles.php')){
                require('modules/'.$module.'/metadata/metafiles.php');
            }

            if (file_exists('custom/modules/'.$module.'/metadata/searchdefs.php'))  {
                require_once('custom/modules/'.$module.'/metadata/searchdefs.php');
            } elseif (!empty($metafiles[$module]['searchdefs'])) {
                require_once($metafiles[$module]['searchdefs']);
            } elseif (file_exists('modules/'.$module.'/metadata/searchdefs.php')) {
                require_once('modules/'.$module.'/metadata/searchdefs.php');
            }

            if(!empty($metafiles[$module]['searchfields'])){
                require_once($metafiles[$module]['searchfields']);
            }  elseif(file_exists('modules/'.$module.'/metadata/SearchFields.php')){
                require_once('modules/'.$module.'/metadata/SearchFields.php');
            }

            if(!empty($searchdefs) && !empty($searchFields)) {
                $searchForm = new SearchForm($seed, $module);
                $searchForm->setup($searchdefs, $searchFields, 'SearchFormGenericAdvanced.tpl', 'advanced_search');
                $searchForm->populateFromArray($current_query_by_page_array, 'advanced');
                
                $where_clauses_arr = $searchForm->generateSearchWhere(true, $module);
                
                if (count($where_clauses_arr) > 0 ) {
                    $where_clauses = '('. implode(' ) AND ( ', $where_clauses_arr) . ') AND ';
                }
            }
            
            //use subpanel query to return the same data set
            $ListView = new ListView();
            $ListView->source_module = $current_query_by_page_array['parent_module'];
            $ListView->subpanel_module = $current_query_by_page_array['subpanel_name'];
           
            include_once('include/SubPanel/SubPanelDefinitions.php');
            $parent = BeanFactory::getBean($ListView->source_module, $record);
            $panelsdef = new SubPanelDefinitions($parent);
            $subpanelDef = $panelsdef->load_subpanel($ListView->subpanel_module);
            
            $listview_data = $ListView->processUnionBeans($parent,$subpanelDef);
            
            if(!empty($where_clauses)){
                $where = "WHERE ".$where_clauses;
                $pos = strripos($listview_data['query'], "WHERE");

                if($pos !== false) {
                    $listview_data['query'] = substr_replace($listview_data['query'], $where, $pos, strlen("WHERE"));
                }
            }

            $result = $GLOBALS['db']->query($listview_data['query'],true);
            $linked_id = array();
            while($val = $GLOBALS['db']->fetchByAssoc($result,false))  {
                array_push($linked_id, $val['id']);
            }
           
 	}
     
        if(!empty($linked_id)) {
            //allow an array of linked items to be removed
            if(!is_array($linked_id)){
                $linked_id = array($linked_id);
            }

            foreach($linked_id as $linked){
                $focus->load_relationship($linked_field);
                if($focus->$linked_field->_relationship->relationship_name == 'quotes_contacts_shipto'){
                    unset($focus->$linked_field->_relationship->relationship_role_column);
                }
                $focus->$linked_field->delete($record,$linked);
            }
        }

 }
 if ($bean_name == 'Campaign' and $linked_field=='prospectlists' ) {

 	$query="SELECT email_marketing_prospect_lists.id from email_marketing_prospect_lists ";
 	$query.=" left join email_marketing on email_marketing.id=email_marketing_prospect_lists.email_marketing_id";
	$query.=" where email_marketing.campaign_id='$record'";
 	$query.=" and email_marketing_prospect_lists.prospect_list_id='$linked_id'";

 	$result=$focus->db->query($query);
	while (($row=$focus->db->fetchByAssoc($result)) != null) {
			$del_query =" update email_marketing_prospect_lists set email_marketing_prospect_lists.deleted=1, email_marketing_prospect_lists.date_modified=".$focus->db->convert("'".TimeDate::getInstance()->nowDb()."'",'datetime');
 			$del_query.=" WHERE  email_marketing_prospect_lists.id='{$row['id']}'";
		 	$focus->db->query($del_query);
	}
 	$focus->db->query($query);
 }
if ($bean_name == "Meeting") {
    $focus->retrieve($record);
    $user = new User();
    $user->retrieve($linked_id);
    if (!empty($user->id)) {  //make sure that record exists. we may have a contact on our hands.

    	if($focus->update_vcal)
    	{
        	vCal::cache_sugar_vcal($user);
    	}
    }
}
if ($bean_name == "User" && $linked_field == 'eapm') {
    $eapm = new EAPM();
    $eapm->mark_deleted($linked_id);
}

if(!empty($_REQUEST['return_url'])){
	$_REQUEST['return_url'] =urldecode($_REQUEST['return_url']);
}
$GLOBALS['log']->debug("deleted relationship: bean: $bean_name, linked_field: $linked_field, linked_id:$linked_id" );
if(empty($_REQUEST['refresh_page'])){
	handleRedirect();
}


exit;
?>
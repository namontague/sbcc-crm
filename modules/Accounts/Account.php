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

 * Description:  Defines the Account SugarBean Account entity with the necessary
 * methods and variables.
 ********************************************************************************/

require_once("include/SugarObjects/templates/company/Company.php");

// Account is used to store account information.
class Account extends Company {
	var $field_name_map = array();
	// Stored fields
	var $date_entered;
	var $date_modified;
	var $modified_user_id;
	var $assigned_user_id;
	var $annual_revenue;
    //EINTEL 25/09/13 adding new address fields
    var $billing_address_building_type;
    var $billing_address_number;
	var $billing_address_street;
	var $billing_address_city;
	var $billing_address_state;
	var $billing_address_country;
	var $billing_address_postalcode;

    var $billing_address_street_2;
    var $billing_address_street_3;
    var $billing_address_street_4;

	var $description;
	var $email1;
	var $email2;
	var $email_opt_out;
	var $invalid_email;
	var $employees;
	var $id;
	var $industry;
	var $name;
	var $ownership;
	var $parent_id;
	var $phone_alternate;
	var $phone_fax;
	var $phone_office;
	var $rating;
    //EINTEL 25/09/13 adding new address fields
    var $shipping_address_building_type;
    var $shipping_address_number;
	var $shipping_address_street;
	var $shipping_address_city;
	var $shipping_address_state;
	var $shipping_address_country;
	var $shipping_address_postalcode;

    var $shipping_address_street_2;
    var $shipping_address_street_3;
    var $shipping_address_street_4;

        var $campaign_id;

	var $sic_code;
	var $ticker_symbol;
	var $account_type;
	var $website;
	var $custom_fields;

	var $created_by;
	var $created_by_name;
	var $modified_by_name;

	// These are for related fields
	var $opportunity_id;
	var $case_id;
	var $contact_id;
	var $task_id;
	var $note_id;
	var $meeting_id;
	var $call_id;
	var $email_id;
	var $member_id;
	var $parent_name;
	var $assigned_user_name;
	var $account_id = '';
	var $account_name = '';
	var $bug_id ='';
	var $module_dir = 'Accounts';
	var $emailAddress;


	var $table_name = "accounts";
	var $object_name = "Account";
	var $importable = true;
	var $new_schema = true;
	// This is used to retrieve related fields from form posts.
	var $additional_column_fields = Array('assigned_user_name', 'assigned_user_id', 'opportunity_id', 'bug_id', 'case_id', 'contact_id', 'task_id', 'note_id', 'onlinedocument_id', 'meeting_id', 'call_id', 'email_id', 'parent_name', 'member_id'
	);
	var $relationship_fields = Array('opportunity_id'=>'opportunities', 'bug_id' => 'bugs', 'case_id'=>'cases',
									'contact_id'=>'contacts', 'task_id'=>'tasks', 'note_id'=>'notes', 'onlinedocument_id'=>'onlinedocuments',
									'meeting_id'=>'meetings', 'call_id'=>'calls', 'email_id'=>'emails','member_id'=>'members',
									'project_id'=>'project',
									);

    //Meta-Data Framework fields
    var $push_billing;
    var $push_shipping;
    // EINTEL Additions
    var $portal_active;
    var $portal_password;
    var $abn;
    var $acn;
    var $erp_id; // Used to reference ID for account in external ERP application.
    var $update_contact;
    var $code;
    var $do_not_fax;
    var $onlinedocument_id;
    var $shipping_location_id;
    var $billing_location_id;

    function Account() {
        parent::Company();

        $this->setupCustomFields('Accounts');

		foreach ($this->field_defs as $field)
		{
			if(isset($field['name']))
			{
				$this->field_name_map[$field['name']] = $field;
			}
		}


        //Email logic
		if (!empty($_REQUEST['parent_id']) && !empty($_REQUEST['parent_type']) && $_REQUEST['parent_type'] == 'Emails'
        	&& !empty($_REQUEST['return_module']) && $_REQUEST['return_module'] == 'Emails') {
			$_REQUEST['parent_name'] = '';
			$_REQUEST['parent_id'] = '';
		}
	}

	function get_summary_text()
	{
		return $this->name;
	}

	function get_contacts() {
		return $this->get_linked_beans('contacts','Contact');
	}



	function clear_account_case_relationship($account_id='', $case_id='')
	{
		if (empty($case_id)) $where = '';
		else $where = " and id = '$case_id'";
		$query = "UPDATE cases SET account_name = '', account_id = '' WHERE account_id = '$account_id' AND deleted = 0 " . $where;
		$this->db->query($query,true,"Error clearing account to case relationship: ");
	}

	/**
	* This method is used to provide backward compatibility with old data that was prefixed with http://
	* We now automatically prefix http://
	* @deprecated.
 	*/
	function remove_redundant_http()
	{	/*
		if(preg_match("@http://@", $this->website))
		{
			$this->website = substr($this->website, 7);
		}
		*/
	}

	function fill_in_additional_list_fields()
	{
		parent::fill_in_additional_list_fields();
	// Fill in the assigned_user_name
	//	$this->assigned_user_name = get_assigned_user_name($this->assigned_user_id);
                //EINTEL, Location module functions
                if(module_enabled("Locations")){
                    //if location module is active retreive the location details and replace the currents objects address.
                    //this one the source is always coming from the locations table.
                    $this->setAddressFromLocationTable('billing');
                    $this->setAddressFromLocationTable('shipping');

                }
        //EINTEL 25/09/13 adding new address fields
        //format address to include type and number together with street        
        $this->list_view_format_address();
        //END
	}

	function fill_in_additional_detail_fields()
	{
        parent::fill_in_additional_detail_fields();

        //rrs bug: 28184 - instead of removing this code altogether just adding this check to ensure that if the parent_name
        //is empty then go ahead and fill it.
        if(empty($this->parent_name) && !empty($this->id)){
			$query = "SELECT a1.name from accounts a1, accounts a2 where a1.id = a2.parent_id and a2.id = '$this->id' and a1.deleted=0";
			$result = $this->db->query($query,true," Error filling in additional detail fields: ");

			// Get the id and the name.
			$row = $this->db->fetchByAssoc($result);

			if($row != null)
			{
				$this->parent_name = $row['name'];
			}
			else
			{
				$this->parent_name = '';
			}
        }

        // Set campaign name if there is a campaign id
		if( !empty($this->campaign_id)){

			$camp = new Campaign();
		    $where = "campaigns.id='{$this->campaign_id}'";
		    $campaign_list = $camp->get_full_list("campaigns.name", $where, true);
		    $this->campaign_name = $campaign_list[0]->name;
		}

                //EINTEL, Location module functions
                if(module_enabled("Locations") && !empty($this->id)){
                    //if location module is active retreive the location details and replace the currents objects address.
                    //this one the source is always coming from the locations table.
                    $this->setAddressFromLocationTable('billing');
                    $this->setAddressFromLocationTable('shipping');

                }


	}

         //EINTEL, function to load address from location table
        function setAddressFromLocationTable($type,&$temp_array = null){
            $id_field = $type .'_location_id';
            if(empty($this->$id_field)){
                //try to set db value for locations.
                //this field is not in the list view object
                  $query = "SELECT a.{$type}_location_id
                            FROM {$this->table_name} a
                            WHERE a.id = '$this->id'
                            AND a.deleted = 0";
                  $result = $this->db->query($query,true," Error getting location id ");
                  $row = $this->db->fetchByAssoc($result);
                  if(isset($row[$id_field])){
                      $this->$id_field = $row[$id_field];
                  }
            }


            if(isset($this->$id_field) && !empty($this->$id_field)){
                $location = BeanFactory::getBean('Locations', $this->$id_field);
                if(isset($temp_array)){
                    //listview
                    if(!empty($this->{$type.'_address_state'})) {
                        $temp_array["CITY"] = $location->city . ', '. $location->state;
                    }else {
                        $temp_array["CITY"] = $location->city;
                    }
                    $temp_array[strtoupper($type)."_ADDRESS_STREET"]  = trim(trim($location->building_type." ".$location->number)." ".$location->address1);
                    if(!empty($location->address2)){
                        $temp_array[strtoupper($type)."_ADDRESS_STREET"]  = $temp_array[strtoupper($type)."_ADDRESS_STREET"]."\n".$location->address2;
                    }  
                    $temp_array[strtoupper($type)."_ADDRESS_CITY"] = $location->city;
                    $temp_array[strtoupper($type)."_ADDRESS_STATE"] = $location->state;
                    $temp_array[strtoupper($type)."_ADDRESS_POSTALCODE"] = $location->postalcode;
                    $temp_array[strtoupper($type)."_ADDRESS_COUNTRY" ]= $location->country;
                }else{
                    //edit/detail view
                    $this->{$type.'_address_street'} = $location->address1;
                    if(!empty($location->address2)){
                        $this->{$type.'_address_street'}  = $location->address1."\n".$location->address2;
                    } 
                    $this->{$type.'_address_city'} = $location->city;
                    $this->{$type.'_address_state'} = $location->state;
                    $this->{$type.'_address_postalcode'} = $location->postalcode;
                    $this->{$type.'_address_country'} = $location->country;
                }
            }
            return;
        }


	function get_list_view_data(){

		$temp_array = parent::get_list_view_data();

		$temp_array["ENCODED_NAME"] = $this->name;

		if(!empty($this->billing_address_state))
		{
			$temp_array["CITY"] = $this->billing_address_city . ', '. $this->billing_address_state;
		}
		else
		{
			$temp_array["CITY"] = $this->billing_address_city;
		}
        
        //$temp_array["BILLING_ADDRESS_STREET"]  = $this->billing_address_street;
		//$temp_array["SHIPPING_ADDRESS_STREET"] = $this->shipping_address_street;

        //EINTEL, Location module functionssetAddressFromLocationTable($type,$temp_array = null)
        if(module_enabled("Locations") && !empty($this->id)){
            //if location module is active get the location details and replace the currents objects address.
            //this way the source is always coming from the locations table.
            $this->setAddressFromLocationTable('billing',$temp_array);
            $this->setAddressFromLocationTable('shipping',$temp_array);

        }

		return $temp_array;
	}
	/**
		builds a generic search based on the query string using or
		do not include any $this-> because this is called on without having the class instantiated
	*/
	function build_generic_where_clause ($the_query_string) {
	$where_clauses = Array();
	$the_query_string = $this->db->quote($the_query_string);
	array_push($where_clauses, "accounts.name like '$the_query_string%'");
	if (is_numeric($the_query_string)) {
		array_push($where_clauses, "accounts.phone_alternate like '%$the_query_string%'");
		array_push($where_clauses, "accounts.phone_fax like '%$the_query_string%'");
		array_push($where_clauses, "accounts.phone_office like '%$the_query_string%'");
	}

	$the_where = "";
	foreach($where_clauses as $clause)
	{
		if(!empty($the_where)) $the_where .= " or ";
		$the_where .= $clause;
	}

	return $the_where;
}


        function create_export_query(&$order_by, &$where, $relate_link_join='')
        {
            $custom_join = $this->getCustomJoin(true, true, $where);
            $custom_join['join'] .= $relate_link_join;
                         $query = "SELECT
                                accounts.*,
                                email_addresses.email_address email_address,
                                '' email_addresses_non_primary, " . // email_addresses_non_primary needed for get_field_order_mapping()
                                "accounts.name as account_name,
                                users.user_name as assigned_user_name ";
            $query .= $custom_join['select'];
						 $query .= " FROM accounts ";
                         $query .= "LEFT JOIN users
	                                ON accounts.assigned_user_id=users.id ";

						//join email address table too.
						$query .=  ' LEFT JOIN  email_addr_bean_rel on accounts.id = email_addr_bean_rel.bean_id and email_addr_bean_rel.bean_module=\'Accounts\' and email_addr_bean_rel.deleted=0 and email_addr_bean_rel.primary_address=1 ';
						$query .=  ' LEFT JOIN email_addresses on email_addresses.id = email_addr_bean_rel.email_address_id ' ;

            $query .= $custom_join['join'];

		        $where_auto = "( accounts.deleted IS NULL OR accounts.deleted=0 )";

                if($where != "")
                        $query .= "where ($where) AND ".$where_auto;
                else
                        $query .= "where ".$where_auto;

        $order_by = $this->process_order_by($order_by);
        if (!empty($order_by)) {
            $query .= ' ORDER BY ' . $order_by;
        }

                return $query;
        }

	function set_notification_body($xtpl, $account)
	{
		$xtpl->assign("ACCOUNT_NAME", $account->name);
		$xtpl->assign("ACCOUNT_TYPE", $account->account_type);
		$xtpl->assign("ACCOUNT_DESCRIPTION", $account->description);

		return $xtpl;
	}

	function bean_implements($interface){
		switch($interface){
			case 'ACL':return true;
		}
		return false;
	}
	function get_unlinked_email_query($type=array()) {

		return get_unlinked_email_query($type, $this);
	}

    function portalEnabled() {
        static $enabled;
        if( !isset($enabled)) {
            $admin = new Administration();
            $admin->retrieveSettings();
            if(empty($admin->settings['portal_on']) || !$admin->settings['portal_on'] ||
                !isset($admin->settings['portal_accounts_access']) || !$admin->settings['portal_accounts_access']) {
                $enabled = false;
            } else {
                $enabled = true;
            }
        }
        return $enabled;
    }


    //EINTEL, when a relationship is made Update each contact in sugar with the cardcode and contactId
    function onRelationshipAdd($rel_name, $rel_key){
        if($rel_name == 'accounts_contacts'){
            if(!empty($this->erp_id)){
                $ERPNotify = ERPSync::getERPNotify();
                $person = new Contact();
                if(!is_array($rel_key)){
                    $rel_key = array($rel_key);
                }
                foreach($rel_key as $contact){
                    $person = $person->retrieve($contact);
                    $ERPNotify->addContactToAccount($person, $this);
                }
            }
        }
        return true;
    }

    //EINTEL, when creating/updating an account do the same in clear
    function save($check_notify=false, $erp_notify=true) {

        //get old and current description and compare before the old one is saved over
        if($erp_notify){
            $updateDescripton = true;
            if(!empty($this->id) && !empty($this->erp_id)){
                $db_accountOb = new Account();
                $db_accountOb = $db_accountOb->retrieve($this->id);
                if($db_accountOb->description == $this->description){
                    $updateDescripton = false;
                    $GLOBALS['log']->debug('XML Account Note: Account Description has not changed so the call to clear is skiped for account note add.');
                }
            }
        }

        if(isset($this->erp_id) && !empty($this->erp_id)){
            ERPSync::getERPNotify()->account_pre_save($bean);
        }

        //need to save locations and get ids to be used for when the account is saved.
        
        $record_id = parent::save();

         //EINTEL, Location module functions
        if(module_enabled("Locations")){
            $this->setLocationChanges();
        }
        
        if($erp_notify){
            $ERPNotify = ERPSync::getERPNotify();
            if(empty($this->erp_id)){
                //new account
                $ERPNotify->createAccount($this, $updateDescripton);
            }else{
                //updating account
                $ERPNotify->updateAccount($this, $updateDescripton);
            }
        }

        return $record_id;
    }

	function validate(&$errors) {
        $valid = parent::validate($errors);

        if ($this->code != "" || $this->code != null)
        {
            $query = "select id from accounts a where a.code='$this->code' and a.id <> '$this->id' and a.deleted=0";
            $result = $this->db->query($query);
            if( $this->db->getRowCount($result) > 0 ) {
                $errors['code'] = "Account already exists with this code: $this->code";
                $valid = false;
            }
        }
        $erpNotify = ERPSync::getERPNotify();
        $requiredFields = $erpNotify->accountRequiredFields();
        foreach($requiredFields as $field) {
            if( empty($this->$field)) {
                $vname = $this->field_defs[$field]['vname'];
                $label = isset($vname) ? translate($vname, $this->module_dir) : $field;
                $errors[$field] = "Missing required field: $label";
                $valid=false;
            }
        }
        return $valid;
    }

    function save_relationship_changes($is_update) {
        parent::save_relationship_changes($is_update);
    }

    //EINTEL, when a relationship is removed
    function onRelationshipRemove($rel_name, $bean_id, $related_id){
        if($rel_name == 'accounts_contacts'){
            $ERPNotify = ERPSync::getERPNotify();
            $contact = new Contact();
            $contact = $contact->retrieve($related_id);

            if(!empty($contact->erp_id) ){
                $account = $this;
                if( empty($account->id) || $account->id != $bean_id) {
                    $account = new Account();
                    $account->retrieve($bean_id);
                }
                $removed = $ERPNotify->removeContactFromAccount($contact, $account);
                if($removed){
                    return true;
                }else{
                    sugar_die("Contact was not removed from account {$contact->account_name}");
                }
            }
        }
        return true;

    }

    //EINTEL, check to see if a contact already is apart of another account.
    //Added in so contacts can only be related to 1 account with out changing core functions.
    function onRelationshipChange($rel_name, $rel_key){
        if($rel_name == 'accounts_contacts' ){
            if(!is_array($rel_key)){
                $rel_key = array($rel_key);
            }
            foreach($rel_key as $related_id){
                $contact = new Contact();
                $contact = $contact->retrieve($related_id);
                if(!empty($contact->account_id) && !empty($this->id)){
                    if($contact->account_id != $this->id){
                        //contact already is apart of another account
                        $removed_fromClear = $this->onRelationshipRemove($rel_name, $contact->account_id, $related_id);
                        //allow multiple contacts for now
                        if(false){
                            //update the accounts contact table to remove the old contact relation ship.
                            $where = "WHERE account_id = '{$contact->account_id}' AND contact_id = '{$contact->id}' AND deleted = 0";
                            $query='UPDATE accounts_contacts SET deleted = 1, primary_account=0 '.$where;
                            $GLOBALS['log']->debug("XML Contact Relationship change: Relationship Update Statement :".$query);
                            $result=$this->db->query($query, true);
                            $GLOBALS['log']->debug("XML Contact Relationship change: Relationship Update result :".$result);

                            if($result){
                                //removed, mark new as primary
                                $where = "WHERE account_id = '{$this->id}' AND contact_id = '{$contact->id}' AND deleted = 0";
                                $query='UPDATE accounts_contacts SET primary_account=1 '.$where;
                                $result=$this->db->query($query, true);
                                
                            }else{
                                sugar_die("Query to update contacts relationship with account failed, result:".$result." Query: ".$query);
                            }
                        }
                    }
                }
            }
        }
        return true;
    }


    //EINTEL, when a account is deleted and is in sync with ERP mark in ERP as deleted.
    function mark_deleted($id){
        if(!empty($this->erp_id) ){
            $ERPNotify = ERPSync::getERPNotify();
            $removed = $ERPNotify->deleteAccount($this);
            if($removed){
                $GLOBALS['log']->debug('XML: Account mark as Obsolete in ERP.');
            }else{
                $GLOBALS['log']->debug('XML: Unable to mark Account as Obsolete in ERP.');
                 sugar_die("Unable to mark Account as Obsolete in ERP. Account has not been deleted in Sugar");
            }
        }
        parent::mark_deleted($id);
    }


    function getContactsByRole($role) {
      $query = "select contact_id as id
                from accounts_contacts a
                inner join contacts c on (c.id = a.contact_id and c.deleted = 0)
                where a.deleted=0 and a.account_id='{$this->id}' and a.account_contact_roles like '%^$role^%'";
      $result = $this->db->query($query);
      // Get the id and the name.
      $contacts = array();
      while(($row=$this->db->fetchByAssoc($result)) != null) {
        $contact = new Contact();
        if($contact->retrieve($row['id'])) {
          $contacts[] = $contact;
        }
      }
      return $contacts;
    }


    //EINTEL
    //before save look for address changes
    function setLocationChanges(){
        //BILLING
        if(!empty($this->fetched_row['billing_location_id']) && 
                $this->fetched_row['billing_location_id'] != $this->billing_location_id){
            //need to remove default flags from the prevision location
            if($this->fetched_row['billing_location_id'] != 'new') {
                Location::updateDefaultFlags($this->fetched_row['billing_location_id'],'billing', false, true);
            }
        }
                
        if( $this->billing_location_id == 'new' ){
            Location::createAddressforLocationType('billing', $this);
        }else if(!empty($this->billing_location_id) ){
            $bill = BeanFactory::getBean('Locations', $this->billing_location_id);
            $bill->updateAddressforLocationType('billing', $this); 
        }
        
        
        //SHIPPING
        //use request value as when its disabled the $this value is always the fetched value
        if(!empty($this->fetched_row['shipping_location_id']) &&
                $this->fetched_row['shipping_location_id'] != $_REQUEST['shipping_location_id']){
            //need to remove default flags from the prevision location
            if($this->fetched_row['shipping_location_id'] != 'new') {
                Location::updateDefaultFlags($this->fetched_row['shipping_location_id'],'shipping', false, true);
            }
        }

        // Set shipping location from billing location if request contained billing location but not shipping location
        if(isset($_REQUEST['billing_location_id']) && !isset($_REQUEST['shipping_location_id'] ) ) {
            $this->shipping_location_id = $this->billing_location_id;
        } else if (!isset($this->shipping_location_id)) {
            $this->shipping_location_id = $this->billing_location_id;
            $this->shipping_address_street = $this->billing_address_street;
            $this->shipping_address_city = $this->billing_address_city;
            $this->shipping_address_state = $this->billing_address_state;
            $this->shipping_address_postalcode = $this->billing_address_postalcode;
            $this->shipping_address_country = $this->billing_address_country;
        }

        if(!empty($this->shipping_location_id)) {
            if($this->shipping_location_id == 'new') {
                Location::createAddressforLocationType('shipping', $this);
            }else {
                $alt = BeanFactory::getBean('Locations', $this->shipping_location_id);
                $alt->updateAddressforLocationType('shipping', $this);
            }
        }
    }

    
    function displaySyncPanel($module){
        $mapping = SourceFactory::getSource('ext_rest_openbravo')->getMapping();
        return key_exists($module, $mapping['beans']);
    }
                        

    function labelsForLetters_pdf($template_id,$bean_id){
        $module = BeanFactory::getBean('Accounts',$bean_id);
        $table_header = '<div style="font-size: 10pt; font-weight: bold; font-family: helvetica;"><table border="0" style="border-color:red;"><tbody>';
        $table_row_start ='<tr>';
        $table_cell = '<td style="width: 65.5mm; height: 38.1mm; table-layout: fixed; padding-left:8px;padding-right:4px;">$contacts_full_name <br /> $contacts_primary_address_line1 <br /> $contacts_primary_address_line2 <br /> $contacts_primary_address_country</td>';
        $table_row_end='</tr>';
        $table_footer = '</tbody></table></div>';
        $printable = '';
        $query = "SELECT 
                    GROUP_CONCAT(c.id) as contactid,
                    CONCAT_WS(' ',c.primary_address_building_type, c.primary_address_number, c.primary_address_street) as primary_address_line1,
                    CONCAT_WS(' ',c.primary_address_city, c.primary_address_state, c.primary_address_postalcode) as primary_address_line2,
                    c.primary_address_country
                    from accounts a
                    left join accounts_contacts ac on ac.account_id = a.id and ac.deleted is false 
                    left join contacts c on c.id = ac.contact_id and c.deleted is false
                  WHERE
                    a.deleted is false
                  AND
                    CONCAT_WS(' ',c.primary_address_building_type, c.primary_address_number, c.primary_address_street) <> ''     
                  AND
                    a.id = '{$module->id}'
                  GROUP BY primary_address_line1 
                  ORDER BY 
                    c.last_name, c.first_name";
        $result = $module->db->query($query, true, "Error attempting to retrieve prospects");
        $pdf_rows = array();
        while($row = $module->db->fetchByAssoc($result)) {
            $contact_names = array();
            $Contacts = explode(",",$row['contactid']);
            foreach($Contacts as $bean_id) {
              $focus = new Contact;
              $focus->retrieve($bean_id,false);
              $contact_names[] = $focus->full_name;
            }
            if( count($contact_names) > 3) {
              $contact_names = array_slice($contact_names, 0, 3);
              $contact_names[2] = " ...";
            }

            //replace names
            $_table_cell = str_replace('$contacts_full_name', implode(",",$contact_names), $table_cell);
          
            //replace address
            $_table_cell = str_replace('$contacts_primary_address_line1', $row['primary_address_line1'], $_table_cell);
            $_table_cell = str_replace('$contacts_primary_address_line2', $row['primary_address_line2'], $_table_cell);
            $_table_cell = str_replace('$contacts_primary_address_country', $row['primary_address_country'], $_table_cell);
            
            $pdf_rows[] = $_table_cell;
        }
        $count=1;
        foreach ($pdf_rows as $pdf_row) {
            $str = '';
            if($count % 3 == 0){
                //start new row
                $pdf_row .= $table_row_end;
                if($count < count($pdf_rows)){
                    $str = $table_row_start;
                }
            }elseif($count==1){
                    $pdf_row = $table_row_start.$pdf_row;
            }

            if($count == count($pdf_rows)){
                $str = $table_row_end;
            }

            $printable .= str_replace("\n", "<br />", $pdf_row.$str);

            $count++;
        }

        return $printable = $table_header.$printable.$table_footer;
    }
}



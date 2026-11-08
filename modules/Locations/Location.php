<?php
/* * *******************************************************************************
 * (C) 2009 Enterprise Intelligence Systems Pty Ltd
 * No part of this file should be used without the express permission
 * of the authors.
 *
 * Contact: info@eintel.com.au for more information.
 *
 *
 * ****************************************************************************** */



class Location extends Basic
{
    var $new_schema = true;
    var $module_dir = 'Locations';
    var $object_name = 'Location';
    var $table_name = 'locations';
    var $importable = false;
    var $id;
    var $name;
    var $date_entered;
    var $date_modified;
    var $modified_user_id;
    var $modified_by_name;
    var $created_by;
    var $created_by_name;
    var $description;
    var $deleted;
    var $created_by_link;
    var $modified_user_link;
    var $assigned_user_id;
    var $assigned_user_name;
    var $assigned_user_link;
    var $account_id;
    var $account_name;
    var $building_type;
    var $number;
    var $address1;
    var $address2;
    var $city;
    var $state;
    var $postalcode;
    var $country;
    var $isbilling;
    var $isshipping;
    var $defaultinvoiceto;
    var $defaultshipto;
    var $erp_id;

    var $address_map = array(
            '_address_building_type' => 'building_type',
            '_address_number' => 'number',
            '_address_street' => 'address1',
            '_address_city' => 'city',
            '_address_state' => 'state',
            '_address_postalcode' => 'postalcode',
            '_address_country' => 'country',
        );
    var $type_map = array(
            'shipping' => 'billing',
            'alt' => 'primary'
        );

    function Location()
    {

        parent::Basic();
    }

    function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }

    function get_summary_text(){
        return "$this->name";
    }

    function save($check_notify = FALSE) {
        //if(empty($this->name)){
            //$this->name = "{$this->address1} {$this->address2} {$this->city} {$this->state} {$this->postalcode} {$this->country}";
            $this->name = ".{$this->city}, ". trim(trim($this->building_type." ".$this->number)." ".$this->address1);
        //}

        //update every account and every contact which uses this location as a billing, shipping, primary or alternate address
        if(!empty($this->id)){
            $this->updateAddressDetails('billing','accounts');
            $this->updateAddressDetails('shipping','accounts');
            $this->updateAddressDetails('primary','contacts');
            $this->updateAddressDetails('alt','contacts');
        }
        parent::save($check_notify);
    }


    function mark_deleted($id){
        //due to forgein  "c_bpartner_location" violates foreign key constraint
        //we can not delete the location in openbravo from sugar
//        if(module_enabled('ERPSync')){
//            if(!empty($this->erp_id) ){
//                if(ERPSync::getERPNotify()->delete_business_partner_location($this)){
//                    $GLOBALS['log']->debug('XML: Location mark as Obsolete in ERP.');
//                }else{
//                    $GLOBALS['log']->debug('XML: Unable to mark Location as Obsolete in ERP.');
//                     sugar_die("Unable to mark Location as Obsolete in ERP. Location has not been deleted in Sugar");
//                }
//            }
//        }
        
        //if the location is being used we need to prevent this location being deleted
        
        
            $this->updateAddressDetailsForDeletion('billing','accounts');
            $this->updateAddressDetailsForDeletion('shipping','accounts');
            $this->updateAddressDetailsForDeletion('primary','contacts');
            $this->updateAddressDetailsForDeletion('alt','contacts');
        parent::mark_deleted($id);



    }


    function updateAddressDetails($type,$table){
        $street = addslashes($this->address1);
        if(!empty($this->address2)){
            $street  = addslashes($this->address1."\n".$this->address2);
        }
        $query = "UPDATE {$table}
                    SET
                    ".$type."_address_building_type = '".addslashes($this->building_type)."',
                    ".$type."_address_number = '".addslashes($this->number)."',
                    ".$type."_address_street = '{$street}',
                    ".$type."_address_city = '".addslashes($this->city)."',
                    ".$type."_address_state = '".addslashes($this->state)."',
                    ".$type."_address_postalcode = '".addslashes($this->postalcode)."',
                    ".$type."_address_country = '".addslashes($this->country)."'
                    WHERE
                      {$table}.".$type."_location_id = '{$this->id}'
                    AND
                      {$table}.deleted is false;";
//        $query = "UPDATE {$table}
//                    JOIN (
//                            SELECT {$table}.id
//                            FROM {$table}
//                            WHERE
//                                {$table}.".$type."_location_id = '{$this->id}'
//                            AND
//                                {$table}.deleted is false
//                    ) data on data.id = {$table}.id
//                    SET
//                    ".$type."_address_street = '".addslashes($this->address1)."',
//                    ".$type."_address_city = '".addslashes($this->city)."',
//                    ".$type."_address_state = '".addslashes($this->state)."',
//                    ".$type."_address_postalcode = '".addslashes($this->postalcode)."',
//                    ".$type."_address_country = '".addslashes($this->country)."';";
        $this->db->query($query,true,"Error updating {$table} with location details: ");
    }

    function updateAddressDetailsForDeletion($type,$table){
        $query = "UPDATE {$table}
                    SET
                    ".$type."_location_id = '',
                    ".$type."_address_building_type = '',
                    ".$type."_address_number = '',    
                    ".$type."_address_street = '',
                    ".$type."_address_city = '',
                    ".$type."_address_state = '',
                    ".$type."_address_postalcode = '',
                    ".$type."_address_country = ''
                    WHERE
                      {$table}.".$type."_location_id = '{$this->id}'
                    AND
                      {$table}.deleted is false;";
//        $query = "UPDATE {$table}
//                    JOIN (
//                            SELECT {$table}.id
//                            FROM {$table}
//                            WHERE
//                                {$table}.".$type."_location_id = '{$this->id}'
//                            AND
//                                {$table}.deleted is false
//                    ) data on data.id = {$table}.id
//                    SET
//                    ".$type."_location_id = '',
//                    ".$type."_address_street = '',
//                    ".$type."_address_city = '',
//                    ".$type."_address_state = '',
//                    ".$type."_address_postalcode = '',
//                    ".$type."_address_country = '';";
        $this->db->query($query,true,"Error updating {$table} with location details: ");
    }

    function updateContactsAddresses(){

    }


    function return_location($type, $account_id){
        $locations = new self;

        $where = '';
        if($type == 'billing'){
            $where = ' AND isbilling is true';
        }
        if($type == 'shipping'){
            $where = ' AND isshipping is true';
        }

        return $locations->get_full_list("name",  "account_id='{$account_id}' {$where} ",true);
    }
    
    function retrieve_billing_locations($focus,$field,$value,$view){
        return self::return_locations_list_array('billing',$focus,$view);       
    }


    function retrieve_shipping_locations($focus,$field,$value,$view){
        return self::return_locations_list_array('shipping',$focus,$view);
    }
    
    function retrieve_locations($focus,$field,$value,$view){
        return self::return_locations_list_array(null,$focus,$view);       
    }
    
    //Get list of locations in a format array(id=>name)
    //Used for drop downs
    function return_locations_list_array($type, $focus, $view){
        if($view != 'EditView'){
            return;
        }
        $list = array('new' =>'new');
        if(isset($_REQUEST['isDuplicate']) && isset($_REQUEST['isDuplicate']) == 'true'){
            return $list;
        }
        if(is_a($focus, 'Account')){
            $account_id = $focus->id;
        }elseif(is_a($focus, 'Contact')){
            if(!empty($focus->account_id)){
                $account_id = $focus->account_id;
            }else{
                //if contact is not related to an account
                //retrieve the saved location to be used in the drop down list
                $location = BeanFactory::getBean('Locations', $focus->alt_location_id);
                if(isset($location->id)) {
                    $list[$location->id] = $location->name;
                }               
                return $list;
            }            
        }else{
            return $list;
        }
        $location_list = self::return_location($type, $account_id);
        if(!empty($location_list)){
            foreach($location_list as $location){
                $list[$location->id] = $location->name;
            }
        }
        return $list;
    }
    
    
    function splitAddress($address){
        $address_lines = array(
            'line1'=>'',
            'line2'=>'',
        );
        //split address on line breaks
        $address_array = explode("\n", trim($address));
        if( count($address_array) > 0) {
            $loops = (count($address_array) & 1) ? (count($address_array) / 2) + 0.5 : count($address_array) / 2;

            for($i=0;$i<$loops;$i++){
                $address_lines['line1'] .= (($i>0)?" ":null) .trim($address_array[$i]);
            }
            
            for($i=$i;$i<count($address_array);$i++){
                $address_lines['line2'] .= (($i>$loops)?" ":null) .trim($address_array[$i]);
            }
            
        }
        return $address_lines;
    }
    

    function updateAddressforLocationType($type, &$bean){
        //create new instance of a location object to gain access to the vars used in below loops

        
        if(is_a($bean, 'Account')){
            $this->updateAddressForAccount($type, $bean);
        }
        if(is_a($bean, 'Contact')){
            $this->updateAddressForContact($type, $bean);
        }
        return $bean;
        
        
        /*        
        if($bean->$id_field != 'new') {
            //option is a location from the list
            //or if shipping/alt it could be a copy of the bill/prim
            
            
//            if($type == 'shipping' || $type == 'alt'){
//                //check for copy
//                if(!empty($bean->{$location->type_map[$type].'_location_id'})){
//                    $location_oftype = BeanFactory::getBean('Locations', $bean->{$location->type_map[$type].'_location_id'});
//                    foreach($location->address_map as $bean_field => $location_field) {
//                        if($bean->{$type.$bean_field} != $location_oftype->$location_field){
//                            $is_copy = false;
//                            break;
//                        }else{
//                            $is_copy = true;
//                        }
//                    }
//                    if($is_copy){
//                        $location_oftype->isshipping = ($type == 'shipping' || $type == 'alt')?true:$location_oftype->isshipping;
//                        $location_oftype->defaultshipto = ($type == 'shipping')?true:$location_oftype->defaultshipto;
//                        $location_oftype->save();
//                        
//                        $bean->$id_field = $location_oftype->id;
//                    }
//                }
//            }
            
            
            
            
            
           //look for field changes
            if(trim($bean->{$type .'_address_street'}) == trim($this->address1.' '.$this->address2) &&
                    $bean->{$type .'_address_city'} == $this->city &&
                        $bean->{$type .'_address_state'} == $this->state &&
                            $bean->{$type .'_address_postalcode'} == $this->postalcode &&
                                $bean->{$type .'_address_country'} == $this->country &&
                                        (is_a($bean, 'Account')? $bean->id:$bean->account_id) == $this->account_id &&
                                            (is_a($bean, 'Account')? $this->{'is'.$type}:true) == true){
                                    //only update flags if we are in the context of an account
                                    //also return the new fields value in the bean to be sent to openbravo once saved
                                    (is_a($bean, 'Account')?$this->updateDefaultFlags($this->id,$type,$flag = true):null);
                                    if($type == 'shipping' || $type == 'alt'){
                                        //force the update of the bean table as when copying, the above will always be true
                                        //and the bean will never get the correct id
                                        $query = "UPDATE {$bean->table_name}
                                                SET ".$id_field." = '{$bean->$id_field}'
                                                WHERE {$bean->table_name}.id = '{$bean->id}' ;";

                                        $this->db->query($query,true,"Error updating {$bean->table_name} {$bean->name} with location details: ");
                                    }
                
            }else{
               
                if(empty($this->id)){
                    //this is to prevent the creation of null address when the a contact or any other bean is created via a subpanel 
                    //or for what ever reason the location does not have an id we do know want to create a null address.
                    return;
                }
                if(!is_a($this, 'Location')){
                    //make sure we are in the context of an location
                    //as we call save();
                    return;
                }
                                    

                
                //update location as atleast 1 field has changed
                $this->account_id = (is_a($bean, 'Account'))? $bean->id:$bean->account_id;
                $this->account_name = (is_a($bean, 'Account'))? $bean->name:$bean->account_name;
               
                $this->address1 = $bean->{$type .'_address_street'};
                $this->address2 = '';
                $this->city = $bean->{$type .'_address_city'};
                $this->state = $bean->{$type .'_address_state'};
                $this->postalcode = $bean->{$type .'_address_postalcode'};
                $this->country = $bean->{$type .'_address_country'};
                $this->name = ".{$this->city}, {$this->address1}";
                $this->isbilling = ($type == 'billing' || $type == 'primary')?true:$this->isbilling;
                $this->isshipping = ($type == 'shipping' || $type == 'alt')?true:$this->isshipping;
                $this->defaultinvoiceto = ($type == 'billing')?true:$this->defaultinvoiceto;
                $this->defaultshipto = ($type == 'shipping')?true:$this->defaultshipto;
                
                
                
                $this->save();
               
            }
        }else{
            //check for complete empty address fields
            if(trim($bean->{$type .'_address_street'}) == false &&
                trim($bean->{$type .'_address_city'}) == false &&
                    trim($bean->{$type .'_address_state'}) == false &&
                        trim($bean->{$type .'_address_postalcode'}) == false &&
                            trim($bean->{$type .'_address_country'}) == false){
                                //stop empty address being created and clear out current address
                                $bean->$id_field = null;
                                return $bean;
                            }
                            
              
            
            
            //need to do a check to see if the 2 address are the same
            //i.e billing/primary is new and shipping/alt was copied            
            $is_copy = false;
            if($type == 'shipping' || $type == 'alt'){
                if(!empty($bean->{$location->type_map[$type].'_location_id'})){
                    $location_oftype = BeanFactory::getBean('Locations', $bean->{$location->type_map[$type].'_location_id'});
                    foreach($location->address_map as $bean_field => $location_field) {
                        if($bean->{$type.$bean_field} != $location_oftype->$location_field){
                            $is_copy = false;
                            break;
                        }else{
                            $is_copy = true;
                        }
                    }
                    if($is_copy){
                        $location_oftype->isshipping = ($type == 'shipping' || $type == 'alt')?true:$location_oftype->isshipping;
                        $location_oftype->defaultshipto = ($type == 'shipping')?true:$location_oftype->defaultshipto;
                        $location_oftype->save();
                        
                        $bean->$id_field = $location_oftype->id;
                    }
                }
            }
            
            if(!$is_copy){
                
                $location->account_id = (is_a($bean, 'Account'))? $bean->id:$bean->account_id;
                $location->account_name = (is_a($bean, 'Account'))? $bean->name:$bean->account_name;
                $location->address1 = $bean->{$type .'_address_street'};
                $location->address2 = '';
                $location->city = $bean->{$type .'_address_city'};
                $location->state = $bean->{$type .'_address_state'};
                $location->postalcode = $bean->{$type .'_address_postalcode'};
                $location->country = $bean->{$type .'_address_country'};
                $location->isbilling = ($type == 'billing' || $type == 'primary')?true:false;
                $location->isshipping = ($type == 'shipping' || $type == 'alt')?true:false;
                $location->defaultinvoiceto = ($type == 'billing')?true:false;
                $location->defaultshipto = ($type == 'shipping')?true:false;
                $location->name = ".{$location->city}, {$location->address1}";
                $location->save();

                $bean->$id_field = $location->id;
            }
            // to prevent the bean being saved twice we need to update the bean via the database directly
           
            $query = "UPDATE {$bean->table_name}
                    SET ".$id_field." = '{$bean->$id_field}'
                    WHERE {$bean->table_name}.id = '{$bean->id}' ;";
                    
            $this->db->query($query,true,"Error updating {$bean->table_name} {$bean->name} with location details: ");
            
            return $bean;
        }
        */
    }

    function updateDefaultFlags($id,$type,$flag = false, $isFetched = false){
        if($id == null || $type == null){
            return;
        }
        $locations = new self; 
        $fields = array(
            'billing' => 'defaultinvoiceto',
            'shipping' => 'defaultshipto',
        );
        
        if(!$isFetched){
            if(is_a($this, 'Location')) {
                $this->$fields[$type] = $flag;
                return $this->save();
            }
        }
        
        
        $query = "UPDATE {$locations->table_name}
                    SET {$fields[$type]} = '{$flag}'
                    WHERE {$locations->table_name}.id = '{$id}';";       
                   
        return $locations->db->query($query,true,"Error updating locations {$fields[$type]} with false: ");
        
    }
    
    function is_location_in_use(){
        
        $query = "SELECT count(*)
                FROM accounts, contacts
                WHERE
                    accounts.shipping_location_id = '{$this->id}' OR
                    accounts.billing_location_id = '{$this->id}' OR
                    contacts.alt_location_id = '{$this->id}' OR
                    contacts.primary_location_id = '{$this->id}'
                AND
                    accounts.deleted is false
                AND
                    contacts.deleted is false;";
        
        $result = $this->db->query($query,true,"Error: ");
        $row = $this->db->fetchByAssoc($result);
        //return true:false;
    }
    
    function createAddressforLocationType($type, &$bean){
        $location = new Location();
        if(is_a($bean, 'Account')){
            $location->updateAddressForAccount($type, $bean);
        }
        if(is_a($bean, 'Contact')){
            $location->updateAddressForContact($type, $bean);
        }
        return $bean;
    }
    
    function updateAddressForAccount($type, &$bean){
         //create new instance of a location object to gain access to the vars used in below loops
        $location = new Location();    
        
        $id_field = $type .'_location_id';
        
        //if this value is not set
        //we need to assume its coming from a disabled 
        //selection and need to retrieve the other(right <-) location id
        
        //**Check to see if you are coming from a webserver call with rest data
        if(!isset($_REQUEST['rest_data']) && isset($location->type_map[$type])){
            $primary_location_id = $location->type_map[$type] . '_location_id';
            if(!isset($_REQUEST[$id_field]) && !is_a($bean, 'Location') && isset($_REQUEST[$primary_location_id])){
                $bean->$id_field = $bean->$primary_location_id;
            }
        }
        
        $address_lines = $location->splitAddress(trim($bean->{$type. '_address_street'}));
        
        
        
        if($bean->$id_field == 'new') { 
            //check for complete empty address fields
            $empty = true;
            foreach($location->address_map as $bean_field => $location_field) {
                if(trim($bean->{$type.$bean_field}) != false){
                    //set new value to location
                    if($location_field == 'address1'){
                        $this->address1 = $address_lines['line1'];
                        $this->address2 = $address_lines['line2'];
                    }else{
                        $this->$location_field = $bean->{$type.$bean_field};
                    }
                    $empty = false;                    
                }
            }
            if($empty){
                $bean->$id_field = null;
                return $bean;
            }
           
            //need to check if the new address is the same as the address on the right
            //but the copy address check box was not selected
            //i.e billing/primary is new and shipping/alt was copied            
            $is_copy = false;
            if($type == 'shipping'){
                if(!empty($bean->{$location->type_map[$type].'_location_id'})){
                    $location_oftype = BeanFactory::getBean('Locations', $bean->{$location->type_map[$type].'_location_id'});
                    foreach($location->address_map as $bean_field => $location_field) {
                        if($bean->{$type.$bean_field} != $location_oftype->$location_field){
                            $is_copy = false;
                            break;
                        }else{
                            $is_copy = true;
                        }
                    }
                    if($is_copy){
                        $location_oftype->isshipping = ($type == 'shipping')?true:$location_oftype->isshipping;
                        $location_oftype->defaultshipto = ($type == 'shipping')?true:$location_oftype->defaultshipto;
                        $location_oftype->save();
                        
                        $bean->$id_field = $location_oftype->id;
                    }
                }
            }
            
            //The address is not empty and is not a copy
            //create a new address 
            //add location values outside of the mapped fields
            if(!$is_copy){                
                $this->account_id = $bean->id;
                $this->account_name = $bean->name;
                $this->isbilling = ($type == 'billing')?true:false;
                $this->isshipping = ($type == 'shipping')?true:false;
                $this->defaultinvoiceto = ($type == 'billing')?true:false;
                $this->defaultshipto = ($type == 'shipping')?true:false;
                $this->name = ".{$this->city}, {$this->address1}";
                $this->save();

                $bean->$id_field = $this->id;
            }
            
           
        
            
        }else {
            //existing address has been selected from the list
            //look for field value changes in the existing address
            $change = false;
         
            foreach($location->address_map as $bean_field => $location_field) {
                if($location_field == 'address1'){
                    if(trim($address_lines['line1']) != trim($this->address1)){
                        $this->address1 = $address_lines['line1'];
                        $change = true;  
                    }
                    if(trim($address_lines['line2']) != trim($this->address2)){
                        $this->address2 = $address_lines['line2'];
                        $change = true;  
                    }
                }else{
                    if(trim($bean->{$type.$bean_field}) != trim($this->$location_field)){
                        //set new value to location
                        $this->$location_field = $bean->{$type.$bean_field};
                        $change = true;                    
                    }
                }
            }
            if(!$change && $this->{'is'.$type} == true){
                //only update flags if we are in the context of an account
                //also return the new fields value in the bean to be sent to openbravo once saved
                $this->updateDefaultFlags($this->id,$type,$flag = true);
            }
           
            if($change){   
                if(empty($this->id)){
                    //this is to prevent the creation of null address when the a contact or any other bean is created via a subpanel 
                    //or for what ever reason the location does not have an id we do know want to create a null address.
                    return;
                }
                if(!is_a($this, 'Location')){
                    //make sure we are in the context of an location
                    //as we call save();
                    return;
                }
                //update location values outside of the mapped fields
                $this->name = ".{$this->city}, {$this->address1}";
                $this->account_id = $bean->id;
                $this->account_name = $bean->name;               
                $this->isbilling = ($type == 'billing')?true:$this->isbilling;
                $this->isshipping = ($type == 'shipping')?true:$this->isshipping;
                $this->defaultinvoiceto = ($type == 'billing')?true:$this->defaultinvoiceto;
                $this->defaultshipto = ($type == 'shipping')?true:$this->defaultshipto;
                $this->save();               
            }
        
        }
        // to prevent the bean being saved twice we need to update the bean via the database directly
        $query = "UPDATE {$bean->table_name}
                SET ".$id_field." = '{$bean->$id_field}'
                WHERE {$bean->table_name}.id = '{$bean->id}' ;";

        $this->db->query($query,true,"Error updating {$bean->table_name} {$bean->name} with location details: ");

        return $bean;
    }

    function updateAddressForContact($type, &$bean){
         //create new instance of a location object to gain access to the vars used in below loops
        $location = new Location();    
        
        $id_field = $type .'_location_id';
        
        //if this value is not new or a value
        //we need to assume its coming from a disabled 
        //selection and need to retrieve the other location id
        //**Check to see if you are coming from a webserver call with rest data
        if(!isset($_REQUEST['rest_data']) && isset($location->type_map[$type])){
            $primary_location_id = $location->type_map[$type] . '_location_id';
            if(!isset($_REQUEST[$id_field]) && !is_a($bean, 'Location') && isset($_REQUEST[$primary_location_id])){
                $bean->$id_field = $bean->$primary_location_id;
            }
        }
        
        $address_lines = $location->splitAddress(trim($bean->{$type. '_address_street'}));
        
        if($bean->$id_field == 'new') {
            //check for complete empty address fields
            $empty = true;
            foreach($location->address_map as $bean_field => $location_field) {
                if(trim($bean->{$type.$bean_field}) != false){
                    //set new value to location
                    if($location_field == 'address1'){
                        $this->address1 = $address_lines['line1'];
                        $this->address2 = $address_lines['line2'];
                    }else{
                        $this->$location_field = $bean->{$type.$bean_field};
                    }
                    $this->$location_field = $bean->{$type.$bean_field};
                    $empty = false;                    
                }
            }
            
            
            if($empty){
               return $bean;
            }
            //need to check if the new address is the same as the address on the right
            //but the copy address check box was not selected
            //i.e billing/primary is new and shipping/alt was copied            
            $is_copy = false;
            if($type == 'shipping'){
                if(!empty($bean->{$location->type_map[$type].'_location_id'})){
                    $location_oftype = BeanFactory::getBean('Locations', $bean->{$location->type_map[$type].'_location_id'});
                    foreach($location->address_map as $bean_field => $location_field) {
                        if($bean->{$type.$bean_field} != $location_oftype->$location_field){
                            $is_copy = false;
                            break;
                        }else{
                            $is_copy = true;
                        }
                    }
                    if($is_copy){
                        $location_oftype->isshipping = ($type == 'shipping')?true:$location_oftype->isshipping;
                        $location_oftype->defaultshipto = ($type == 'shipping')?true:$location_oftype->defaultshipto;
                        $location_oftype->save();
                        
                        $bean->$id_field = $location_oftype->id;
                    }
                }
            }
            
            //The address is not empty and is not a copy
            //create a new address 
            //add location values outside of the mapped fields
            if(!$is_copy){                
                $this->account_id = $bean->account_id;
                $this->account_name = $bean->account_name;
                $this->isbilling = ($type == 'primary')?true:false;
                $this->isshipping = ($type == 'alt')?true:false;
                $this->name = ".{$this->city}, {$this->address1}";
                $this->save();

                $bean->$id_field = $this->id;
            }
            
        } else{    
            //existing address has been selected from the list
            //look for field value changes in the existing address
            $changed = false;
            foreach($location->address_map as $bean_field => $location_field) {
                if(trim($bean->{$type.$bean_field}) != trim($this->$location_field)){
                    //set new value to location
                    if($location_field == 'address1'){
                        $this->address1 = $address_lines['line1'];
                        $this->address2 = $address_lines['line2'];
                    }else{
                        $this->$location_field = $bean->{$type.$bean_field};
                    }
                        
                    $changed = true;                    
                }
            }
          
            if($changed){   
                if(empty($this->id)){
                    //this is to prevent the creation of null address when the a contact or any other bean is created via a subpanel 
                    //or for what ever reason the location does not have an id we do know want to create a null address.
                    return;
                }
                if(!is_a($this, 'Location')){
                    //make sure we are in the context of an location
                    //as we call save();
                    return;
                }
                //update location values outside of the mapped fields
                $this->account_id = $bean->account_id;
                $this->account_name = $bean->account_name;              
                $this->name = ".{$this->city}, {$this->address1}";
                $this->isbilling = ($type == 'billing')?true:$this->isbilling;
                $this->isshipping = ($type == 'shipping')?true:$this->isshipping;
                $this->save();               
            }
        }
        // to prevent the bean being saved twice we need to update the bean via the database directly
        $query = "UPDATE {$bean->table_name}
                SET ".$id_field." = '{$bean->$id_field}'
                WHERE {$bean->table_name}.id = '{$bean->id}' ;";

        $this->db->query($query,true,"Error updating {$bean->table_name} {$bean->name} with location details: ");

        return $bean;
    }
    
    
}

?>
    
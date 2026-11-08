<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/* * *******************************************************************************
 * (C) 2013 Enterprise Intelligence Systems Pty Ltd
 * No part of this file should be used without the express permission
 * of the authors.
 *
 * Contact: info@eintel.com.au for more information.
 *
 *
 * ****************************************************************************** */

class CampGroupBooking extends Basic {

	var $new_schema = true;
	var $module_dir = 'CampGroupBookings';
	var $object_name = 'CampGroupBooking';
	var $table_name = 'campgroupbookings';
	var $importable = true;
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
	var $type;
	var $start_date;
	var $end_date;
	var $contact_id;
	var $contact_name;
    var $primary_address_building_type;
    var $primary_address_number;
    var $primary_address_street;
    var $primary_address_city;
    var $primary_address_postalcode;
    var $primary_address_state;
    var $primary_address_country;
    
	var $update_vcal = true;
	var $additional_column_fields = array();

	function CampGroupBookings()	{
		parent::Basic();
	}


	function bean_implements($interface) {
		switch ($interface) {
			case 'ACL': return true;
		}
		return false;
	}
    
    function get_summary_text() {
        return "$this->contact_name";
    }
    
    function updateContactData(){
           
        $contact = BeanFactory::getBean('Contacts');
 
       
        $contact->salutation = $this->salutation;
        $contact->first_name = $this->first_name;
        $contact->last_name = $this->last_name;
        $contact->primary_address_building_type = $this->primary_address_building_type;
        $contact->primary_address_number = $this->primary_address_number;
        $contact->primary_address_street = $this->primary_address_street;
        $contact->primary_address_city = $this->primary_address_city;
        $contact->primary_address_postalcode = $this->primary_address_postalcode;
        $contact->primary_address_state = $this->primary_address_state;
        
        $contact->phone_mobile = $this->phone_mobile;
        $contact->phone_home = $this->phone_home;
        $contact->phone_work = $this->phone_work;         
        $contact->phone_other = $this->phone_other; 
        
        //if(!isset($contact->id)){
        $contact->save();
        //}
        //add email address to contact
        if(isset($this->email_address) && !empty($this->email_address)) {
            $contact->emailAddress->addAddress($this->email_address,true);
            $contact->emailAddress->save($contact->id, $contact->module_dir);
        }
        return $contact->id;
    }
    
    function save($check_notify=false) {
        if (empty($this->id)){
            //new booking so all participants should not have an id
            unset($_POST['campparticipants_id']);
        }
        if(empty($this->contact_id)) {
            //TODO:: 
            //attempt to match contact to an existing one
            //ask user if they wish to use the matched contact or create a new
//            $contact = BeanFactory::getBean('Contacts');
//            $contact->first_name = $this->first_name;
//            $contact->last_name = $this->last_name;
//            $contact->save();
            
            $this->contact_id = $this->updateContactData();
        }
        parent::save($check_notify);

        $this->save_lines($_POST, $this, 'campparticipants_');
        
        
    }
    
    function save_lines($post_data, $parent, $key = ''){
        $line_count = count($post_data[$key.'first_name']);
        $j = 0;
        for ($i = 0; $i < $line_count; ++$i) {
            if($post_data[$key.'deleted'][$i] == 1){
                BeanFactory::getBean('CampParticipants')->mark_deleted($post_data[$key.'id'][$i]);
            } else {
                //get existing or empty bean
                $participant = BeanFactory::getBean('CampParticipants',(!empty($post_data[$key.'id'][$i]))? $post_data[$key.'id'][$i]: null);
                foreach($participant->field_defs as $field_def) {
                    if(isset($post_data[$key.$field_def['name']][$i])){
                        $participant->$field_def['name'] = $post_data[$key.$field_def['name']][$i];
                    }
                }
                //only add lines with a name,  ignore empty rows
                if(trim($participant->first_name) != '') {
                    //$participant->cb_create_contact = (!empty($post_data[$key.'id'][$i]))? false: true;
                    $participant->booked_date = $parent->booked_date;
                    $participant->camp_id = $parent->camp_id;
                    $participant->campgroupbooking_id = $parent->id;
                    $participant->save();
                }
            }
        }
    }
}
?>
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
require_once('include/SugarObjects/templates/person/Person.php');
// Contact is used to store customer information.
class CampParticipant extends Person {

	var $new_schema = true;
	var $module_dir = 'CampParticipants';
	var $object_name = 'CampParticipant';
	var $table_name = 'campparticipants';
	var $importable = true;
	var $id;
	var $name;
    var $title;
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
    var $participant_age;
    var $phone_mobile;
    var $phone_home;
    var $phone_work;
    var $phone_other;
    //var $email_address;
    var $booked_date;
    var $how_booked;
    var $allergy;
    var $medical;
    var $comments;
    var $payment_type;
    var $amount_paid;
    var $amount_owing;
    var $date_paid;
    var $confirmed;
    var $purchase_cap;
    var $purchase_photo;
    var $purchase_ball;
    var $purchase_mini_bat;
    var $purchase_academy;
    var $additional_purchases;
    var $confirmation;
    var $medical_condition;
    var $add_to_target_list = false;
    var $friend_first_name;
    var $friend_surname;
    var $friend_fullname;
    var $club_name;
    var $gender;
    var $emailAddress;
    
    var $update_vcal = true;
    var $update_contact = true;
    var $additional_column_fields = array('campgroup_name');

	function CampParticipant()	{
		parent::Person();
	}
    
	function bean_implements($interface) {
		switch ($interface) {
			case 'ACL': return true;
		}
		return false;
	}
    
    function get_summary_text() {
      if(empty($this->contact_name)) {
        return "{$this->first_name} {$this->last_name}";
      } else {
        return "$this->contact_name";
      }
    }
    
    function updateContactData() {
        $id = $this->contact_id;      
            
        $contact = BeanFactory::getBean('Contacts',$id);

        $contact->salutation = $this->salutation;
        $contact->first_name = $this->first_name;
        $contact->last_name = $this->last_name;
        $contact->primary_address_building_type = $this->primary_address_building_type;
        $contact->primary_address_number = $this->primary_address_number;
        $contact->primary_address_street = $this->primary_address_street;
        $contact->primary_address_city = $this->primary_address_city;
        $contact->primary_address_postalcode = $this->primary_address_postalcode;
        $contact->primary_address_state = $this->primary_address_state;
        $contact->primary_address_country = $this->primary_address_country;

        $contact->phone_mobile = $this->phone_mobile;
        $contact->phone_home = $this->phone_home;
        $contact->phone_work = $this->phone_work;         
        $contact->phone_other = $this->phone_other; 

        $contact->skip_participant_save = true;

        $contact->in_workflow = true;

        $contact->save();
        
        $contact->emailAddress->addAddress($this->email1,true,false,$this->invalid_email,$this->email_opt_out);
        $contact->emailAddress->save($contact->id, $contact->module_dir, null,'','','','',false);

       
        
        return $contact->id;
    }
    
    function save($check_notify=false) {
        $resave = false;
        if((isset($_POST['action']) && $_POST['action'] == 'ConvertLead')  || (empty($this->contact_id) && empty($this->id))){
            $this->create_contact = true;
        }

        $this->additional_purchases = $this->additional_purchases();
        $this->friend_fullname = trim(implode(" ", array($this->friend_first_name,$this->friend_surname)));

        
        parent::save($check_notify);
        
        //create/update contact
        //dont update if relationship change
        if(empty($GLOBALS['resavingRelatedBeans']) && $this->update_contact){
            $this->contact_id = $this->updateContactData();
        }
        
        //set contact id
        $create_contact = false;
        if(isset($this->create_contact) && $this->create_contact){
            $create_contact = true;
            parent::save($check_notify);
        }
        
        $this->add_remove_participant_targetList($create_contact);        
    }
    
    function add_remove_participant_targetList($new) {
        //Add/Remove from target Lists
        if(isset($this->fetched_row['camp_id'])) {
          
           if(isset($this->fetched_row['camp_id']) && $this->fetched_row['camp_id'] != $this->camp_id) {
              $contact = BeanFactory::getBean('Contacts',$this->contact_id);
              $camp = BeanFactory::getBean('Camps',$this->fetched_row['camp_id']);    
              $prospect_list = $camp->get_linked_beans('prospect_lists','ProspectList');
              //remove from old camp
              foreach($prospect_list as $list) {
                 if($list->camp_master_list == '1') {
                    if($list->load_relationship('contacts')){
                        $list->contacts->delete($list->id,$contact->id);
                    }
                 }
              }
              
              $new_camp = BeanFactory::getBean('Camps',$this->camp_id);    
              $new_prospect_list = $new_camp->get_linked_beans('prospect_lists','ProspectList');
              //add to new camp
              foreach($new_prospect_list as $new_list) {
                 if($new_list->camp_master_list == '1') {
                    if($new_list->load_relationship('contacts')){
                        $added = $new_list->contacts->add($contact->id);
                    }
                 }
              }
            }  
            if(isset($this->fetched_row['contact_id']) && $this->fetched_row['contact_id'] != $this->contact_id) {
              $original_contact = BeanFactory::getBean('Contacts',$this->fetched_row['contact_id']);
            }
        } elseif($new === true) {
            //new participant
            $contact = BeanFactory::getBean('Contacts',$this->contact_id);
            $new_camp = BeanFactory::getBean('Camps',$this->camp_id);    
            $new_prospect_list = $new_camp->get_linked_beans('prospect_lists','ProspectList');
            //add to new camp
            foreach($new_prospect_list as $new_list) {
               if($new_list->camp_master_list == '1') {
                  if($new_list->load_relationship('contacts')){
                      $added = $new_list->contacts->add($contact->id);
                  }
               }
            }
        }
    }
    
    function mark_deleted($id) {
        //delete from target list
        $camp = BeanFactory::getBean('Camps',$this->camp_id);    
        $prospect_list = $camp->get_linked_beans('prospect_lists','ProspectList');
        if(isset($prospect_list)) {
            foreach($prospect_list as $list) {
                //remove contact from all lists linked to a camp
                if($list->load_relationship('contacts')){
                    $list->contacts->delete($list->id,$this->contact_id);
                }
            }
        }
        parent::mark_deleted($id);
        
    }
    //create additional purchases string
    function additional_purchases() {
        $purchases = array();
        if($this->purchase_cap) {
            $purchases[] = 'Cap';
        }
        if($this->purchase_photo) {
            $purchases[] = 'Photo';
        }
        if($this->purchase_ball) {
            $purchases[] = 'Ball';
        }
        if($this->purchase_mini_bat) {
            $purchases[] = 'Bat';
        }
        if($this->purchase_academy) {
            $purchases[] = 'Academy';
        }
        return implode(", ", $purchases);
    }
    
    function hasContact(){
        $display = false;
        if(empty($this->contact_id)) {
            $display = true;
        }
        return $display;
    }
    
    
    
}
?>
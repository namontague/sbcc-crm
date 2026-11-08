<?php
/*
 * Class to Process a record for sms lists.
 */
class logic_hooks_class {
    /*
     * Adds to new list
     */
    function after_relationship_add_method(&$bean, $event, $arguments)   {
        //contact related
        if($arguments['relationship'] == "participants_camp" && 
                $arguments['related_module'] == "Camps") {
            //adding new contact
            if($event == 'after_relationship_add') {
                //create a sms list for this camp
                $camp = BeanFactory::getBean("Camps",$arguments['related_id']);
                //add all targets to this list
                if(isset($bean) ) {
                    if($bean->camp_master_list != true){
                        $participants = $camp->get_linked_beans('campparticipants','CampParticipants');
                        $bean->load_relationship('contacts');
                        foreach($participants as $participant) {
                            if(!empty($participant->contact_id)) {
                                $bean->contacts->add($participant->contact_id);
                            }
                        }
                    }
                }
            } 
        }
    }
    /*
     * Remove from old lists
     */
    function after_relationship_remove_method(&$bean, $event, $arguments)   {
        //contact related
        if($arguments['relationship'] == "participants_camp" && 
                $arguments['related_module'] == "Camps") {
            //adding new contact
            if($event == 'after_relationship_remove') {
                //create a sms list for this camp
                $camp = BeanFactory::getBean("Camps",$arguments['related_id']);
                //add all targets to this list
                if(isset($bean) ) {
                    if($bean->camp_master_list != true){
                        $participants = $camp->get_linked_beans('campparticipants','CampParticipants');
                        $bean->load_relationship('contacts');
                        foreach($participants as $participant) {
                            if(!empty($participant->contact_id)) {
                                $bean->contacts->add($participant->contact_id);
                            }
                        }
                    }
                }
            } 
        }
    }

    
    //Executes after a record has been retrieved from the database.
    function after_retrieve_method(&$bean, $event, $arguments) {
        //get contact fields
        if (!empty($bean->contact_id)) {
            $contact = BeanFactory::getBean('Contacts', $bean->contact_id); // Module is your related module, such as 'Accounts'
            $this->override_with_contact($bean,$contact);
        }
    }
    
    
    //Executes when the record is being processed as a part of the ListView or subpanel list.
    function process_record_method($bean, $event, $arguments) {
        //get contact fields
        if (!empty($bean->contact_id)) {
            $contact = BeanFactory::getBean('Contacts', $bean->contact_id); // Module is your related module, such as 'Accounts'
            $this->override_with_contact($bean,$contact);
        }
    }
        
    function override_with_contact(&$bean,$contact){
        $contact_fields = array(
            'salutation',
            'first_name',
            'last_name',
            'primary_address_building_type',
            'primary_address_number',
            'primary_address_street',
            'primary_address_city',
            'primary_address_postalcode',
            'primary_address_state',
            'primary_address_country',
            'phone_mobile',
            'phone_home',
            'phone_work',
            'phone_other',
            //'email',	
            //'email1',		
            //'email2',	
            //'invalid_email',	
           // 'email_opt_out'	
        );   
        //$email?
        foreach($contact_fields as $field) {
            if(isset($bean->{'contacts_'.$field.'_c'})) {
                $bean->{'contacts_'.$field.'_c'} = $contact->$field;
            }
            if(isset($bean->{$field})) {
                $bean->{$field} = $contact->$field;
            }
        }
    }
    
}

?>
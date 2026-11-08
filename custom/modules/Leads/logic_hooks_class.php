<?php
/**
 * Contact logic hooks
 */
class lead_logic_hooks {

  /**
   * After save method
   */
  function after_save_method(&$bean, $event, $arguments) {

    if((isset($bean->lead_source) && $bean->lead_source == 'Facebook') && $bean->modified_by_name = "zapier" && $bean->status == 'New') {
      //convert to participant

      //get camp by name
      $camp = BeanFactory::getBean('Camps')->retrieve_by_string_fields(array("name"=>$bean->department));
      if(is_null($camp)) {
        //create camp if not found
        $camp = BeanFactory::getBean('Camps');
        $camp->name = $bean->department;
        $camp->save();
      }
      if($camp != null){
        //get participant details//creating if not found
        if(($participant = $this->createParticipant($bean,$camp))!=null) {
          //add $participant to $camp
          //includes updating db record to booked if relation is added
          $this->addParticitpantToCamp($participant, $camp,$row['file_name'],$participant_data);
          $participants[] = $participant->id;
        }

        //Mark the original Lead converted
        if (!empty($bean)) {
          $bean->status = "Converted";
          $bean->converted = '1';
          $bean->in_workflow = true;
          $bean->save();
        }
      }
    }
  }

  function createParticipant($lead,$camp) {
    global $db, $timedate;

    $update_contact = false;
    $fields = array(
      //'salutation' => $line_converted['TITLE'],
      'first_name'=> $lead->first_name,
      'last_name' => $lead->last_name,
      'email1' => $lead->email1,
      'phone_mobile' => $lead->phone_mobile,
      'comment' => $lead->description,
    );

    //create a new contact for this participant
    $contact = BeanFactory::getBean('Contacts');
    $contact->first_name = $fields['first_name'];
    $contact->last_name = $fields['last_name'];
    $contact->phone_mobile = $fields['phone_mobile'];

    $contact->save();
    //add email address to contact
    if(isset($fields['email1'])) {
      $sea = new SugarEmailAddress;
      // Add a primary email address
      $sea->addAddress($fields['email1'], true);
      $sea->save($contact->id, $contact->module_dir);
    }

    //create participant record
    $participant = BeanFactory::getBean('CampParticipants');
    $participant->contact_id = $contact->id;
    $participant->first_name = $fields['first_name'];
    $participant->last_name = $fields['last_name'];
    $participant->phone_mobile = $fields['phone_mobile'];
    $participant->comments = $fields['comment'];

    $participant->booked_date = $timedate->nowDb();

    //create contact
    $participant->cb_create_contact = $update_contact;
    $participant->update_contact = false;
    //this flag will stop the email address being deleted
    $participant->in_workflow = true;

    $participant->save();
    //add email address to participant
    if(isset($fields['email1'])) {
      $sea = new SugarEmailAddress;
      // Add a primary email address
      $sea->addAddress($fields['email1'], true);
      $sea->save($participant->id, $participant->module_dir);
    }
    return $participant;
  }

  function addParticitpantToCamp($participant,$camp) {
    global $db;
    if ($camp->load_relationship('campparticipants')) {
      $participant->update_contact = false;
      $participant->cb_create_contact = false;
      $participant->add_to_target_list = true;

      $camp->campparticipants->add($participant->id);
    }
  }
}



?>
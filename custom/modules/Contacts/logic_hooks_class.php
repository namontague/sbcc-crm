<?php
/**
 * Contact logic hooks
 */
class contact_logic_hooks {

    /**
     * After save method
     */
    function after_save_method(&$bean, $event, $arguments) {
        //get related participants
        if(isset($bean->skip_participant_save) && $bean->skip_participant_save == true) {
            return;
        }else{
            $participants = $bean->get_linked_beans('participants', 'CampParticipants');
            foreach($participants as $participant) {
                $participant->update_contact = false;
               
                $participant->in_workflow = true;
                $participant->save();
                 if(isset($bean->email1) && !empty($bean->email1)) {                    
                    $participant->emailAddress->addAddress($bean->email1,true,false,$bean->invalid_email,$bean->email_opt_out);
                    $participant->emailAddress->save($participant->id, $participant->module_dir, null,'','','','',false);
                }
            }
        }
    }

    
}

?>
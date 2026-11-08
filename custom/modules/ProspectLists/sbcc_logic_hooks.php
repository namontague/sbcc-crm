<?php
/*
 * Class to Process a record for sms lists.
 */
class SBCC_ProspectLists_hooks {
    /*
     * Adds all the contacts related to a camp to a target list
     */
     function AddtoList(&$bean, $event, $arguments)   {

        //contact related
        if($arguments['relationship'] == "prospect_list_camps" &&
                $arguments['related_module'] == "Camps") {
            //increase time for this hook as some camps have 1,000s on participants
            //ini_set('max_execution_time', '300');
            //adding new contact
            if($event == 'after_relationship_add') {
                //create a sms list for this camp
                $camp = BeanFactory::getBean("Camps",$arguments['related_id']);
                //add all targets to this list
                if(isset($bean) ) {
                    $age_groups = null;
                    $gender = null;
		      //$GLOBALS['log']->error($_POST['search_results']);

                    if(isset($_POST['search_results'])) {   
                        $params = explode(';', $_POST['search_results']);
                        foreach($params as $param) {
                            if(strpos($param, "age_groups_advanced")!==false) {                                
                                $age_groups = str_replace('age_groups_advanced[]:', '', $param);
                                if($age_groups == 'null') {
                                    $age_groups = null;
                                } else{
                                    global $timedate;
                                    $date1 = date_create();
                                    $age_groups = explode(',', $age_groups);
                                }
                                break;
                            }
                            if(strpos($param, "participant_gender_advanced")!==false) {
                              $gender = str_replace('participant_gender_advanced[]:', '', $param);
                              if($gender == 'null') {
                                $gender = null;
                              }
                              break;
                            }
                        }
                    }                                              
                    if($bean->camp_master_list != true){                      
                        $participants = $camp->get_linked_beans('campparticipants','CampParticipants');
                        $bean->load_relationship('contacts');
                        foreach($participants as $participant) {                            
                            if(!empty($participant->contact_id)) {
                                if(!is_null($age_groups) && !is_null($gender)) {
                                  if($participant->participant_age > '0' && $participant->gender == $gender) {
                                    $date2 = $timedate->fromUser($participant->date_entered);
                                    $diff = date_diff($date1,$date2);
                                    $days = $diff->days;
                                    if(isset($days) && $days > 0) {
                                      $years = $days/365;
                                      $estimate_age = $participant->participant_age + round($years, 0, PHP_ROUND_HALF_DOWN);
                                      if(in_array($estimate_age, $age_groups)) {
                                        $bean->contacts->add($participant->contact_id);
                                      }
                                    }
                                  }
                                } elseif(!is_null($age_groups)) {
                                    if($participant->participant_age > '0') {
                                        $date2 = $timedate->fromUser($participant->date_entered);
                                        $diff = date_diff($date1,$date2);
                                        $days = $diff->days;
                                        if(isset($days) && $days > 0) {
                                            $years = $days/365;
                                            $estimate_age = $participant->participant_age + round($years, 0, PHP_ROUND_HALF_DOWN);
                                            
                                            //$GLOBALS['log']->error("Estimated age: " . $estimate_age .", Actual value: ". $participant->participant_age);
                                            
                                            if(in_array($estimate_age, $age_groups)) {
                                                $bean->contacts->add($participant->contact_id);
                                            }
                                        }
                                    }
                                } elseif(!is_null($gender)) {
                                  if($participant->gender == $gender) {
                                    $bean->contacts->add($participant->contact_id);
                                  }
                                } else{
                                    $bean->contacts->add($participant->contact_id);
                                }
                            }
                        }
                    }
                }
            } 
        }
    }
}


<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');


require_once('include/MVC/View/SugarView.php');

class ProspectListsViewResetTargets extends SugarView {

    function ProspectListsViewResetTargets() {
        parent::SugarView();
    }

    function process() {
        $this->display();
    }

    function display() {
        if (isset($_REQUEST['record_id'])) {
            ini_set('memory_limit', '512M');
            $focus = BeanFactory::getBean('ProspectLists', $_REQUEST['record_id']);
            $focus->load_relationship('contacts');

            $targets = $focus->get_linked_beans('contacts', 'Contacts');
            $target_ids = array();
            foreach ($targets as $target) {
                $target_ids[$target->id] = $target->id;
            }

            $focus->load_relationship('prospect_list_camps');

            $camps = $focus->get_linked_beans('camps', 'Camps');
            $contacts = array();
            foreach ($camps as $camp) {
                $participants = $camp->get_linked_beans('campparticipants', 'CampParticipants');
                foreach ($participants as $participant) {
                    if (!isset($contacts[$participant->contact_id])) {
                        $contacts[$participant->contact_id] = $participant->contact_id;
                    }
                }
            }
            if(!empty($contacts)) {
              $add = array_diff_key ($contacts,$target_ids);
              $delete = array_diff_key ($target_ids , $contacts);

              if(!empty($delete)) {
                foreach($delete as $did) {
                  $focus->contacts->delete($focus->id,$did);
                }
              }
              if(!empty($add)) {
                foreach($add as $aid) {
                  $focus->contacts->add($aid);
                }
              }
            } 
            $success = true;
        }        
        
        if ($success) {
            $message = "</br>Resetting Lists complete";
            $data_return = array(
                'success' => array(
                  'msg' => $message
                )
            );
        } else {
            $data_return = array(
                'error' => array(
                    'msg' => 'List record id missing'
                )
            );
        }
         if($data_return == null){
            return false;
        }else{
            $json = getJSONobj();
            $decoded = array_map('from_html', $data_return); // Need to decode from html as the db manager encodes values by default
            $out = $json->encode($decoded);
            echo($out);
        }
    }

}

?>

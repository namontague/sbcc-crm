<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
 *
 *
 *
 * This class overrides SugarView and provides an implementation for the AgentDetails method
 * used to retrieve details about the agent for this account.
 * We take advantage of the MVC framework to provide this action which is invoked from
 * a javascript AJAX request.
 * */

require_once('include/MVC/View/SugarView.php');

class CampGroupsViewCreateGroups extends SugarView {

 	function CampGroupsViewCreateGroups(){
        parent::SugarView();
 	}

 	function process() {
		$this->display();
 	}

 	function display(){
        $sort_by = $_REQUEST['sort_by'];
        $group_size = $_REQUEST['group_size'];
        
        
        $camp = BeanFactory::getBean('Camps', $_REQUEST['camp_id']);
        //get all participants
        $camp->load_relationship('campparticipants');
        $participants = $camp->campparticipants->getBeans();
        
        //$sort_by
        if($sort_by == 'contact_name'){
            usort( $participants, array( $this , "cmp_participantName" ) ) ;
        }else{
            //default sort by to age
            usort( $participants, array( $this , "cmp_participantAge" ) ) ;
        }
        
        $group_name = 'A';
        $groups_created = 0;
        while($participants != null) {
            $new_group = BeanFactory::getBean('CampGroups');
            $new_group->camp_id = $camp->id;
            $new_group->camp_name = $camp->name;
            $new_group->name = $group_name;
            $new_group->save();
            
            $new_group->load_relationship('participants');
                
            for ($i = 0; $i < $group_size; $i++) {
                //add $group_size number of participants to the new group
                $participant = array_shift($participants);
                $new_group->participants->add($participant->id);
            }
            ++$group_name;
            ++$groups_created;
        }
        if($groups_created > 0){
            $data = array(
                'created' => "Created {$groups_created} groups."
                );
        }else{
            return false;
        }
        
        ob_clean();
        $json = getJSONobj();
        $decoded = array_map('from_html', $data); // Need to decode from html as the db manager encodes values by default
        $out = $json->encode($decoded);
        echo($out);        
        
 	}
    
    function cmp_participantAge($a, $b ) {
        if( $a->participant_age ==  $b->participant_age ) return 0 ;
        return ($a->participant_age >  $b->participant_age ) ? 1 : -1;
    }

    function cmp_participantName($a,$b) {
	  return strcmp($a->first_name, $b->first_name);
	}
}

?>

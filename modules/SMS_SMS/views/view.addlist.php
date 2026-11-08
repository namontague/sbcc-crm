<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');


require_once('include/MVC/View/SugarView.php');

class SMS_SMSViewAddList extends SugarView {

 	function SMS_SMSViewAddList(){
            parent::SugarView();
 	}

 	function process() {
            $this->display();
 	}

 	function display(){
            require_once('modules/SMS_SMS/SMSFunctions.php');
            if(isset($_REQUEST['parent_id'],$_REQUEST['parent_module'])) {
                $focus = BeanFactory::getBean($_REQUEST['parent_module'],$_REQUEST['parent_id']);
            }
            $data =null;
            $data_return = array();
            //create a sms list for this camp
            //$this = camp/target list
            
            if(!empty($focus->sms_list_id)){
                $data_return = array(
                            'EXISTS' => array(
                                'msg' => 'list already exists'
                            )
                        );
                echo json_encode($data_return);
                return;
            }
            
            $name = $focus->name;
            $name .= isset($focus->start_date)?" ".$focus->start_date:'';

            if(($data = AddListFromButton($name)) != null){
            //if($data!= null){    
                if($data->error->code=='SUCCESS') {
                    if($data->id){
                        $focus->sms_list_id = $data->id;
                        $focus->save();
                        if($_REQUEST['parent_module'] == 'Camps'){
                            $this->addListtoTargetList($focus);
                        }
			//add members??
                        //$members_return = $this->addMembers($focus);
                        
                        
                        $data_return = array(
                            'SUCCESS' => array(
                                'id' => $data->id,
                                'name' => $data->name,
                            )
                        );
                        echo json_encode($data_return);
                    }else{
                        $data_return = array(
                            'ERROR' => array(
                                'msg' => $data->error->description
                            )
                        );
                        echo json_encode($data_return);
                    }
                } else  {
                    $data_return = array(
                        'ERROR' => array(
                            'msg' => $data->error->description
                        )
                    );
                    echo json_encode($data_return);
                }        
            }
            if($data == null){
                return false;
            }
 	}
        
        function addMembers($camp){
            //add all targets to this list
            $prospect_list = $camp->get_linked_beans('prospect_lists','ProspectList');
            if(isset($prospect_list) && count($prospect_list) ==1  ) {
                if($prospect_list[0]->camp_master_list) {
                    //this is a master list used for sms
                    $targets = $prospect_list[0]->get_linked_beans('contacts','Contacts');
                    foreach($targets as $target) {
                        $attempts = 1;
                        while (($res = addToList($camp->sms_list_id,$target))!=true){
                            //$res = http result
                            switch ($res->error->code) {
                                case 429:
                                    //Too Many Requests//OVER_LIMIT
                                    sleep(2);  
                                    break;
                                case 401: //You have not provided auth data//AUTH_FAILED_NO_DATA
                                          //The auth data you have provided is invalid//AUTH_FAILED
                                case 400:
                                    //The mobile provided is invalid//BAD_MOBILE
                                    //The country of the mobile provided is unavailable//BAD_MOBILE_COUNTRY
                                    //The recipient provided was previously opted-out//OPTOUT
                                    //The recipient provided was previously deleted//DUPLICATE_DELETED
                                    $attempts = 6;
                                    break;
                                default:
                                    break;
                            }
                            if($attempts > 5){
                                //only try 5 times for each number
                                //unless 401/400 error
                                break;
                            }
                            $attempts++;
                        }
                    }                         
                }
            }
        }
        
        function addListtoTargetList($focus) {
            $list = null;
            $prospect_list = $focus->get_linked_beans('prospect_lists','ProspectList');
            if(isset($prospect_list) && count($prospect_list) > 1) {
                foreach($prospect_list as $p_list){
                    if($p_list->camp_master_list){
                        $list = $p_list;
                        break;
                    }
                }
            }elseif(isset($prospect_list) && count($prospect_list) ==1) {
                $list = $prospect_list[0];
            }
            if(!is_null($list)){
                $list->sms_list_id = $focus->sms_list_id;
                $list->save();
            }
        }
}

?>

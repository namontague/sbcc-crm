<?php
/*
 * Class to Process a record for sms lists.
 */
class sms_hooks {
    
    function ManageList(&$bean, $event, $arguments)   {
        //contact related
        if($arguments['relationship'] == "prospect_list_contacts" && 
                $arguments['related_module'] == "Contacts" && module_enabled('SMS_SMS')) {
            require_once('modules/SMS_SMS/SMSFunctions.php');
            $oBean = BeanFactory::getBean("ProspectLists",$arguments['id']);
            
            if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'resetTargets') {
                //skip hook, let 
                return;
            }
            
            //adding new contact
            if($event == 'after_relationship_add') {
                $sms_list_id = null;
                if($oBean->camp_master_list == true){
                    //this is a master list used for sms
                    $camp = $oBean->get_linked_beans('camps','Camp');
                    if(count($camp) > 1){
                         //error there should only be 1 camp related to a master list
                         return;
                    }
                    if(!empty($camp[0]->sms_list_id)) {
                          $sms_list_id = $camp[0]->sms_list_id;
                    }
                }else if(isset($oBean->sms_list_id) && !empty($oBean->sms_list_id)) {
                    $sms_list_id = $oBean->sms_list_id;
                }
                if(!is_null($sms_list_id)){
                    $contact = BeanFactory::getBean('Contacts',$arguments['related_id']);
                    addToList($sms_list_id,$contact);
                }
            } 
            //adding removing contact
            if($event == 'after_relationship_delete') {
                $sms_list_id = null;
                if($oBean->camp_master_list == true) {
                    //this is a master list used for sms
                    $camp = $oBean->get_linked_beans('camps','Camp');
                    if(count($camp) > 1) {
                         //error there should only be 1 camp related to a master list
                         return;
                    }
                    if(!empty($camp[0]->sms_list_id)) {
                          $sms_list_id = $camp[0]->sms_list_id;
                    }
                }else if(isset($oBean->sms_list_id) && !empty($oBean->sms_list_id)) {
                    $sms_list_id = $oBean->sms_list_id;
                }
                if(!is_null($sms_list_id)){
                    $contact = BeanFactory::getBean('Contacts',$arguments['related_id']);
                    deleteFromList($sms_list_id,$contact);
                }
            }  
        }
    }
    
    function before_save_method($bean, $event, $arguments)  {
        //store as a new bean property
        $bean->stored_fetched_row_c = $bean->fetched_row;
    }
    //after_save
    function UpdateListMember($bean, $event, $arguments) {
      if($event== "after_save" && $bean->module_name == "Contacts") {
        global $sugar_config;
        if($sugar_config['SMS_ACTIVE']) {
          require_once('modules/SMS_SMS/SMSFunctions.php');
          //find if first/last/mobile has changed   
          $fname = $bean->fetched_row['first_name'];
          $lname = $bean->fetched_row['last_name'];
          $mobile = $bean->fetched_row['phone_mobile'];
          
          $optout = $bean->stored_fetched_row_c['do_not_sms'];
          
          if(($optout != $bean->do_not_sms ) && $bean->do_not_sms = true){
            optOutMemberAllLists($bean);
          } else if($fname != $bean->first_name|| $lname != $bean->last_name || $mobile != $bean->phone_mobile) {
            //update sms records
            $lists = $bean->get_linked_beans('prospect_lists','ProspectLists');
            foreach($lists as $list) {
              if(isset($list->sms_list_id)) {
                if($mobile != $bean->phone_mobile) {
                  //mobile number changed
                  //need to delete old one and add new one
                  deleteFromList($list->sms_list_id, (object)array('phone_mobile'=>$mobile));
                  
                  addToList($list->sms_list_id, $bean);
                } else{
                  updateListMember($list->sms_list_id, $bean);
                }
              }
            }
          }
        }
      }
    }
}


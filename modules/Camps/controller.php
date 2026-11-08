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

require_once('include/MVC/Controller/SugarController.php');

class CampsController extends SugarController
{
    /*
     * Handle Booking files
     * Create records based on those files
     * 
     * 
     * Load up a dir listing and work out which files have not been imported before
     * 
     * Grab those new bookings and process
     *  
     */
     
    #http://sbcc.crm.com.au/index.php?module=Camps&action=handleCampBookings
    function action_handleCampBookings()	{
        //$this->bean->schedulejob_handleCampBookings();
        $this->bean->schedulejob_retireveWooCommerceOrders();

    }

    #http://sbcc.crm.com.au/index.php?module=Camps&action=processCampBookings
    function action_processCampBookings()	{
        //   $this->bean->schedulejob_handleCampBookings();
        $this->bean->schedulejob_retireveWooCommerceOrders();

    }
    
    //Only used to create list and populate with targets as the camp and targets already existed 
    //before the auto add lists and contacts was created
    function action_createSMSLists() {
        if(module_enabled('SMS_SMS') ){
            require_once('modules/SMS_SMS/SMSFunctions.php');
            $query = "SELECT id 
                      FROM camps 
                      WHERE 
                       deleted is false
                      AND
                       start_date > DATE_FORMAT(CONCAT(now()), '%Y-%m-%d') 
                      AND
                       sms_list_id = '' 
                      ORDER BY start_date";
            $result = $this->bean->db->query($query, true, "Error attempting to retrieve camps");
              
            while($row = $this->bean->db->fetchByAssoc($result)) {
                //create a sms list for this camp
                $camp = BeanFactory::getBean("Camps",$row['id']);
                if(($camp->sms_list_id = addList($camp)) != null) {
                    $camp->save();
                }
                //add all targets to this list
                $prospect_list = $camp->get_linked_beans('prospect_lists','ProspectList');
                if(isset($prospect_list) && count($prospect_list) >1) {
                    foreach($prospect_list as $p_list){
                        if($p_list->camp_master_list){
                            $prospect_list = $p_list;
                            break;
                        }
                    }
                }elseif(isset($prospect_list) && count($prospect_list) ==1) {
                    $prospect_list = $prospect_list[0];
                }
                if(isset($prospect_list) ) {
                    if($prospect_list->camp_master_list) {
                        //this is a master list used for sms
                        $targets = $prospect_list->get_linked_beans('contacts','Contacts');
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
        }
    }
    
   
    //Only used to create list and populate with targets as the camp and targets already existed 
    //before the auto add lists and contacts was created
    function action_addToMasterTargetList() {
        $query = "SELECT id 
                  FROM camps 
                  WHERE 
                   deleted is false
                  AND 
                   start_date > DATE_FORMAT(CONCAT(now()), '%Y-%m-%d') 
                  ORDER BY start_date";
        $result = $this->bean->db->query($query, true, "Error attempting to retrieve camps");

        while($row = $this->bean->db->fetchByAssoc($result)) {
            //create a sms list for this camp
            $camp = BeanFactory::getBean("Camps",$row['id']);
            //add all targets to this list
            $prospect_list = $camp->get_linked_beans('prospect_lists','ProspectList');
            if(isset($prospect_list) && count($prospect_list) == 1 ) {
                if($prospect_list[0]->camp_master_list) {
                    $participants = $camp->get_linked_beans('campparticipants','CampParticipants');
                    $prospect_list[0]->load_relationship('contacts');
                    foreach($participants as $participant) {
                        if(!empty($participant->contact_id)) {
                            $prospect_list[0]->contacts->add($participant->contact_id);
                        }
                    }
                }
            }
        }
    }
    
    
    
    function action_configSave(){
        require_once('modules/Configurator/Configurator.php');

        $configurator = new Configurator();
        global $sugar_config;
        global $current_user;
        if ( !is_admin($current_user)
                && !is_admin_for_module($GLOBALS['current_user'],'Camps')){
            sugar_die("Unauthorized access to administration.");
        }

        $configurator->config['sbcc_ftp_server'] = $_REQUEST['sbcc_ftp_server'];
        $configurator->config['sbcc_ftp_booking_dir'] = $_REQUEST['sbcc_ftp_booking_dir'];
        $configurator->config['sbcc_ftp_payment_dir'] = $_REQUEST['sbcc_ftp_payment_dir'];
        $configurator->config['sbcc_ftp_username'] = $_REQUEST['sbcc_ftp_username'];
        $configurator->config['sbcc_ftp_password'] = $_REQUEST['sbcc_ftp_password'];

        ksort($sugar_config);

        $configurator->handleOverride();
        
        header("Location: index.php?action={$_POST['return_action']}&module={$_POST['return_module']}");
        exit;
  
    }
    
}
?>

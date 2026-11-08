<?php
/*
 * EINTEL
 * File to store logic hooks
 *
 */




class eintel_logic_hooks {


    /*
     * This hook is for the following modules
     * -Accounts
     * -Contacts
     * -Leads
     *
     * Its fired when calls & meetings are saved
     *
     */
    function updateLastContacted($bean){
        global $timedate;

        if($bean->status == 'Held'){
            if(in_array($bean->parent_type, array('Accounts','Contacts','Leads'))){
                $parent_table = strtolower($bean->parent_type);
                $sql = "UPDATE $parent_table set last_contact_date = '$bean->date_start'
                          where id = '$bean->parent_id'
                          and (last_contact_date is null or last_contact_Date < '$bean->date_start')";

                $bean->db->query($sql);
            }
        }

        return;
    }




}






















?>

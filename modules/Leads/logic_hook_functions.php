<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */




class leads_logic_hooks {


    function updateLastContacted($bean){
        global $timedate;
        //if related to lead
        if($bean->status == 'Held'){

            if($bean->parent_type == "Leads"){
                $parent = BeanFactory::getBean("Leads", $bean->parent_id);
                //get start date time object
                $sd = $timedate->fromDb($bean->date_start);
                if(empty($parent->last_contact_date)){
                    //empty, set the date to current beans start date.
                    $parent->last_contact_date = $timedate->to_display_date_time($bean->date_start);
                    $parent->save();
                }else{
                    //get last contact date object
                    $lcd = $timedate->fromUser($parent->last_contact_date);
                    if($sd->getTimestamp() > $lcd->getTimestamp()){
                        //date is greater than current date in table
                        //update with current beans start date
                        $parent->last_contact_date = $timedate->to_display_date_time($bean->date_start);
                        $parent->save();
                    }
                }
            }else {
                return;
            }
        }
        return;
    }

    


}






















?>

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
class Camp extends Basic {

    var $new_schema = true;
    var $module_dir = 'Camps';
    var $object_name = 'Camp';
    var $table_name = 'camps';
    var $importable = true;
    var $id;
    var $name;
    var $date_entered;
    var $date_modified;
    var $modified_user_id;
    var $modified_by_name;
    var $created_by;
    var $created_by_name;
    var $description;
    var $deleted;
    var $created_by_link;
    var $modified_user_link;
    var $assigned_user_id;
    var $assigned_user_name;
    var $assigned_user_link;
    var $type;
    var $start_date;
    var $end_date;
    var $contact_id;
    var $contact_name;
    var $venue_address_building_type;
    var $venue_address_number;
    var $venue_address_street;
    var $venue_address_city;
    var $venue_address_postalcode;
    var $venue_address_state;
    var $venue_address_country;

    var $sms_list_id;
    var $update_vcal = true;
    var $additional_fields = array('contact_name');
    var $additional_column_fields = array('contact_name');

    function Camp()	{
        parent::Basic();
    }


    function bean_implements($interface) {
        switch ($interface) {
            case 'ACL': return true;
        }
        return false;
    }


    function get_summary_text() {
        return "$this->name";
    }

    function fill_in_additional_list_fields(){
        parent::fill_in_additional_list_fields();
        if (!empty($this->id)) {
            $focus = $this->retrieve($this->id);
            $this->total_participants =  $focus->getTotalParticipants($focus->id);
            $this->total_confirmed_participants =  $focus->getTotalConfirmedParticipants($focus->id);
        }
    }

    function create_new_list_query($order_by, $where,$filter=array(),$params=array(), $show_deleted = 0,$join_type='', $return_array = false,$parentbean=null, $singleSelect = false)  {
        if(isset($where)) {
            if(strpos($where, 'and camps.age_groups in ') !== false) {
                $string = substr($where, strpos($where, 'and camps.age_groups in ') + strlen('and camps.age_groups in '));
                if (strstr ($string,')',TRUE) != FALSE) {
                    $string = strstr ($string,')',TRUE). ')';
                    $where = str_replace('and camps.age_groups in '.$string, '', $where);
                }
            }elseif(strpos($where, 'camps.age_groups in ') !== false) {
                $string = substr($where, strpos($where, 'camps.age_groups in ') + strlen('camps.age_groups in '));
                if (strstr ($string,')',TRUE) != FALSE) {
                    $string = strstr ($string,')',TRUE). ')';
                    $where = str_replace('camps.age_groups in '.$string, '', $where);
                    if($where == "(  )") {
                        $where = null;
                    }
                }
            }

            if(strpos($where, 'and camps.participant_gender in ') !== false) {
                $string = substr($where, strpos($where, 'and camps.participant_gender in ') + strlen('and camps.participant_gender in '));
                if (strstr ($string,')',TRUE) != FALSE) {
                    $string = strstr ($string,')',TRUE). ')';
                    $where = str_replace('and camps.participant_gender in '.$string, '', $where);
                }
            }elseif(strpos($where, 'camps.participant_gender in ') !== false) {
                $string = substr($where, strpos($where, 'camps.participant_gender in ') + strlen('camps.participant_gender in '));
                if (strstr ($string,')',TRUE) != FALSE) {
                    $string = strstr ($string,')',TRUE). ')';
                    $where = str_replace('camps.participant_gender in '.$string, '', $where);
                    if($where == "(  )") {
                        $where = null;
                    }
                }
            }
        }
        return parent::create_new_list_query($order_by, $where, $filter, $params, $show_deleted, $join_type, $return_array, $parentbean, $singleSelect);
    }

    function save($check_notify=false) {
        $create_target_list = false;
        if(!isset($this->id) || empty($this->id)) {
            $create_target_list = true;
            $target_list_name = "{$this->name} {$this->start_date} - {$this->end_date}";
        }

        parent::save();

        if($create_target_list){
            $target_list = BeanFactory::getBean('ProspectLists');
            $target_list->name = $target_list_name;
            $target_list->description = "Target List for Controlling Messaging(sms) and Emailing of Targets for Camp: {$this->name}";
            $target_list->camp_master_list = true;
            $target_list->list_type = "default";
            $target_list->save();
            if($target_list->load_relationship('camps')){
                $target_list->camps->add($this->id);
            }

            //create a sms list for this camp
            if(module_enabled('SMS_SMS') && !isset($this->sms_list_id)){
                require_once('modules/SMS_SMS/SMSFunctions.php');
                if(($this->sms_list_id = addList($this)) != null){
                    parent::save();
                    $target_list->sms_list_id = $this->sms_list_id;
                    $target_list->save();
                }
            }
        }
    }

    function groupCreate($bean) {
        return CampGroup::groupCreate($bean);
    }

    function schedulejob_handleCampBookings() {
        return $this->schedulejob_retireveWooCommerceOrders();

        require_once('handleBookings.php');
        $bookings = new handleBookings();
//        if(($new = $bookings->getBookings()) != null){
//            foreach($new as $file){
//                $bookings->addBooking($file);
//             }
//        }
//        if(($payments = $bookings->getPaymentRecord()) != null){
//            foreach($payments as $p_file){
//                $bookings->addPayment($p_file);
//             }
//        }
//        if($bookings->connection){
//           $bookings->close_connection(); 
//        }
        if($bookings->processNewBookings()){
            $bookings->processCompleteBookings();
        }
        return true;
    }

    function schedulejob_retireveWooCommerceOrders() {
        require_once('handleWooCommerceOrdersV2.php');
        $orders = new handleWooCommerceOrdersV2();

        $admin = new Administration();
        $admin->retrieveSettings();

        $afterDate = isset($admin->settings['sync_last_after_date'])
          ? $admin->settings['sync_last_after_date']
          : date('Y-m-d', strtotime('-1 month'));
        //$afterDate = '2026-01-16';

        $new_orders = $orders->getNewOrdersAfterDate($afterDate);

        if (!empty($new_orders['orders'])) {
            foreach ($new_orders['orders'] as $order) {
                $order_json = json_encode($order, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                if ($order_json === false) {
                    error_log("Failed to JSON encode order: " . print_r($order, true));
                    continue;
                }

                $file_name = "WP-{$order['order_id']}";

                if (!$orders->isExistingBooking($file_name)) {
                    $escaped_file_name = $this->db->quote($file_name);
                    $escaped_json      = $this->db->quote($order_json);

                    $query = "INSERT INTO sbcc_website_data
                                (file_name, date_created, record_status, file_data)
                              VALUES
                                ('{$escaped_file_name}', now(), 'pending-wp', '{$escaped_json}')";

                    $this->db->query($query, true, "Error attempting to insert order data");
                }
            }

            $last_order = end($new_orders['orders']);
            $admin->saveSetting('sync', 'last_after_date', $last_order['date']);
        }

        //now process any of the retrieved bookings
        $orders->processNewBookings();
        //$orders->processCompleteBookings();
        return true;
    }


    function schedulejob_clearQueue() {
        //run sql query to delete camp emails
        $query = "DELETE FROM emailman where related_type = 'Camps';";
        $this->db->query($query, true, "Error attempting to clear email queue");

        return true;
    }


    /*
     * Function to construct bodies of templates
     */

    function participantsConfirmed_pdf($template_id,$bean_id){
        $module = BeanFactory::getBean('Camps',$bean_id);
        $table_header = '<div style="font-size: 10pt; font-weight: bold; font-family: helvetica;"><table border="1"><thead><tr><td><strong>Group</strong></td><td><strong>Name</strong></td><td><strong>Surname</strong></td><td><strong>Age</strong></td><td><strong>Mobile</strong></td><td><strong>Extras</strong></td><td><strong>Medical</strong></td><td><strong>Commets</strong></td></tr></thead><tbody>';
        $table_cell = '<tr><td>$campparticipants_campgroup_name</td><td>$campparticipants_first_name</td><td>$campparticipants_last_name</td><td>$campparticipants_participant_age</td><td>$campparticipants_phone_mobile</td><td>$campparticipants_additional_purchases</td><td>$campparticipants_medical</td><td>$campparticipants_comments</td></tr>';
        $table_footer = '</tbody></table></div>';
        $count=1;
        $printable = '';
        $query = "SELECT cp.id 
                  FROM campparticipants cp
                  LEFT JOIN camps c ON c.id = cp.camp_id AND c.deleted is false
                  LEFT JOIN campgroups cg ON cg.id = cp.campgroup_id AND cg.deleted is false
                  WHERE
                    cp.deleted is false
                  AND
                    cp.confirmed is true
                  AND
                    c.id = '{$module->id}'
                  ORDER BY 
                    cg.name, cp.last_name, cp.first_name";
        $result = $module->db->query($query, true, "Error attempting to retrieve participants");
        $object_arr = array();
        while($row = $module->db->fetchByAssoc($result)) {
            $object_arr['CampParticipants'] = $row['id'];
            $converted = templateParser::parse_template($table_cell, $object_arr);
            $printable .= str_replace("\n", "<br />", $converted.$str);
            $count++;
        }
        return $printable = $table_header.$printable.$table_footer;
    }

    function participants_pdf($template_id,$bean_id) {
        $module = BeanFactory::getBean('Camps',$bean_id);
        $table_header = '<div style="font-size: 10pt; font-weight: bold; font-family: helvetica;"><table border="1"><thead><tr><td><strong>Group</strong></td><td><strong>Name</strong></td><td><strong>Surname</strong></td><td><strong>Age</strong></td><td><strong>Mobile</strong></td><td><strong>Extras</strong></td><td><strong>Medical</strong></td><td><strong>Commets</strong></td></tr></thead><tbody>';
        $table_cell = '<tr><td>$campparticipants_campgroup_name</td><td>$campparticipants_first_name</td><td>$campparticipants_last_name</td><td>$campparticipants_participant_age</td><td>$campparticipants_phone_mobile</td><td>$campparticipants_additional_purchases</td><td>$campparticipants_medical</td><td>$campparticipants_comments</td></tr>';
        $table_footer = '</tbody></table></div>';

        $printable = '';
        $count=1;
        $query = "SELECT cp.id 
                  FROM campparticipants cp
                  LEFT JOIN camps c ON c.id = cp.camp_id AND c.deleted is false
                  LEFT JOIN campgroups cg ON cg.id = cp.campgroup_id AND cg.deleted is false
                  WHERE
                    cp.deleted is false
                  AND
                    c.id = '{$module->id}'
                  ORDER BY 
                    cg.name, cp.last_name, cp.first_name";
        $result = $module->db->query($query, true, "Error attempting to retrieve participants");
        $object_arr = array();
        while($row = $module->db->fetchByAssoc($result)) {
            $object_arr['CampParticipants'] = $row['id'];
            $converted = templateParser::parse_template($table_cell, $object_arr);
            $printable .= str_replace("\n", "<br />", $converted.$str);
            $count++;
        }
        return $printable = $table_header.$printable.$table_footer;
    }

    function labels_pdf($template_id,$bean_id){
        $module = BeanFactory::getBean('Camps',$bean_id);
        $table_header = '<div style="font-size: 10pt; font-weight: bold; font-family: helvetica;"><table border="0" style="border-color:red;"><tbody>';
        $table_row_start ='<tr>';
        $table_cell = '<td style="width: 65.5mm; height: 38.1mm; table-layout: fixed;">$campparticipants_campgroup_name <br /> $campparticipants_contact_name</td>';
        $table_row_end='</tr>';
        $table_footer = '</tbody></table></div>';
        $printable = '';
        $query = "SELECT cp.id 
                  FROM campparticipants cp
                  LEFT JOIN camps c ON c.id = cp.camp_id AND c.deleted is false
                  LEFT JOIN campgroups cg ON cg.id = cp.campgroup_id AND cg.deleted is false
                  WHERE
                    cp.deleted is false
                  AND
                    c.id = '{$module->id}'
                  ORDER BY 
                    cg.name, cp.last_name, cp.first_name";
        $result = $module->db->query($query, true, "Error attempting to retrieve participants");
        $object_arr = array();
        $pdf_rows = array();
        while($row = $module->db->fetchByAssoc($result)) {
            $object_arr['CampParticipants'] = $row['id'];
            $pdf_rows[] = templateParser::parse_template($table_cell, $object_arr);
        }
        $count=1;
        foreach ($pdf_rows as $pdf_row) {
            $str = '';
            if($count % 3 == 0){
                //start new row
                $pdf_row .= $table_row_end;
                if($count < count($pdf_rows)){
                    $str = $table_row_start;
                }
            }elseif($count==1){
                $pdf_row = $table_row_start.$pdf_row;
            }

            if($count == count($pdf_rows)){
                $str = $table_row_end;
            }

            $printable .= str_replace("\n", "<br />", $pdf_row.$str);

            $count++;
        }

        return $printable = $table_header.$printable.$table_footer;
    }

    /*
     * Label design
    
        <group>
        <name>
        <purchase>
        
        A
        Nathan Montague
        Bat
     * 
     */
    function labelsForAdditionalPurchases_pdf($template_id,$bean_id){
        $module = BeanFactory::getBean('Camps',$bean_id);
        $table_header = '<div style="font-size: 10pt; font-weight: bold; font-family: helvetica;"><table border="0"><tbody>';
        $table_row_start ='<tr>';
        $table_cell = '<td style="width: 65.5mm; height: 38.1mm; table-layout: fixed;">$campparticipants_campgroup_name <br /> $campparticipants_contact_name';
        $table_row_end='</tr>';
        $table_footer = '</tbody></table></div>';
        $printable = '';
        $object_arr = array();

        $query = "SELECT cp.* 
                  FROM campparticipants cp
                  LEFT JOIN camps c ON c.id = cp.camp_id AND c.deleted is false
                  LEFT JOIN campgroups cg ON cg.id = cp.campgroup_id AND cg.deleted is false
                  WHERE
                    cp.deleted is false
                  AND
                    c.id = '{$module->id}'
                  AND
                  (
                        cp.purchase_ball is true 
                    OR
                        cp.purchase_cap is true
                    OR
                        cp.purchase_mini_bat is true
                    OR
                        cp.purchase_photo is true
                  )
                  ORDER BY 
                    cg.name, cp.last_name, cp.first_name";
        $result = $module->db->query($query, true, "Error attempting to retrieve participants");

        $pdf_rows = array();
        while($row = $module->db->fetchByAssoc($result)) {
            $object_arr['CampParticipants'] = $row['id'];
            $converted = templateParser::parse_template($table_cell, $object_arr);
            //cap label

            if($row['purchase_cap']){
                $pdf_rows[] =$converted.'<br />Cap</td>';
            }
            //photo label
            if($row['purchase_photo']){
                $pdf_rows[] =$converted.'<br />Photo</td>';
            }
            //ball label
            if($row['purchase_ball']){
                $pdf_rows[] =$converted.'<br />Ball</td>';
            }
            //bat label
            if($row['purchase_mini_bat']){
                $pdf_rows[] =$converted.'<br />Mini Bat</td>';
            }


        }
        $count=1;
        foreach($pdf_rows as $pdf_row){
            $str = '';
            if($count % 3 == 0){
                //start new row
                $pdf_row .= $table_row_end;
                if($count < count($pdf_rows)){
                    $str = $table_row_start;
                }
            }elseif($count==1){
                $pdf_row = $table_row_start.$pdf_row;
            }

            if($count == count($pdf_rows)){
                $str = $table_row_end;
            }
            $printable .= str_replace("\n", "<br />", $pdf_row.$str);

            $count++;
        }


        return $printable = $table_header.$printable.$table_footer;
    }

    function labelsForLetters_pdf($template_id,$bean_id){
        $module = BeanFactory::getBean('Camps',$bean_id);
        $table_header = '<div style="font-size: 10pt; font-weight: bold; font-family: helvetica;"><table border="0" style="border-color:red;"><tbody>';
        $table_row_start ='<tr>';
        $table_cell = '<td style="width: 65.5mm; height: 38.1mm; table-layout: fixed; padding-left:8px;padding-right:4px">$campparticipants_contact_name <br /> $campparticipants_primary_address_line1 <br /> $campparticipants_primary_address_line2 <br /> $campparticipants_primary_address_country</td>';
        $table_row_end='</tr>';
        $table_footer = '</tbody></table></div>';
        $printable = '';
        $query = "SELECT 
                    GROUP_CONCAT(cp.id) as campparticipantsid,
                    CONCAT_WS(' ',cp.primary_address_building_type, cp.primary_address_number, cp.primary_address_street) as primary_address_line1,
                    CONCAT_WS(' ',cp.primary_address_city, cp.primary_address_state, cp.primary_address_postalcode) as primary_address_line2,
                    cp.primary_address_country
                  FROM campparticipants cp
                  LEFT JOIN camps c ON c.id = cp.camp_id AND c.deleted is false
                  WHERE
                    cp.deleted is false
                  AND
                    c.id = '{$module->id}'
                  GROUP BY CONCAT_WS(' ',cp.primary_address_building_type, cp.primary_address_number, cp.primary_address_street)    
                  ORDER BY 
                    cp.last_name, cp.first_name";
        $result = $module->db->query($query, true, "Error attempting to retrieve participants");
        $pdf_rows = array();

        while($row = $module->db->fetchByAssoc($result)) {
            $contact_names = array();
            $CampParticipants = explode(",",$row['campparticipantsid']);

            foreach($CampParticipants as $bean_id) {
                $focus = new CampParticipant;
                $focus->retrieve($bean_id,false);
                $contact_names[] = $focus->contact_name;
            }
            if( count($contact_names) > 3) {
                $contact_names = array_slice($contact_names, 0, 3);
                $contact_names[2] = " ...";
            }
            //replace names
            $_table_cell = str_replace('$campparticipants_contact_name', implode(",",$contact_names), $table_cell);
            //replace address
            $_table_cell = str_replace('$campparticipants_primary_address_line1', $row['primary_address_line1'], $_table_cell);
            $_table_cell = str_replace('$campparticipants_primary_address_line2', $row['primary_address_line2'], $_table_cell);
            $_table_cell = str_replace('$campparticipants_primary_address_country', $row['primary_address_country'], $_table_cell);

            $pdf_rows[] = $_table_cell;
        }
        $count=1;
        foreach ($pdf_rows as $pdf_row) {
            $str = '';
            if($count % 3 == 0){
                //start new row
                $pdf_row .= $table_row_end;
                if($count < count($pdf_rows)){
                    $str = $table_row_start;
                }
            }elseif($count==1){
                $pdf_row = $table_row_start.$pdf_row;
            }

            if($count == count($pdf_rows)){
                $str = $table_row_end;
            }

            $printable .= str_replace("\n", "<br />", $pdf_row.$str);

            $count++;
        }

        return $printable = $table_header.$printable.$table_footer;
    }


    //list view functions
    static function getTotalParticipants($id) {
        $query = "SELECT cp.id 
                  FROM campparticipants cp
                  LEFT JOIN camps c ON c.id = cp.camp_id AND c.deleted is false
                  WHERE
                    cp.deleted is false
                  AND
                    c.id = '{$id}'";
        $camp = BeanFactory::getBean("Camps",$id);
        $result = $camp->db->query($query, true, "Error attempting to retrieve total participants");
        $total = 0;
        while($row = $camp->db->fetchByAssoc($result)) {
            $total++;
        }
        return $total;

    }

    static function getTotalConfirmedParticipants($id) {
        $query = "SELECT cp.id 
                  FROM campparticipants cp
                  LEFT JOIN camps c ON c.id = cp.camp_id AND c.deleted is false
                  WHERE
                    cp.deleted is false
                  AND
                    cp.confirmed = true
                  AND
                    c.id = '{$id}'";
        $camp = BeanFactory::getBean("Camps",$id);
        $result = $camp->db->query($query, true, "Error attempting to retrieve confirmed participants");
        $total = 0;
        while($row = $camp->db->fetchByAssoc($result)) {
            $total++;
        }
        return $total;
    }
}
?>

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


const WOOCOMMERCE_URL = 'https://test.cricketcoaching.com.au/';
const CONSUMER_KEY = 'ck_47158d7172259f67cf9b7015c0ae02bd80ee04e3';//'ck_368b7637d74a155b2c16e9b63959684a2c6fc547';
const CONSUMER_SECRET = 'cs_146fe075dfd528c8027a0419f376f2b58a5675c6';//'cs_d54cef5ddfdac95b1882bc66f3653a9c5e75ff87';

class handleWooCommerceOrders {
    //define fields here incase they get changed
    const FIELD_CAMP_NAME ="CAMP_NAME";
    const FIELD_CAMP_DATE ="CAMP_DATE";

    private $baseUrl;
    private $consumerKey;
    private $consumerSecret;

    var $camp_types = array(
      'coaching' => 'Coaching',
      'country_nsw_camp' => 'Country NSW Camp',
      'country_vic_camp' => 'Country Vic Camp',
      'cwws' => 'Country Winter Warriors',
      'epp' => 'Emerging Player Program',
      'metro_camp' => 'Metro Camp',
      'sbcc_academy' => 'SBCC Academy',
      'september_holiday_camp' => 'September Holiday Cricket Clinic',
      'september_specialist_sessions' => 'September Specialist Sessions',
      'super_camp' => 'Super Camp',
      'winter_warrior' => 'Winter Warriors',
    );

    var $product_types = array(
      'tour_baggy_green_pack',
      'cricket_helmet',
      'clothing',
      'bags',
      'clothing',
    );

    var $strip_from_name = array('/SPECIAL [A-Z ]*-/');

    function __construct() {
        global $sugar_config;
        //setup connection settings
        $this->baseUrl = rtrim(WOOCOMMERCE_URL, '/');
        $this->consumerKey = CONSUMER_KEY;
        $this->consumerSecret = CONSUMER_SECRET;
    }



    /**
     * Make authenticated request to WooCommerce API
     */
    //private function makeRequest($endpoint, $params = []) {
    private function makeRequest_old($endpoint, $params) {
        $url = $this->baseUrl . $endpoint;

        // Add authentication parameters
        //$params['consumer_key'] = $this->consumerKey;
        //$params['consumer_secret'] = $this->consumerSecret;

        $credentials = base64_encode($this->consumerKey . ':' . $this->consumerSecret);
        $authHeader = 'Basic ' . $credentials;

        // Build query string
        $queryString = http_build_query($params);
        $fullUrl = $url . '?' . $queryString;

        // Initialize cURL
        $ch = curl_init();
        curl_setopt_array($ch, array(
          CURLOPT_URL => $fullUrl,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_SSL_VERIFYPEER => true,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_USERAGENT => 'WooCommerce Orders API Client/1.0',
          CURLOPT_HTTPHEADER => array(
            'Authorization: ' . $authHeader,
            'Content-Type: application/json',
            'Accept: application/json'
          )
        ));



        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception('cURL Error: ' . $error);
        }

        if ($httpCode !== 200) {
            throw new Exception('HTTP Error: ' . $httpCode . ' - ' . $response);
        }

        return json_decode($response, true);
    }

    private function makeRequest($endpoint, $params) {
        $url = $this->baseUrl . $endpoint;

        $credentials = base64_encode($this->consumerKey . ':' . $this->consumerSecret);
        $authHeader = 'Basic ' . $credentials;

        // Build query string — use PHP_QUERY_RFC3986 and handle arrays properly
        $queryString = $this->buildQueryString($params);
        $fullUrl = $url . '?' . $queryString;

        $ch = curl_init();
        curl_setopt_array($ch, array(
          CURLOPT_URL            => $fullUrl,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_TIMEOUT        => 30,
          CURLOPT_CUSTOMREQUEST  => 'GET',
          CURLOPT_USERAGENT      => 'WooCommerce Orders API Client/1.0',
          CURLOPT_HTTPHEADER     => array(
            'Authorization: ' . $authHeader,
            'Content-Type: application/json',
            'Accept: application/json'
          )
        ));

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception('cURL Error: ' . $error);
        }

        if ($httpCode !== 200) {
            throw new Exception('HTTP Error: ' . $httpCode . ' - ' . $response);
        }

        return json_decode($response, true);
    }

    /**
     * Builds a query string that correctly encodes array values for WooCommerce.
     * e.g. ['status' => ['processing', 'completed']] => "status[]=processing&status[]=completed"
     */
    private function buildQueryString(array $params): string {
        $parts = [];

        foreach ($params as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $parts[] = urlencode($key) . '[]=' . urlencode($item);
                }
            } else {
                $parts[] = urlencode($key) . '=' . urlencode($value);
            }
        }

        return implode('&', $parts);
    }
    function sort_by_id($a, $b) {
        return $a['id'] - $b['id'];
    }
    /**
     * Get orders after a specific date
     */
    public function getNewOrdersAfterDate($afterDate, $status = 'any', $perPage = 50) {
        try {
            // Validate and format date
            $dateTime = new DateTime($afterDate);
            $formattedDate = $dateTime->format('Y-m-d\TH:i:s');

//                $params = array(
//                  'after' => $formattedDate,
//                  'status' => 'completed',
//                  'per_page' => min($perPage, 100), // WooCommerce max is 100
//                  'orderby' => 'date',
//                  'order' => 'desc'
//                );

            //$orders = $this->makeRequest('orders', $params);

            $orders = $this->makeRequest('/wp-json/wc/v3/orders', [
              'after' => $formattedDate,
              'status'   => ['processing', 'completed'],
              'per_page' => min($perPage, 100),
              'page'     => 1,
              'orderby' => 'date',
              'order' => 'asc'
            ]);

            //save the order data

            $meta_data = $orders[0]['meta_data'];
            //uasort($meta_data, 'this->sort_by_id');

            uasort($meta_data, function($a, $b) {
                return $a['id'] - $b['id'];
            });

            return array(
              'success' => true,
              'count' => count($orders),
              'orders' => json_encode($orders)//$this->formatOrders($orders)
            );

        } catch (Exception $e) {
            return array(
              'success' => false,
              'error' => $e->getMessage()
            );
        }
    }

    /**
     * Format orders for clean output
     */
    private function formatOrders($orders) {
        $formatted = array();

        foreach ($orders as $order) {



            $formatted[] = array(
              'ORDER_ID' => $order['id'],

              'PARTICIPANTS' => array_map(function($item) {
                  return array(
                    "FIRST_NAME" => '',
                    "LAST_NAME" => '',
                    "AGE" => '',
                    "GENDER" => '',
                    "CAMP_NAME" => '',
                    "CAMP_TYPE" => '',
                    "CAMP_DATE" => '',
                    "CAMP_ADDRESS" => '',
                    "VENUE" => '',
                    "VENUE_ADDRESS" => '',
                    "BOOKED_DATE" => '',



                    'id' => $item['id'],
                    'name' => $item['name'],
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total']
                  );
              }, $order['line_items']),
//billing details
              "ADDRESS" => $order['billing']['address_1'],
              "SUBURB" => $order['billing']['address_2'],
              "STATE" => $order['billing']['state'],
              "POSTCODE" => $order['billing']['postcode'],
              "COUNTRY" => $order['billing']['county'],
              "PHONE_TYPE" => '',
              "TELEPHONE_NUMBER" => $order['billing']['phone'],
              "EMAIL_ADDRESS" => $order['billing']['email'],


              //need to find fields

              "HOW_TEXT" => '',
              "FRIEND_NAME" => '',
              "FRIEND_SURNAME" => '',
              "CLUB_NAME" => $this->find_meta_value($meta_data, 'child_name'),
              "LEVEL" => $this->find_meta_value($meta_data, 'skill_level'),
              "ALLERGY" => '',
              "MEDICAL" => '',
              "COMMENTS" => '',
              "ALL_AGREE" => '',
              "MED" => '',
              "PRIV" => '',
              "PAYMENT_TYPE" => '',
              "AMOUNT_OWING" => $order['needs_payment'],


//paymebt details
              'payment_method' => $order['payment_method'],
              'payment_method_title' => $order['payment_method_title'],
              'PAYMENT_TYPE' => '',
              'AMOUNT_PAID' => '',
              'DATE_PAID' => $order['date_paid'],
              'TRANSACTION_ID' => $order['id'],
            );



//                $formatted[] = array(
//                  'id' => $order['id'],
//                  'order_number' => isset($order['number'])? $order['number'] : $order['id'],
//                  'status' => $order['status'],
//                  'total' => $order['total'],
//                  'currency' => $order['currency'],
//                  'date_created' => $order['date_created'],
//                  'date_modified' => $order['date_modified'],
//                  'customer' => array(
//                    'id' => $order['customer_id'],
//                    'email' => isset($order['billing']['email'])? $order['billing']['email'] : '',
//                    'first_name' => isset($order['billing']['first_name'])? $order['billing']['first_name'] : '',
//                    'last_name' => isset($order['billing']['last_name'])? $order['billing']['last_name'] : '',
//
//                  ),
//                  'billing_address' => $order['billing'],
//                  'shipping_address' => $order['shipping'],
//                  'line_items' => array_map(function($item) {
//                      return array(
//                        'id' => $item['id'],
//                        'name' => $item['name'],
//                        'product_id' => $item['product_id'],
//                        'quantity' => $item['quantity'],
//                        'price' => $item['price'],
//                        'total' => $item['total']
//                      );
//                  }, $order['line_items']),
//                  'payment_method' => $order['payment_method'],
//                  'payment_method_title' => $order['payment_method_title']
//                );
        }

        return $formatted;
    }

    function find_meta_value($meta_data, $search_key) {
        foreach ($meta_data as $item) {
            if ($item['key'] === $search_key) {
                return $item['value'];
            }
        }
        return null; // not found
    }




    //OLD FILES
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

    function connect() {
        // set up a connection or die
        $conn_id = ftp_connect($this->ftp_server) or die("Couldn't connect to $this->ftp_server");
        // try to login
        if (@ftp_login($conn_id, $this->ftp_user, $this->ftp_pass)) {
            //    echo "Connected as $ftp_user@$ftp_server\n";
            return $conn_id;
        } else {
            return null;
            //    echo "Couldn't connect as $ftp_user\n";
        }


    }

    function close_connection() {
        if($this->connection) {
            ftp_close($this->connection);
        }
    }

    function setCampName($camp_name){
        return trim(preg_replace($this->strip_from_name, "", $camp_name));
    }

    function processNewBookings() {
        global $db,$timedate;
        //Order by file_name so duplicate records are ordered in the correct date order.
        //This way the booking time is set as the date on the first file.
        $query = "SELECT * from sbcc_website_data where record_status = 'pending' order by file_name";
        $result = $db->query($query, true, "Error attempting to retrieve file list from sbcc_website_data");
        while($row = $db->fetchByAssoc($result)) {
            $json_data = json_decode(htmlspecialchars_decode($row['file_data']),true);
            //if coaching record to status to 'coaching' so its skipped next time
            if(isset($json_data['COACH_LEVEL'])) {
                $query = "UPDATE sbcc_website_data
                            SET record_status = 'coaching'
                            WHERE id = '{$row['id']}'";
                $db->query($query, true, "Error attempting to update a record with coaching status file");
            } else {
                $product_order = false;
                $participants = array();
                $filedata = $json_data;
                unset($filedata['PARTICIPANTS']);
                if(isset($json_data['PARTICIPANTS'])) {
                    foreach($json_data['PARTICIPANTS'] as  $participant_data) {
                        $participant_data = array_merge($filedata,$participant_data);

                        //if camp type is one of these skip the record
                        if(!empty($participant_data['CAMP_TYPE'])  && in_array($participant_data['CAMP_TYPE'], $this->product_types)  ) {
                            //SKIP
                            $product_order = true;
                            continue;
                        }

                        //camp record
                        if(!empty($participant_data['CAMP_NAME'])) {
                            //set the camp name.
                            //used to potentially strip prepended text from camp name
                            $participant_data['CAMP_NAME'] = $this->setCampName($participant_data['CAMP_NAME']);
                        }
                        if(!isset($participant_data['BOOKED_DATE'])) {
                            $participant_data['BOOKED_DATE'] = preg_replace("/[^0-9,.]/", "", $row['file_name']);
                        }

                        if(isset($participant_data['FIRST_NAME']) && $participant_data['FIRST_NAME'] == $participant_data['LAST_NAME']) {
                            $name_data = explode(' ',trim($participant_data['FIRST_NAME']));
                            $participant_data['FIRST_NAME'] = $name_data[0];
                            unset($name_data[0]);
                            $participant_data['LAST_NAME'] = implode(' ',$name_data);
                        }

                        if(is_numeric($participant_data['BOOKED_DATE'])){
                            $ts = $timedate->fromTimestamp($participant_data['BOOKED_DATE']);
                            $participant_data['BOOKED_DATE'] = $timedate->asDb($ts);
                        } else {
                            $ts = $timedate->fromTimestamp(strtotime($participant_data['BOOKED_DATE']));
                            $participant_data['BOOKED_DATE'] = $timedate->asDb($ts);
                        }

                        if(isset($participant_data['CAMP_TYPE']) && isset($this->camp_types[$participant_data['CAMP_TYPE']])) {
                            $participant_data['CAMP_TYPE'] = $this->camp_types[$participant_data['CAMP_TYPE']];
                        }

                        if(($camp = $this->getCamp($participant_data)) != null){
                            //return;
                            //get participant details//creating if not found
                            if(($participant = $this->getParticipant($participant_data,$camp))!=null) {
                                //add $participant to $camp
                                //includes updating db record to booked if relation is added
                                $this->addParticitpantToCamp($participant, $camp,$row['file_name'],$participant_data);
                                $participants[] = $participant->id;
                            }
                        }
                    }
                }
                //update row with all participants
                if(!empty($participants)) {
                    if(count($participants) >= 1) {
                        $participants = json_encode($participants);
                    }
                    $query = "UPDATE
                            sbcc_website_data
                          SET
                            record_status = 'booked',
                            participant_id = '$participants'
                          WHERE
                            file_name = '{$row['file_name']}'";
                    $db->query($query, true, "Error attempting to update a booking record for file: {$row['file_name']}");
                } elseif(empty($participants) && $product_order) {
                    $query = "UPDATE
                            sbcc_website_data
                          SET
                            record_status = 'product_order'
                          WHERE
                            file_name = '{$row['file_name']}'";
                    $db->query($query, true, "Error attempting to update a booking record for file: {$row['file_name']}");
                }

            }
        }
        return true;

    }

    function processCompleteBookings() {
        global $db, $timedate;
        $c = 1;
        $query = "SELECT * from sbcc_website_data
                where record_status = 'booked'
                and file_payment_data is not null;";
        $result = $db->query($query, true, "Error attempting to retrieve file list from sbcc_website_data");
        while($row = $db->fetchByAssoc($result)) {
            $data = explode("\n", $row['file_payment_data']);
            $converted_data = array();
            foreach($data as $field) {
                $field_data = explode("=", str_replace("\r", "",$field));
                if(isset($field_data[0]) && !empty($field_data[1])) {
                    $converted_data[$field_data[0]]= rawurldecode($field_data[1]);
                } else {
                    $no_data = true;
                }
            }

            //convert date paid to db format
            if(isset($converted_data['DATE_PAID']) && !empty($converted_data['DATE_PAID'])) {
                if(is_numeric($converted_data['DATE_PAID'])){
                    $ts = $timedate->fromTimestamp($converted_data['DATE_PAID']);
                    $converted_data['BOOKED_DATE'] = $timedate->asDb($ts);
                } else {
                    $ts = $timedate->fromTimestamp(strtotime($converted_data['DATE_PAID']));
                    $converted_data['DATE_PAID'] = $timedate->asDb($ts);
                }
            }

            //get participant record
            if(!empty($row['participant_id'])) {
                $participants = json_decode(htmlspecialchars_decode($row['participant_id']),true);
                if(!is_null($participants)) {
                    if(count($participants) > 1 ) {
                        //group
                        foreach($participants as $participant_id ) {
                            $participant = BeanFactory::getBean("CampParticipants",$participant_id);
                            //add $participant to $camp
                            if(!empty($participant->id)) {
                                $this->updateGroupParticipantWithPayment($participant,$converted_data,$row_id);
                            }
                        }
                        //update data table to flag booking at complete
                        $query = "UPDATE
                                    sbcc_website_data
                                  SET
                                    record_status = 'complete'
                                  WHERE
                                    id = '{$row['id']}'";
                        $db->query($query, true, "Error attempting to complete a booking record for ID: {$row['id']}");
                    } else {
                        $participant = BeanFactory::getBean("CampParticipants",$participants[0]);
                        //add $participant to $camp
                        if(!empty($participant->id)) {
                            $this->updateParticipantWithPayment($participant,$converted_data,$row['id']);
                            $query = "UPDATE
                                        sbcc_website_data
                                      SET
                                        record_status = 'complete'
                                      WHERE
                                        id = '{$row['id']}'";
                            $db->query($query, true, "Error attempting to complete a booking record for ID: {$row['id']}");
                        }
                    }
                }
            }
        }
        return true;
    }

    function formatDates($data) {
        global $timedate;
        //get year off the end
        $year = substr($data['CAMP_DATE'], -4);
        //remove year from string

        $date = trim(substr($data['CAMP_DATE'],0,-4));

        //remove tailing common if exists
        if(substr($date,-1) == ',') {
            $date = substr($date,0,-1);
        }

        //get date values
        $date_data = explode(",",$date);
        $date_data = array_map("trim", $date_data);

        //if year value is not in date string add it
        if (strpos($date_data[0],$year) === false) {
            $date_data[0] = $date_data[0]." ".$year;
        }

        $start_month = date ("F", strtotime ($date_data[0], $timedate->getNow()->getTimestamp()));

        $months = array(
          'Jan','Feb','Mar','Apr','May','Jun',
          'Jul','Aug','Sep','Oct','Nov','Dec'
        );

        $month_missing = false;
        foreach ($months as $month) {
            if (strpos($date_data[count($date_data)-1],$month) === false) {
                $month_missing = true;
            } else {
                $month_missing = false;
                break;
            }
        }
        //Add month if found to be missing
        //eg: October 3, 4 2013
        if($month_missing == true) {
            $date_data[count($date_data)-1] =  $start_month." ".$date_data[count($date_data)-1];
        }

        //add year to the last value in the array
        if (strpos($date_data[count($date_data)-1],$year) === false) {
            $date_data[count($date_data)-1] = $date_data[count($date_data)-1]." ".$year;
        }

        return array('start'=>$date_data[0],'end'=>$date_data[count($date_data)-1]);
    }

    function getExistingBookingList() {
        global $db;

        $query = "SELECT file_name from sbcc_website_data";
        $result = $db->query($query, true, "Error attempting to retrieve file list from sbcc_website_data");
        $files = array();
        while($row = $db->fetchByAssoc($result)) {
            //return everything
            $files[] = $row['file_name'];
        }
        return $files;

    }

    function getExistingBookingWithPaymentList() {
        global $db;
        //get file names of those records with payment data
        $query = "SELECT file_name from sbcc_website_data where file_payment_data is not null";
        $result = $db->query($query, true, "Error attempting to retrieve file list from sbcc_website_data");
        $files = array();
        while($row = $db->fetchByAssoc($result)) {
            //return everything
            $files[] = $row['file_name'];
        }
        return $files;

    }

    function getBookings() {
        // get dir listing
        $contents = array();//ftp_nlist($this->connection, $this->booking_dir);
        if(!empty($contents)) {
            $func = function($value) {
                return substr( $value, strrpos( $value, '/' )+1 );
            };
            $contents_altered = array_map($func, $contents);
        }else{
            //no contents
            return;
        }
        //load up all the existing booking files
        $existing_files = $this->getExistingBookingList();
        foreach($contents_altered as $file) {
            //look for new files
            if(!in_array($file, $existing_files) && $file != 'payment') {
                $files_needed[] = $file;
            }
        }
        return !empty($files_needed)?$files_needed:null;

    }

    function getPaymentRecord() {
        // get dir listing
        $contents = ftp_nlist($this->connection, $this->payment_dir);
        if(!empty($contents)) {
            $func = function($value) {
                return substr( $value, strrpos( $value, '/' )+1 );
            };
            $contents_altered = array_map($func, $contents);
        }else{
            //no contents
            return;
        }
        //load up all the existing booking files
        $existing_files = $this->getExistingBookingWithPaymentList();
        foreach($contents_altered as $file) {
            //look for new files
            if(!in_array($file, $existing_files)) {
                $files_needed[] = $file;
            }
        }
        return !empty($files_needed)?$files_needed:null;

    }

    function addBooking($remote_filename) {
        $remote_file = $this->booking_dir."/".$remote_filename;
        ob_end_flush();
        ob_start();
        $out = fopen('php://output', 'w');
        if (($data = ftp_fget($this->connection, $out, $remote_file, FTP_ASCII)) == false) {
            //echo "There was a problem\n";
            fclose($out);
        } else {
            fclose($out);
            $data = ob_get_clean();
            if(!empty($data)) {
                global $db;
                $query = "INSERT INTO sbcc_website_data
                            (file_name, date_created, record_status, file_data)
                          VALUES
                            ('$remote_filename', now(), 'pending', '$data')";
                $db->query($query, true, "Error attempting to insert a booking record for file: ".$remote_filename);
            }
        }
        return;


    }

    function addPayment($remote_filename) {
        $remote_file = $this->payment_dir."/".$remote_filename;
        ob_end_flush();
        $pathinfo = pathinfo($remote_file);
        if(isset($pathinfo['extension'])) {
            //not a payment file/these dont have extensions
            return;
        }
        ob_start();
        $out = fopen('php://output', 'w');
        if (($data = ftp_fget($this->connection, $out, $remote_file, FTP_ASCII)) == false) {
            //echo "There was a problem\n";
        } else {
            //echo "connected\n";
        }
        fclose($out);
        $data = ob_get_clean();
        if(!empty($data)) {
            global $db;
            $query = "UPDATE sbcc_website_data
                        SET file_payment_data = '$data'
                      WHERE
                        file_name = '$remote_filename'
                      AND
                        file_payment_data is null";
            //('$remote_filename', now(), 'pending', '$data')";
            $db->query($query, true, "Error attempting to update a booking record with payment data for file: ".$remote_filename);
            return;
        }
        return;

    }

    function retrieveExistingCamp($data, $fields){
        $camp = null;
        switch ($data['CAMP_TYPE']) {
            case 'Country Winter Warriors':
            case 'Winter Warriors':
                $booking_date = date_parse($data['BOOKED_DATE']);
                $name = "{$data['CAMP_TYPE']} {$booking_date['year']} - ".trim($data['VENUE']);
                $camp = BeanFactory::getBean('Camps')->retrieve_by_string_fields(array('name'=> $name));
                break;
            default:
                $fields['name'] = $data['CAMP_NAME'] . ' ' .$data['CAMP_TYPE'];
                $camp = BeanFactory::getBean('Camps')->retrieve_by_string_fields($fields);
                break;
        }
        return $camp;
    }
    function createCamp($data, $fields) {
        $camp = BeanFactory::getBean('Camps');

        //override default data with type specfic details
        switch ($data['CAMP_TYPE']) {
            case 'Country Winter Warriors':
            case 'Winter Warriors':
                //name = Winter Warriors - {$venue}
                $booking_date = date_parse($data['BOOKED_DATE']);
                $camp_name = "{$data['CAMP_TYPE']} {$booking_date['year']} - ".trim($data['VENUE']);
                $start_date = '';//$fields['start_date']; //set to the first of may for year
                $end_date = '';//$fields['end_date']; //set to the end of sep for year

                //create venue/address str
                $venue = $data['VENUE'];
                $venue_address_street = '';
                $venue_address_city = '';
                if(isset($data['VENUE_ADDRESS'])) {
                    $venue_address_street = $data['VENUE_ADDRESS'];
                }
                if(isset($data['VENUE_SUBURB'])) {
                    $venue_address_city = $data['VENUE_SUBURB'];
                }
                break;

            default:
                $camp_name = $data['CAMP_NAME'] . ' ' .$data['CAMP_TYPE'];
                $start_date = is_null($fields['start_date']) ? '' : $fields['start_date'];
                $end_date =  is_null($fields['end_date']) ? '' : $fields['end_date'];
                //create venue/address str
                $venue = '';
                $venue_address_street = '';
                $venue_address_city = '';
                if(isset($data['CAMP_ADDRESS'])) {
                    $address = explode(",", $data['CAMP_ADDRESS']);
                    if(!preg_match('/[A-Z]+[a-z]+[0-9]+/', $address[0]) && count($address) >= 3) {
                        $venue =  trim($address[0]);
                        $venue_address_street = isset($address[1])? $address[1]: '';
                        $venue_address_city = isset($address[2])? $address[2]: '';
                    } else {
                        $venue_address_street = $data['CAMP_ADDRESS'];
                    }
                }
                break;
        }
        $camp->name = $camp_name;
        $camp->start_date = $start_date;
        $camp->end_date = $end_date;
        $camp->venue = $venue;
        $camp->venue_address_street = trim($venue_address_street);
        $camp->venue_address_city = trim($venue_address_city);
        $camp->type = $data['CAMP_TYPE'];
        $camp->save();

        return $camp;
    }


    function isCampBooking($line_converted){
        $isCampBooking = true;
        if(empty($line_converted['CAMP_NAME'])) {
            //not a camp booking
            $isCampBooking = false;
        }
        //need this extra case as winter warrior camps dont have a name
        //adding strpos as there are now different types of warrior camps
        if(isset($line_converted['CAMP_TYPE']) && strpos($line_converted['CAMP_TYPE'],'Winter Warriors') !== false) {
            //is a camp booking
            $isCampBooking = true;
        }
        return $isCampBooking;
    }


    function getCamp($line_converted) {
        if(!$this->isCampBooking($line_converted)) {
            //not a camp booking
            return null;
        }
        //format dates to be used in search
        $date_data =  $this->formatDates($line_converted);
        //look for matching camp in the db
        $name = $line_converted['CAMP_NAME'] . ' ' . $line_converted['CAMP_TYPE'];
        $fields = array(
          'name'=> $name,
          'start_date' => $this->getDate($date_data['start']),
          'end_date' => $this->getDate($date_data['end'])

        );

        $camp = $this->retrieveExistingCamp($line_converted,$fields);

        if(!empty($camp) && count($camp) == 1) {
            return $camp;
        }else if(empty($camp)) {

            //create a new function to create the camp
            //this is so we can support the need for different 'types'
            //of camps which may need different processing.
            return $this->createCamp($line_converted,$fields);
        }else{
            //multiple camps found

        }
    }

    function getDate($data) {
        global $timedate;
        $ts = strtotime (str_replace("-", "", $data), $timedate->getNow()->getTimestamp());
        if($ts === false) {
            //date unable to be determined, will require manual update of record
            //return null and allow the camp to be created and process to continue
            return null;
        }
        $user_date= $timedate->asUserDate($timedate->fromTimestamp($ts),true);
        return $timedate->asDbDate($timedate->fromUserDate($user_date),false);
    }

    /*
     * Get participant record
     * Create or retrieve participant from line data
     *
     *
     * return participant object or null
     */

    function getParticipant($line_converted,$camp) {
        global $db;
        if(!$this->isCampBooking($line_converted)) {
            //not a camp booking
            return null;
        }
        $update_contact = false;
        $fields = array(
            //'salutation' => $line_converted['TITLE'],
          'first_name'=> $line_converted['FIRST_NAME'],
          'last_name' => $line_converted['LAST_NAME'],
            //'email1' => $line_converted['EMAIL_ADDRESS']
        );

        //Check if this is a duplicate booking
        $duplicate_participant = BeanFactory::getBean('CampParticipants')->retrieve_by_string_fields($fields + array('camp_id'=> $camp->id));
        if(isset($duplicate_participant) && !empty($duplicate_participant)){
            //return this participant as the data is duplicated
            if(!is_array($duplicate_participant)){
                if($line_converted['EMAIL_ADDRESS'] == $duplicate_participant->emailAddress->getPrimaryAddress($duplicate_participant)){
                    return $duplicate_participant;
                }
            }else{
                //error 2 participants with the same data exist.
                //manual checking needed ??
                //return null;
            }
        }


        //if the not first time flag is set look for existing participants
        if(isset($line_converted['NOT_FIRST']) && $line_converted['NOT_FIRST'] == "TRUE") {
            $participant = BeanFactory::getBean('CampParticipants')->retrieve_by_string_fields($fields);
            if(isset($participant) && !empty($participant)) {
                if(!is_array($participant)){
                    if($line_converted['EMAIL_ADDRESS'] == $participant->emailAddress->getPrimaryAddress($participant)){
                        if(is_null($participant->camp_id)) {
                            //existing record for the participants email/name;
                            //or $participant has not been added to the camp yet
                            return $participant;
                        }else if( $participant->camp_id == $camp->id){
                            //existing record for the participants email/name/camp;
                            //for some reason this record has not been updated in the database to booked
                            $query = "UPDATE
                                        sbcc_website_data
                                      SET
                                        record_status = 'booked'
                                      WHERE
                                        participant_id = '{$participant->id}'";
                            $db->query($query, true, "Error attempting to update a booking record for participant: {$participant->id}");
                            return;
                        }

                        //get the contact related to the existing participant record
                        if(!empty($participant->contact_id)) {
                            $contact = BeanFactory::getBean('Contacts',$participant->contact_id);
                            $update_contact = true;
                        }
                    }

                }
            }
        }

        //create participant record

        if($line_converted['FIRST_NAME'] != "") {
            if(!isset($contact)) {
                //create a new contact for this participant
                $update_contact = false;
                $contact = BeanFactory::getBean('Contacts');
                $contact->salutation = isset($line_converted['TITLE'])? $line_converted['TITLE']: null;
                $contact->first_name = isset($line_converted['FIRST_NAME'])? $line_converted['FIRST_NAME']: null;
                $contact->last_name = isset($line_converted['LAST_NAME'])? $line_converted['LAST_NAME']: null;
                $contact->primary_address_street = isset($line_converted['ADDRESS'])? $line_converted['ADDRESS']: null;
                $contact->primary_address_city = isset($line_converted['SUBURB'])? $line_converted['SUBURB']: null;
                $contact->primary_address_postalcode = isset($line_converted['POSTCODE'])? $line_converted['POSTCODE']: null;
                $contact->primary_address_state = isset($line_converted['STATE'])? $line_converted['STATE']: null;

                if(isset($line_converted['TELEPHONE_NUMBER']) && isset($line_converted['PHONE_TYPE'])) {
                    $contact->{'phone_'.strtolower($line_converted['PHONE_TYPE'])} = str_replace(' ', '',$line_converted['TELEPHONE_NUMBER']);
                }
                if(isset($line_converted['TELEPHONE_NUMBER2']) && isset($line_converted['PHONE_TYPE2'])) {
                    if($contact->{'phone_'.strtolower($line_converted['PHONE_TYPE2'])} != "") {
                        $contact->phone_other = $line_converted['TELEPHONE_NUMBER2'];
                    } else {
                        $contact->{'phone_'.strtolower($line_converted['PHONE_TYPE2'])} = $line_converted['TELEPHONE_NUMBER2'];
                    }
                }
                if(isset($line_converted['TELEPHONE_NUMBER3']) && isset($line_converted['PHONE_TYPE3'])) {
                    if($contact->{'phone_'.strtolower($line_converted['PHONE_TYPE3'])} != "" && empty($contact->phone_other)) {
                        $contact->phone_other = $line_converted['TELEPHONE_NUMBER3'];
                    } elseif($contact->{'phone_'.strtolower($line_converted['PHONE_TYPE3'])} == "") {
                        $contact->{'phone_'.strtolower($line_converted['PHONE_TYPE3'])} = $line_converted['TELEPHONE_NUMBER3'];
                    } else {
                        //$contact->phone_other_alternative =  $line_converted['TELEPHONE_NUMBER3'];
                    }
                }
                $contact->save();
                //add email address to contact
                if(isset($line_converted['EMAIL_ADDRESS']) && !empty($line_converted['EMAIL_ADDRESS'])) {
                    $sea = new SugarEmailAddress;
                    // Add a primary email address
                    $sea->addAddress($line_converted['EMAIL_ADDRESS'], true);
                    $sea->save($contact->id, $contact->module_dir);
                }

            }

            $participant = BeanFactory::getBean('CampParticipants');
            $participant->contact_id = $contact->id;
            $participant->salutation = isset($line_converted['TITLE'])? $line_converted['TITLE']: '';
            $participant->first_name = isset($line_converted['FIRST_NAME'])? $line_converted['FIRST_NAME']: '';
            $participant->last_name = isset($line_converted['LAST_NAME'])? $line_converted['LAST_NAME']: '';
            $participant->primary_address_street = isset($line_converted['ADDRESS'])? $line_converted['ADDRESS']: '';
            $participant->primary_address_city = isset($line_converted['SUBURB'])? $line_converted['SUBURB']: '';
            $participant->primary_address_postalcode = isset($line_converted['POSTCODE'])? $line_converted['POSTCODE']: '';
            $participant->primary_address_state = isset($line_converted['STATE'])? $line_converted['STATE']: '';
            //$participant->primary_address_country;


            if(isset($line_converted['TELEPHONE_NUMBER']) && isset($line_converted['PHONE_TYPE'])) {
                $participant->{'phone_'.strtolower($line_converted['PHONE_TYPE'])} = str_replace(' ', '',$line_converted['TELEPHONE_NUMBER']);
            }
            if(isset($line_converted['TELEPHONE_NUMBER2']) && isset($line_converted['PHONE_TYPE2'])) {
                if($participant->{'phone_'.strtolower($line_converted['PHONE_TYPE2'])} != "") {
                    $participant->phone_other = $line_converted['TELEPHONE_NUMBER2'];
                } else {
                    $participant->{'phone_'.strtolower($line_converted['PHONE_TYPE2'])} = $line_converted['TELEPHONE_NUMBER2'];
                }
            }
            if(isset($line_converted['TELEPHONE_NUMBER3']) && isset($line_converted['PHONE_TYPE3'])) {
                if($contact->{'phone_'.strtolower($line_converted['PHONE_TYPE3'])} != "" && empty($contact->phone_other)) {
                    $contact->phone_other = $line_converted['TELEPHONE_NUMBER3'];
                } elseif($contact->{'phone_'.strtolower($line_converted['PHONE_TYPE3'])} == "") {
                    $contact->{'phone_'.strtolower($line_converted['PHONE_TYPE3'])} = $line_converted['TELEPHONE_NUMBER3'];
                } else {
                    //$contact->phone_other_alternative =  $line_converted['TELEPHONE_NUMBER3'];
                }
            }



            $participant->participant_age = isset($line_converted['AGE'])? $line_converted['AGE']: '';
            //$participant->email_address = isset($line_converted['EMAIL_ADDRESS'])? $line_converted['EMAIL_ADDRESS']: '';
            $participant->how_booked = isset($line_converted['HOW_TEXT'])? $line_converted['HOW_TEXT']: null;

            $participant->booked_date = isset($line_converted['BOOKED_DATE'])? $line_converted['BOOKED_DATE']: null;

            $participant->allergy = isset($line_converted['ALLERGY'])? $line_converted['ALLERGY']: null;
            $participant->medical  = isset($line_converted['MEDICAL'])? $line_converted['MEDICAL']: '';
            $participant->comments = isset($line_converted['COMMENTS'])? $line_converted['COMMENTS']: '';

            //$participant->confirmed
            $participant->purchase_cap = isset($line_converted['CCCAP'])?TRUE:FALSE;
            $participant->purchase_photo = isset($line_converted['SGP'])?TRUE:FALSE;
            $participant->purchase_ball = isset($line_converted['RWB'])?TRUE:FALSE;
            $participant->purchase_mini_bat = isset($line_converted['MBAT'])?TRUE:FALSE;

            $participant->friend_first_name = isset($line_converted['FRIEND_NAME'])? $line_converted['FRIEND_NAME']: '';
            $participant->friend_surname = isset($line_converted['FRIEND_SURNAME'])? $line_converted['FRIEND_SURNAME']: '';
            $participant->club_name = isset($line_converted['CLUB_NAME'])? $line_converted['CLUB_NAME']: '';



            $participant->amount_owing = isset($line_converted['AMOUNT_OWING'])? '$' . number_format(($line_converted['AMOUNT_OWING'] /100), 2, '.', ' '): null;
            if(isset($line_converted['PAYMENT_TYPE'])) {
                if($line_converted['PAYMENT_TYPE'] == 'Pay in person') {
                    $line_converted['PAYMENT_TYPE'] = 'cash';
                }
                if($line_converted['PAYMENT_TYPE'] == 'Cheque') {
                    $line_converted['PAYMENT_TYPE'] = 'cheque';
                }
            }
            $participant->payment_type = isset($line_converted['PAYMENT_TYPE'])? $line_converted['PAYMENT_TYPE']: null;


            $participant->confirmation = FALSE;

            //create contact
            $participant->cb_create_contact = $update_contact;
            $participant->update_contact = false;
            //this flag will stop the email address being deleted
            $participant->in_workflow = true;

            $participant->save();
            //add email address to participant
            if(isset($line_converted['EMAIL_ADDRESS']) && !empty($line_converted['EMAIL_ADDRESS'])) {
                $sea = new SugarEmailAddress;
                // Add a primary email address
                $sea->addAddress($line_converted['EMAIL_ADDRESS'], true);
                $sea->save($participant->id, $participant->module_dir);
            }


            return $participant;
        }
        return;

    }

    function addParticitpantToCamp($participant,$camp,$filename,$data) {
        global $db;
        if ($camp->load_relationship('campparticipants')) {
            $participant->update_contact = false;
            $participant->cb_create_contact = false;
            $participant->add_to_target_list = true;
            if($camp->campparticipants->add($participant->id)!=false) {
                //$participant = $participant->id;
            }
            //extra processing for camp types
            switch ($data['CAMP_TYPE']) {
                case 'Country Winter Warriors':
                case 'Winter Warriors':
                    $this->addParticitpantToCampSession($participant, $camp, $data);
                    break;
                default:
                    break;
            }

        }
    }

    function updateParticipantWithPayment($participant,$line_converted) {
        $participant->amount_paid = isset($line_converted['AMOUNT_PAID'])? '$' . number_format(($line_converted['AMOUNT_PAID'] /100), 2, '.', ' ') : null;
        $participant->amount_owing = isset($line_converted['AMOUNT_OWING'])? '$' . number_format(($line_converted['AMOUNT_OWING'] /100), 2, '.', ' ') : null;
        if(isset($line_converted['PAYMENT_TYPE'])) {
            if($line_converted['PAYMENT_TYPE'] == 'securepayau_xml') {
                $line_converted['PAYMENT_TYPE'] = 'card';
            }
        }
        $participant->payment_type = isset($line_converted['PAYMENT_TYPE'])? $line_converted['PAYMENT_TYPE']: null;
        $participant->date_paid = isset($line_converted['DATE_PAID'])? $line_converted['DATE_PAID']: null;
        $participant->confirmation = true;

        $participant->update_contact = false;
        $participant->save();
    }

    function updateGroupParticipantWithPayment($participant,$line_converted) {
        $participant->amount_paid = isset($line_converted['AMOUNT_PAID'])? '$' . number_format(($line_converted['AMOUNT_PAID'] /100), 2, '.', ' ') . " GROUP BOOKING": null;
        $participant->amount_owing = isset($line_converted['AMOUNT_OWING'])? '$' . number_format(($line_converted['AMOUNT_OWING'] /100), 2, '.', ' ') . " GROUP BOOKING": null;
        if(isset($line_converted['PAYMENT_TYPE'])) {
            if($line_converted['PAYMENT_TYPE'] == 'securepayau_xml') {
                $line_converted['PAYMENT_TYPE'] = 'card';
            }
        }
        $participant->payment_type = isset($line_converted['PAYMENT_TYPE'])? $line_converted['PAYMENT_TYPE']: null;
        $participant->date_paid = isset($line_converted['DATE_PAID'])? $line_converted['DATE_PAID']: null;
        $participant->confirmation = true;

        $participant->update_contact = false;
        $participant->save();
    }

    /*
     * Session refers to 'group'
     */
    function addParticitpantToCampSession($participant, $camp, $data) {
        if($data['CAMP_TYPE'] == 'Country Winter Warriors' && !isset($data['DAY'])){
            $data['DAY'] = 'Sunday';
        }
        //look for existing session
        $addTo_session = null;
        $time = trim($data['TIMES']);//remove spaces
        $time = str_replace ( "." , ":" , $time);
        $sesson_name = $data['DAY']." - ".strtolower($time);
        if (($sessions = $camp->get_linked_beans('campgroups','CampGroups')) != null) {
            foreach($sessions as $session){
                if(strcasecmp($session->name, $sesson_name) == 0){
                    $addTo_session = $session;
                    break;
                }
            }
        }
        //create session if not found
        if($addTo_session == null){
            $addTo_session = BeanFactory::getBean('CampGroups');
            $addTo_session->name = $sesson_name;
            $addTo_session->camp_id = $camp->id;
            $addTo_session->camp_name = $camp->name;
            $addTo_session->description = "{$camp->type} Session {$addTo_session->name}";
            $addTo_session->save();
        }
        //add participant to sesson
        $addTo_session->load_relationship('participants');
        $addTo_session->participants->add($participant->id);
    }


}
?>

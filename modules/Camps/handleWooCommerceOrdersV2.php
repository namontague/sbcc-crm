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


define('WOOCOMMERCE_URL', 'https://cricketcoaching.com.au/');
define('CONSUMER_KEY', 'ck_47158d7172259f67cf9b7015c0ae02bd80ee04e3');//'ck_368b7637d74a155b2c16e9b63959684a2c6fc547'
define('CONSUMER_SECRET', 'cs_146fe075dfd528c8027a0419f376f2b58a5675c6');//'cs_d54cef5ddfdac95b1882bc66f3653a9c5e75ff87'

class handleWooCommerceOrdersV2 {
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
//  private function makeRequest_old($endpoint, $params) {
//    $url = $this->baseUrl . $endpoint;
//
//    // Add authentication parameters
//    //$params['consumer_key'] = $this->consumerKey;
//    //$params['consumer_secret'] = $this->consumerSecret;
//
//    $credentials = base64_encode($this->consumerKey . ':' . $this->consumerSecret);
//    $authHeader = 'Basic ' . $credentials;
//
//    // Build query string
//    $queryString = http_build_query($params);
//    $fullUrl = $url . '?' . $queryString;
//
//    // Initialize cURL
//    $ch = curl_init();
//    curl_setopt_array($ch, array(
//      CURLOPT_URL => $fullUrl,
//      CURLOPT_RETURNTRANSFER => true,
//      CURLOPT_TIMEOUT => 30,
//      CURLOPT_SSL_VERIFYPEER => true,
//      CURLOPT_CUSTOMREQUEST => 'GET',
//      CURLOPT_USERAGENT => 'WooCommerce Orders API Client/1.0',
//      CURLOPT_HTTPHEADER => array(
//        'Authorization: ' . $authHeader,
//        'Content-Type: application/json',
//        'Accept: application/json'
//      )
//    ));
//
//
//
//    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
//    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
//
//    $response = curl_exec($ch);
//    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//    $error = curl_error($ch);
//    curl_close($ch);
//
//    if ($error) {
//      throw new Exception('cURL Error: ' . $error);
//    }
//
//    if ($httpCode !== 200) {
//      throw new Exception('HTTP Error: ' . $httpCode . ' - ' . $response);
//    }
//
//    return json_decode($response, true);
//  }

  private function makeRequest($endpoint, $params) {
    $url = $this->baseUrl . $endpoint;

    $credentials = base64_encode($this->consumerKey . ':' . $this->consumerSecret);
    $authHeader = 'Basic ' . $credentials;

    // Build query string â€” use PHP_QUERY_RFC3986 and handle arrays properly
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
  private function buildQueryString(array $params) {
    $parts = array();

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

  /**
   * Get orders after a specific date
   */
  public function getNewOrdersAfterDate($afterDate, $status = 'any', $perPage = 50) {
    try {
      // Validate and format date
      $dateTime = new DateTime($afterDate);
      $formattedDate = $dateTime->format('Y-m-d\TH:i:s');

      $orders = $this->makeRequest('/wp-json/wc/v3/orders', array(
        'after'    =>$formattedDate,// '2026-08-15T20:50:05',//$formattedDate,
       // 'before'   => '2026-08-15T20:56:05',
        'status'   => array('processing', 'completed'),
        'per_page' => min($perPage, 100),
        'page'     => 1,
        'orderby'  => 'date',
        'order'    => 'asc'
      ));

      return array(
        'success' => true,
        'count'   => count($orders),
        'orders'  => $this->formatOrders($orders)
      );

    } catch (Exception $e) {
      return array(
        'success' => false,
        'error'   => $e->getMessage()
      );
    }
  }

  /**
   * Format orders for clean output
   */
  private function formatOrders($orders) {
    $orders_fomatted = array();
    foreach ($orders as $raw_order) {
      $orders_fomatted[] = $this->parse_cricket_order($raw_order);
    }
    return $orders_fomatted;
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

//  function connect() {
//    // set up a connection or die
//    $conn_id = ftp_connect($this->ftp_server) or die("Couldn't connect to $this->ftp_server");
//    // try to login
//    if (@ftp_login($conn_id, $this->ftp_user, $this->ftp_pass)) {
//      //    echo "Connected as $ftp_user@$ftp_server\n";
//      return $conn_id;
//    } else {
//      return null;
//      //    echo "Couldn't connect as $ftp_user\n";
//    }
//
//
//  }
//
//  function close_connection() {
//    if($this->connection) {
//      ftp_close($this->connection);
//    }
//  }

//  function setCampName($camp_name){
//    return trim(preg_replace($this->strip_from_name, "", $camp_name));
//  }

  function processNewBookings() {
    global $db,$timedate;
    //Order by file_name so duplicate records are ordered in the correct date order.
    //This way the booking time is set as the date on the first file.
    $query = "SELECT * from sbcc_website_data where record_status = 'pending-wp' order by file_name";
    $result = $db->query($query, true, "Error attempting to retrieve file list from sbcc_website_data");
    while($row = $db->fetchByAssoc($result)) {
      $order_data = json_decode(htmlspecialchars_decode($row['file_data']),true);

      $camps = $order_data['camps'];
      if(!empty($camps)) {
        foreach($camps as $camp) {
          $camp_data = array();
          if($camp['children'] != null) {
            $camp_data['CAMP_NAME'] = "WP Test - " . $camp['name'];
            $camp_data['CAMP_START'] = $camp['booking']['from_date'];
            $camp_data['CAMP_END'] = $camp['booking']['to_date'];

            $dateTime = new DateTime($order_data['date']);
            $camp_data['BOOKED_DATE'] = $dateTime->format('Y-m-d H:i:s');

            foreach($camp['children'] as $participant) {

              if(($camp = $this->getCamp($camp_data,$row['file_name'])) != null){
                //get participant details//creating if not found
                if(($participant = $this->getParticipant($camp_data,$camp,$order_data,$participant))!=null) {
                  //add $participant to $camp
                  //includes updating db record to booked if relation is added
                  $this->addParticitpantToCamp($participant, $camp, $row['file_name'] ,$camp_data);
                  $participants[] = $participant->id;
                }
              } else {
                //not a camp booking, update db
                //$db->quote()
                $escaped_file_name = $db->quote($row['file_name']);
                $query = "UPDATE sbcc_website_data SET record_status = 'skip-wp' WHERE file_name = '{$escaped_file_name}'";
                $db->query($query, true, "Error attempting to update a booking record for file: {$row['file_name']}");
              }
            }
          } else {
            // no participants, update db record
            $escaped_file_name = $db->quote($row['file_name']);
            $query = "UPDATE sbcc_website_data SET record_status = 'no-participants-wp' WHERE file_name = '{$escaped_file_name}'";
            $db->query($query, true, "Error attempting to update a booking record for file: {$row['file_name']}");
          }
        }
      } else {
        //not a camp booking, update db
        //$db->quote()
        $escaped_file_name = $db->quote($row['file_name']);
        $query = "UPDATE sbcc_website_data SET record_status = 'not-camp-wp' WHERE file_name = '{$escaped_file_name}'";
        $db->query($query, true, "Error attempting to update a booking record for file: {$row['file_name']}");
      }

      //update row with all participants
      if(!empty($participants)) {
        if(count($participants) >= 1) {
          $participants = json_encode($participants);
//          $group = false;
//          if(count($participants) > 1) {
//            $group = true;
//          }
        }

        $query = "UPDATE
                        sbcc_website_data
                      SET
                        record_status = 'completed-wp',
                        participant_id = '$participants'
                      WHERE
                        file_name = '{$row['file_name']}'";
        $db->query($query, true, "Error attempting to update a booking record for file: {$row['file_name']}");
        $participants = array();
      }
    }
    return true;

  }

  function processCompleteBookings() {
    global $db, $timedate;
    $c = 1;
    $query = "SELECT * from sbcc_website_data
                where record_status = 'booked-wp'";

    $result = $db->query($query, true, "Error attempting to retrieve file list from sbcc_website_data");
    while($row = $db->fetchByAssoc($result)) {
      $converted_data = json_decode(htmlspecialchars_decode($row['file_data']),true);

      if(isset($converted_data['payment']['method'])) {
        if($converted_data['payment']['method'] == 'stripe') {
          $converted_data['PAYMENT_TYPE'] = 'card';
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

//  function formatDates($data) {
//    global $timedate;
//    //get year off the end
//    $year = substr($data['CAMP_DATE'], -4);
//    //remove year from string
//
//    $date = trim(substr($data['CAMP_DATE'],0,-4));
//
//    //remove tailing common if exists
//    if(substr($date,-1) == ',') {
//      $date = substr($date,0,-1);
//    }
//
//    //get date values
//    $date_data = explode(",",$date);
//    $date_data = array_map("trim", $date_data);
//
//    //if year value is not in date string add it
//    if (strpos($date_data[0],$year) === false) {
//      $date_data[0] = $date_data[0]." ".$year;
//    }
//
//    $start_month = date ("F", strtotime ($date_data[0], $timedate->getNow()->getTimestamp()));
//
//    $months = array(
//      'Jan','Feb','Mar','Apr','May','Jun',
//      'Jul','Aug','Sep','Oct','Nov','Dec'
//    );
//
//    $month_missing = false;
//    foreach ($months as $month) {
//      if (strpos($date_data[count($date_data)-1],$month) === false) {
//        $month_missing = true;
//      } else {
//        $month_missing = false;
//        break;
//      }
//    }
//    //Add month if found to be missing
//    //eg: October 3, 4 2013
//    if($month_missing == true) {
//      $date_data[count($date_data)-1] =  $start_month." ".$date_data[count($date_data)-1];
//    }
//
//    //add year to the last value in the array
//    if (strpos($date_data[count($date_data)-1],$year) === false) {
//      $date_data[count($date_data)-1] = $date_data[count($date_data)-1]." ".$year;
//    }
//
//    return array('start'=>$date_data[0],'end'=>$date_data[count($date_data)-1]);
//  }

//  function getExistingBookingList() {
//    global $db;
//
//    $query = "SELECT file_name from sbcc_website_data";
//    $result = $db->query($query, true, "Error attempting to retrieve file list from sbcc_website_data");
//    $files = array();
//    while($row = $db->fetchByAssoc($result)) {
//      //return everything
//      $files[] = $row['file_name'];
//    }
//    return $files;
//
//  }

//  function getExistingBookingWithPaymentList() {
//    global $db;
//    //get file names of those records with payment data
//    $query = "SELECT file_name from sbcc_website_data where file_payment_data is not null";
//    $result = $db->query($query, true, "Error attempting to retrieve file list from sbcc_website_data");
//    $files = array();
//    while($row = $db->fetchByAssoc($result)) {
//      //return everything
//      $files[] = $row['file_name'];
//    }
//    return $files;
//
//  }

//  function getBookings() {
//    // get dir listing
//    $contents = array();//ftp_nlist($this->connection, $this->booking_dir);
//    if(!empty($contents)) {
//      $func = function($value) {
//        return substr( $value, strrpos( $value, '/' )+1 );
//      };
//      $contents_altered = array_map($func, $contents);
//    }else{
//      //no contents
//      return;
//    }
//    //load up all the existing booking files
//    $existing_files = $this->getExistingBookingList();
//    foreach($contents_altered as $file) {
//      //look for new files
//      if(!in_array($file, $existing_files) && $file != 'payment') {
//        $files_needed[] = $file;
//      }
//    }
//    return !empty($files_needed)?$files_needed:null;
//
//  }

//  function getPaymentRecord() {
//    // get dir listing
//    $contents = ftp_nlist($this->connection, $this->payment_dir);
//    if(!empty($contents)) {
//      $func = function($value) {
//        return substr( $value, strrpos( $value, '/' )+1 );
//      };
//      $contents_altered = array_map($func, $contents);
//    }else{
//      //no contents
//      return;
//    }
//    //load up all the existing booking files
//    $existing_files = $this->getExistingBookingWithPaymentList();
//    foreach($contents_altered as $file) {
//      //look for new files
//      if(!in_array($file, $existing_files)) {
//        $files_needed[] = $file;
//      }
//    }
//    return !empty($files_needed)?$files_needed:null;
//
//  }

//  function addBooking($remote_filename) {
//    $remote_file = $this->booking_dir."/".$remote_filename;
//    ob_end_flush();
//    ob_start();
//    $out = fopen('php://output', 'w');
//    if (($data = ftp_fget($this->connection, $out, $remote_file, FTP_ASCII)) == false) {
//      //echo "There was a problem\n";
//      fclose($out);
//    } else {
//      fclose($out);
//      $data = ob_get_clean();
//      if(!empty($data)) {
//        global $db;
//        $query = "INSERT INTO sbcc_website_data
//                            (file_name, date_created, record_status, file_data)
//                          VALUES
//                            ('$remote_filename', now(), 'pending', '$data')";
//        $db->query($query, true, "Error attempting to insert a booking record for file: ".$remote_filename);
//      }
//    }
//    return;
//
//
//  }
//
//  function addPayment($remote_filename) {
//    $remote_file = $this->payment_dir."/".$remote_filename;
//    ob_end_flush();
//    $pathinfo = pathinfo($remote_file);
//    if(isset($pathinfo['extension'])) {
//      //not a payment file/these dont have extensions
//      return;
//    }
//    ob_start();
//    $out = fopen('php://output', 'w');
//    if (($data = ftp_fget($this->connection, $out, $remote_file, FTP_ASCII)) == false) {
//      //echo "There was a problem\n";
//    } else {
//      //echo "connected\n";
//    }
//    fclose($out);
//    $data = ob_get_clean();
//    if(!empty($data)) {
//      global $db;
//      $query = "UPDATE sbcc_website_data
//                        SET file_payment_data = '$data'
//                      WHERE
//                        file_name = '$remote_filename'
//                      AND
//                        file_payment_data is null";
//      //('$remote_filename', now(), 'pending', '$data')";
//      $db->query($query, true, "Error attempting to update a booking record with payment data for file: ".$remote_filename);
//      return;
//    }
//    return;
//
//  }

  function retrieveExistingCamp($data, $fields){
    $camp = null;
//    switch ($data['CAMP_TYPE']) {
//      case 'Country Winter Warriors':
//      case 'Winter Warriors':
//        $booking_date = date_parse($data['BOOKED_DATE']);
//        $name = "{$data['CAMP_TYPE']} {$booking_date['year']} - ".trim($data['VENUE']);
//        $camp = BeanFactory::getBean('Camps')->retrieve_by_string_fields(array('name'=> $name));
//        break;
//      default:
//        $camp = BeanFactory::getBean('Camps')->retrieve_by_string_fields($fields);
//        break;
//    }
    $camp = BeanFactory::getBean('Camps')->retrieve_by_string_fields($fields);
    return $camp;
  }
  function createCamp($data, $fields) {
    $camp = BeanFactory::getBean('Camps');

    //override default data with type specfic details
//    switch ($data['CAMP_TYPE']) {
//      case 'Country Winter Warriors':
//      case 'Winter Warriors':
//        //name = Winter Warriors - {$venue}
//        $booking_date = date_parse($data['BOOKED_DATE']);
//        $camp_name = "{$data['CAMP_TYPE']} {$booking_date['year']} - ".trim($data['VENUE']);
//        $start_date = '';//$fields['start_date']; //set to the first of may for year
//        $end_date = '';//$fields['end_date']; //set to the end of sep for year
//
//        //create venue/address str
//        $venue = $data['VENUE'];
//        $venue_address_street = '';
//        $venue_address_city = '';
//        if(isset($data['VENUE_ADDRESS'])) {
//          $venue_address_street = $data['VENUE_ADDRESS'];
//        }
//        if(isset($data['VENUE_SUBURB'])) {
//          $venue_address_city = $data['VENUE_SUBURB'];
//        }
//        break;
//
//      default:
//        $camp_name = $data['CAMP_NAME'] . ' ' .$data['CAMP_TYPE'];
//        $start_date = is_null($fields['start_date']) ? '' : $fields['start_date'];
//        $end_date =  is_null($fields['end_date']) ? '' : $fields['end_date'];
//        //create venue/address str
//        $venue = '';
//        $venue_address_street = '';
//        $venue_address_city = '';
//        if(isset($data['CAMP_ADDRESS'])) {
//          $address = explode(",", $data['CAMP_ADDRESS']);
//          if(!preg_match('/[A-Z]+[a-z]+[0-9]+/', $address[0]) && count($address) >= 3) {
//            $venue =  trim($address[0]);
//            $venue_address_street = isset($address[1])? $address[1]: '';
//            $venue_address_city = isset($address[2])? $address[2]: '';
//          } else {
//            $venue_address_street = $data['CAMP_ADDRESS'];
//          }
//        }
//        break;
//    }
    $camp_name = $data['CAMP_NAME'];
    $start_date = is_null($fields['start_date']) ? '' : $fields['start_date'];
    $end_date =  is_null($fields['end_date']) ? '' : $fields['end_date'];
    //create venue/address str
    $venue = '';
    $venue_address_street = '';
    $venue_address_city = '';
//    if(isset($data['CAMP_ADDRESS'])) {
//      $address = explode(",", $data['CAMP_ADDRESS']);
//      if(!preg_match('/[A-Z]+[a-z]+[0-9]+/', $address[0]) && count($address) >= 3) {
//        $venue =  trim($address[0]);
//        $venue_address_street = isset($address[1])? $address[1]: '';
//        $venue_address_city = isset($address[2])? $address[2]: '';
//      } else {
//        $venue_address_street = $data['CAMP_ADDRESS'];
//      }
//    }

    $camp->name = $camp_name;
    $camp->start_date = $start_date;
    $camp->end_date = $end_date;
    $camp->venue = $venue;
    $camp->venue_address_street = trim($venue_address_street);
    $camp->venue_address_city = trim($venue_address_city);
    //$camp->type = $data['CAMP_TYPE'];
    $camp->save();

    return $camp;
  }


  function isCampBooking($line_converted){
    $isCampBooking = true;
    if(empty($line_converted['CAMP_NAME'])) {
      //not a camp booking
      return false;
    }

    if(strpos($line_converted['CAMP_NAME'],'Weekly') !== false ||
       strpos($line_converted['CAMP_NAME'],'Fortnightly') !== false ||
       strpos($line_converted['CAMP_NAME'],'Monthly') !== false) {
      //not a camp booking,
      return false;
    }

    if($line_converted['CAMP_NAME'] == 'Casual Net Hire' || $line_converted['CAMP_NAME'] == 'Club Net Hire') {
      //not a camp booking, this is net hire
      return false;
    }

    //need this extra case as winter warrior camps dont have a name
    //adding strpos as there are now different types of warrior camps
    if(isset($line_converted['CAMP_TYPE']) && strpos($line_converted['CAMP_TYPE'],'Winter Warriors') !== false) {
      //is a camp booking
      return true;
    }
    return $isCampBooking;
  }


  function getCamp($line_converted, $order_id = null) {
    //look for matching camp in the db
    $fields = array(
      'name'=> $line_converted['CAMP_NAME'],
      'start_date' => date('Y-m-d', strtotime($line_converted['CAMP_START'])),
      'end_date' => date('Y-m-d', strtotime($line_converted['CAMP_END']))
    );


    if($this->isCampBooking($line_converted)) {
      $camp = $this->retrieveExistingCamp($line_converted, $fields);

      if (!empty($camp) && count($camp) == 1) {
        return $camp;
      } else if (empty($camp)) {

        //create a new function to create the camp
        //this is so we can support the need for different 'types'
        //of camps which may need different processing.
        return $this->createCamp($line_converted, $fields);
      } else {
        //multiple camps found

      }
    } else {


      return false; //not a camp booking
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
    $user_date = $timedate->asUserDate($timedate->fromTimestamp($ts),true);
    return $timedate->asDbDate($timedate->fromUserDate($user_date),false);
  }

  /*
   * Get participant record
   * Create or retrieve participant from line data
   *
   *
   * return participant object or null
   */

  function getParticipant($line_converted,$camp,$order_data, $participant_data) {
    global $db, $timedate;
    if(!$this->isCampBooking($line_converted)) {
      //not a camp booking
      return null;
    }
    $update_contact = false;

    $participant_name_data = explode(' ', trim($participant_data['child_name']), 2);
    $participant_data['FIRST_NAME'] = $participant_name_data[0];
    $participant_data['LAST_NAME']  = isset($participant_name_data[1]) ? $participant_name_data[1] : '';

    $fields = array(
      'first_name'=> $participant_data['FIRST_NAME'],
      'last_name' => $participant_data['LAST_NAME'],
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

    //create participant record
    if($participant_data['FIRST_NAME'] != "") {
      if(!isset($contact)) {
        //create a new contact for this participant
        $update_contact = false;
        $contact = BeanFactory::getBean('Contacts');
        $contact->salutation = isset($line_converted['TITLE'])? $line_converted['TITLE']: null;
        $contact->first_name = isset($participant_data['FIRST_NAME'])? $participant_data['FIRST_NAME']: null;
        $contact->last_name = isset($participant_data['LAST_NAME'])? $participant_data['LAST_NAME']: null;

//        $contact->primary_address_street = isset($line_converted['ADDRESS'])? $line_converted['ADDRESS']: null;
//        $contact->primary_address_city = isset($line_converted['SUBURB'])? $line_converted['SUBURB']: null;
//        $contact->primary_address_postalcode = isset($line_converted['POSTCODE'])? $line_converted['POSTCODE']: null;
//        $contact->primary_address_state = isset($line_converted['STATE'])? $line_converted['STATE']: null;

        if(isset($order_data['contact']['phone'])) {
          $contact->phone_mobile = str_replace(' ', '',$order_data['contact']['phone']);
        }

        $contact->save();
        //add email address to contact
        if(isset($order_data['contact']['email']) && !empty($order_data['contact']['email'])) {
          $sea = new SugarEmailAddress;
          // Add a primary email address
          $sea->addAddress($line_converted['contact']['email'], true);
          $sea->save($contact->id, $contact->module_dir);
        }

      }

      $participant = BeanFactory::getBean('CampParticipants');
      $participant->contact_id = $contact->id;
      $participant->salutation = isset($line_converted['TITLE'])? $line_converted['TITLE']: '';
      $participant->first_name = isset($participant_data['FIRST_NAME'])? $participant_data['FIRST_NAME']: '';
      $participant->last_name = isset($participant_data['LAST_NAME'])? $participant_data['LAST_NAME']: '';
//      $participant->primary_address_street = isset($line_converted['ADDRESS'])? $line_converted['ADDRESS']: '';
//      $participant->primary_address_city = isset($line_converted['SUBURB'])? $line_converted['SUBURB']: '';
//      $participant->primary_address_postalcode = isset($line_converted['POSTCODE'])? $line_converted['POSTCODE']: '';
//      $participant->primary_address_state = isset($line_converted['STATE'])? $line_converted['STATE']: '';
//      $participant->primary_address_country;

      if(isset($order_data['contact']['phone'])) {
        $participant->phone_mobile = str_replace(' ', '',$order_data['contact']['phone']);
      }

      $participant->participant_age = isset($participant_data['age'])? (int)$participant_data['age']: '';
      //$participant->how_booked = isset($line_converted['HOW_TEXT'])? $line_converted['HOW_TEXT']: null;


      $participant->booked_date = isset($line_converted['BOOKED_DATE'])? $line_converted['BOOKED_DATE']: null;

      //$participant->allergy = isset($line_converted['allergy'])? $line_converted['allergy']: null;
      $participant->medical  = isset($participant_data['allergy'])? $participant_data['allergy']: '';
      $participant->comments = isset($participant_data['COMMENTS'])? $participant_data['COMMENTS']: '';
      $participant->friend_first_name = isset($participant_data['friend_grouping'])? $participant_data['friend_grouping']: '';
      $participant->club_name = isset($participant_data['cricket_club'])? $participant_data['cricket_club']: '';
      $participant->confirmation = FALSE;

      if(!empty($order_data['addon_products'])) {
        $booking_for_participants = 0;
        foreach($order_data['camps'] as $camp_data) {
          $booking_for_participants += $camp_data['quantity'];
        }

        foreach($order_data['addon_products'] as $addon_product) {
          if($addon_product['quantity'] == $booking_for_participants) {
            if($addon_product['name'] == 'Red/White Ball') {
              $participant->purchase_ball = TRUE;
            }elseif($addon_product['name'] == 'Camp Cap') {
              $participant->purchase_cap = TRUE;
            }elseif($addon_product['name'] == 'Signed Group Photo') {
              $participant->purchase_photo = TRUE;
            }elseif($addon_product['name'] == 'Mini Bat') {
              $participant->purchase_mini_bat = TRUE;
            }
          } else {
            //qty of addon prodcuts is not equal to the booking for participants
            //this meams not all participants have the same addon products
            //add a comment to make this clear
            $participant->comments = ' Group: Not all participants have the same addon products';
          }
        }
      }



      if(isset($order_data['payment']['method'])) {
        if($order_data['payment']['method'] == 'stripe') {
          $participant->payment_type  = 'card';

          if($booking_for_participants > 1) {
            $participant->amount_paid = isset($order_data['total'])? '$' . $order_data['total'] . " GROUP BOOKING" : null;
          } else {
            $participant->amount_paid = isset($order_data['total'])? '$' . $order_data['total'] : null;
          }
          $participant->date_paid = isset($line_converted['BOOKED_DATE'])? $line_converted['BOOKED_DATE'] : null;
          $participant->confirmation = true;
        } else {
          if($booking_for_participants > 1) {
            $participant->amount_owing = isset($order_data['total'])? '$' . $order_data['total']. " GROUP BOOKING": null;
          } else {
            $participant->amount_owing = isset($order_data['total'])? '$' . $order_data['total']: null;
          }
        }
      }

      //create contact
      $participant->cb_create_contact = $update_contact;
      $participant->update_contact = false;
      //this flag will stop the email address being deleted
      $participant->in_workflow = true;

      $participant->save();
      //add email address to participant
      if(isset($order_data['contact']['email']) && !empty($order_data['contact']['email'])) {
        $sea = new SugarEmailAddress;
        // Add a primary email address
        $sea->addAddress($order_data['contact']['email'], true);
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
    $participant->amount_paid = isset($line_converted['total'])? '$' . $line_converted['total'] : '$' . '0.00';
    $participant->payment_type = isset($line_converted['PAYMENT_TYPE'])? $line_converted['PAYMENT_TYPE']: null;
    $participant->date_paid = isset($line_converted['DATE_PAID'])? $line_converted['DATE_PAID']: null;
    $participant->confirmation = !is_null($participant->payment_type) ? true : false;

    $participant->update_contact = false;
    $participant->save();
  }

  function updateGroupParticipantWithPayment($participant,$line_converted) {
    $participant->amount_paid = isset($line_converted['total'])? '$' . $line_converted['total'] . " GROUP BOOKING": null;
    $participant->payment_type = isset($line_converted['PAYMENT_TYPE'])? $line_converted['PAYMENT_TYPE']: null;
    $participant->date_paid = isset($line_converted['DATE_PAID'])? $line_converted['DATE_PAID']: null;
    $participant->confirmation = !is_null($participant->payment_type) ? true : false;

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







  /**
   * WooCommerce Cricket Camp Order Parser
   *
   * Parses a WooCommerce REST API order response into structured data:
   *  - Order contact details
   *  - Camp line items with booking info
   *  - Children grouped into their correct camps (sequential assignment)
   *  - Add-on products (per-child extras)
   */


// ENTRY POINT
  /**
   * Master parser. Pass in a single order array (one element from the API response).
   *
   * @param array $order One order object decoded from the WC REST API JSON.
   * @return array {
   *   order_id, date, status, total, currency,
   *   contact       => [ fullname, email, phone ],
   *   payment       => [ method, title, transaction_id ],
   *   camps         => [ ...camp ],
   *   addon_products => [ ...product ],
   * }
   */
  function parse_cricket_order(array $order)
  {
    $meta_data = isset($order['meta_data']) ? $order['meta_data'] : array();
    $meta = $this->build_meta_lookup($meta_data);
    $line_items = isset($order['line_items']) ? $order['line_items'] : array();

    $split = $this->split_line_items($line_items);
    $camp_items  = $split[0];
    $addon_items = $split[1];

    $children = $this->extract_all_children($meta);
    $camps    = $this->build_camps($camp_items, $children);
    $addons   = $this->build_addon_products($addon_items);

    return array(
      'order_id'      => $order['id'],
      'order_number'  => $order['number'],
      'date'          => $order['date_created'],
      'status'        => $order['status'],
      'total'         => $order['total'],
      'currency'      => $order['currency'],
      'contact'       => $this->extract_contact($order, $meta),
      'payment'       => $this->extract_payment($order),
      'camps'         => $camps,
      'addon_products' => $addons,
    );
  }


// META HELPERS
  /**
   * Flatten the meta_data array into a simple key => value lookup.
   * Keeps the last value when duplicate keys exist.
   */
  function build_meta_lookup(array $meta_data)
  {
    $lookup = array();
    foreach ($meta_data as $entry) {
      $lookup[$entry['key']] = $entry['value'];
    }
    return $lookup;
  }

  /**
   * Same helper for a line item's meta_data array.
   */
  function build_line_item_meta_lookup(array $meta_data)
  {
    return $this->build_meta_lookup($meta_data);
  }


// LINE ITEM SPLITTING
  /**
   * Separate camp (booking) products from add-on (non-booking) products.
   *
   * A camp product has a "Booked From" key in its meta_data.
   * Add-on products do not.
   *
   * @return array [ $camp_items[], $addon_items[] ]
   */
  function split_line_items(array $line_items)
  {
    $camps  = array();
    $addons = array();

    foreach ($line_items as $item) {
      $meta_data = isset($item['meta_data']) ? $item['meta_data'] : array();
      $meta = $this->build_line_item_meta_lookup($meta_data);

      if (isset($meta['Booked From']) || isset($meta['select-game-match'])) {
        $camps[] = $item;
      } else {
        $addons[] = $item;
      }
    }

    return array($camps, $addons);
  }


// CONTACT & PAYMENT
  /**
   * Extract parent/guardian contact details.
   * Falls back through: order additional > order meta > billing fields.
   */
  function extract_contact(array $order, array $meta)
  {
    $billing_first = isset($order['billing']['first_name']) ? $order['billing']['first_name'] : '';
    $billing_last  = isset($order['billing']['last_name'])  ? $order['billing']['last_name']  : '';

    if (isset($order['additional']['fullname'])) {
      $fullname = $order['additional']['fullname'];
    } elseif (isset($meta['fullname'])) {
      $fullname = $meta['fullname'];
    } else {
      $fullname = trim($billing_first . ' ' . $billing_last);
    }

    if (isset($order['additional']['billing_email'])) {
      $email = $order['additional']['billing_email'];
    } elseif (isset($meta['billing_email'])) {
      $email = $meta['billing_email'];
    } elseif (isset($order['billing']['email'])) {
      $email = $order['billing']['email'];
    } else {
      $email = '';
    }

    if (isset($order['additional']['phone'])) {
      $phone = $order['additional']['phone'];
    } elseif (isset($meta['phone'])) {
      $phone = $meta['phone'];
    } elseif (isset($order['billing']['phone'])) {
      $phone = $order['billing']['phone'];
    } else {
      $phone = '';
    }

    return array(
      'fullname' => $fullname,
      'email'    => $email,
      'phone'    => $phone,
    );
  }

  /**
   * Extract payment method details.
   */
  function extract_payment(array $order)
  {
    return array(
      'method'         => isset($order['payment_method'])       ? $order['payment_method']       : '',
      'title'          => isset($order['payment_method_title']) ? $order['payment_method_title'] : '',
      'transaction_id' => isset($order['transaction_id'])       ? $order['transaction_id']       : '',
    );
  }


// CHILD EXTRACTION
  /**
   * Extract every child from the order meta_data.
   *
   * The first child uses bare keys (child_name, gender, age, â€¦).
   * Subsequent children append _N (child_name_2, gender_2, age_2, â€¦).
   *
   * Returns an array of child arrays, index 0 = first child.
   */
  function extract_all_children(array $meta)
  {
    $children = array();
    $index = 1;

    while (true) {
      $suffix = ($index === 1) ? '' : '_' . $index;

      $name = isset($meta['child_name' . $suffix]) ? $meta['child_name' . $suffix] : null;

      // Stop when there's no child name for this index
      if ($name === null) {
        break;
      }

      $children[] = array(
        'index'          => $index,
        'child_name'     => $name,
        'gender'         => isset($meta['gender' . $suffix])         ? $meta['gender' . $suffix]         : '',
        'age'            => isset($meta['age' . $suffix])            ? $meta['age' . $suffix]            : '',
        'allergy'        => isset($meta['allergy' . $suffix])        ? $meta['allergy' . $suffix]        : '',
        'cricket_club'   => isset($meta['cricket_club' . $suffix])   ? $meta['cricket_club' . $suffix]   : '',
        'skill_level'    => isset($meta['skill_level' . $suffix])    ? $meta['skill_level' . $suffix]    : '',
        'friend_grouping' => isset($meta['friend_grouping' . $suffix]) ? $meta['friend_grouping' . $suffix] : '',
        // "Are you coming with a friend" only appears on the first child
        'with_friend'    => ($index === 1) ? (isset($meta['friend_grouping1']) ? $meta['friend_grouping1'] : '') : '',
      );

      $index++;
    }

    return $children;
  }


// CAMP BUILDING
  /**
   * Build the full camp structures, each with its booking details and
   * the children assigned to it (sequential fill by quantity).
   *
   * @param array $camp_items Line items identified as booking products.
   * @param array $children All children extracted from order meta.
   * @return array
   */
  function build_camps(array $camp_items, array $children)
  {
    $camps = array();
    $child_cursor = 0;  // Tracks which child to assign next

    foreach ($camp_items as $item) {
      $meta_data = isset($item['meta_data']) ? $item['meta_data'] : array();
      $meta = $this->build_line_item_meta_lookup($meta_data);
      $quantity = (int)$item['quantity'];

      // Pull the participant label (e.g. "Junior (Ages 7-15)")
      $participant_label = $this->extract_participant_label($meta);

      // Assign the next $quantity children to this camp
      $assigned_children = array_slice($children, $child_cursor, $quantity);
      $child_cursor += $quantity;

      $camps[] = array(
        'line_item_id'      => $item['id'],
        'product_id'        => $item['product_id'],
        'name'              => $item['name'],
        'quantity'          => $quantity,
        'total'             => $item['total'],
        'price_per_head'    => $item['price'],
        'participant_label' => $participant_label,
        'booking'           => $this->extract_booking_details($meta),
        'discount'          => $this->extract_discount_details($meta),
        'children'          => $assigned_children,
      );
    }

    return $camps;
  }

  /**
   * Pull the participant type label from line item meta.
   * e.g. "Junior (Ages 7-15)" or "Senior (Ages 7-15)"
   */
  function extract_participant_label(array $meta)
  {
    $booking_data = isset($meta['ph_bookings_participant_booking_data']) ? $meta['ph_bookings_participant_booking_data'] : array();

    if (!empty($booking_data) && is_array($booking_data)) {
      return isset($booking_data[0]['participant_label']) ? $booking_data[0]['participant_label'] : '';
    }

    return '';
  }

  /**
   * Extract booking date and status info from a camp line item's meta.
   */
  function extract_booking_details(array $meta)
  {
    $from = isset($meta['From']) ? $meta['From'] : '';
    $from_date = is_array($from) ? (isset($from[0]) ? $from[0] : '') : $from;

    $to = isset($meta['To']) ? $meta['To'] : '';
    $to_date = is_array($to) ? (isset($to[0]) ? $to[0] : '') : $to;

    $cost = isset($meta['Cost']) ? $meta['Cost'] : '';
    $cost_val = is_array($cost) ? (isset($cost[0]) ? $cost[0] : '') : $cost;

    $interval_details = isset($meta['_phive_booking_product_interval_details']) ? $meta['_phive_booking_product_interval_details'] : array();
    $interval_days = isset($interval_details['interval']) ? $interval_details['interval'] : '';

    return array(
      'booked_from'    => isset($meta['Booked From'])      ? $meta['Booked From']      : '',
      'booked_to'      => isset($meta['Booked To'])        ? $meta['Booked To']        : '',
      'from_date'      => $from_date,
      'to_date'        => $to_date,
      'booking_status' => isset($meta['Booking Status'])   ? $meta['Booking Status']   : '',
      'cost'           => $cost_val,
      'interval_days'  => $interval_days,
    );
  }

  /**
   * Extract discount information from a camp line item's meta.
   */
  function extract_discount_details(array $meta)
  {
    $wdr = isset($meta['_wdr_discounts']) ? $meta['_wdr_discounts'] : array();

    if (empty($wdr)) {
      return array();
    }

    $applied_rules = isset($wdr['applied_rules']) ? $wdr['applied_rules'] : array();
    $rules = array();
    foreach ($applied_rules as $rule) {
      $discount_value = isset($rule['discount']['discount_value']) ? $rule['discount']['discount_value'] : '';
      $rules[] = array(
        'title'          => isset($rule['title']) ? $rule['title'] : '',
        'type'           => isset($rule['type'])  ? $rule['type']  : '',
        'discount_value' => $discount_value,
      );
    }

    return array(
      'initial_price'    => isset($wdr['initial_price'])    ? $wdr['initial_price']    : '',
      'discounted_price' => isset($wdr['discounted_price']) ? $wdr['discounted_price'] : '',
      'saved_amount'     => isset($wdr['saved_amount'])     ? $wdr['saved_amount']     : 0,
      'rules'            => $rules,
    );
  }


// ADD-ON PRODUCTS
  /**
   * Build structured add-on product list.
   * Quantity will always match total child count (one per child).
   */
  function build_addon_products(array $addon_items)
  {
    $addons = array();

    foreach ($addon_items as $item) {
      $addons[] = array(
        'line_item_id' => $item['id'],
        'product_id'   => $item['product_id'],
        'name'         => $item['name'],
        'quantity'     => $item['quantity'],
        'total'        => $item['total'],
        'price_each'   => $item['price'],
      );
    }

    return $addons;
  }


// UTILITY: GET CHILDREN BY CAMP NAME
  /**
   * Convenience function â€” find a camp by name and return its children.
   *
   * @param array $parsed_order Output from parse_cricket_order()
   * @param string $camp_name Full or partial camp name (case-insensitive)
   * @return array  Children assigned to that camp, or empty array.
   */
  function get_children_for_camp(array $parsed_order, $camp_name)
  {
    foreach ($parsed_order['camps'] as $camp) {
      if (stripos($camp['name'], $camp_name) !== false) {
        return $camp['children'];
      }
    }
    return array();
  }

  /**
   * Convenience function â€” get all children across all camps as a flat array.
   *
   * @param array $parsed_order Output from parse_cricket_order()
   * @return array
   */
  function get_all_children(array $parsed_order)
  {
    $all = array();
    foreach ($parsed_order['camps'] as $camp) {
      foreach ($camp['children'] as $child) {
        $all[] = array_merge($child, array('camp_name' => $camp['name']));
      }
    }
    return $all;
  }

  function isExistingBooking($order_id) {
    global $db;

    $query = "SELECT file_name from sbcc_website_data where file_name = '{$order_id}'";
    $result = $db->query($query, true, "Error attempting to retrieve existing booking from sbcc_website_data");

    if($result->num_rows > 0) {
      //existing order id
      return true;
    } else {
      return false;
    }
  }



}
?>
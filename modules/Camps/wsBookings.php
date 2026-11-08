<?php

  $GLOBALS['log']->debug('Debug Start Booking Webhook:');

  if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');


  
  $GLOBALS['log']->debug('Debug Booking Webhook Data:', var_export(json_encode($_SERVER),true));


  $data = json_encode($_POST); //'test 123 abc';

  global $db;
  $query = "INSERT INTO sbcc_website_data (`file_name`,`date_created`,`record_status`,`file_data`)
            VALUES (now(),now(),'pending','{$data}')";
  $db->query($query, true, "Error attempting to insert a record from webhook");


  $GLOBALS['log']->debug('Debug End Booking Webhook:');

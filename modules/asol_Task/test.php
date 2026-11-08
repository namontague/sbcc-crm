<?php

require_once ("functions.php");

//
//
//// send_email //////////////////////////////////////////////
//$From = "cybergerar@hotmail.com";
//$FromName = "gerardo";
//$Timeout = 30;
//$AddAddress = "cybergerar@hotmail.com";
//$Subject = "subject_test_gerardo";
//$Body = "body_test_gerardo CURL accounts save hook";
//$AddAttachment= "/modules/asol_Task/functions.php";
//
//$implementationField  = "";
//$implementationField .= "email_type";
//$implementationField .= "\${pipe}" . $From;
//$implementationField .= "\${pipe}" . $FromName;
//$implementationField .= "\${pipe}" . $Timeout;
//$implementationField .= "\${pipe}" . $AddAddress;
//$implementationField .= "\${pipe}" . $Subject;
//$implementationField .= "\${pipe}" . $Body;
//$implementationField .= "\${pipe}" . $AddAttachment;
//
////sleep (30);
////task_send_email($implementationField);
/////////////////////////////////////////////////////////////////////
//
//// create_object///////////////////////////////////////////////////
//$implementationField  = "";
//$implementationField .= "Accounts" . "\${mod}";
//$implementationField .= "name" . "\${dp}" . "test_account_raro" . "\${pipe}";
//$implementationField .= "account_type" . "\${dp}" . "Competitor";
//
////task_create_object($implementationField);
/////////////////////////////////////////////////////////////////////
//
//// modify_object //////////////////////////////////////////
//$implementationField  = "";
//$implementationField .= "Accounts" . "\${mod}";
//$implementationField .= "name" . "\${dp}" . "test_account" . "\${pipe}";
//$implementationField .= "industry" . "\${dp}" . "Banking" . "\${pipe}";
//$implementationField .= "account_type" . "\${dp}" . "Customer";
//
//$id_object = "b1c56b45-e46a-513b-15a4-4da86efa6605";
//
////task_modify_object($id_object, $implementationField);
////////////////////////////////////////////////////////////////
//
//// php_custom ///////////////////////
//$script = "echo 'eval_custom_php'; ";
//
////task_php_custom($script);
//////////////////////////////////////////

$ch = curl_init();

// set URL and other appropriate options
curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1/sugarcrm/index.php?entryPoint=asol_curl");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_TIMEOUT, 1);
// grab URL and pass it to the browser
curl_exec($ch);

// close cURL resource, and free up system resources
curl_close($ch);

//

?>

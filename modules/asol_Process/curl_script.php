<?php
//sleep(15);
$GLOBALS['log']->debug("ASOL***************************************************** executing CURL");

//require_once ("C:/xampp/htdocs/sugarcrm/modules/asol_Task/functions.php"); //ok
//require_once ("functions.php"); //wrong
//require_once ("../asol_Task/functions.php"); //wrong
require_once ("modules/asol_Task/functions.php"); //ok

// send_email //////////////////////////////////////////////
$From = "cybergerar@hotmail.com";
$FromName = "gerardo";
$Timeout = 30;
$AddAddress = "cybergerar@hotmail.com";
$Subject = "subject_test_gerardo";
$Body = "body_test_gerardo CURL 20 04 2011 1420";
$AddAttachment= "/modules/asol_Task/functions.php";

$implementationField  = "";
$implementationField .= "email_type";
$implementationField .= "\${pipe}" . $From;
$implementationField .= "\${pipe}" . $FromName;
$implementationField .= "\${pipe}" . $Timeout;
$implementationField .= "\${pipe}" . $AddAddress;
$implementationField .= "\${pipe}" . $Subject;
$implementationField .= "\${pipe}" . $Body;
$implementationField .= "\${pipe}" . $AddAttachment;

sleep (30);
task_send_email($implementationField);


$GLOBALS['log']->debug("ASOL***************************************************** executing CURL***********************************************END");
///////////////////////////////////////////////////////////////////
?>

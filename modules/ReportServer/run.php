<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

require_once('modules/ReportServer/ReportClient.php');
require_once('modules/ReportServer/ReportRestClient.php');
$excluded_params=array('module','action','report');

$ws= new ReportRestClient();
$ws->reportUnit =$_GET['report'];
$ws->format($_GET['format']);
 
foreach (array_keys($_GET) AS $param_name)
{
	if(!in_array($param_name, $excluded_params)) 
	{
		$ws->addParam($param_name, $_GET[$param_name]);
	}
}

$result = $ws->runRestReport();

$ws->displayResult();
	
?>

<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

//require_once('modules/Administration/Administration.php');


//$focus = new Administration();

if(isset($_POST['tracking_entities_location_type'])) {
	if ($_POST['tracking_entities_location_type'] != '2') {
		unset($_POST['tracking_entities_location']);
		unset($_POST['tracking_entities_location_type']);
	}
}
//$focus->saveConfig();

// save User defaults for emails
//$sugar_config['email_default_client'] = $_REQUEST['email_default_client'];
//$sugar_config['email_default_editor'] = $_REQUEST['email_default_editor'];
//$sugar_config['default_email_charset'] = $_REQUEST['default_email_charset'];
//$sugar_config['email_default_delete_attachments'] = (isset($_REQUEST['email_default_delete_attachments'])) ? true : false;
// save outbound raw-save setting
//$sugar_config['email_outbound_save_raw'] = ($_REQUEST['email_outbound_save_raw'] == 1) ? true : false; 

//$sugar_config['email_outbound_save_raw'] = ($_REQUEST['email_outbound_save_raw'] == 1) ? true : false; 

$reportserver_url = $_REQUEST['reportserver_url'];
$reportserver_basefolder = $_REQUEST['reportserver_basefolder'];

//if(substr($reportserver_basefolder, 0, 1) != "/"){//doesnt start with / then add it
//    $reportserver_basefolder = "/$reportserver_basefolder";
//}
//if(substr($reportserver_basefolder, -1) != "/"){//dosent end with / then add it
//    $reportserver_basefolder = "$reportserver_basefolder/";
//}
if(substr($reportserver_basefolder, 0, 1) == "/"){//doesnt start with /
    $reportserver_basefolder = substr($reportserver_basefolder, 1);
}
if(substr($reportserver_basefolder, -1) == "/"){//doesnt start with /
    $reportserver_basefolder = substr($reportserver_basefolder, 0,-1);
}

if(substr($reportserver_url, -1) != "/"){//dosent end with / then add it
    $reportserver_url = "$reportserver_url/";
}


$sugar_config['reportserver_url'] = $reportserver_url;
$sugar_config['reportserver_basefolder'] = $reportserver_basefolder; 
$sugar_config['reportserver_username'] = $_REQUEST['reportserver_username']; 
$sugar_config['reportserver_password'] = $_REQUEST['reportserver_password'];
$sugar_config['reportserver_organization'] = $_REQUEST['reportserver_organization'];

ksort($sugar_config);
write_array_to_file('sugar_config', $sugar_config, 'config.php');

header("Location: index.php?action={$_POST['return_action']}&module={$_POST['return_module']}");
?>

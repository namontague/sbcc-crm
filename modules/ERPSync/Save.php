<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');


if(isset($_POST['tracking_entities_location_type'])) {
	if ($_POST['tracking_entities_location_type'] != '2') {
		unset($_POST['tracking_entities_location']);
		unset($_POST['tracking_entities_location_type']);
	}
}

$sugar_config['erpsync_type'] = $_REQUEST['erpsync_type'];
$sugar_config['erpsync_url'] = $_REQUEST['erpsync_url'];
$sugar_config['erpsync_username'] = $_REQUEST['erpsync_username'];
$sugar_config['erpsync_password'] = $_REQUEST['erpsync_password'];

ksort($sugar_config);
write_array_to_file('sugar_config', $sugar_config, 'config.php');

header("Location: index.php?action={$_POST['return_action']}&module={$_POST['return_module']}");


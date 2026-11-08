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

$module_name = 'CampGroups';
$listViewDefs ['CampGroups'] = array(
			'NAME' =>
			array(
				'width' => '15%',
				'label' => 'LBL_NAME',
				'default' => true,
				'link' => true,
            ),
            'CAMP_NAME' => array(
				'width' => '25%',
				'label' => 'LBL_CAMP',
				'link' => true,
				'id' => 'CAMP_ID',
				'module' => 'Camps',
				'default' => true,
				'ACLTag' => 'CAMPS',
				'related_fields' => array('camp_id')
			),
);
?>

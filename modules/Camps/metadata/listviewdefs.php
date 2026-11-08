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

$module_name = 'Camps';
$listViewDefs ['Camps'] = array(
			'NAME' =>
			array(
				'width' => '15%',
				'label' => 'LBL_NAME',
				'default' => true,
				'link' => true,
			),
			'START_DATE' =>
			array(
				'width' => '11%',
				'label' => 'LBL_START_DATE',
				'default' => true,
			),
			'END_DATE' =>
			array(
				'width' => '11%',
				'label' => 'LBL_END_DATE',
				'default' => true,
			),
    		'TYPE' =>
			array(
				'width' => '10%',
				'label' => 'LBL_TYPE',
				'default' => true,
			),
            'VENUE' =>
			array(
				'width' => '10%',
				'label' => 'LBL_VENUE',
				'default' => true,
			),
      'CONTACT_NAME' => array(
          'width' => '10%', 
          'link' => true,
          'label' => 'LBL_CONTACT_NAME',
          'id' => 'CONTACT_ID',
          'module' => 'Contacts',
          'related_fields' => array('contact_id'),
          'default' => true

      ),
      'TOTAL_PARTICIPANTS' => array(
        'label' => 'LBL_TOTAL_PARTICIPANTS',
        'default' => true,
        'sortable' => false,
      ),
      'TOTAL_CONFIRMED_PARTICIPANTS' => array(
        'label' => 'LBL_TOTAL_CONFIRMED_PARTICIPANTS',
        'default' => true,
        'sortable' => false,    
      ),
//			'ASSIGNED_USER_NAME' =>
//			array(
//				'width' => '10%',
//				'label' => 'LBL_ASSIGNED_USER',
//				'default' => true,
//			),
            
);
?>

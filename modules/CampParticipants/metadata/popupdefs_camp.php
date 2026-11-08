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

$module_name = 'CampParticipants';
$_module_name = strtolower($module_name);
$table_name = 'campparticipants';
$bean_name = 'CampParticipant';


if(!empty($_REQUEST['request_data'])) {
    $request_data = json_decode(htmlspecialchars_decode($_REQUEST['request_data']));
    $url = htmlspecialchars_decode($request_data->passthru_data->return_url);
    $parts = parse_url(urldecode($url));
    parse_str($parts['query'], $query);
    if(isset($query['record'])){
        $group = BeanFactory::getBean('CampGroups',$query['record']);
        if(isset($group->id) && isset($group->camp_id)){
            $_REQUEST['camp_id_advanced'] = $group->camp_id;
        }
    }
}



$popupMeta = array('moduleMain' => $bean_name,
	'varName' => $bean_name,
	'orderBy' => 'name',
	'whereClauses' => array(
		'name' => $_module_name . '.name',
	),
    
    'whereStatement'=> '',
    
    'searchInputs' => array(
		'name',
	),
    'searchdefs'  => array(
        'name',
	),
        'listviewdefs' => array(
            'NAME' =>
			array(
				'width' => '20%',
				'label' => 'LBL_CONTACT_NAME',
				'default' => true,
				'link' => true,
                'sortable'=>false,
            ),
            'CAMP_NAME' => array(
				'width' => '25%',
				'label' => 'LBL_CAMP',
				'link' => true,
				'id' => 'CAMP_ID',
				'module' => 'Camps',
				'default' => true,
				'ACLTag' => 'CAMPS',
                'sortable'=>false,
				'related_fields' => array('camp_id')
			),
            'CAMPGROUP_NAME' => array(
                'width' => '5%', 
                'link' => true,
                'label' => 'LBL_CAMPGROUPS',
                'id' => 'CAMPGROUP_ID',
                'module' => 'CampGroups',
                'related_fields' => array('campgroup_id'),
                'sortable'=>false,
                'default' => true
             ),
    
            'PARTICIPANT_AGE' => array(
                'label' => 'LBL_AGE',
                'width' => '3%',
                'default' => true,
                'sortable'=>false,
            ),
            'PHONE_MOBILE' => array(
                'label' => 'LBL_PHONE_MOBILE',
                'width' => '5%',
                'default' => true,
                'sortable'=>false,
            ),
            //as email is only 1 word,
            //set the size to 1% so the field only takes up as much remore as needed.
            //eg: if the default width is 15% but all the displayed address only fill 8% we loss 7%.
            //    if the default width is 15% but an address takes up 20% the columm is extended anyway as its 1 word.
            //So setting to 1% will help when the displayed addresses are less than orginal default size of 15%
            'EMAIL1' => array(
                'label' => 'LBL_EMAIL',
                'width' => '1%',
                'default' => true,
                'sortable'=>false,
            ),
            'BOOKED_DATE' => array(
                'label' => 'LBL_BOOKED_DATE',
                'width' => '8%',
                'default' => true,
                'sortable'=>false,
            ),
            'AMOUNT_PAID' => array(
                'label' => 'LBL_AMOUNT_PAID',
                'width' => '5%',
                'default' => true,
                'sortable'=>false,
            ),
            'AMOUNT_OWING' => array(
                'label' => 'LBL_AMOUNT_OWING',
                'width' => '5%',
                'default' => true,
                'sortable'=>false,
            ),
            'CONFIRMED' => array(
                'label' => 'LBL_CONFIRMED',
                'width' => '5%',
                'default' => true,
                'sortable'=>false,
            ),
            'ADDITIONAL_PURCHASES' => array(
                'label' => 'LBL_ADDITIONAL_PURCHASES',
                'width' => '5%',
                'default' => true,
                'sortable'=>false,
            ),
            'CONFIRMATION' => array(
                'label' => 'LBL_CONFIRMATION',
                'width' => '5%',
                'default' => true,
                'sortable'=>false,
            ),        
            'MEDICAL' => array(
                'label' => 'LBL_MEDICAL',
                'width' => '20%',
                'default' => true,
                'sortable'=>false,
            ),
            'COMMENTS' => array(
                'label' => 'LBL_COMMENTS',
                'width' => '20%',
                'default' => true,
                'sortable'=>false,
            ),
        ),
    
    
	//'searchInputs' => array('name'),
	//'listview' => 'modules/' . $module_name . '/metadata/listviewdefs.php',
	//'search' => 'modules/' . $module_name . '/metadata/searchdefs.php',
);
?>
 


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

global $app_strings;

$module_name = 'CampParticipants';
$dashlet_name = 'CampParticipantsDashlet';

$dashletMeta[$dashlet_name] = array('module' => $module_name,
	'title' => translate('LBL_HOMEPAGE_TITLE', $module_name),
	'description' => "A customizable view into {$module_name}",
	'icon' => "themes/default/images/icon_{$module_name}_32.gif",
	'category' => 'Module Views');

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


require_once('include/Dashlets/DashletGeneric.php');

class CampParticipantsDashlet extends DashletGeneric
{
	function CampParticipantsDashlet($id, $def = null)
	{
		$module_name = 'Camps';
		$bean_name = 'Camp';
		$dashlet_name = 'CampsDashlet';
		require_once("modules/{$module_name}/{$bean_name}.php");

		global $current_user, $app_strings;
		require("modules/{$module_name}/Dashlets/{$dashlet_name}/{$dashlet_name}.data.php");

		parent::DashletGeneric($id, $def);

		if (empty($def['title']))
			$this->title = translate('LBL_HOMEPAGE_TITLE', $module_name);

		$this->searchFields = $dashletData[$dashlet_name]['searchFields'];
		$this->columns = $dashletData[$dashlet_name]['columns'];

		$this->seedBean = new $bean_name();
	}

}

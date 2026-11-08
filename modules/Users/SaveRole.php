<?php
/*********************************************************************************
 * This file is an customisation to SugarCRM developed by Enterprise Intelligence
 * Systems Pty Ltd.  Copyright (C) 2009-2010 Enterprise Intelligence Systems Pty Ltd.
 *
 * SugarCRM is a customer relationship management program developed by
 * SugarCRM, Inc.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact Enterprise Intelligence Systems Pty Ltd. headquarters at
 * 25/20 Enterprise Dr, Bundoora, Vic, 3083, Australia or at email address
 * info@eintel.com.au
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 ********************************************************************************/
	global $sugar_config, $db;
	$project_id = $_REQUEST['project_id'];
	$project_team_member_id  = $_REQUEST['project_team_member_id'];
	$res = $db->query("SELECT id FROM projects_team_members WHERE deleted=0 AND project_id='".$project_id."' AND project_team_member_id='".$project_team_member_id."'");
	$row = $db->fetchByAssoc($res);
	if($row['id']!=""){
            $user_project_roles = explode(",",$_REQUEST['selected_roles']);
            $roles = '';
            if(is_array($user_project_roles)) {
                $roles = encodeMultienumValue($user_project_roles);
            }
            $update_qry = "UPDATE projects_team_members ac SET user_project_roles = '".$roles."' WHERE ac.id='".$row['id']."'";
            $db->query($update_qry);

	}
       
?>
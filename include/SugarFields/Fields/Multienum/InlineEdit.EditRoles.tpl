{*
/*********************************************************************************
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2011 SugarCRM Inc.
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
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
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

*}

{assign var=field_name_up value=$vardef.name|upper}
{assign var=field_name value=$vardef.name|lower}

{capture name=idname assign=idname}{$parentFieldArray.ID|replace:'-':''}_{$field_name}{/capture}

{if $displayParams.firstCall}
	<link rel='stylesheet' type='text/css' href='modules/{$module}/lb/lb.css' media='screen' />
	<script type='text/javascript' src='modules/{$module}/lb/lb.js'></script>
{/if}

<span style='padding:0px;'>	
	<table border='0' >
		<tr>
			<td id='role_td_{$parentFieldArray.ID}' style='padding:0px 0px 0px 0px !important;border:0px;width:100px;'>{$cell}</td>
			<td style='border:0px;'>
				<a href="javascript:openbox('{$parentFieldArray.ID}',true,'{$parentFieldArray.ID}');"><img border='0' src='themes/default/images/edit_inline.gif'></a>

				<div id='filter_{$parentFieldArray.ID}' class='filter'></div>
				<div id='box_{$parentFieldArray.ID}' class='box' style='display:none;background-color:#ffffff;'>
					<div id='boxcontent_{$parentFieldArray.ID}'>
						<table cellpadding='0' cellspacing='0' width='100%' style='border:3px solid #4e8ccf; border-left:none; border-right:none; margin:0; background-color:#ffffff;'>
							<tr>
								<td>
									<h3>Edit Roles for: {$parentFieldArray.NAME} with {$related_name}</h3>
								</td>
							</tr>
						</table>

						<div style='background-color:#f6f6f6; height:200px; overflow:auto;'>
							<div style='padding-left:10px;padding-top:20px;float:left; width:100px; align:left;'><strong>Roles</strong></div>

							<div style='padding-top:20px;padding-left:20px;float:left;'>
								<select id='role_source_prev_{$parentFieldArray.ID}' name='role_source_prev' multiple='true' size='6'  style='width:150px; margin:8px 10px 0 0; display:none;' >{$role_source}</select>
								<select id='role_source_{$parentFieldArray.ID}' name='role_source' multiple='true' size='6'  style='width:150px; margin:8px 10px 0 0; display:none;'>{$role_source}</select>
								<div id='div_role_{$parentFieldArray.ID}'></div>
								<div class='selectbox' style='padding:1px 10px 0 0; float:left;'>
									<select id='rolelist_{$parentFieldArray.ID}' style='width:150px;' >{$role_source_raw}</select>
								</div>
								<div style='float:left;'>
									<input type='button' id='add' name='add' value='Add' onClick="AddRole('{$parentFieldArray.ID}');">
								</div>
								<div style='clear:both;'></div>
								<script>ListSelectedRoles('{$parentFieldArray.ID}');</script>
								<div id='boxtitle_{$parentFieldArray.ID}' class='boxtitle'></div>
							</div>
						</div>
						<div style='clear:both; border-top:1px solid #abc3d7; margin-bottom:15px; border-bottom:1px solid #abc3d7; height:8px; background-color:#f6f6f6;'></div>
						<div style='padding-left:10px;'>
							<input type='button' id='SaveRolebtn' value='Save' onClick="javascript:SaveRole('{$parentFieldArray.ID}');">
							<input type='button' id='SaveRolebtn' value='Cancel' onClick="javascript:closebox('{$parentFieldArray.ID}');">
						</div>
					</div>
				</div>
			</td>
		</tr>
	</table>
</span>


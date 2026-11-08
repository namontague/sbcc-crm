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

{assign var=flag_field value=$vardef.name|cat:_flag}

{if $displayParams.firstCall}
	<script type='text/javascript' src='include/SugarFields/Fields/Datetimecombo/listview.edit.js'></script>
{/if}

<input type="hidden" class="DateTimeCombo" id="{$idname}" name="{$idname}" value="{$parentFieldArray.$field_name_up}">
<input type="hidden" name="{$idname}_cancel" id='{$idname}_cancel' value='{$parentFieldArray.$field_name_up}' />
    
<span style='padding:0px;'>

	<input style="float:left;" disabled class="text"
		name="{$idname}_display"
		size='17'
		maxlength='25'
		id='{$idname}_display'
		value='{$parentFieldArray.$field_name_up}' />

	<a style="padding: 4px 0px 0px 5px !important; position:absolute; display:inline;"
		href="javascript:EditDateTime('{$idname}', {if $displayParams.splitDateTime}true{else}false{/if});">
	<img border='0' src='themes/default/images/edit_inline.gif'></a>
    
</span>

<style type="text/css">
	.datetimedlg
	{ldelim}
		background-color: #FFF;
		border: 1px solid #777;
	{rdelim}

	.noDlgTitle .ui-dialog-titlebar {ldelim}display:none{rdelim}
</style>

<div id="dialog_{$idname}" class="datetimedlg" style="display:none; border-radius: 5px" title="">

	<table border="0" cellpadding="3" cellspacing="3" style="margin:auto auto auto auto;" class="dateTime">
		<tr valign="middle">
			<td nowrap>
				<input autocomplete="off" type="text" id="{$idname}_date" value="{$parentFieldArray.$field_name_up}" size="11" maxlength="10" title='{$vardef.help}' tabindex="{$tabindex}" onblur="combo_{$idname}.update(); {if isset($displayParams.updateCallback)}{$displayParams.updateCallback}{/if}" onchange="combo_{$idname}.update(); {if isset($displayParams.updateCallback)}{$displayParams.updateCallback}{/if}" />
				<img border="0" src="{sugar_getimagepath file='jscalendar.gif'}" alt="{$APP.LBL_ENTER_DATE}" id="{$idname}_trigger" name="{$idname}_trigger" align="absmiddle">&nbsp;
				{if empty($displayParams.splitDateTime)}
			</td>
			<td nowrap>
				{else}
				<br>
				{/if}
				<div id="{$idname}_time_section"></div>
				{if $displayParams.showNoneCheckbox}
					<script type="text/javascript">
						function set_{$idname}_values(form) {ldelim}
							if(form.{$idname}_flag.checked)  {ldelim}
								form.{$idname}flag.value=1;
								form.{$idname}.value="";
								form.{$idname}.readOnly=true;
							{rdelim} else  {ldelim}
								form.{$idname}_flag.value=0;
								form.{$idname}.readOnly=false;
							{rdelim}
						{rdelim}
					</script>
				{/if}
			</td>
		</tr>
		{if $displayParams.showFormats}
			<tr valign="middle">
				<td nowrap>
					<span class="dateFormat">{$displayParams.dateFormat}</span>
				</td>
				<td nowrap>
					<span class="dateFormat">{$displayParams.timeFormat}</span>
				</td>
			</tr>
		{/if}
		<tr>
			<td colspan="2">
				<a style="padding: 4px 0px 0px 0px !important; display:inline; text-decoration:none;"
					href="javascript:javascript:cancelDialog('{$idname}');">Cancel</a>
				<a style="float:right; text-decoration:none;"
					href="javascript:SaveDateTime('{$idname}',
					'{$parentFieldArray.ID}',
					'{$field_name}',
					'{$bean_name}',
					'{$vardef.validation.compareto}',
					'{$vardef.validation.type}',
					{if !isset($vardef.validation.blank) || $vardef.validation.blank}true{else}false{/if});">Save</a>
			</td>
		</tr>
	</table>

</div>


<script type="text/javascript" src="include/SugarFields/Fields/Datetimecombo/Datetimecombo.js"></script>
<script type="text/javascript">
 	// Add class="widget" to the cell/td (for other widgets to compare with)
	$('#{$idname}').closest("td").addClass("widget");
    
	var combo_{$idname} = new Datetimecombo("{$parentFieldArray.$field_name_up}", "{$idname}", "{$displayParams.timeFormat}", "{$tabindex}", {if $displayParams.showNoneCheckbox}true{else}false{/if}, false, true);
	//Render the remaining widget fields
	text = combo_{$idname}.html('{$displayParams.updateCallback}');
	document.getElementById('{$idname}_time_section').innerHTML = text;

	//Call eval on the update function to handle updates to calendar picker object
	eval(combo_{$idname}.jsscript('{$displayParams.updateCallback}'));

	//bug 47718: this causes too many addToValidates to be called, resulting in the error messages being displayed multiple times
	//    removing it here to mirror the Datetime SugarField, where the validation is not added at this level
	//addToValidate('{$form_name}',"{$idname}_date",'date',false,"{$idname}");

	//addToValidateBinaryDependency('{$form_name}',"{$idname}_hours", 'alpha', false, "{$APP.ERR_MISSING_REQUIRED_FIELDS} {$APP.LBL_HOURS}" ,"{$idname}_date");
	//addToValidateBinaryDependency('{$form_name}', "{$idname}_minutes", 'alpha', false, "{$APP.ERR_MISSING_REQUIRED_FIELDS} {$APP.LBL_MINUTES}" ,"{$idname}_date");
	//addToValidateBinaryDependency('{$form_name}', "{$idname}_meridiem", 'alpha', false, "{$APP.ERR_MISSING_REQUIRED_FIELDS} {$APP.LBL_MERIDIEM}","{$idname}_date");

	YAHOO.util.Event.onDOMReady(function()
	{ldelim}

		Calendar.setup ({ldelim}
		onClose : update_{$idname},
		inputField : "{$idname}_date",
		ifFormat : "{$displayParams.dateFormat}",
		daFormat : "{$displayParams.dateFormat}",
		button : "{$idname}_trigger",
		singleClick : true,
		step : 1,
		weekNumbers: false,
		comboObject: combo_{$idname}
		{rdelim});

		//Call update for first time to round hours and minute values
		combo_{$idname}.update(false);

	{rdelim});
</script>
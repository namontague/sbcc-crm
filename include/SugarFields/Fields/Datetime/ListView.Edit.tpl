{*
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
*}

{if $displayParams.firstCall}
<script type='text/javascript' src='include/SugarFields/Fields/Datetime/listview.edit.js'></script>
{/if}

{assign var=fieldName value=$vardef.name|upper}

<span style='padding:0px;'>
	<input style="float:left;" disabled class="text"
		name="{$parentFieldArray.ID}_{$vardef.name|lower}"
		size='9'
		onchange="onChangeDate('{$parentFieldArray.ID}',
		'{$vardef.name|lower}',
		'{$beanName}',
		'{$vardef.validation.compareto}',
		'{$vardef.validation.type}',
		{if !isset($vardef.validation.blank) || $vardef.validation.blank}true{else}false{/if});"
		maxlength='10'
		id='{$parentFieldArray.ID}_{$vardef.name|lower}'
		value='{$parentFieldArray.$fieldName}' />

	<img style="padding: 4px 0px 0px 5px !important; position:absolute; display:inline;"
		 id="{$parentFieldArray.ID}_{$vardef.name|lower}_trigger" border='0'
		 src='themes/default/images/edit_inline.gif'
		 onclick="EditDate('{$parentFieldArray.ID}','{$vardef.name|lower}');" >
</span>


<script type="text/javascript">
	$('#{$parentFieldArray.ID}_{$vardef.name|lower}').closest("td").addClass("widget");

	Calendar.setup ({ldelim}
		inputField : "{$parentFieldArray.ID}_{$vardef.name|lower}",
		ifFormat : "{$cal_dateformat}",
		daFormat : "{$cal_dateformat}",
		singleClick : true,
		dateStr : "{$parentFieldArray.$fieldName}",
		step : 1,
		button : "{$parentFieldArray.ID}_{$vardef.name|lower}_trigger",
		weekNumbers:false,
		{rdelim}
	);
</script>


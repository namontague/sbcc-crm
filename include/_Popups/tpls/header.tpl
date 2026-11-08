{*
/*********************************************************************************
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2012 SugarCRM Inc.
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
<script type="text/javascript" src="{sugar_getjspath file='include/javascript/sugar_3.js'}"></script>
<script type="text/javascript" src="{sugar_getjspath file='include/javascript/popup_helper.js'}"></script>
<script type="text/javascript">
	{$ASSOCIATED_JAVASCRIPT_DATA}

{literal}
function clearAll() {
   for(i=0; i < document.popup_query_form.length; i++) {
       if(/select/i.test(document.popup_query_form.elements[i].type)) {
          for(x=0; x < document.popup_query_form.elements[i].options.length; x++) {
             document.popup_query_form.elements[i].options[x].removeAttribute('selected');
          }
       }
   }
}
{/literal}
</script>
{{if isset($formData)}}
{$SEARCH_FORM_HEADER}
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit view">
<tr>
<td>
<form action="index.php" method="post" name="popup_query_form" id="popup_query_form">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td>
{$searchForm}
</td></tr>
<tr>
<td>
<input type="hidden" name="module" value="{$module}" />
<input type="hidden" name="action" value="_Popup" />
<input type="hidden" name="query" value="true" />
<input type="hidden" name="func_name" value="" />
<input type="hidden" name="request_data" value="{$request_data}" />
<input type="hidden" name="metadata" value="{$metadata}" />
<input type="hidden" name="parent_bean" value="{$parent_bean}" />
<input type="hidden" name="parent_module" value="{$parent_module}" />
<input type="hidden" name="subpanel_name" value="{$subpanel_name}" />
<input type="hidden" name="populate_parent" value="false" />
<input type="hidden" name="hide_clear_button" value="true" />
<input type="hidden" name="record_id" value="" />
{$MODE}
<input type="submit" name="button" class="button" id="search_form_submit"
	title="{$APP.LBL_SEARCH_BUTTON_TITLE}"
	value="{$APP.LBL_SEARCH_BUTTON_LABEL}" />
<input type="reset" onclick="SUGAR.searchForm.clear_form(this.form); return false;" class="button" id="search_form_clear"
	title="{$APP.LBL_CLEAR_BUTTON_TITLE}"
	value="{$APP.LBL_CLEAR_BUTTON_LABEL}"/>
<script>
  {literal}$('#popup_query_form').submit(function() {
    var mass = $('#MassUpdate input[name="mass[]"]:checked').clone().hide();
    mass.appendTo($(this));
    var uid = $('#MassUpdate textarea[name="uid"]').clone().hide();
    var uidValues = [];
    mass.each(function() {
      uidValues.push($(this).val());
    });
    uid.val(uidValues.join(','));
    uid.appendTo($(this));
  });
  {/literal}
</script>

</td>
<td align='right'></td>
</tr>
</table>
</form>
</td>
</tr>
</table>
{{/if}}
{{if isset($ADDFORM)}}
<p>
{{if isset($popupMeta)}}
<div id='addformlink'>
<input type="button" name="showAdd" class="button" value="{$popupMeta.create.createButton}" onclick="toggleDisplay('addform');" />
</div>
{{/if}}
<div id='addform' style='display:none;position:relative;z-index:2;left:0px;top:0px;'>
<form name="form_QuickCreate_{$module}" id="form_QuickCreate_{$module}" {*onsubmit="return check_form('form_popupQuickCreate{$module}');"*} method="post" action="index.php">
{$ADDFORMHEADER}
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit view">
<tr>
<td>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td>
<input type="hidden" name="doAction" value="save" />
<input type="hidden" name="query" value="true" />
{$ADDFORM}
</td></tr>
</table></td></tr></table>
</form>
</div>
</p>
{{/if}}
{{if $prerow}}
	<form action="index.php" method="post" name="MassUpdate" id="MassUpdate">
	{$MODE}
<input type="hidden" name="mu" value="false" />
<input type='hidden' name='massupdate' value='true' />
<!-- EINTEL : hidden field needed for custom metadata file to be loaded for next and prev pages of multiselect popups-->
<input type="hidden" name="metadata" value="{$metadata}" />
<!--EINTEL END-->
{$massUpdateData}
<input type='hidden' name='Leads_LEAD_offset' value=''>
<input type='hidden' name='saved_associated_data' value=''>
<input type='hidden' name='module' value='{$module}'>
<input type='hidden' name='action' value='_Popup'>
<input type='hidden' name='return_module' value='{$module}'>
<input type='hidden' name='return_action' value='_Popup'>
<input type='hidden' name='hide_clear_button' value='true'>
<input type='hidden' name='current_query_by_page' value='{$current_query}'>

	{$multiSelectData}
   
    
    <!--Removing Delete button -->
    <!--<input type="submit" name="Delete" value="{$APP.LBL_DELETE_BUTTON_LABEL}" id="delete_listview_{$loc}" onclick="return relationship_alter_selected('selected', '{$APP.LBL_LISTVIEW_NO_SELECTED}', 1);"  class="button">-->
                                                                                                   
    <input type="submit" name="Remove" value="Remove" onclick="return relationship_alter_selected('{$module}',document.MassUpdate,'mass[]', 'Please select at least 1 record to proceed.',0);" class="button">
    <span id="selected_count" style="float:right;display:block">Selected: <input  style='border: 0px; background: transparent; font-size: inherit; color: inherit;' size='2' type='text' readonly name='selectCount[]' value='0' /></span>
    
    <script>
    {literal}
    var checks_count = sugarListView.get_checks_count();
    $('input[name="selectCount[]"]').val(checks_count);
    {/literal}
  </script>
{{/if}}
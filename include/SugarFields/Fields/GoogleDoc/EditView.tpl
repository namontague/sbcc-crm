{*
/*********************************************************************************
 * SugarCRM is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2010 SugarCRM Inc.
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
{if strlen({{sugarvar key='value' string=true}}) <= 0}
<script type="text/javascript" src="modules/GoogleDocuments/javascript/editview.js"></script>
<input name="document_title" readonly="true" />
<input name="document_type" type="hidden" />
<input class="reportid" type="button" value="Select Google Document" name="button_google_doc_list" onClick=onExecute(); />
<input name="document_id" type="hidden"/>
<input name="document_url" type="hidden"/>
<input name="content_type" type="hidden"/>
<input name="content_url" type="hidden"/>
<br />
<strong>&nbsp;or&nbsp;</strong>
<br />
<input type="file" name="uploadDocument" maxlength="10485760" />
<input type="button" value="Upload to Google Docs" name="button_google_doc_upload" onClick=onUpload(this); />
<br />
<input type="checkbox" name="convertDoc" value="true" checked />&nbsp;<label>Convert to Google Docs format</label>
<input name="googledoc_name" id="googledoc_name" type="hidden">
{{if $googledoc_name|lower|in_array:$displayParams.required}}
<span class="required">{$APP.LBL_REQUIRED_SYMBOL}</span>
{{/if}}

{else}
<input name="googledoc" type="text" value="{{sugarvar key='value'}}" disabled="true">
{/if}

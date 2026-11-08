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
</form>
{{if $externalJSFile}}
require_once("'".$externalJSFile."'");
{{/if}}
{$set_focus_block}
{{if isset($scriptBlocks)}}
<!-- Begin Meta-Data Javascript -->
{{$scriptBlocks}}
<!-- End Meta-Data Javascript -->
{{/if}}


<div class="h3Row" id="scheduler"></div>

<script type="text/javascript">
{literal}
SUGAR.tasks = {};
var tasksLoader = new YAHOO.util.YUILoader({
    require : ["sugar_grp_jsolait"],
    onSuccess: function(){
		SUGAR.tasks.fill_invitees = function() {
			if (typeof(GLOBAL_REGISTRY) != 'undefined')  {
				SugarWidgetScheduler.fill_invitees(document.EditView);
			}
		}
		var root_div = document.getElementById('scheduler');
		var sugarContainer_instance = new SugarContainer(document.getElementById('scheduler'));
		sugarContainer_instance.start(SugarWidgetScheduler);
		if ( document.getElementById('save_and_continue') ) {
			var oldclick = document.getElementById('save_and_continue').attributes['onclick'].nodeValue;
			document.getElementById('save_and_continue').onclick = function(){
				SUGAR.tasks.fill_invitees();
				eval(oldclick);
			}
		}
	}
});
tasksLoader.addModule({
    name :"sugar_grp_jsolait",
    type : "js",
    fullpath: "cache/include/javascript/sugar_grp_jsolait.js",
    varName: "global_rpcClient",
    requires: []
});
tasksLoader.insert();
    
    
YAHOO.util.Event.onContentReady("{/literal}{{$form_name}}{literal}",function() {    
    //set the assigned user on load to be usered to detect if user has changed    
    assigned_user_id = document.EditView.elements["assigned_user_id"].value;    
});

YAHOO.util.Event.addListener(document.getElementsByName('assigned_user_name'),"change",function() {
        var exists = false;
        if(document.EditView.elements["assigned_user_id"].value != "") {   
            for(i=0; i < GLOBAL_REGISTRY.focus.users_arr.length; i++) {
                if(GLOBAL_REGISTRY.focus.users_arr[i].fields.id == document.EditView.elements["assigned_user_id"].value){
                    exists = true;
                }    
            }

            if(!exists){
                assigned_user = {};
                assigned_user['fields'] = {
                                'id':document.EditView.elements["assigned_user_id"].value,
                                'full_name':document.EditView.elements["assigned_user_name"].value
                            };
                assigned_user['module'] = 'User';   
                SugarWidgetSchedulerAttendees.add_attendee(assigned_user); 
            }
        }
        
        if (typeof(GLOBAL_REGISTRY) != 'undefined' && assigned_user_id != "")  {       
            for(i=0; i<GLOBAL_REGISTRY.focus.users_arr.length; i++) {
                if(GLOBAL_REGISTRY.focus.users_arr[i].fields.id == assigned_user_id){ 
                    if(confirm("Remove previous assigned user from attendees list?")){
                        SugarWidgetScheduleRow.deleteRow(assigned_user_id); 
                    } 
                }
            }
        }
        assigned_user_id = document.EditView.elements["assigned_user_id"].value;    
 });       
     
{/literal}
</script>
</form>
<form >
	<div class="buttons">
		{{if !empty($form) && !empty($form.buttons)}}
		   {{foreach from=$form.buttons key=val item=button}}
		      {{sugar_button module="$module" id="$button" view="$view"}}
		   {{/foreach}}
		{{else}}
				{{sugar_button module="$module" id="SAVE" view="$view"}}
				{{sugar_button module="$module" id="CANCEL" view="$view"}}
		{{/if}}
		{{sugar_button module="$module" id="Audit" view="$view"}}
	</div>
</form> 
	
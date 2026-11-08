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



function EditEnum(id, bean_id, module_name, field,sp, refresh_panel){
    var element_id = '#'+id;
    $(element_id).removeAttr('disabled');
    var value = $(element_id).val();
    var record = $("input[name=record]").val();

    $(element_id).unbind('change'); // Prevent duplicate calls
    $(element_id).change(function() {
      if($(this).val() != value){
        SaveEnum(element_id, bean_id, $(this).val(), module_name, field, record, sp, refresh_panel,true);
      }
    });
}

function EditEnum_onchange(id, bean_id, module_name, field, sp, refresh_panel){
    var element_id = '#'+id;
    var record = $("input[name=record]").val();
    SaveEnum(element_id, bean_id, $(element_id).val(), module_name, field, record, sp, refresh_panel,false);
    
}


function ajaxResult(o) {
  if (o.status > 0) {
        ajaxStatus.flashStatus(SUGAR.language.get('app_strings', 'LBL_SAVED'), 1000);
        
        if(o.argument.refresh == true){
            return_url="index.php?module="+get_module_name()+"&action=SubPanelViewer&subpanel="+o.argument.sp+"&record="+get_record_id()+"&sugar_body_only=1&inline=1";
            showSubPanel(o.argument.sp,return_url,true);
        }

        return true;
  }
}

function SaveEnum(element_id, bean_id, value, module_name, field, record, sp, refresh_panel,disable) {
    var url = 'index.php?action=EditEnum&list_item_id='+bean_id+'&enum_value='+value+'&module_bean='+module_name+'&field='+field+'&viewrecord='+record;
    ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_SAVING'));

    var callback =
	{
	  success:ajaxResult,
	  failure: ajaxResult,
	  argument:{refresh:refresh_panel,sp:sp}
	};


    var cObj = YAHOO.util.Connect.asyncRequest('GET',url,callback);
  
    if(disable){
        $(element_id).attr('disabled', 'disabled');
    }
}



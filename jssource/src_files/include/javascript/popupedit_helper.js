/*********************************************************************************
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
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
/**
 * Helper function used in send_backs to close the popup window if closePopup is true.
 */
function closePopup() {
    var closePopup = window.opener.get_close_popup();
    if (closePopup) {
        window.close();
    }
}
function send_back_popupedit(_form) {
    var form_data = _form;
    
    eval("var temp_request_data = " + _form.request_data.value);
    if(temp_request_data.jsonObject) {
        var request_data = temp_request_data.jsonObject;
    } else {
        var request_data = temp_request_data; 
    }

    var passthru_data = Object();
    if(typeof(request_data.passthru_data) != 'undefined') {
        passthru_data = request_data.passthru_data;
    }

    var call_back_function = eval("window.opener." + request_data.call_back_function);

    var field_to_name_array = request_data.field_to_name_array;
    var array_contents = Array();
    var fill_array_contents = function(the_key, the_name) {
        var the_value = '';        
        for (i = 0; i < form_data.elements.length; i++) {
            if (form_data.elements[i].name == the_key) {                   
                if(form_data.elements[i].type == 'radio' || form_data.elements[i].type == 'checkbox'){
                    the_value = form_data.elements[i].checked;   
                }  else{
                    the_value = form_data.elements[i].value;   
                }  
                break;
            }
        }
        if (typeof (the_value) == 'string') {
            the_value = the_value.replace(/\r\n|\n|\r/g, '\\n');
        }
        
        if( typeof (the_value) == 'boolean') {
            array_contents.push('"' + the_name + '":' + the_value );
        }else{
            array_contents.push('"' + the_name + '":"' + the_value + '"');
        }
    }
    for (var the_key in field_to_name_array) {
        if (the_key != 'toJSON') {
            if (YAHOO.lang.isArray(field_to_name_array[the_key])) {
                for (var i = 0; i < field_to_name_array[the_key].length; i++) {
                    fill_array_contents(the_key, field_to_name_array[the_key][i]);
                }
            } else {
                fill_array_contents(the_key, field_to_name_array[the_key]);
            }
        }
    }
    eval("var name_to_value_array = {" + array_contents.join(",") + "}");

    closePopup();
    
    var result_data = {"form_data":_form, "form_name":request_data.form_name, "passthru_data":passthru_data, "name_to_value_array": name_to_value_array};
    call_back_function(result_data);
}

SUGAR.util.doWhen("window.document.forms['PopupEditView'] != null "
        + "&& typeof(window.document.forms['PopupEditView'].request_data) != 'undefined'", function () {
            if (window.document.forms['PopupEditView'].request_data.value == "")
            {
                window.document.forms['PopupEditView'].request_data.value = window.opener.get_popup_request_data();
            }
        });
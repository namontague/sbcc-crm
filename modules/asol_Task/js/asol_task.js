var ASOLTask= {
  idCount:0,
  fieldLabels:'',
  typeFields:'',
  optionFields:'',
  fieldRequired:'',
  relatedModules:'',
  users:{},
  aclRoles:{},
  calendar_dateformat:'',
  labels: {
    sendEmailTemplate:SUGAR.language.get('asol_Task', 'LBL_ASOL_TASK_JS_SEND_EMAIL_EMAIL_TPL'),
    sendEmailFrom:SUGAR.language.get('asol_Task', 'LBL_ASOL_TASK_JS_SEND_EMAIL_FROM'),
    sendEmailArchive:SUGAR.language.get('asol_Task', 'LBL_ASOL_TASK_JS_SEND_EMAIL_ARCHIVE'),
    sendEmailTo:SUGAR.language.get('asol_Task', 'LBL_ASOL_TASK_JS_SEND_EMAIL_TO'),
    sendEmailUsers:SUGAR.language.get('asol_Task', 'LBL_ASOL_TASK_JS_SEND_EMAIL_USERS'),
    sendEmailRoles:SUGAR.language.get('asol_Task', 'LBL_ASOL_TASK_JS_SEND_EMAIL_ROLES'),
    sendEmailDistributionList:SUGAR.language.get('asol_Task', 'LBL_ASOL_TASK_JS_SEND_EMAIL_DISTRIBUTION_LIST'),
    customPHPCode:SUGAR.language.get('asol_Task', 'LBL_ASOL_TASK_JS_PHP_CUSTOM_PHP_CODE'),
    endTerminateProcess:SUGAR.language.get('asol_Task', 'LBL_ASOL_TASK_JS_END_TERMINATE_PROCESS'),
    continueWait:SUGAR.language.get('asol_Task', 'LBL_ASOL_TASK_JS_CONTINUE_WAIT_FOR_SUGAR_TASK_TO_CONTINUE_PROCESS'),
    callProcessProcess:SUGAR.language.get('asol_Task', 'LBL_ASOL_TASK_JS_CALL_PROCESS_PROCESS'),
    callProcessEvent:SUGAR.language.get('asol_Task', 'LBL_ASOL_TASK_JS_CALL_PROCESS_EVENT_FROM_PROCESS'),
    deleteButton:SUGAR.language.get('asol_Task', 'LBL_ASOL_DELETE_BUTTON'),
    deleteRowAlert:SUGAR.language.get('asol_Task', 'LBL_ASOL_DELETE_ROW_ALERT')
  },
  dateTimeDefaults: {
    'year':'',
    'month':'',
    'day':'',
    'hour':'',
    'minute':''
  },
  relateDefaults: {
    'record_id':'',
    'record_name':'',
  },
  createSelect: function(name, options) {
    var sel = "<select id='"+name+"' name='"+name+"' multiple='' size=4 style='width: 140px'>";
    for( id in options) {
      sel += "<option value='" + id + "'>" + options[id] + "</option>";
    }
    sel += "</select>";
    return sel;
  },

  selectTaskType: function(taskSelectId, selectedType) {
    if(selectedType == null ) {
        selectedType = $('#'+taskSelectId).val();
    }

    switch (selectedType) {
      case "send_email":
        var users_select = ASOLTask.createSelect('email_users', ASOLTask.users);
        var acl_roles_select = ASOLTask.createSelect('email_roles', ASOLTask.aclRoles);
        $('#task_implementation_field').html(
                  + "<table class='edit view'>"
                  + "<tr>"
                  + "<td>"+ASOLTask.labels.sendEmailTemplate+":</td>"
                  + "<td><input type='text' autocomplete='off' title='' value='' size='45' id='email_tpl_name' name='email_tpl_name'><input type='hidden' value='' id='email_tpl_id' name='email_tpl_id'><button onclick='open_popup(\"EmailTemplates\", 700, 500, \"\", true, false, {\"call_back_function\":\"set_return\",\"form_name\":\"EditView\",\"field_to_name_array\":{\"id\":\"email_tpl_id\",\"name\":\"email_tpl_name\"}}, \"single\", true );' value='Select' class='button firstChild' id='btn_assigned_user_name' name='btn_assigned_user_name' type='button'><img src='themes/default/images/id-ff-select.png'></button><button value='Clear' onclick='document.getElementById(\"email_tpl_name\").value =\"\"; document.getElementById(\"email_tpl_id\").value = \"\";' class='button lastChild' id='btn_clr_assigned_user_name' name='btn_clr_assigned_user_name' type='button'><img src='themes/default/images/id-ff-clear.png'></button></td>"
                  + "<td>"+ASOLTask.labels.sendEmailFrom+":</td>"
                  + "<td><input type='text' id='email_from' name='email_from' value=''></td>"
                  + "</tr>"

                  + "<tr>"
                  + "<td>"+ASOLTask.labels.sendEmailArchive+":</td>"
                  + "<td><input type='checkbox' title='' size='45' id='email_archive' name='email_archive'></td>"
                  + "<td></td>"
                  + "<td></td>"
                  + "</tr>"

                  + "<tr>"
                  + "<td>"
                  + ASOLTask.labels.sendEmailTo+":"
                  + "</td>"
                  + "</tr>"

                  + "<tr>"
                  + "<td>"
                  + ASOLTask.labels.sendEmailUsers
                  + "</td>"
                  + "<td>"
                  + users_select
                  + "</td>"
                  + "<td>"
                  + ASOLTask.labels.sendEmailRoles
                  + "</td>"
                  + "<td>"
                  + acl_roles_select
                  + "</td>"
                  + "</tr>"
                  + "<tr>"
                  + "<td>"+ASOLTask.labels.sendEmailDistributionList+":</td>"
                  + "<td><textarea id='email_list' name='email_list' rows='6' cols='40'></textarea></td>"
                  + "<td colspan='2'></td>" + "</tr>" + "</table>");
        break;


/*	case "assign_call":

		var call_status_select = "<select id='call_status' name='call_status'>";
		for ( var i = 0; i < call_status.length; i++)
			call_status_select += "<option value='" + call_status[i] + "'>"
					+ call_status[i] + "</option>";
		call_status_select += "</select>";

		document.getElementById("task_implementation_field").innerHTML = ""
				+ "<table class='edit view'>"
				+ "<tr>"
				+ "<td>Subject:</td>"
				+ "<td><input type='text' id='call_name' name='call_name'></td>"
				+ "<td>Status:</td>" + "<td>"
				+ call_status_select
				+ "</td>"
				+ "</tr>"
				+ "<tr>"
				+ "<td>Start Date & Time:</td>"
				+ "<td>"
				+ "<input type='text' size='11' value='' id='call_date_start' autocomplete='off'>"
				+ "<img border='0' align='absmiddle' id='date_start_trigger' alt='Enter Date' src='themes/Sugar5/images/jscalendar.gif'>&nbsp;"
				+ "<select id='call_date_start_hours' size='1' class='datetimecombo_time'><option></option><option value='01'>01</option><option value='02'>02</option><option value='03'>03</option><option value='04'>04</option><option value='05'>05</option><option value='06'>06</option><option value='07'>07</option><option value='08'>08</option><option value='09'>09</option><option value='10'>10</option><option value='11'>11</option><option value='12'>12</option></select>&nbsp;:"
				+ "&nbsp;<select id='call_date_start_minutes' size='1' class='datetimecombo_time'><option></option><option value='00'>00</option><option value='15'>15</option><option value='30'>30</option><option value='45'>45</option></select>"
				+ "&nbsp;<select id='call_date_start_meridiem' size='1' class='datetimecombo_time'><option></option><option value='am'>am</option><option value='pm'>pm</option></select></td>"
				+ "<td>Duration:</td>"
				+ "<td>"
				+ "<input type='text' value='0' maxlength='2' size='2' name='call_duration_hours' id='call_duration_hours'>"
				+ "<select name='call_duration_minutes' tabindex='1' id='call_duration_minutes'>"
				+ "<option value='0'>00</option>"
				+ "<option value='15'>15</option>"
				+ "<option value='30'>30</option>"
				+ "<option value='45'>45</option>"
				+ "</select>&nbsp;"
				+ "<span class='dateFormat'>(hours/minutes)</span></td>"
				+ "</tr>"
				+ "<tr>"
				+ "<td>Assign To:</td>"
				+ "<td><input type='text' autocomplete='off' title='' value='' size='' id='call_user_name' name='call_user_name'><input type='hidden' value='' id='call_user_id' name='call_user_id'><button onclick='open_popup(\"Users\", 600, 400, \"\", true, false, {\"call_back_function\":\"set_return\",\"form_name\":\"EditView\",\"field_to_name_array\":{\"id\":\"call_user_id\",\"user_name\":\"call_user_name\"}}, \"single\", true );' value='Select' class='button firstChild' id='btn_assigned_user_name' name='btn_assigned_user_name' type='button'><img src='themes/default/images/id-ff-select.png'></button><button value='Clear' onclick='document.getElementById(\"call_user_name\").value =\"\"; document.getElementById(\"call_user_id\").value = \"\";' class='button lastChild' id='btn_clr_assigned_user_name' name='btn_clr_assigned_user_name' type='button'><img src='themes/default/images/id-ff-clear.png'></button></td>"
				+ "<td colspan='2'></td>" + "<tr>" + "</table>";

		Calendar.setup({
			inputField : "call_date_start",
			daFormat : calendar_dateformat,
			button : "date_start_trigger",
			singleClick : true,
			dateStr : '',
			step : 1,
			weekNumbers : false
		});

		break;

	case "assign_task":

		var task_status_select = "<select id='task_status' name='task_status'>";
		for ( var i = 0; i < task_status.length; i++)
			task_status_select += "<option value='" + task_status[i] + "'>"
					+ task_status[i] + "</option>";
		task_status_select += "</select>";

		var task_priority_select = "<select id='task_priority' name='task_priority'>";
		for ( var i = 0; i < task_priority.length; i++)
			task_priority_select += "<option value='" + task_priority[i] + "'>"
					+ task_priority[i] + "</option>";
		task_priority_select += "</select>";

		document.getElementById("task_implementation_field").innerHTML = ""
				+ "<table class='edit view'>"
				+ "<tr>"
				+ "<td>Subject:</td>"
				+ "<td><input type='text' id='task_name' name='task_name'></td>"
				+ "<td>Status:</td>"
				+ "<td>"
				+ task_status_select
				+ "</td>"
				+ "</tr>"
				+ "<tr>"
				+ "<td>Priority:</td>"
				+ "<td>"
				+ task_priority_select
				+ "</td>"
				+ "<td>Assign To:</td>"
				+ "<td><input type='text' autocomplete='off' title='' value='' size='' id='task_user_name' name='task_user_name'><input type='hidden' value='' id='task_user_id' name='task_user_id'><button onclick='open_popup(\"Users\", 600, 400, \"\", true, false, {\"call_back_function\":\"set_return\",\"form_name\":\"EditView\",\"field_to_name_array\":{\"id\":\"task_user_id\",\"user_name\":\"task_user_name\"}}, \"single\", true );' value='Select' class='button firstChild' id='btn_assigned_user_name' name='btn_assigned_user_name' type='button'><img src='themes/default/images/id-ff-select.png'></button><button value='Clear' onclick='document.getElementById(\"task_user_name\").value =\"\"; document.getElementById(\"task_user_id\").value = \"\";' class='button lastChild' id='btn_clr_assigned_user_name' name='btn_clr_assigned_user_name' type='button'><img src='themes/default/images/id-ff-clear.png'></button></td>"
				+ "</tr>";
		break;
*/

      case "php_custom":
        $("#task_implementation_field").html(
                    "<table class='edit view'>"
                  + "<tr>"
                  + "<td>"+ASOLTask.labels.customPHPCode+":</td>"
                  + "<td><textarea id='php_custom' name='php_custom' rows='10' cols='60'></textarea></td>"
                  + "</tr>");
        break;

      case "end":
        $("#task_implementation_field").html(
                    "<table class='edit view'>"
                  + "<tr>"
                  + "<td>"+ASOLTask.labels.endTerminateProcess+": <input type='checkbox' id='terminate_process' name='terminate_process' ></td>"
                  + "</tr>" + "</table>");
        break;

      case "continue":
        $("#task_implementation_field").html(
                    "<table class='edit view'>" + "<tr>"
                  + "<td>"+ASOLTask.labels.continueWait+"...</td>"
                  + "</tr>" + "</table>");
        break;

      case "call_process":
        $("#task_implementation_field").html(
                   "<table class='edit view'>"
                  + "<tr>"
                  + "<td>"+ASOLTask.labels.callProcessProcess+":</td>"
                  + "<td><input type='text' autocomplete='off' title='' value='' size='' id='process_name' name='process_name'><input type='hidden'   value='' id='process_id' name='process_id'><button onclick='open_popup(\"asol_Process\", 600, 400, \"\", true, false, {\"call_back_function\":\"set_return\",\"form_name\":\"EditView\",\"field_to_name_array\":{\"id\":\"process_id\",\"name\":\"process_name\"}}, \"single\", true );' value='Select' class='button firstChild' id='btn_assigned_user_name' name='btn_assigned_user_name' type='button'><img src='themes/default/images/id-ff-select.png'></button><button value='Clear' onclick='document.getElementById(\"process_name\").value =\"\"; document.getElementById(\"process_id\").value = \"\";' class='button lastChild' id='btn_clr_assigned_user_name' name='btn_clr_assigned_user_name' type='button'><img src='themes/default/images/id-ff-clear.png'></td>"
                  + "<td>"+ASOLTask.labels.callProcessEvent+":</td>"
                  + "<td><input type='text' autocomplete='off' title='' value='' size='' id='event_name' name='event_name'><input type='hidden'   value='' id='event_id' name='event_id'><button onclick='open_popup(\"asol_Events\", 600, 400, \"\", true, false, {\"call_back_function\":\"set_return\",\"form_name\":\"EditView\",\"field_to_name_array\":{\"id\":\"event_id\",\"name\":\"event_name\"}}, \"single&parent_module=asol_Task&parent_id=\" + document.getElementById(\"process_id\").value , true );' value='Select' class='button firstChild' id='btn_assigned_user_name2' name='btn_assigned_user_name2' type='button'><img src='themes/default/images/id-ff-select.png'></button><button value='Clear' onclick='document.getElementById(\"event_name\").value =\"\"; document.getElementById(\"event_id\").value = \"\";' class='button lastChild' id='btn_clr_assigned_user_name2' name='btn_clr_assigned_user_name2' type='button'><img src='themes/default/images/id-ff-clear.png'></td>"
                  + "</tr>");
        break;

      default:
          break;

    }
  },

  fieldValue:function(name, type, required, currentValue, options,relatedModule) {
    if (type.indexOf("int") === 0) {
      type = 'int';
    }

    var field = '';
    field += "<td><b><span class='name'>"+name+"</span>";
    if(required) {
      field += '<span> *</span>;'
    }
    field +="</b></td>";

    field += '<td>';
    switch (type) {
      case "enum":
        field  += "<select name='Param1' class='value' style='width:140px'>";

        for (var key  in options) {
          var sel = '';
          if(currentValue== key ) {
            sel = ' selected';
            break;
          }
          field += "<option value='" + key + "'"+sel+">" + options[key]+ "</option>";
        }
        field += "</select>";
        break;

      case "int":
      case "double":
      case "currency":
        field += "<input type='text' name='Param1' class='value'  style='width:140px' value='"+currentValue+"'>";
      break;

    case "datetime":
      field += "<select class='base' size='1''>"
                + "<option value='current_datetime'>Current Date/Time +</option>"
                +"</select>";
      if(currentValue != null && currentValue != '' ) {
        $.merge(currentValue, ASOLTask.dateTimeDefaults);
      } else {
        currentValue = ASOLTask.dateTimeDefaults
      }
      field+= "<label>Y:</label><input class='year' size='2' value='"+currentValue['year']+"'></input>";
      field+= "<label>M:</label><input class='month' size='2' value='"+currentValue['month']+"'></input>";
      field+= "<label>D:</label><input class='day' size='2' value='"+currentValue['day']+"'></input>";
      field+= "<label>h:</label><input class='hour' size='2' value='"+currentValue['hour']+"'></input>";
      field+= "<label>m:</label><input class='minute' size='2' value='"+currentValue['minute']+"'></input>";
      break;

    case "date":
      field += "<select class='base' size='1''>"
                + "<option value='current_date'>Current Date +</option>"
                +"</select>";
      if(currentValue != null && currentValue != '') {
        $.merge(currentValue, ASOLTask.dateTimeDefaults);
      } else {
        currentValue = ASOLTask.dateTimeDefaults
      }
      field+= "<label>Y:</label><input class='year' size='2'>"+currentValue['year']+"</input>";
      field+= "<label>M:</label><input class='month' size='2'>"+currentValue['month']+"</input>";
      field+= "<label>D:</label><input class='day' size='2'>"+currentValue['day']+"</input>";
      break;

    case "tinyint(1)":
      var checked ='';
      if( currentValue) {
        checked =' checked';
      }
      field +="<input type='checkbox' name='Param1' class='value'"+checked+" value='"+currentValue+"'>";
      break;

    case "relate":
      if(currentValue != null && currentValue != '') {
        $.merge(currentValue, ASOLTask.relateDefaults);
      } else {
        currentValue = ASOLTask.relateDefaults
      }
      var popup_name = (relatedModule == 'Users') ? "user_name" : "name";
      var record_name_id = 'record_name_' + ASOLTask.idCount;
      var record_id_id = 'record_id_' + ASOLTask.idCount;
      field += "<input type='text' autocomplete='off' title='' size='' class='record_name' id='"+record_name_id
          + "' name='"+record_name_id+ "' value='"+currentValue['record_name']+"'>"
          +"<input type='hidden' class='record_id' id='"+record_id_id
          + "' name='"+record_id_id+ "' value='"+currentValue['record_id']+"'>"
          + "<button onclick='open_popup(\"" + relatedModule
          + "\", 600, 400, \"\", true, false, {\"call_back_function\":\"set_return\",\"form_name\":\"EditView\",\"field_to_name_array\":{\"id\":\"" +
          +record_id_id
          + "\",\""
          + popup_name
          + "\":\""+record_name_id
          + "\"}}, \"single\", true );' value='Select' class='button firstChild' id='btn_assigned_user_name' name='btn_assigned_user_name' type='button'><img src='themes/default/images/id-ff-select.png'></button><button value='Clear' onclick='document.getElementById(\""
          +record_name_id
          + "\").value =\"\"; document.getElementById(\""+record_id_id
          + "\").value = \"\";' class='button lastChild' id='btn_clr_assigned_user_name' name='btn_clr_assigned_user_name' type='button'><img src='themes/default/images/id-ff-clear.png'></button>";

      break;

    default:
      field+= "<input type='text' name='Param1' class='value' style='width:140px' value='"+currentValue+"'>";
      break;
    }

    field += '</td>';

    field +="<td>"
          + "<img border='0' src='themes/default/images/minus_inline.gif' title=\""+ASOLTask.labels.deleteButton+"\" OnMouseOver='this.style.cursor=\"pointer\"' OnMouseOut='this.style.cursor=\"default\"' onClick='if(confirm(\""+ASOLTask.labels.deleteRowAlert+"\")) { this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode); } ' name='del_button'>"
          + "<input type='hidden' class='type' name='field_type' value='"+type+"'>"
          + "<input type='hidden' class='required' name='is_required' value='"+required+"'>"
          + "<input type='hidden' class='options' name='options' value='"+YAHOO.lang.JSON.stringify(options) +"'>"
          + "<input type='hidden' class='relatedModule' name='relate_module' value='"+relatedModule+"'>"
          +"</td>";


    return '<tr class="field">'+field+'</tr>';
  },

  insertObjectField: function(idTable, idSelectFields) {
    var table = $('#'+idTable);
    var fields = $('#'+idSelectFields);
    fields.children('option').each(function(i, fieldOp){
      if(fieldOp.selected) {
        table.append(ASOLTask.fieldValue(fieldOp.value,ASOLTask.typeFields[i], ASOLTask.fieldRequired[i], '',
            ASOLTask.optionFields[i],ASOLTask.relatedModules[i]));
      }
    });

	},

  /**
   * When you select a Object Module within asol_Task, this is the code intented to create and to fill the object's
   * fields(only the obligatory fields).
   */
  fillRequiredFields:function(idTable, idSelectFields) {
    var table = $('#'+idTable);
    var fields = $('#'+idSelectFields);
    fields.children('option').each(function(i, fieldOp){
      if(ASOLTask.fieldRequired[i]) {
        table.append(ASOLTask.fieldValue(fieldOp.value,ASOLTask.typeFields[i], ASOLTask.fieldRequired[i], '',
            ASOLTask.optionFields[i],ASOLTask.relatedModules[i]));
      }
    });

  },

  loadImplementationField: function(taskType, implementationField) {
    var info =YAHOO.lang.JSON.parse(implementationField);;
    switch (taskType) {
      case "send_email":
        if(info) {
          if( info['template_id'] != null ) {
            $('#email_tpl_id').val(info['template_id']);
          }
          if( info['template_name'] != null ) {
            $('#email_tpl_name').val(info['template_name']);
          }
          if( info['from'] != null ) {
            $('#email_from').val(info['from']);
          }
          if( info['archive']) {
            $('#email_archive').attr('checked', true);
          }
          if( info['users'] != null ) {
            $('#email_users').val(info['users']);
          }
          if( info['roles'] != null ) {
            $('#email_roles').val(info['roles']);
          }
          if( info['list'] != null ) {
            $('#email_list').val(info['list']);
          }
        }
        break;
/*
      case "assign_call":
        break;
      case "assign_task":
        break;
*/
      case "php_custom":
        $("#php_custom").val(info['php']);
        break;
      case "end":
        if(info['terminated_process'] ) {
          $("#terminate_process").attr('checked', true);
        }
        break;
      case "continue":
        break;
      case "create_object":
      case "modify_object":
        $('#task_implementation_field').hide();
        $('#task_implementation_field_create_object').show();
        var module = info['module'];
        var fields = info['fields'];
        var table = $('#module_values_Table');

        $.each(fields,function(i, f){
          table.append(ASOLTask.fieldValue(f.name,f.type, f.required, f.value,f.options,f.relatedModule));
        });
        $()
        break;
      case "call_process":
        $("#process_id").val(info['process_id']);
        $("#process_name").val(info['process_name']);
        $("#event_id").val(info['event_id']);
        $("#event_name").val(info['event_name']);
        break;
    }
  },

  saveImplementationField:function(task_select_id) {
    var task_type_selected = $('#'+task_select_id).val();

    var implementation;

    switch (task_type_selected) {

      case "send_email":
        implementation = {
          template_id:$('#email_tpl_id').val(),
          template_name:$('#email_tpl_name').val(),
          from:$('#email_from').val(),
          archive:$('#email_archive').is(':checked'),
          users:$('#email_users').val(),
          roles:$('#email_roles').val(),
          list:$('#email_list').val(),
        };
        break;
      /*
       case "assign_call":
       formated_string = document.getElementById('call_name').value
       + "${pipe}";
       formated_string += document.getElementById('call_status').value
       + "${pipe}";
       formated_string += document.getElementById('call_date_start').value
       + " " + document.getElementById('call_date_start_hours').value
       + ":"
       + document.getElementById('call_date_start_minutes').value
       + ":00 "
       + document.getElementById('call_date_start_meridiem').value
       + "${pipe}";
       formated_string += document.getElementById('call_duration_hours').value
       + ":" + document.getElementById('call_duration_minutes').value
       + "${pipe}";
       formated_string += document.getElementById('call_user_id').value;
       break;

       case "assign_task":
       formated_string = document.getElementById('task_name').value
       + "${pipe}";
       formated_string += document.getElementById('task_status').value
       + "${pipe}";
       formated_string += document.getElementById('task_priority').value
       + "${pipe}";
       formated_string += document.getElementById('task_user_id').value;
       break;
       */
      case "php_custom":
        implementation = {
          php:$('#php_custom').val()
        };
        break;
      case "end":
        implementation= {
          terminate_process:$('#terminate_process').is(':checked')
        };
        break;
      case "continue":
        break;

      case "create_object":
      case "modify_object":

        var fieldModule = {
          module:$("#objectModule").val(),
          fields:[],
        }
        $('#module_values_Table .field').each(function(){
          var field = $(this);
          var type = field.find('.type').val();
          var value;
          switch(type) {
            case "datetime":
            case "date":
              value = {
                base:field.find('.base').val(),
                year:field.find('.year').val(),
                month:field.find('.month').val(),
                day:field.find('.day').val(),
                hour:field.find('.hour').val(),
                minute:field.find('.minute').val(),
              };
              break;
            case "relate":
              value = {
                record_id:field.find('.record_id').val(),
                record_name:field.find('.record_name').val(),
              };
              break;
            case "tinyint(1)":
              value = field.find('.value').is(':checked');
              break;
            default:
              value = field.find('.value').val();
              break;
          }

          fieldModule.fields.push({
            name:field.find('.name').text(),
            type:type,
            required:field.find('.required').val(),
            value:value,
            options:YAHOO.lang.JSON.parse(field.find('.options').val()),
            relatedModule:field.find('.relatedModule').val(),

          });
        });
        implementation = fieldModule;
        break;
      case "call_process":
        implementation = {
          process_id:$("#process_id").val(),
          process_name:$("#process_name").val(),
          event_id:$("#event_id").val(),
          event_name:$("#event_name").val(),
        };
        break;
    }

    implementation = YAHOO.lang.JSON.stringify(implementation);
    return implementation;
  }
};








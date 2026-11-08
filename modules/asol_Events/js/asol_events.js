var ASOLEvents = {
  idCount:0,
  fieldLabels:'',
  typeFields:'',
  optionFields:'',
  typeRelatedFields:'',
  optionRelatedFields:'',
  calendar_dateformat:'',
  labels: {
    deleteButton:SUGAR.language.get('asol_Events', 'LBL_ASOL_DELETE_BUTTON'),
    deleteRowAlert:SUGAR.language.get('asol_Events', 'LBL_ASOL_DELETE_ROW_ALERT')
  },

  operators : {
    'equals': {
      'label': SUGAR.language.get('asol_Events', 'LBL_EVENT_EQUALS'),
      'param1' : 'value',
      'param2' : ''
    },
    'not equals': {
      'label': SUGAR.language.get('asol_Events', 'LBL_EVENT_NOT_EQUALS'),
      'param1' : 'value',
      'param2' : ''
    },
    'one of': {
      'label': SUGAR.language.get('asol_Events', 'LBL_EVENT_ONE_OF'),
      'param1' : 'multi',
      'param2' : ''
    },
    'not one of': {
      'label': SUGAR.language.get('asol_Events', 'LBL_EVENT_NOT_ONE_OF'),
      'param1' : 'multi',
      'param2' : ''
    },
    'less than': {
      'label': SUGAR.language.get('asol_Events', 'LBL_EVENT_LESS_THAN'),
      'param1' : 'value',
      'param2' : ''
    },
    'more than': {
      'label': SUGAR.language.get('asol_Events', 'LBL_EVENT_MORE_THAN'),
      'param1' : 'value',
      'param2' : ''
    },
    'like': {
      'label': SUGAR.language.get('asol_Events', 'LBL_EVENT_LIKE'),
      'param1' : 'value',
      'param2' : ''
    },
    'not like': {
      'label': SUGAR.language.get('asol_Events', 'LBL_EVENT_NOT_LIKE'),
      'param1' : 'value',
      'param2' : ''
    },
    'before date': {
      'label': SUGAR.language.get('asol_Events', 'LBL_EVENT_BEFORE_DATE'),
      'param1' : 'value',
      'param2' : ''
    },
    'after date': {
      'label': SUGAR.language.get('asol_Events', 'LBL_EVENT_AFTER_DATE'),
      'param1' : 'value',
      'param2' : ''
    },
    'between': {
      'label': SUGAR.language.get('asol_Events', 'LBL_EVENT_BETWEEN'),
      'param1' : 'value',
      'param2' : 'value'
    },
    'not between': {
      'label': SUGAR.language.get('asol_Events', 'LBL_EVENT_NOT_BETWEEN'),
      'param1' : 'value',
      'param2' : 'value'
    },
    'last': { 'label': SUGAR.language.get('asol_Events', 'LBL_EVENT_LAST'),
      'param1' : 'period',
      'param2' : ''
    },
    'not last': {
      'label': SUGAR.language.get('asol_Events', 'LBL_EVENT_NOT_LAST'),
      'param1' : 'period',
      'param2' : ''
    },
    'this': {
      'label': SUGAR.language.get('asol_Events', 'LBL_EVENT_THIS'),
      'param1' : 'period',
      'param2' : ''
    },
    'not this': {
      'label': SUGAR.language.get('asol_Events', 'LBL_EVENT_NOT_THIS'),
      'param1' : 'period',
      'param2' : ''
    },
    'next': {
      'label': SUGAR.language.get('asol_Events', 'LBL_EVENT_NEXT'),
      'param1' : 'period',
      'param2' : ''
    },
    'not next': {
      'label': SUGAR.language.get('asol_Events', 'LBL_EVENT_NOT_NEXT'),
      'param1' : 'period',
      'param2' : ''
    },
    'changed': {
      'label': SUGAR.language.get('asol_Events', 'LBL_EVENT_CHANGED'),
      'param1' : '',
      'param2' : ''
    },
    'not changed': {
      'label': SUGAR.language.get('asol_Events', 'LBL_EVENT_NOT_CHANGED'),
      'param1' : '',
      'param2' : ''
    },
    'not equals bean value': {
      'label': SUGAR.language.get('asol_Events', 'LBL_EVENT_NOT_EQUALS_BEAN_VALUE'),
      'param1' : 'value',
      'param2' : ''
    }
  },

  fieldTypes: {
    'enum' : {
      'operators' : ['equals','not equals', 'one of','not one of','changed', 'not changed','not equals bean value'],
      'valueControl' : 'select'
    },
    'int' : {
      'operators' : ['equals','not equals', 'less than','more than','changed', 'not changed','not equals bean value'],
      'valueControl' : 'input'
    },
    'double' : {
      'operators' : ['equals','not equals', 'less than','more than','changed', 'not changed','not equals bean value'],
      'valueControl' : 'input'
    },
    'currency' : {
      'operators' : ['equals','not equals', 'less than','more than','changed', 'not changed','not equals bean value'],
      'valueControl' : 'input'
    },
    'tinyint(1)' : {
      'operators' : ['equals','not equals', 'changed', 'not changed'],
      'valueControl' : 'boolean'
    },
    'date' : {
      'operators' : ['equals','not equals', 'before date','after date','between','not between','last','not last','this','not this','next','not next','changed', 'not changed','not equals bean value'],
      'valueControl' : 'date'
    },
    'datetime' : {
      'operators' : ['equals','not equals', 'before date','after date','between','not between','last','not last','this','not this','next','not next','changed', 'not changed','not equals bean value'],
      'valueControl' : 'date'
    },

    'default' : {
      'operators' : ['equals','not equals', 'like','not like','changed', 'not changed','not equals bean value'],
      'valueControl' : 'input'
    },
  },

  conditionParam: function (name, controlType, paramSettings, currentValue, options) {
    var param;
    if(!paramSettings) {
      controlType ='hidden';
    }
    switch(controlType) {
      case 'select':
        param = "<select name='"+name;
        if( paramSettings == 'multi') {
          param += "[]' multiple=true size=3"
        } else {
          param += "'";
        }
        param += " style='width:140px'>";
        var selected = currentValue;
        if($.isArray(currentValue) == false) {
          selected = [currentValue];
        }

        for (var key  in options) {
          var sel = '';
          for( var s=0; s < selected.length; s++) {
            if(selected[s] == key ) {
              sel = ' selected';
              break;
            }
          }
          param += "<option value='" + key + "'"+sel+">" + options[key]+ "</option>";
        }
        param += "</select>";
        break;
      case 'input':
        param = "<input name='"+name+"' style='width:140px' value='"+currentValue+"'/>"
        break;
      case 'boolean':
        param = "<select name='"+name+"' style='width:140px'>"
        param += "<option value='1'"+(currentValue==1?'selected':'')+">" + SUGAR.language.get('asol_Events', 'LBL_EVENT_TRUE'), + "</option>";
        param += "<option value='0'"+(currentValue==0?'selected':'')+">" + SUGAR.language.get('asol_Events', 'LBL_EVENT_FALSE'), + "</option>";
        param += "</select>";
        break;
      case 'date':
        if( paramSettings=='period') {
          var periodOptions = {
            day : SUGAR.language.get('asol_Events', 'LBL_EVENT_DAY'),
            week : SUGAR.language.get('asol_Events', 'LBL_EVENT_WEEK'),
            month : SUGAR.language.get('asol_Events', 'LBL_EVENT_MONTH'),
            Nquarter : SUGAR.language.get('asol_Events', 'LBL_EVENT_NQUARTER'),
            Fquarter : SUGAR.language.get('asol_Events', 'LBL_EVENT_FQUARTER'),
            Nyear : SUGAR.language.get('asol_Events', 'LBL_EVENT_NYEAR'),
            Fyear : SUGAR.language.get('asol_Events', 'LBL_EVENT_FYEAR'),
          };
          param= "<select style='width:140px'>"
          for( var period in periodOptions) {
            var sel = '';
            if(currentValue == period ) {
                sel = ' selected';
            }
            param+= "<option value='"+period+"'"+sel+">" +periodOptions[period]+ "</option>"
          }
          param+="</select>"

        }else {
          param = "<input type='text' class='fielddate' name='"+name+"' id='"+name+"_" + ASOLEvents.idCount+ "' value='"+currentValue+"' style='width:120px' >"
          param +="<img class='fielddate' border='0' align='absmiddle' src='themes/default/images/jscalendar.gif' alt='Enter Date' "
              +"id='"+name+"_trigger_"+ASOLEvents.idCount + "'></img>";
          ASOLEvents.idCount++;
        }
        break;
      default:
        param = "<input type='hidden' name='"+name+"' value='"+currentValue+"'/>";
        break;
    }
    return param;
  },

  fieldCondition:function(name, type, op, param1, param2, options, key) {

    if (type.indexOf("int") === 0) {
      type = 'int';
    }
    if( ASOLEvents.fieldTypes[type] == null ) {
      type = 'default';
    }

    var field = "<td><b class='fieldName'>" + name+ "</b></td>";

    field += "<td>"
          + "<select class='operator' style='width:90px' >";
    var selected = ' selected';
    $.each(ASOLEvents.fieldTypes[type].operators, function(index, value) {
      var selected = '';
      if( op == value ) {
        selected = ' selected';
      }
      field += "<option value='" + value + "'" + selected + ">" + ASOLEvents.operators[value].label + "</option>";
    });
    field +="</select></td>";

    field += "<td class='param1'>"+ASOLEvents.conditionParam('Param1', ASOLEvents.fieldTypes[type].valueControl, ASOLEvents.operators[op].param1, param1, options) + "</td>" ;

    field += "<td class='param2'>"+ASOLEvents.conditionParam('Param2', ASOLEvents.fieldTypes[type].valueControl, ASOLEvents.operators[op].param2, param2, options) + "</td>" ;

    field += "<td>"
         +"<img border='0' src='themes/default/images/minus_inline.gif' title=\"" + ASOLEvents.labels.delButton + "\" OnMouseOver='this.style.cursor=\"pointer\"' OnMouseOut='this.style.cursor=\"default\"' onClick='if(confirm(\"" + ASOLEvents.labels.deleteRowAlert+ "\")) { this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode); } ' name='del_button'>"
         + "<input type='hidden' class='value_type' name='value_type' value='"
         + type
         + "'>"
         + "<input type='hidden' class='related_key' name='related_key' value='"+key+"'>"
//         + "<input type='hidden' name='is_related' value='"+(key?true:false)+"'>"
  //         + "<input type='hidden' name='index' value='" + i+ "'>"
         + "<input type='hidden' class='options' name='options' value='"
         + YAHOO.lang.JSON.stringify(options) + "'>"
        + "</td>"

    return "<tr class='condition'>"+field+"</tr>"
  },

  calendarSetup: function (){
    var fieldId = $(this).attr('id');
    var buttonId = $(this).next('img.fielddate').attr('id');
    Calendar.setup({ inputField : fieldId , daFormat : ASOLEvents.calendar_dateformat, button : buttonId, singleClick : true, dateStr : '', step : 1, weekNumbers:false });
    $(this).removeClass('fielddate');
  },



  insertConditions: function(idTable, idSelectFields, idSelectRelatedFields, key) {
    var table = $('#'+idTable);
    var fields = $('#'+idSelectFields);
    //alert(fields);
    var types = ASOLEvents.typeFields;
    var options = ASOLEvents.optionFields;

    var related_fields = $('#'+idSelectRelatedFields);
    var related_types = ASOLEvents.typeRelatedFields;
    var related_options = ASOLEvents.optionRelatedFields;

    var fieldsLabelsAlias = ASOLEvents.fieldLabels;

    fields.children('option').each(function(i, fieldOp){
      if(fieldOp.selected) {
        table.append(ASOLEvents.fieldCondition(fieldOp.value,types[i], 'equals','','', options[i], '' ));
      }
    });

    related_fields.children('option').each(function(i, fieldOp){
      if (fieldOp.selected) {
        table.append(ASOLEvents.fieldCondition(fieldOp.value,related_types[i], 'equals', '','', related_options[i], key ));
      }
    });

    $('input.fielddate', table).each(ASOLEvents.calendarSetup);

    $('.operator', table).change(function() {
      var row =$(this).closest('tr');
      var op = $(this).val();
      var type=$("input.value_type", row).val();
      var options =YAHOO.lang.JSON.parse($("input.options", row).val());
      $('.param1', row).html(ASOLEvents.conditionParam('Param1', ASOLEvents.fieldTypes[type].valueControl, ASOLEvents.operators[op].param1, '', options));
      $('.param2', row).html(ASOLEvents.conditionParam('Param2', ASOLEvents.fieldTypes[type].valueControl, ASOLEvents.operators[op].param2, '', options));

      $('input.fielddate', row).each(ASOLEvents.calendarSetup);
    });
  },

  loadConditions: function(idTable, idConditionValues) {
    var table = $('#'+idTable);
    var conditionValues = $('#'+idConditionValues).val();
    var conditions = YAHOO.lang.JSON.parse(conditionValues);

    $.each(conditions, function(index, value){
      table.append(ASOLEvents.fieldCondition(value.name,value.type, value.operator, value.param1, value.param2, value.options,value.relatedKey));
    });
    $('input.fielddate', table).each(ASOLEvents.calendarSetup);

    $('.operator', table).change(function() {
      var row =$(this).closest('tr');
      var op = $(this).val();
      var type=$("input.value_type", row).val();
      var options =YAHOO.lang.JSON.parse($("input.options", row).val());
      $('.param1', row).html(ASOLEvents.conditionParam('Param1', ASOLEvents.fieldTypes[type].valueControl, ASOLEvents.operators[op].param1, '', options));
      $('.param2', row).html(ASOLEvents.conditionParam('Param2', ASOLEvents.fieldTypes[type].valueControl, ASOLEvents.operators[op].param2, '', options));

      $('input.fielddate', row).each(ASOLEvents.calendarSetup);
    });
  },

  saveConditions: function(idConditionsTable) {
    var conditions= [];

    $('#'+idConditionsTable +' tr.condition').each(function() {
      var row =$(this);
      var row_vals = {
        name: $('.fieldName', row).text(),
        operator:$('.operator', row).val(),
        param1:$('.param1 :input', row).val(),
        param2:$('.param2 :input', row).val(),
        type:$('.value_type', row).val(),
        relatedKey:$('.related_key', row).val(),
        options:YAHOO.lang.JSON.parse($('.options', row).val()),
      };
      conditions.push(row_vals);
    })
    var json = YAHOO.lang.JSON.stringify(conditions);
    return json;
  }
};

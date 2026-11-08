function EditDateTime(idname, split_dt)
{
	var display_elem_id = '#'+idname+'_display';
	var position = $(display_elem_id).position();
	var inputHeight =  $(display_elem_id).outerHeight() + 2;

    var xScroll, yScroll;
    if (self.pageYOffset) {
      yScroll = self.pageYOffset;
      xScroll = self.pageXOffset;
    } else if (document.documentElement && document.documentElement.scrollTop) {
      yScroll = document.documentElement.scrollTop;
      xScroll = document.documentElement.scrollLeft;
    } else if (document.body) {// all other Explorers
      yScroll = document.body.scrollTop;
      xScroll = document.body.scrollLeft;
    }
    
	$('#dialog_'+idname).dialog({resizable: false,
        modal: true,
		dialogClass: 'noDlgTitle',
        width: split_dt ? 170 : 285,
        height: split_dt ? 70 : 50,
		position: [position.left - xScroll, position.top - yScroll + inputHeight],
		draggable: false,
		closeOnEscape: true
    });
}

function ajaxResult(o)
{
    if (o.status > 0)
    {
        ajaxStatus.flashStatus(SUGAR.language.get('app_strings', 'LBL_SAVED'), 1000);
    }
}

function SaveDateTime(idname, field_id, field_name, bean_name, compare_field, type, blank)
{
	var hidden_element_id = '#'+idname;
	var value = $(hidden_element_id).val();
	var display_element_id = '#'+idname+'_display';
	var cancel_element_id = '#'+idname+'_cancel';

	// Whether successful or not, change the display elem,
	// this is where the hidden cancel input comes into use
	$(display_element_id).val($(hidden_element_id).val())

	if(validateDatetimeField(hidden_element_id, compare_field, type, blank))
	{
		// Valid - Update elements
		$(display_element_id).css('color', '');
		$(cancel_element_id).val($(hidden_element_id).val())

		// Save
		var url = 'index.php?action=EditDate&list_item_id='+field_id+'&date_value='+value+'&module_bean='+bean_name+'&field='+field_name;
		ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_SAVING'));
		var cObj = YAHOO.util.Connect.asyncRequest('GET',url,{success: ajaxResult, failure: ajaxResult});

		// Close dialog
		var dialog_id = '#dialog_'+idname;
		$(dialog_id).dialog('destroy');
	}
	else
	{
		// Invalid - Flash red
		$(display_element_id).css('color', 'red');
		$(display_element_id).show('highlight',{color: '#FF0000'},'fast');
	}
}

function cancelDialog(idname)
{
	var display_element_id = '#'+idname+'_display';
	var hidden_element_id = '#'+idname;
	var cancel_element_id = '#'+idname+'_cancel';

	// Reset back to what it was (or last successful save)
	$(display_element_id).css('color', '');
	$(display_element_id).val($(cancel_element_id).val());
	$(hidden_element_id).val($(cancel_element_id).val());

	// Close dialog
	var dialog_id = '#dialog_'+idname;
	$(dialog_id).dialog('destroy');
}

function validateDatetimeField(field, compare_field, type, blank)
{
    
	if (trim($(field).val()).length == 0 && blank == true)
	{
		return true;
	}
	else if (trim($(field).val()).length == 0 && blank == false)
	{
		return false;
	}
	else
	{
		//Grab the adjacent field in the record with the name (compare_field)
		var cmp_elem = $(field).closest("tr").children("td[name="+compare_field+"]");
        
		// If the compare_field and element exist both exist then...
		if(compare_field != '' && $(cmp_elem).length)
		{            
			var cmp_elem_type = $(cmp_elem).attr("class");
            var cmp_elem_value;
            
			// Depending on whether its a widget or field, grab the text (value) in the field
			if (cmp_elem_type == "widget")
			{
				cmp_elem_value = $(cmp_elem).find(':input').val();
			}
			else
			{
				if ($(cmp_elem).find('span').length > 0)
					// Subpanel
					cmp_elem_value = $(cmp_elem).find('span').text();
				else
					// Listview
					cmp_elem_value = $(cmp_elem).text();
			}

			var date1 = trim(cmp_elem_value);
			var date2 = trim($(field).val());

			switch (type)
			{
				case 'isbefore' :
					if(trim(date1).length != 0 && isDateBefore(date1,date2))
						return false;
					break;

				case 'isafter' :
					if(trim(date1).length != 0 && !isDateBefore(date1,date2))
						return false;
					break;
			}
		}
	}
	return true;
}

function isDateBefore(value1,value2)
{
	var d1 = getDateObject(value1);
	var d2 = getDateObject(value2);
	if(typeof(d2) == 'boolean')
	{
		return true;
	}
	return d2 >= d1;
}

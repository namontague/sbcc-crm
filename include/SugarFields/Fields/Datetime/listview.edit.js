function EditDate(bean_id, field_name)
{
    var element_id = '#'+bean_id+'_'+field_name;
    $(element_id).removeAttr('disabled');
    var value = $(element_id).val();
}

function onChangeDate(bean_id, field_name, bean_name, compare_field, type, blank)
{
	var element_id = '#'+bean_id+'_'+field_name;	
	$(element_id).attr("disabled", true);

	if(validateDateField(element_id, compare_field, type, blank))
	{
		$(element_id).css('color', '');
		SaveDate(bean_id, field_name, bean_name, $(element_id).val());
	}
	else
	{
		$(element_id).css('color', 'red');
		$(element_id).show('highlight',{color: '#FF0000'},'fast');
	}		
}

function SaveDate(bean_id, field_name, bean_name, value)
{
	var element_id = '#'+bean_id+'_'+field_name;
    var url = 'index.php?action=EditDate&list_item_id='+bean_id+'&date_value='+value+'&module_bean='+bean_name+'&field='+field_name;
    ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_SAVING'));
    var cObj = YAHOO.util.Connect.asyncRequest('GET',url,{success: ajaxResult, failure: ajaxResult});
}

function validateDateField(field, compare_field, type, blank)
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

			// Depending on whether its a widget or field, grab the text (value) in the field
			if (cmp_elem_type == "widget")
			{
				var cmp_elem_value = $(cmp_elem).find(':input').val();
			}
			else
			{
				if ($(cmp_elem).find('span').length > 0)
					// Subpanel
					var cmp_elem_value = $(cmp_elem).find('span').text();				
				else
					// Listview
					var cmp_elem_value = $(cmp_elem).text();
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

function ajaxResult(o)
{
    if (o.status > 0)
    {
        ajaxStatus.flashStatus(SUGAR.language.get('app_strings', 'LBL_SAVED'), 1000);
    }
}

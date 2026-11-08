// eintel addition
function set_return_and_copy_targets(popup_reply_data)
{
	var name_to_value_array = popup_reply_data.name_to_value_array;
	// construct the POST request
	var query_array =  new Array();
	if (name_to_value_array != 'undefined') {
		for (var the_key in name_to_value_array) {
			if(the_key != 'toJSON') {
				query_array.push(the_key+"="+name_to_value_array[the_key]);
			}
		}
	}
	var module = get_module_name();
	var id = get_record_id();

	query_array.push('module='+module);
	query_array.push('http_method=get');
	query_array.push('return_module='+module);
	query_array.push('return_id='+id);
	query_array.push('record='+id);
	query_array.push('action=CopyTargets');

	var query_string = query_array.join('&');
	request_map[request_id] = '';

	ajaxStatus.showStatus(SUGAR.language.get('ProspectLists', 'LBL_COPYING'));
	var returnstuff = http_fetch_sync('index.php',query_string);
	request_id++;
	got_data(returnstuff, true);
	ajaxStatus.flashStatus(SUGAR.language.get('ProspectLists', 'LBL_COPIED'), 1500);

}

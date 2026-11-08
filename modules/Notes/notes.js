

function contacts_notes_filter(module_name, width, height, initial_filter, close_popup, hide_clear_button, popup_request_data, popup_mode, create, metadata){
    var parent_type = $('#parent_type').val();
    var parent_id = $('#parent_id').val();
    if(parent_type == "Accounts" && parent_id != ''){
        var account_id = parent_id;
        var account_name = $('#parent_name').val();
        initial_filter += '&account_id=' + account_id + '&account_name=' + account_name;
    }
    open_popup(module_name, width, height, initial_filter, close_popup, hide_clear_button, popup_request_data, popup_mode, create, metadata);
}

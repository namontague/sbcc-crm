function ajaxResult(o) {
  if (o.status > 0) {
    ajaxStatus.flashStatus(SUGAR.language.get('app_strings', 'LBL_SAVED'), 1000);
  }
}

function SaveBase(bean_name, field_name, record_id, value)
{
	var url = 'index.php?action=EditBase&module_bean=' + bean_name + '&field=' + field_name + '&list_item_id=' + record_id + '&value='+value;
	ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_SAVING'));
	var cObj = YAHOO.util.Connect.asyncRequest('GET',url,{success: ajaxResult, failure: ajaxResult});
}

function EditBase(elem_id, bean_name, field_name, record_id) {
    var element_id = '#'+elem_id;
    $(element_id).removeAttr('disabled');
    var value = $(element_id).val();
    $(element_id).focus();
    
    $(element_id).unbind('blur'); // Prevent duplicate calls
    $(element_id).blur(function() {
        if($(this).val() != value){
            SaveBase(bean_name, field_name, record_id, $(this).val());
        }
        $(element_id).attr("disabled", "disabled");
    });
}
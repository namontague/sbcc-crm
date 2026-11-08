function ajaxResult(o) {
  if (o.status > 0) {
    ajaxStatus.flashStatus(SUGAR.language.get('app_strings', 'LBL_SAVED'), 1000);
  }
}

function SaveBool(view_record_id, record_id, field_name, bean_name, value)
{
	var url = 'index.php?action=EditBool&viewrecord=' + view_record_id + '&record_id=' + record_id + '&bean=' + bean_name + '&field=' + field_name + '&value='+value;
	ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_SAVING'));
	var cObj = YAHOO.util.Connect.asyncRequest('GET',url,{success: ajaxResult, failure: ajaxResult});
}
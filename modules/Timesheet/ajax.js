var ajaxStatus = new SUGAR.ajaxStatusClass;
function fillFormVars(formObject) {
  formObject.elements['ajax'].value = 'true';
  formObject.elements['sugar_body_only'].value = 'true';
}
function clearFormVars(formObject) {
  formObject.elements['ajax'].value = '';
  formObject.elements['sugar_body_only'].value = '';
}
function ajaxPostForm(form_name) {
  var formObject = document.getElementById(form_name);
  fillFormVars(formObject);
  YAHOO.util.Connect.setForm(formObject);
  ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_SAVING'));
  var cObj = YAHOO.util.Connect.asyncRequest('POST', 'index.php', {success: ajaxResult, failure: ajaxResult});
}
function ajaxResult(o) {
  if (o.status > 0) {
    ajaxStatus.flashStatus(SUGAR.language.get('app_strings', 'LBL_SAVED'), 1000);
  }
}
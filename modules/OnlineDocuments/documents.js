function onlineDocEditView() {
  $('#EditView #googledoc_name').change(function(){
    var docName = $('#document_name');
    if( !docName.val()) {
      docName.val($(this).val())
    }
  });
}
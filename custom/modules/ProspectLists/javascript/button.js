


function showResetCompletionPanel()  {
    SUGAR.ajaxUI.resetCompletionPanel = new YAHOO.widget.Panel("ajaxloading",
    {
        width:"240px",
        fixedcenter:true,
        close:false,
        draggable:false,
        constraintoviewport:false,
        modal:true,
        visible:false
    });
    SUGAR.ajaxUI.resetCompletionPanel.setBody('<div id="loadingPage" align="center" style="vertical-align:middle;"><img src="' + SUGAR.themes.loading_image + '" align="absmiddle" /> <b>Resetting list, please wait...</b></div>');
    SUGAR.ajaxUI.resetCompletionPanel.render(document.body);

    if (document.getElementById('ajaxloading_c')) {
        document.getElementById('ajaxloading_c').style.display = '';
    }
    SUGAR.ajaxUI.resetCompletionPanel.show();
    return true;
}
function hideResetCompletionPanel(){
    SUGAR.ajaxUI.resetCompletionPanel.hide();
    if (document.getElementById('ajaxloading_c')) {
        document.getElementById('ajaxloading_c').style.display = 'none';
    }
}

/*
 * remove and add targets
 * 
 */
function resetTargets() {
    var id = $('form[name="DetailView"] input[name="record"]').val()
    $.ajax({
      url: 'index.php?action=resetTargets&module=ProspectLists&record_id='+id,
      dataType: 'json',
      async:true,
      beforeSend: showResetCompletionPanel,
      success:  function(data) {
                    if(data['success']!=null){
                        $('#button_info').html(data['success']['msg']);
                        syncSMSList(id);
                    } else if(data['error']!=null){
                        hideResetCompletionPanel();
                        $('#button_info').html(data['error']['msg']);
                    }
              },
      error: function(data,response) {
        $('#button_info').html(response);
        hideResetCompletionPanel(); 
      },
      done: function() { $('#button_info').text('Complete'); },
    });
}

/*
 * load an existing method
 */
function syncSMSList(id) {
    $.ajax({
      url: 'index.php?action=syncList&module=SMS_SMS&parent_module=ProspectLists&parent_id='+id,
      dataType: 'json',
      async:true,
      beforeSend: SUGAR.ajaxUI.resetCompletionPanel.setBody('<div id="loadingPage" align="center" style="vertical-align:middle;"><img src="' + SUGAR.themes.loading_image + '" align="absmiddle" /> <b>Syncing sms list, please wait...</b></div>'),
    }).done(function(data) {
        //add error mesages to display
        if(data['success'] != null){
            $('#button_info').html(data['success']['msg']);
        } else if(data['error']!=null){
            $('#button_info').html(data['error']['msg']);
        }
        hideResetCompletionPanel();
    }).fail(function(data,response) {
        if(response == 'parseerror') {
            $('#button_info').text(data.responseText);
        } else {
            $('#button_info').text(response);
        }
        
        hideResetCompletionPanel();
    });
}

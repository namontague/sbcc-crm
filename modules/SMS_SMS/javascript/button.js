/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */



function addList() {
    $('#button_info').text('');
    $.ajax({
      url: 'index.php?action=addlist&module=SMS_SMS&parent_id='+$('form[name="DetailView"] input[name="record"]').val()+'&parent_module='+$('form[name="DetailView"] input[name="module"]').val(),
      dataType: 'json',
      beforeSend: function() { showWait();},
      complete: function() {  hideWait(); },
      success:  function(data) {
                    if(data!=null) {
                        if(data['SUCCESS'] != null){
                            //change button into sync button button
                            $('#button_info').text('List '+data['SUCCESS']['name']+ ' created');
                            $('#button_add_list').attr('onClick','syncList()');
                            $('#button_add_list').val('Sync SMS List');
                            $('#button_add_list').attr('title','Sync SMS List');
                        }else if(data['EXISTS'] != null){
                            $('#button_add_list').attr('onClick','syncList()');
                            $('#button_add_list').val('Sync SMS List');
                            $('#button_add_list').attr('title','Sync SMS List');
                            $('#button_info').text(data['EXISTS']['msg']); 
                        }else{
                            $('#button_info').text('error');
                        }    
                    }else{
                        $('#button_info').text('error');
                    }
              },
      error: function(data,response) {
        $('#button_info').text(response);
      }
    });
}

function syncList() {
    $('#button_info').text('');
    $.ajax({
      url: 'index.php?action=syncList&module=SMS_SMS&parent_id='+$('form[name="DetailView"] input[name="record"]').val()+'&parent_module='+$('form[name="DetailView"] input[name="module"]').val(),
      dataType: 'json',
      beforeSend: function() { showWait(); },
      complete: function() {  hideWait(); },
      success:  function(data) {
                    if(data!=null) {
                        if(data['success'] != null){
                            $('#button_info').html(data['success']['msg']);
                        }else if(data['error'] != null){
                            $('#button_info').html(data['error']['msg']);
                        } else{
                            $('#button_info').html('Error with sync.');
                        }  
                    }else{
                        $('#button_info').html('Error with sync.');
                    }
              },
      error: function(data,response) {
        $('#button_info').text(response);
      }
    });
}


function showWait(){
    $('#ajax_wait').show();
}

function hideWait(){
     $('#ajax_wait').hide();
}
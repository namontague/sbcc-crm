/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(getList);

function getList() {
    $.ajax({
      url: 'index.php?action=getList&module=SMS_SMS&record='+$('input[name="record"]').val(),
      dataType: 'json',
      success:  function(data) {
                    if(data['success'] != null){
                        $('#number_list_span').text(data['success']['members_active']);
                    }else{
                        $('#number_list_span').text('error');
                    }    
              },
      error: function(data,response) {
        $('#number_list_span').text(response);
      }
    });
}

function sendSMS() {
    $('#send_sms').attr('disabled',true);
    $('#status_msg').text('Sending...');
    $.ajax({
      url: 'index.php?action=SendSMS&module=SMS_SMS&record='+$('input[name="record"]').val(),
      dataType: 'json',
      type:'POST',
      success:  function(data) {
                    if(data['success']){
                        $('#status_msg').text('Sent');
                        $('#status').text('Sent');
                        response = data['success'];
                    }else{
                        $('#send_sms').attr('disabled',false);
                        $('#status_msg').text('Failed');
                        $('#status').text('Failed');
                        response = data['data'];
                    }    
                    $('#response_data').text(response);
              },
      error: function(data,response) {
        $('#send_sms').attr('disabled',false);  
        $('#status_msg').text('Failed');
        $('#status').text('Failed');
        $('#response_data').text(data['responseText']);
      }
    });
}


// "Added To List" dropdown in the Replies subpanel: moves the reply's contact
// to the chosen reply list (SMS_RepliesController::action_setReplyList).
function smsReplySetList(select, replyId) {
    var $select = $(select);
    var $status = $select.next('.sms_reply_list_status');
    var previous = $select.attr('data-current');
    $select.attr('disabled', true);
    $status.text('Saving...');
    $.ajax({
      url: 'index.php?module=SMS_Replies&action=setReplyList&to_pdf=1',
      type: 'POST',
      dataType: 'json',
      data: {record: replyId, list_id: $select.val()},
      success:  function(data) {
                    $select.attr('disabled', false);
                    if(data && data['success']){
                        $select.attr('data-current', $select.val());
                        $status.text('Saved');
                        // refresh the list counts in the Reply Lists subpanel
                        if(typeof showSubPanel == 'function' && document.getElementById('list_subpanel_reply_lists')){
                            showSubPanel('reply_lists', null, true);
                        }
                    }else{
                        $select.val(previous);
                        $status.text((data && data['message']) ? data['message'] : 'Failed');
                    }
              },
      error: function() {
        $select.attr('disabled', false);
        $select.val(previous);
        $status.text('Failed');
      }
    });
}

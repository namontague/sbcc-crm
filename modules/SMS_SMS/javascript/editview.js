


$("#to_list_id_value").change(function() {
    $("#to_list_id").val($(this).val());  
    $("#to_list_name").val($("#to_list_id option:selected").text());  
    getList($(this).val());
});
$("#to_list_id").change(function() {
    $("#to_list_name").val($(this).find("option:selected").text()); 
    getList($(this).val());
});

$("#to_list_id").change();
//set the correct remaing char number when sms is loaded up
document.getElementById("msgCnt").value = document.getElementById('msgCnt').innerHTML = (document.getElementById("message").value.length);

function strcount(id) {
	var str=document.getElementById("message").value;
	document.getElementById('msgCnt').innerHTML=(str.length);
}
function chngcnt(id) {
	strcount(id);
	if(document.getElementById("message").value.length>612)	{      	
		document.getElementById("message").value = document.getElementById("message").value.substring(0, 612);	
		strcount(id);
		document.getElementById("message").focus();
		return true;
	}
}


function getList(id) {
    if(id && id!='none') {
        $.ajax({
          url: 'index.php?action=getList&module=SMS_SMS&list_id='+id,
          dataType: 'json',
          success:  function(data) {
                        if(data['success'] != null){
                            $('#number_list').text(data['success']['members_active']);
                        }else{
                            $('#number_list').text('error');
                        }    
                  },
          error: function(data,response) {
            $('#number_list').text(response);
          }
        });
    }else {
        $('#number_list').text(0);
    }
}

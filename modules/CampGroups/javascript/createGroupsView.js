
function viewHeight() {
    if(window.innerHeight) {
        return(window.innerHeight);
    }
    if(document.documentElement && document.documentElement.clientHeight) {
         return(document.documentElement.clientHeight);
    }
    if(document.body){
        return(document.body.clientHeight);
    }
    return 50;
}

function gradient(id, level) {
    var box = document.getElementById(id);
    box.style.opacity = level;
    box.style.MozOpacity = level;
    box.style.KhtmlOpacity = level;
    box.style.filter = "alpha(opacity=" + level * 100 + ")";
    box.style.display="block";
    return;
}


function fadein(id) {
    var level = 0;
    while(level <= 1) {
        setTimeout( "gradient('" + id + "'," + level + ")", (level* 1000) + 10);
        level += 0.01;
    }
}



///////////////////////
// Open the lightbox //
///////////////////////
function openbox(fadin,url) {
    var box;

    $.get(
        url,
        function(result){
            $("#group_container").html(result);
        }
    );

    box = document.getElementById('group_container');

    document.getElementById('filter').style.display='block';

    var filter = document.getElementById('filter');

    if(fadin) {
        gradient('group_container', 0);
    }else  {
        box.style.display='block';
    }

    if(navigator.appName.substring(0, 3) == "Mic")  { // for IE

        x = document.documentElement.scrollTop + document.body.scrollTop +
        box.offsetHeight / 4;
        box.style.top = x + "px";
        filter.style.top = document.documentElement.scrollTop + document.body.scrollTop;
    }else {
        //var top =  (viewHeight() - box.offsetHeight ) / 2;
        //box.style.top = top + 'px';
        box.style.position='fixed'; // fixed does not work on IE
        filter.style.position='fixed';
    }

    if(fadin) {
        fadein("group_container");
    }

}

// Close the lightbox

function closebox(data){
    document.getElementById('group_container').style.display='none';
    document.getElementById('filter').style.display='none';
    clear_everything();
}



function updateEquipment(current) {
    var formdata = $("form#createGroupsView").serializeArray();

//    var request = $.ajax({
//      url: 'index.php?action=updateequipment&module=Equipment',
//      type: 'POST',
//      dataType: 'json',
//      data: formdata,
//      parsedata: false,
//      success: success,
//      error: clear_everything,
//    });


//    request.fail(function(jqXHR, textStatus, errorThrown ) {
//      alert( "Request failed: " + textStatus + errorThrown );
//    });

}


//function success(data){
//
//    if(data){
//        ajaxStatus.flashStatus(SUGAR.language.get('app_strings', 'LBL_SAVED'), 1000);
//        document.getElementById('equipment_container').style.display='none';
//        document.getElementById('filter').style.display='none';
//    }
//    clear_everything();
//
//}



function clear_everything() {
    clear_all_errors();

    $("#group_window  form#EditView").find('input').each(function() {
        if($(this).attr('type') != "button"){
            $(this).val(null);

        }
    });

    $("#group_container").html(null);

}



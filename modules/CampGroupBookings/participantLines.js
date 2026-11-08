
var condln = 0;
var condln_count = 0;
var report_fields =  new Array();
var report_module = '';


function loadParticipantLine(participant){

    var prefix = 'campparticipants_';
    var ln = 0;

    ln = insertParticipantLine();

    for(var a in participant){
        if(document.getElementById(prefix + a + ln) != null){
            document.getElementById(prefix + a + ln).value = participant[a];
        }
    }
}


/**
 * Insert Header
 */

function insertParticipantHeader(){
    tablehead = document.createElement("thead");
    tablehead.id = "participantLines_head";
    document.getElementById('participantLines').appendChild(tablehead);

    var x=tablehead.insertRow(-1);
    x.id='participantLines_head';

    var a=x.insertCell(0);

    var b=x.insertCell(1);
    b.style.color="rgb(0,0,0)";
    b.innerHTML='First Name';

    var c=x.insertCell(2);
    c.style.color="rgb(0,0,0)";
    c.innerHTML='Last Name';

    var d=x.insertCell(3);
    d.style.color="rgb(0,0,0)";
    d.innerHTML='Age'

    var e=x.insertCell(4);
    e.style.color="rgb(0,0,0)";
    e.innerHTML='Phone';

    var f=x.insertCell(5);
    f.style.color="rgb(0,0,0)";
    f.innerHTML='Medical Conditions';
}

function insertParticipantLine(){

    if (document.getElementById('participantLines_head') == null) {
        insertParticipantHeader();
    } else {
        document.getElementById('participantLines_head').style.display = '';
    }


    tablebody = document.createElement("tbody");
    tablebody.id = "campparticipants_body" + condln;
    document.getElementById('participantLines').appendChild(tablebody);

    
    var x = tablebody.insertRow(-1);
    x.id = 'participant_line' + condln;

    var a = x.insertCell(0);
    if(action_sugar_grp1 == 'EditView'){
        a.innerHTML = "<button type='button' id='campparticipants_delete_line" + condln + "' class='button' tabindex='116' onclick='markParticipantLineDeleted(" + condln + ")'><img src='themes/default/images/id-ff-remove-nobg.png' alt='Delete Participant'></button><br>";
        a.innerHTML += "<input type='hidden' name='campparticipants_deleted[" + condln + "]' id='campparticipants_deleted" + condln + "' value='0'><input type='hidden' name='campparticipants_id[" + condln + "]' id='campparticipants_id" + condln + "' value=''>";
    } else{
        a.innerHTML = condln +1;
    }
    a.style.width = '5%';
 
    var b = x.insertCell(1);
    //b.id='campparticipants_first_name'+condln;
    
    b.innerHTML = "<input style='width:178px;' name='campparticipants_first_name["+ condln +"]' id='campparticipants_first_name" + condln + "' value='' title='' tabindex='116' />";
    
    
    b.style.width = '15%';

    var c = x.insertCell(2);
    //c.id='campparticipants_last_name'+condln;
    c.innerHTML = "<input style='width:178px;' name='campparticipants_last_name["+ condln +"]' id='campparticipants_last_name" + condln + "' value='' title='' tabindex='116' />";
    
    c.style.width = '15%';

    var d = x.insertCell(3);
    //d.id='campparticipants_participant_age'+condln;
    d.innerHTML = "<input style='width:178px;' name='campparticipants_participant_age["+ condln +"]' id='campparticipants_participant_age" + condln + "' value='' title='' tabindex='116' />";
    
    d.style.width = '15%';

    var e = x.insertCell(4);
    //e.id='campparticipants_phone_mobile'+condln;
    e.innerHTML = "<input style='width:178px;' name='campparticipants_phone_mobile["+ condln +"]' id='campparticipants_phone_mobile" + condln + "' value='' title='' tabindex='116' />";
    
    e.style.width = '15%';

    var f = x.insertCell(5);
    //f.id='campparticipants_medical_condition'+condln;
    
    f.innerHTML = "<select style='width:178px;' name='campparticipants_medical_condition["+ condln +"]' id='campparticipants_medical_condition" + condln + "' value='' title='' tabindex='116' ><option value='No'>No</option><option value='Yes'>Yes</option></select>";

    f.style.width = '30%';

    //mark each field as require to prevent saving
    addToValidate('EditView', 'campparticipants_first_name'+condln, 'varchar', true,'First Name' );
    addToValidate('EditView', 'campparticipants_last_name'+condln, 'varchar', true,'Last Name' );
    addToValidate('EditView', 'campparticipants_participant_age'+condln, 'varchar', true,'Age' );
    addToValidate('EditView', 'campparticipants_phone_mobile'+condln, 'varchar', true,'Phone' );
    
    condln++;
    condln_count++;
    return condln -1;
}

function markParticipantLineDeleted(ln)
{
    // collapse line; update deleted value
    document.getElementById('campparticipants_body' + ln).style.display = 'none';
    document.getElementById('campparticipants_deleted' + ln).value = '1';
    document.getElementById('campparticipants_delete_line' + ln).onclick = '';

    condln_count--;
    if(condln_count == 0){
        document.getElementById('participantLines_head').style.display = "none";
    }
}


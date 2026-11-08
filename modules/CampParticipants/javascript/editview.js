
$(function() {
    $(handleEditParticipant);
});

function handleEditParticipant() {
    if($('#contact_id').val()!="") {
        $id = $('#contact_id').val();
        $('#contact_name_data').append('<input type="button" name="participant_contact_edit_button" id="participant_contact_edit_button" class="button" title="Edit" value="Edit" onclick=open_popupedit("Contacts","' + $id + '",1100,800,{"call_back_function":"set_return_and_save_record","form_name":"' + action_sugar_grp1 + '","field_to_name_array":{"first_name":"first_name","last_name":"last_name","primary_address_number":"primary_address_number","primary_address_building_type":"primary_address_building_type","primary_address_street":"primary_address_street","primary_address_city":"primary_address_city","primary_address_state":"primary_address_state","primary_address_postalcode":"primary_address_postalcode","primary_address_country":"primary_address_country","phone_mobile":"phone_mobile","phone_home":"phone_home","phone_work":"phone_work","phone_other":"phone_other","Contacts0emailAddress0":"CampParticipants0emailAddress0","Contacts0emailAddressPrimaryFlag":"CampParticipants0emailAddressPrimaryFlag","Contacts0emailAddressOptOutFlag[]":"CampParticipants0emailAddressOptOutFlag[]","Contacts0emailAddressInvalidFlag[]":"CampParticipants0emailAddressInvalidFlag[]"}},true); >');
        
        $("#contact_name").attr('disabled','disabled');
        $("#primary_address_building_type").attr('disabled','disabled');
        $("#primary_address_number").attr('disabled','disabled');
        $("#primary_address_street").attr('disabled','disabled');
        $("#primary_address_city").attr('disabled','disabled');
        $("#primary_address_state").attr('disabled','disabled');
        $("#primary_address_postalcode").attr('disabled','disabled');
        $("#primary_address_country").attr('disabled','disabled');
        $("#CampParticipants0emailAddress0").attr('disabled','disabled');
            
    }
}

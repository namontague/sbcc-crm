/*********************************************
 * Custom Javascript Library for SugarCRM
 * (C) 2008 Enterprise Intelligence Systems Pty Ltd
 * NOT TO BE USED WITHOUT EXPRESS WRITTEN PERMISSION OF AUTHORS
 * *******************************************
 *
 * COMMENTED OUT 4/11/2008 to allow for work to continue on
 * Critical tasks. Interface will be left until next phase.
 *
 *
 * *******************************************/

/////////////////
// Setup
//////////////////

//
// $location_addresses[type][location_id][data]
//


var location_addresses = {}

 $(document).ready(function () {
    //load up  all locations for the current account
    if($('#account_id').val() !== ""){
        updateDropdown = false;
        if(contact_id = $("form#EditView input[name=\'record\']").val() === ""){
            updateDropdown = true;
        }
        loadAddressDetails($('#account_id').val(), updateDropdown);
    }else{
        //if no account load up the current locations of the contact
        //this is so we can set the address back to the orgianal address 
        //if the user clicks new then back to the orignal list item.
        contact_id = $("form#EditView input[name=\'record\']").val();
        loadContactAddressDetails(contact_id);
    }
   
 });

$(function(){
    $("#alt_checkbox").live("change",function () {      
        updateAltLocation();
        
    });    
    
    $("#alt_checkbox").change(function () {
        updateAltLocation()
    });
});
   
   


function updateAddressDropdown(type) {
    var location_combo = $('select#'+type+'_location_id');
    if( location_combo.length > 0 ) {
        var current_type = 'new';
        if(typeof location_addresses['address'] != 'undefined' &&
            typeof location_addresses['address']['active_'+type] != 'undefined'){
           
                //only default the primary address drop down
                current_type = location_addresses['address']['active_'+type];
            
        }
       
        var current_selection = location_combo.val();
       
       
        location_combo.html('');

        location_combo.append($("<option></option>")
                                .attr("value","new")
                                .text("new"))


        for( var c in location_addresses['address']) {
            location_combo.append($("<option></option>")
                                .attr("value",c)
                                .text(location_addresses['address'][c]['name']));
        }
        
        var exists = false;
        location_combo.find('option').each(function(){
            if (this.value == current_selection) {
                exists = true;
                location_combo.val(current_selection);
                return exists;
            }
        });
        
        if(current_selection == 'new'){
            location_combo.val(current_type);
        }
        
        
        //if orginal selection is not longer apart of the list clear out address details
        if(!exists){
            clearAddressFields(type);
        }

        //return current_type;
    } 
    //return;
}

function loadAddressDetails(id, updateDropdowns){
    var contact_id = '';
    contact_id = $("form#EditView input[name=\'record\']").val()
    if(id){
         $.ajax({
          url: 'index.php?module=Locations&action=load_account_locations&account_id='+id+'&contact_id='+contact_id,
          dataType: 'json',
          success:  function(data) {
                        if(data) {  
                            if(data['address']){
                                location_addresses = data;
                            }
                            if(updateDropdowns){
                                updateAddressDropdown('primary');
                                updateAddressDropdown('alt');
                            }
                        }
          },
          error: function() {
           location_addresses = {};
           clearlist();
          }
        });
    }else{
        location_addresses = {};
        clearlist();
    }
}

function loadContactAddressDetails(id){
    if(id){
         $.ajax({
          url: 'index.php?module=Locations&action=load_account_locations&contact_only=1&contact_id='+id,
          dataType: 'json',
          success:  function(data) {
                        if(data) {

                            if(typeof data['address'] != 'undefined' ){
                                location_addresses['address'] = data['billing'];
                            }

                            if(typeof data['address'] != 'undefined' ){  
                                location_addresses['address'] = data['shipping'];
                            }
                        }
          },
          error: function() {
           location_addresses = {};
           clearlist();
          }
        });
    }else{
        location_addresses = {};
        clearlist();
    }
}


function clearlist(){
    location_addresses = {};
    updateAddressDropdown('primary');
    updateAddressDropdown('alt');
    //updateAddressDetails('primary','new');
    //updateAddressDetails('alt','new');
}

function clearAddressFields(type){
    if(type == 'primary'){
        field = 'billing';
    }else{
        field = 'shipping';
    }
    for( var c in addressFields) {
        $('#'+type+addressFields[c]).val(null);
    }
}

YAHOO.util.Event.addListener("account_id", "change", function(){
   loadAddressDetails($(this).val(), true);
});

YAHOO.util.Event.addListener("primary_location_id", "change", function(){
    updateAddressDetails('primary',$(this).val());
    
    if($("#alt_checkbox").is(':checked')) {
        $('#alt_location_id').val($(this).val())
        updateAddressDetails('alt',$(this).val());
    }
    
});
YAHOO.util.Event.addListener("alt_location_id", "change", function(){
    updateAddressDetails('alt',$(this).val());
});


var addressFields = {
    '_address_building_type':'_address_building_type',
    '_address_number':'_address_number',
    '_address_street':'_address_street',
    '_address_city':'_address_city',
    '_address_state':'_address_state',
    '_address_postalcode':'_address_postalcode',    
    '_address_country':'_address_country'
}

function updateAddressDetails(type, id){
    if(type == 'primary'){
        field = 'billing';
    }else{
        field = 'shipping';
    }

    if(id != 'new'){
        for( var c in addressFields) {
            //$('#'+type+addressFields[c]).val(location_addresses[type][id][type+c]);
            $('#'+type+addressFields[c]).val(location_addresses['address'][id][c]);
        }
    }else{
        for( var c in addressFields) {
             $('#'+type+addressFields[c]).val(null);
        }
    }
}
 

function updateAltLocation(){
    var primaryAddress = $("#primary_location_id option:selected");
    
    if($('#alt_checkbox').is(':checked')) {
        
        $('#alt_location_id').attr('disabled','disabled');
        

        $('#alt_location_id').val(primaryAddress.val());
        
    }else{
        $('#alt_location_id').removeAttr('disabled');
        $('#alt_location_id').val('new');
        updateAddressDetails('alt', 'new')
    }
}
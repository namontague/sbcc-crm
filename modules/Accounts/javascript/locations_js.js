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

$(function(){

    $("#shipping_checkbox").live("change",function () {
        //$('#shipping_location_id').val($('#billing_location_id').val());
        updateShippingLocation();
//        if($("#shipping_checkbox").is(':checked')){
//            updateShippingLocation();
//         
//        }
        
    });    
    
//    $("#shipping_checkbox").change(function () {
//        updateShippingLocation()
//    });
    

});

    function setIndex() {
        this.selectedIndex = 1;
    }   

var location_addresses = {}

 $(document).ready(function () {
   //load up  all locations for the current account
   
   
        $.ajax({
          url: 'index.php?module=Locations&action=load_account_locations&account_id='+$("input[name=\'record\']").val(),
          dataType: 'json',
          success:  function(data) {
            location_addresses = data;
            },
          error: function() {
           location_addresses = {};
          }
        });

});



YAHOO.util.Event.addListener("billing_location_id", "change", function(){
    
    
    
    updateAddressDetails('billing',$(this).val());
    if($("#shipping_checkbox").is(':checked')){
        
        if($(this).val() == 'new'){
            $('#shipping_location_id').removeAttr('disabled');
        }
        
        $('#shipping_location_id').val($(this).val())
        updateAddressDetails('shipping',$(this).val());
        
    }
    
    
    
    
});
YAHOO.util.Event.addListener("shipping_location_id", "change", function(){

    updateAddressDetails('shipping',$(this).val());
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


function updateShippingLocation(){
    var billAddress = $("#billing_location_id option:selected");
    
    if($('#shipping_checkbox').is(':checked')) {
        
        $('#shipping_location_id').attr('disabled','disabled');

        $('#shipping_location_id').val(billAddress.val());
        
    }else{
        $('#shipping_location_id').removeAttr('disabled');

        $('#shipping_location_id').val('new');

        updateAddressDetails('shipping', 'new')
        
    }
}
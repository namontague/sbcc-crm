

    function show_ext_rest_openbravo() {
       
    }

    function retrieve_business_partner_accounts(id,module) {
       var append_to_field = $('#'+connector_field); 
       $.ajax({
          url:'index.php?module=Connectors&action=CallConnectorFunc&source_id=ext_rest_openbravo&source_func=retrieve_business_partner&record='+id+'&record_module='+module,
          dataType: 'json',
          success:  function(data) {
                        //if(data != false){
                            if(data["id"]){
                                var address;   
                                var html;

                                append_to_field.append('<div id="sync_div"><b id="view_match">View Matching Record</b></div>');

                                if(data["locations"].length > 0){
                                    for(var place in data["locations"]){
                                        if(typeof data["locations"][place]['invoiceTo'] != 'undefined'){
                                            address = set_address(data["locations"][place]);                                       
                                            break;
                                        }
                                    }
                                    if(!address){
                                        if(typeof data["locations"][0] != 'undefined'){
                                            address = set_address(data["locations"][0]);                                        
                                        }
                                    }
                                }

                                html = '<div id="inner_account_view">';
                                html += '<p id="'+data['id']+'">'+data['name']+' (<b>'+data['searchKey']+'</b>) </br>';

                                if(address){
                                    html += address;
                                }

                                html += '</p></br><input type="button" id="'+data['id']+'" class="link_sync_record_button" onclick="link_sync_openbrave(\''+id+'\',\''+module+'\',$(this).attr(\'id\'))"; value="Link" />';
                                html += "</div>";

                                $("b#view_match").mouseenter(function(){
                                    show_sync_openbrave(event, data, html);
                                });
                                $("button#link_sync_record_button").click(function(){
                                    link_sync_openbrave(id,module,data['id']);
                                });

                            }else{
                                append_to_field.append('<div id="sync_div"><button type="button" id="create_sync_record_button" > Create Record </button></div>');
                                $("button#create_sync_record_button").click(function(){
                                    create_sync_openbrave(id,module);
                                });
                            }
                        //}
                        //do nothing as no data was returned
                    },
          error: 
                    function() {
                        append_to_field.append( '<b>'+SUGAR.language.get('app_strings','ERROR_UNABLE_TO_RETRIEVE_DATA')+'</b>');
                    }
        });

    }

    function set_address(location){
        if(!location){
            return '';
        }
        var html;
        html = location["line1"]+' </br>';
        html += location['city']+' '+location['region']+' '+location['postCode']+'</br>';
        html += location['country']+'</br>';
        return html;

    }

    function retrieve_business_partner_contacts(id,module) {
       var append_to_field = $('#'+connector_field); 
       $.ajax({
          url:'index.php?module=Connectors&action=CallConnectorFunc&source_id=ext_rest_openbravo&source_func=retrieve_business_partner_contacts&record='+id+'&record_module='+module,
          dataType: 'json',
          success:  function(data) {
                        if(data){
                            if(data.length > 0){
                                append_to_field.append('<div id="sync_div"><b id="view_match">View Matching Records</b></div>');
                                var html = '<div id="inner_contact_view">';

                                for(var related_id in data){
                                    html += '<p id="'+data[related_id]['id']+'">'+data[related_id]['data']+' <button type="button" id="'+data[related_id]['id']+'" class="link_sync_record_button" href="#" onclick="link_sync_openbrave(\''+id+'\',\''+module+'\',$(this).attr(\'id\'))";>Link</button></p>'
                                }
                                html += "</div>";

                                data['name'] = 'Contacts related to Account'

                                $("b#view_match").mouseenter(function(){
                                    show_sync_openbrave(event, data, html);
                                });                            
                                //append_to_field.append('<div id="sync_div"><button type="button" id="create_sync_record_button" onclick="create_sync_openbrave(\''+id+'\',\''+module+'\');" > Create Record </button></div>');
                            //}else{
                            }
                            append_to_field.append('<div id="sync_div"><button type="button" id="create_sync_record_button" onclick="create_sync_openbrave(\''+id+'\',\''+module+'\');" > Create Record </button></div>');                        
                        }
                    },
          error: 
                    function() {
                        append_to_field.append( '<b>'+SUGAR.language.get('app_strings','ERROR_UNABLE_TO_RETRIEVE_DATA')+'</b>');
                    }
        });

    }

    var cd;
    function show_sync_openbrave(event, data, html) {
        var xCoordinate = event.clientX;
        var yCoordinate = event.clientY;

        cd = new CompanyDetailsDialog("openbravo_popup_div", '<div id="openbravo_div"></div>', xCoordinate, yCoordinate);
        
        var header;
        if(!data["name"]){
            header = 'Openbravo Data'
        }else{
            header = data["name"]
        }
        
        if(!html){
            html = data;
        }
        
        cd.setHeader(header);
        cd.text = html;
        cd.display();
        
    }

    function create_sync_openbrave(id,module) {
        if(typeof cd != "undefined"){
            cd.hide();   
        }
        ajaxStatus.showStatus('Syncing')
        
        $.ajax({
          url:'index.php?module=Connectors&action=CallConnectorFunc&source_id=ext_rest_openbravo&source_func=create_record&record='+id+'&record_module='+module,
          dataType: 'json',
          success:
                    function(data) {
                        if(typeof data['errorMessage'] != "undefined"){                                  
                            ajaxStatus.flashStatus(data['errorMessage'], 1000);
                        }
                        if (data['data']['id'] != null) {
                           
                            updateConnectorField(data['data']['id'],'Created');
//                             ajaxStatus.flashStatus('Created', 1000);
//                            $('img#dswidget_img').unbind('click');
//                            $('div#sync_div').remove();
//                            $('img#dswidget_img').show();
//                            
//                            $('img#dswidget_img').bind('click', function() {
//                                var url = connector_base_url + data['data']['id'];
//                                window.open(url);
//                            });
                        }

                    },
          error: 
                    function(data) {
                        if (data.status > 0) {
                            ajaxStatus.flashStatus(o.responseText, 1000);
                        }

                    }
        });

        return false;
    }


 

    function link_sync_openbrave(id,module,related_id) {
         cd.hide();    
        
         ajaxStatus.showStatus('Syncing')
         
         $.ajax({
          //url:'index.php?module=Connectors&action=CallConnectorFunc&source_id=ext_rest_openbravo&source_func=link_record_to_record&related_record_id='+related_id+'&record='+id+'&record_module='+module,
          url:'index.php?module=Connectors&action=CallConnectorFunc&source_id=ext_rest_openbravo&source_func=sync&related_record_id='+related_id+'&record='+id+'&record_module='+module,
          dataType: 'json',
          success: function(data) {
                        if (data['errorMessage'] != null) {
                            ajaxStatus.flashStatus(data['errorMessage'], 1000);
                        }
                        if (data['erp_id'] != null) {
                            updateConnectorField(data['erp_id'], 'Synced');
//                            ajaxStatus.flashStatus('Synced', 1000);
//                            $('img#dswidget_img').unbind('click');
//                            $('div#sync_div').remove();
//                            $('img#dswidget_img').show();
//
//                            $('img#dswidget_img').bind('click', function() {
//                               var url = connector_base_url + data['erp_id'];
//                               window.open(url);
//                            });
                        }

                    },
          error: ajaxResult
        });
    }

    function ajaxResult(o) {
        if (o.status > 0) {
            ajaxStatus.flashStatus(o.responseText, 1000);
        }
    }
    
    function updateConnectorField(id,msg){
        $('div#sync_div').remove();   

        $('img#dswidget_img').unbind('click');
        $('img#dswidget_img').show();
        $('img#dswidget_img').bind('click', function() {
            var url = connector_base_url + id;
            window.open(url);
        });
        
        ajaxStatus.flashStatus(msg, 1000);
    }
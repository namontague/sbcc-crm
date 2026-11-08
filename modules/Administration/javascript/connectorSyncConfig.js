    function setup() {
        $('#sync_children').bind('click',
                function() {
                    appendChildren()
                });
        $('input[name=master_option]').bind('click',       
            function() {
                var attr = $('#sync_button').attr('disabled');
                // For some browsers, `attr` is undefined; for others,
                // `attr` is false.  Check for both.
                if (typeof attr !== 'undefined' && attr !== false) {
                    $('#sync_button').removeAttr('disabled')
                }
                appendChildren();
            }); 
    }

    
    function appendChildren() {
        if ($('tr.sync_children_extra').length){
            $('tr.sync_children_extra').remove()
        }
        var extraOptions ='';
        if(getSelectedOption('master_option') == 'openbravo'){
            extraOptions = $('<tr class="sync_children_extra"><td style="border:none;">Create missing in Sugar</td><td style="border:none;"><input type="checkbox" name="sync_missing_sugarcrm" value="1"></td></tr>');

        }else{
            extraOptions = $('<tr class="sync_children_extra"><td style="border:none;">Create missing in Openbravo</td><td style="border:none;"><input type="checkbox" name="sync_missing_openbravo" value="1"></td>');
        }
        extraOptions.appendTo('#sync_options');

    }   
    
    function getSelectedOption(name){
        return $('input[name='+name+']:checked').val(); 
    }
    
    function sync_openbrave(){
        if(typeof getSelectedOption('master_option') == "undefined"){
            return ajaxStatus.flashStatus('No option for Master selected', 1500);
        }
        
        ajaxStatus.showStatus('Syncing')
        
        var sync_options = '&master='+getSelectedOption('master_option')
                         + '&sync_children='+getSelectedOption('sync_children')
                         + '&sync_missing_sugarcrm='+getSelectedOption('sync_missing_sugarcrm')
                         + '&sync_missing_openbravo='+getSelectedOption('sync_missing_openbravo')
        
        var source_func = 'start_mass_sync_process';
        
        
        $.ajax({
          url:'index.php?module=Connectors&action=CallConnectorFunc&source_id=ext_rest_openbravo&source_func=business_partner_count',
          dataType: 'json',
          success: 
                function(data) {
                    if(data['success'] == true){
                        var count = data['data']['count'];
                        if( count !=false){
                            var loops = Math.ceil(count / 1000);
                            //always update the entire sync table.
                            //nothing will be deleted and only duplicate matches will be updated, 
                            //new ones will be inserted
                            var page = 1; 
                            while(page <= loops){
                                if(page > 100 || page == false){
                                    //used for testing
                                    return;
                                }
                                execute_sync(page);
                                page++; 
                            }
                        }
                    }
                },
                
          error: 
                function(o) {
                    if (o.status > 0) {
                        ajaxStatus.flashStatus(o.statusText, 1000);
                    }
                }
        });
        
        
        $.ajax({
          url:'index.php?module=Connectors&action=CallConnectorFunc&source_id=ext_rest_openbravo&source_func='+source_func+sync_options,
          dataType: 'json',
          success:
                    function(data) {
                        if(data['success'] == true){
                            ajaxStatus.flashStatus('Sync Completed', 3000);
                            SUGAR.ajaxUI.lastURL = null;
                            SUGAR.ajaxUI.go('index.php?module=Administration&action=connectorSyncConfig');
                        }else{
                            var error_message = null;
                            if(data['errorMessage']){
                                error_message = data['errorMessage'] +'<br />';
                            }
                            if(data['bodyMessage']) {
                                error_message += data['bodyMessage'];
                            }
                            ajaxStatus.flashStatus(error_message, 3000);
                        }
                    
                        
                    },
          error: 
                    function(o) {
                        if (o.status > 0) {
                            ajaxStatus.flashStatus(o.statusText, 1000);
                        }
                    }
        });
       
       
    }
       
       
  
    
    function appendNotice(notice) {
        var message ='';
        message = $('<tr class="sync_notice"><td style="border:none;color:#ff0000">'+notice+'</td></tr>');
        message.appendTo('#sync_options');
    } 
    
    
    var page = 1;
    var count = 0;
    var sync_options = '';
    var source_func = 'start_mass_sync_process_v2';
    
    function sync_openbrave_v2() {
        if(typeof getSelectedOption('master_option') == "undefined"){
            return ajaxStatus.flashStatus('No option for Master selected', 1500);
        }

        ajaxStatus.showStatus('Syncing')

        sync_options = '&master='+getSelectedOption('master_option')
                         + '&sync_children='+getSelectedOption('sync_children')
                         + '&sync_missing_sugarcrm='+getSelectedOption('sync_missing_sugarcrm')
                         + '&sync_missing_openbravo='+getSelectedOption('sync_missing_openbravo')
                         + '&batch_size='+$('input[name=batch_size]').val()
       
       //set up sync and return number of loops
       
       $.ajax({
            url:'index.php?module=Connectors&action=CallConnectorFunc&source_id=ext_rest_openbravo&source_func=perpare_sync_process_v2'+sync_options,
            dataType: 'json',
            beforeSend:
                function(){
                    appendNotice('Starting to Perpare for Sync');
                },
            success: 
                function(data){
                    requests = data;
                    if(data['success'] == true){
                        count = data['data']['count'];
                        if( count != false){
                           if(typeof data['notify'] != "undefined"){
                                var length = data['notify'].length;
                                for (var i = 0; i < length; i++) {
                                    appendNotice(data['notify'][i]);                                     
                                }
                           }
                           appendNotice('Starting Sync');   
                           nextBatch();
                        }
                    }else{
                        if(typeof data['errorMessage'] != "undefined"){
                            appendNotice(data['errorMessage']);
                            ajaxStatus.flashStatus(data['errorMessage'], 3000);
                        }
                    }
                },
            error: 
                function(o) {
                    if (o.status > 0) {
                        ajaxStatus.flashStatus('Error '+o.statusText, 1500);
                    }
                },
            complete: 
               function(data) {
                   
               }
        });
        
    }
    
    function nextBatch() {
        if (page <= count) {
           execute();          
        }else{
           page = 1;
           appendNotice('End Sync');    
           ajaxStatus.flashStatus('Sync Completed', 1000);
           return;
        }
        
    }


    function execute(){
        $.ajax({
             url:'index.php?module=Connectors&action=CallConnectorFunc&source_id=ext_rest_openbravo&source_func=start_mass_sync_process_v2&page='+page+sync_options,
             dataType: 'json',
             beforeSend:
                    function(o){
                        if(page > 0){
                            appendNotice('Start execute Batch '+page+' of '+count);  
                        }
                    },
             success: 
                    function(data) {                        
                        if(data['success'] == true){                            
                            if(typeof data['notify'] != "undefined"){
                                var length = data['notify'].length;
                                for (var i = 0; i < length; i++) {
                                    appendNotice(data['notify'][i]);                                     
                                }
                            }
                        }else{
                            if(typeof data['errorMessage'] != "undefined"){
                                appendNotice(data['errorMessage']);
                                ajaxStatus.flashStatus(data['errorMessage'], 3000);
                            }
                        }
                    },
             complete: 
                    function(data) {
                        if (data.status > 0) {
                            //ajaxStatus.flashStatus(data.statusText + ' execute', 1000);
                            if(page > 0){
                                appendNotice('End execute Batch '+page+' of '+count);  
                            }
                            //ajaxStatus.showStatus('Syncing')
                        }
                        page++                           
                        if(typeof data['notify'] != "undefined"){
                            var length = data['notify'].length;
                            for (var i = 0; i < length; i++) {
                                appendNotice(data['notify'][i]);                                     
                            }
                        }
                        // start next batch
                        nextBatch();                        
                    },
             error: 
                    function(o) {
                        if (o.status > 0) {
                            ajaxStatus.flashStatus(o.statusText, 1000);
                        }
                    }
        });
    }
   
    
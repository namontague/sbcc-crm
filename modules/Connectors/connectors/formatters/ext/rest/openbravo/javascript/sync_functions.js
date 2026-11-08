   
    
    
    function appendOptions(){
        //remove image button
        $('#sync_img').remove();
        
        var optionDisplay = $('<span id = "sync_display" >'
                + '<table id="sync_options" cellpadding="0" cellspacing="0" border="0" style="width:25%;" >'
                + '<tr><td style="border:none;">Master</td><td style="border:none;"><input type="radio"  class="master_option" name="master_option" value="openbravo">Openbravo<input type="radio" class="master_option" name="master_option" value="sugarcrm">Sugar</td></tr>'
                + '<tr><td style="border:none;">Sync Children</td><td style="border:none;"><input type="checkbox" id="sync_children" name="sync_children" value="1"></td></tr>'
                + '</table>'
                + '</span>');
        optionDisplay.appendTo('#sync_button');              

        
        $('#sync_children').bind('click',
                function() {
                    appendChildren()
                });

        $('input[name=master_option]').bind('click',       
            function() {
                if (!$('#sync_ob_button').length){
                   var input = $('<input title="Sync" onclick="sync_openbrave(get_record_id(),get_module_name(),getSelectedOption());" type="button" name="Sync" id="sync_ob_button" value="Sync" >');
                   input.appendTo('#sync_button');     
                }
                if(getSelectedOption('sync_children')){
                    appendChildren();
                }
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

        if(getSelectedOption('sync_children')){
            extraOptions.appendTo('#sync_options');
        }else{
            $('tr.sync_children_extra').remove();
        }
    }   
    
    function getSelectedOption(name){
        return $('input[name='+name+']:checked').val(); 
    }
    
    function sync_openbrave(id, module){
        if(typeof getSelectedOption('master_option') == "undefined"){
            return ajaxStatus.flashStatus('No option for Master selected', 1500);
        }
        
        ajaxStatus.showStatus('Syncing')
        
        var sync_options = '&master='+getSelectedOption('master_option')
                         + '&sync_children='+getSelectedOption('sync_children')
                         + '&sync_missing_sugarcrm='+getSelectedOption('sync_missing_sugarcrm')
                         + '&sync_missing_openbravo='+getSelectedOption('sync_missing_openbravo')
        
        source_func = 'sync_data';
        $.ajax({
          url:'index.php?module=Connectors&action=CallConnectorFunc&source_id=ext_rest_openbravo&source_func='+source_func+'&record='+id+'&record_module='+module+sync_options,
          dataType: 'json',
          success:
                    function(data) {
                        if(data['success'] == true){
                            ajaxStatus.flashStatus('Sync Completed', 1000);
                            SUGAR.ajaxUI.lastURL = null;
                            SUGAR.ajaxUI.go('index.php?module='+module+'&action=DetailView&record='+id);
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
    

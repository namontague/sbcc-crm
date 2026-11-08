




    function generateSalesOrder(id) {
        if( id ) {
            $.ajax({
              url: 'index.php?module=Connectors&action=CallConnectorFunc&source_id=ext_rest_openbravo&source_func=process_sales_order&record='+id,
              dataType: 'json',
              success:  function (data){
                            ajaxStatus.flashStatus('Generating Sales Order',1000);
                            process_data(data);
                        },
              
              error: display_errorMessage
            });
        } else {
            display_errorMessage();
        }
    }

    function ajaxResult(o) {
        if (o.status > 0) {
            ajaxStatus.flashStatus(o.responseText, 1000);
        }
    }

    function process_data(data){
        if(data['errorMessage'] != null){
            var answer;
            if(data['create']){
                answer = confirm('Create this account in Openbravo?');
                if (answer){
                     ajaxStatus.flashStatus('Creating Business Partner',1000);
                     $.ajax({
                      url:'index.php?module=Connectors&action=CallConnectorFunc&source_id=ext_rest_openbravo&source_func=create_record&record='+data['record_id']+'&record_module=Accounts',
                      dataType: 'json',
                      success:  function(){
                                    generateSalesOrder(data['record_id']);
                                },
                      error: ajaxResult
                    });
                }

            } else if (data['link']){
                answer = confirm('Sync Account with record in Openbravo: '+data['data']['name']);
                if (answer){
                    ajaxStatus.flashStatus('Syncing',1000);
                     $.ajax({
                      url:'index.php?module=Connectors&action=CallConnectorFunc&source_id=ext_rest_openbravo&source_func=link_record_to_record&related_record_id='+data['data']['id']+'&record='+data['record_id']+'&record_module=Accounts',
                      dataType: 'json',
                      success:  function(result){
                                    generateSalesOrder(result['id']);
                                },
                      error: ajaxResult
                    });
                }
            }else{
                display_errorMessage(data['errorMessage']);                
            }


        }else{
            window.open(data['data']['url']);
        }
    }

                
   function display_errorMessage(errorMessage) {
       var msg = '';
       if(typeof errorMessage != 'undefined'){
           msg = errorMessage +'\n';            
       }
       
       msg += "Sales Order header could not be produced.";  
       
       alert(msg);
   }            
                